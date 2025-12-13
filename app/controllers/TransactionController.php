<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/PaymentMethod.php';
require_once __DIR__ . '/../models/Vendor.php';

class TransactionController {
  public function list() {
    Auth::requireLogin();

    $filters = [
      'type' => $_GET['type'] ?? null,
      'from' => $_GET['from'] ?? null,
      'to' => $_GET['to'] ?? null,
      'category_id' => $_GET['category_id'] ?? null,
      'payment_method_id' => $_GET['payment_method_id'] ?? null,
      'created_by' => $_GET['created_by'] ?? null,
      'search' => $_GET['search'] ?? null,
      'limit' => 200
    ];

    $items = Transaction::list($filters);
    $categories = Category::all(['is_active' => 1]);
    $paymentMethods = PaymentMethod::all(['is_active' => 1]);
    $user = Auth::user();

    include __DIR__ . '/../views/transactions/list.php';
  }

  public function create() {
    Auth::requireLogin();

    $categories = Category::all(['is_active' => 1]);
    $paymentMethods = PaymentMethod::all(['is_active' => 1]);
    $vendors = Vendor::all(['is_active' => 1]);
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $data = [
          'type' => $_POST['type'] ?? '',
          'amount' => $_POST['amount'] ?? 0,
          'currency' => $_POST['currency'] ?? 'VND',
          'category_id' => $_POST['category_id'] ?? null,
          'payment_method_id' => $_POST['payment_method_id'] ?? null,
          'vendor_id' => !empty($_POST['vendor_id']) ? $_POST['vendor_id'] : null,
          'occurred_at' => $_POST['occurred_at'] ?? date('Y-m-d H:i:s'),
          'note' => $_POST['note'] ?? null,
          'created_by' => Auth::user()['id'],
          'status' => 'approved'
        ];

        if (Transaction::create($data)) {
          header('Location: /index.php?r=transactions/list');
          exit;
        } else {
          $error = __('transaction.create_failed');
        }
      }
    }

    include __DIR__ . '/../views/transactions/create.php';
  }

  public function view() {
    Auth::requireLogin();

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=transactions/list');
      exit;
    }

    $transaction = Transaction::find($id);
    if (!$transaction) {
      header('Location: /index.php?r=transactions/list');
      exit;
    }

    $user = Auth::user();
    include __DIR__ . '/../views/transactions/view.php';
  }

  public function void() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'accountant']);

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=transactions/list');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        die('CSRF invalid');
      }
      Transaction::void($id);
      header('Location: /index.php?r=transactions/list');
      exit;
    }

    $transaction = Transaction::find($id);
    include __DIR__ . '/../views/transactions/void.php';
  }
}

