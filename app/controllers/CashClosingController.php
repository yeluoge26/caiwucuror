<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../models/CashClosing.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/PaymentMethod.php';

class CashClosingController {
  public function create() {
    Auth::requireLogin();
    Auth::requireRole(['manager']);

    $date = $_GET['date'] ?? date('Y-m-d');
    $error = null;
    $existing = CashClosing::findByDate($date);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $cashCounted = floatval($_POST['cash_counted'] ?? 0);
        $cashNote = $_POST['cash_note'] ?? null;

        // 计算理论现金余额
        $previousClosing = CashClosing::getPreviousClosing($date);
        $openingCash = $previousClosing ? floatval($previousClosing['cash_counted']) : 0;
        
        $theoretical = CashClosing::calculateTheoreticalBalance($date, $openingCash);
        $difference = $cashCounted - $theoretical['balance'];

        // 验证：差额不为0时必须填写说明
        if (abs($difference) > 0.01 && empty($cashNote)) {
          $error = __('cash_closing.difference_note_required');
        } else {
          if ($existing) {
            // 更新现有记录
            CashClosing::update($existing['id'], [
              'cash_counted' => $cashCounted,
              'cash_note' => $cashNote,
              'status' => 'draft'
            ]);
            $id = $existing['id'];
          } else {
            // 创建新记录
            $id = CashClosing::create([
              'date' => $date,
              'created_by' => Auth::user()['id'],
              'cash_counted' => $cashCounted,
              'cash_note' => $cashNote,
              'status' => 'draft'
            ]);
          }

          if ($id) {
            header('Location: /index.php?r=cash_closings/view&id=' . $id);
            exit;
          } else {
            $error = __('cash_closing.create_failed');
          }
        }
      }
    }

    // 计算理论现金余额
    $previousClosing = CashClosing::getPreviousClosing($date);
    $openingCash = $previousClosing ? floatval($previousClosing['cash_counted']) : 0;
    $theoretical = CashClosing::calculateTheoreticalBalance($date, $openingCash);

    $data = $existing ?: [
      'date' => $date,
      'cash_counted' => 0,
      'cash_note' => null,
      'status' => 'draft'
    ];

    include __DIR__ . '/../views/cash_closings/create.php';
  }

  public function list() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 50;
    $offset = ($page - 1) * $perPage;

    $filters = [
      'from_date' => $_GET['from_date'] ?? null,
      'to_date' => $_GET['to_date'] ?? null,
      'status' => $_GET['status'] ?? null,
      'limit' => $perPage,
      'offset' => $offset
    ];

    $items = CashClosing::list($filters);
    $total = CashClosing::count($filters);
    $totalPages = ceil($total / $perPage);

    include __DIR__ . '/../views/cash_closings/list.php';
  }

  public function view() {
    Auth::requireLogin();
    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=cash_closings/list');
      exit;
    }

    $item = CashClosing::find($id);
    if (!$item) {
      header('Location: /index.php?r=cash_closings/list');
      exit;
    }

    // 计算理论现金余额
    $previousClosing = CashClosing::getPreviousClosing($item['date']);
    $openingCash = $previousClosing ? floatval($previousClosing['cash_counted']) : 0;
    $theoretical = CashClosing::calculateTheoreticalBalance($item['date'], $openingCash);
    $difference = floatval($item['cash_counted']) - $theoretical['balance'];

    // 获取各支付方式的流水汇总
    $paymentMethods = PaymentMethod::all(['is_active' => 1]);
    $paymentSummaries = [];
    foreach ($paymentMethods as $pm) {
      $income = Transaction::getSummary([
        'from' => $item['date'] . ' 00:00:00',
        'to' => $item['date'] . ' 23:59:59',
        'payment_method_id' => $pm['id'],
        'type' => 'income',
        'status' => 'approved'
      ]);
      $expense = Transaction::getSummary([
        'from' => $item['date'] . ' 00:00:00',
        'to' => $item['date'] . ' 23:59:59',
        'payment_method_id' => $pm['id'],
        'type' => 'expense',
        'status' => 'approved'
      ]);
      $paymentSummaries[] = [
        'payment_method' => $pm,
        'income' => $income['income'] ?? 0,
        'expense' => $expense['expense'] ?? 0
      ];
    }

    include __DIR__ . '/../views/cash_closings/view.php';
  }

  public function submit() {
    Auth::requireLogin();
    Auth::requireRole(['manager']);

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=cash_closings/list');
      exit;
    }

    $item = CashClosing::find($id);
    if (!$item || $item['status'] !== 'draft') {
      header('Location: /index.php?r=cash_closings/list');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        die('CSRF invalid');
      }

      // 验证：差额不为0时必须填写说明
      $previousClosing = CashClosing::getPreviousClosing($item['date']);
      $openingCash = $previousClosing ? floatval($previousClosing['cash_counted']) : 0;
      $theoretical = CashClosing::calculateTheoreticalBalance($item['date'], $openingCash);
      $difference = floatval($item['cash_counted']) - $theoretical['balance'];

      if (abs($difference) > 0.01 && empty($item['cash_note'])) {
        header('Location: /index.php?r=cash_closings/view&id=' . $id . '&error=' . urlencode(__('cash_closing.difference_note_required')));
        exit;
      }

      CashClosing::submit($id);
      header('Location: /index.php?r=cash_closings/view&id=' . $id);
      exit;
    }

    header('Location: /index.php?r=cash_closings/view&id=' . $id);
    exit;
  }

  public function review() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'accountant']);

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=cash_closings/list');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        die('CSRF invalid');
      }

      $status = $_POST['status'] ?? 'approved';
      $note = $_POST['review_note'] ?? null;

      // 驳回时必须填写原因
      if ($status === 'rejected' && empty($note)) {
        header('Location: /index.php?r=cash_closings/view&id=' . $id . '&error=' . urlencode(__('cash_closing.reject_note_required')));
        exit;
      }

      CashClosing::review($id, $status, $note, Auth::user()['id']);
      header('Location: /index.php?r=cash_closings/view&id=' . $id);
      exit;
    }

    header('Location: /index.php?r=cash_closings/view&id=' . $id);
    exit;
  }
}

