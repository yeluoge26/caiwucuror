<?php
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/ApiGuard.php';
require_once __DIR__ . '/../../models/CashClosing.php';
require_once __DIR__ . '/../../models/Transaction.php';

class CashClosingApiController {
  public function list() {
    ApiGuard::requireLogin();
    
    $filters = [
      'date' => $_GET['date'] ?? null,
      'from_date' => $_GET['from_date'] ?? null,
      'to_date' => $_GET['to_date'] ?? null,
      'status' => $_GET['status'] ?? null,
      'page' => max(1, (int)($_GET['page'] ?? 1)),
      'limit' => min(100, max(1, (int)($_GET['limit'] ?? 20)))
    ];
    
    $items = CashClosing::list($filters);
    $total = CashClosing::count($filters);
    $totalPages = ceil($total / $filters['limit']);
    
    Response::success([
      'items' => $items,
      'pagination' => [
        'page' => $filters['page'],
        'limit' => $filters['limit'],
        'total' => $total,
        'totalPages' => $totalPages
      ]
    ]);
  }

  public function view() {
    ApiGuard::requireLogin();
    $id = $_GET['id'] ?? null;
    if (!$id) {
      Response::error('ID is required', 400);
    }
    
    $closing = CashClosing::find($id);
    if (!$closing) {
      Response::error('Cash closing not found', 404);
    }
    
    // 计算理论余额和差额
    $previousClosing = CashClosing::getPreviousClosing($closing['date']);
    $openingCash = $previousClosing ? floatval($previousClosing['cash_counted']) : 0;
    $theoretical = CashClosing::calculateTheoreticalBalance($closing['date'], $openingCash);
    $difference = floatval($closing['cash_counted']) - $theoretical['balance'];
    
    $closing['theoretical_balance'] = $theoretical['balance'];
    $closing['difference'] = $difference;
    $closing['opening_cash'] = $openingCash;
    $closing['cash_income'] = $theoretical['income'];
    $closing['cash_expense'] = $theoretical['expense'];
    
    Response::success($closing);
  }

  public function create() {
    ApiGuard::requireRole(['manager']);
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $date = $data['date'] ?? date('Y-m-d');
    $cashCounted = floatval($data['cash_counted'] ?? 0);
    $cashNote = $data['cash_note'] ?? null;
    
    // 检查是否已存在
    $existing = CashClosing::findByDate($date);
    if ($existing) {
      Response::error('Cash closing for this date already exists', 400);
    }
    
    // 计算理论余额
    $previousClosing = CashClosing::getPreviousClosing($date);
    $openingCash = $previousClosing ? floatval($previousClosing['cash_counted']) : 0;
    $theoretical = CashClosing::calculateTheoreticalBalance($date, $openingCash);
    $difference = $cashCounted - $theoretical['balance'];
    
    // 验证：差额不为0时必须填写说明
    if (abs($difference) > 0.01 && empty($cashNote)) {
      Response::error('Cash note is required when there is a difference', 400);
    }
    
    $payload = [
      'date' => $date,
      'cash_counted' => $cashCounted,
      'cash_note' => $cashNote,
      'created_by' => Auth::user()['id'],
      'status' => 'draft'
    ];
    
    $id = CashClosing::create($payload);
    if ($id) {
      Response::success(['id' => $id], 'Cash closing created successfully', 201);
    } else {
      Response::error('Failed to create cash closing', 500);
    }
  }

  public function submit() {
    ApiGuard::requireRole(['manager']);
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
      Response::error('ID is required', 400);
    }
    
    $closing = CashClosing::find($id);
    if (!$closing) {
      Response::error('Cash closing not found', 404);
    }
    
    if ($closing['status'] !== 'draft') {
      Response::error('Only draft cash closings can be submitted', 400);
    }
    
    // 验证差额说明
    $date = $closing['date'];
    $previousClosing = CashClosing::getPreviousClosing($date);
    $openingCash = $previousClosing ? floatval($previousClosing['cash_counted']) : 0;
    $theoretical = CashClosing::calculateTheoreticalBalance($date, $openingCash);
    $difference = floatval($closing['cash_counted']) - $theoretical['balance'];
    
    if (abs($difference) > 0.01 && empty($closing['cash_note'])) {
      Response::error('Cash note is required when there is a difference', 400);
    }
    
    if (CashClosing::submit($id)) {
      Response::success(['id' => $id], 'Cash closing submitted successfully');
    } else {
      Response::error('Failed to submit cash closing', 500);
    }
  }

  public function review() {
    ApiGuard::requireRole(['owner', 'accountant']);
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $id = $data['id'] ?? $_GET['id'] ?? null;
    $approved = ($data['approved'] ?? false) === true;
    $reviewNote = $data['review_note'] ?? null;
    
    if (!$id) {
      Response::error('ID is required', 400);
    }
    
    $closing = CashClosing::find($id);
    if (!$closing) {
      Response::error('Cash closing not found', 404);
    }
    
    if ($closing['status'] !== 'submitted') {
      Response::error('Only submitted cash closings can be reviewed', 400);
    }
    
    if (CashClosing::review($id, Auth::user()['id'], $approved, $reviewNote)) {
      Response::success(['id' => $id, 'approved' => $approved], 'Cash closing reviewed successfully');
    } else {
      Response::error('Failed to review cash closing', 500);
    }
  }
}
