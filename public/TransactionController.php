<?php
require_once __DIR__ . '/../models/Transaction.php';

class TransactionController {

  public function list() {
    Auth::requireLogin();

    $items = Transaction::list([
      'type' => $_GET['type'] ?? null,
      'from' => $_GET['from'] ?? null,
      'to'   => $_GET['to'] ?? null,
    ]);

    include __DIR__ . '/../views/transactions/list.php';
  }

  public function create() {
    Auth::requireLogin();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        exit('CSRF invalid');
      }

      Transaction::create([
        'type' => $_POST['type'],
        'amount' => $_POST['amount'],
        'category_id' => $_POST['category_id'],
        'payment_method_id' => $_POST['payment_method_id'],
        'vendor_id' => $_POST['vendor_id'] ?? null,
        'occurred_at' => $_POST['occurred_at'],
        'note' => $_POST['note'],
        'created_by' => Auth::user()['id'],
      ]);

      header('Location: /index.php?r=transactions/list');
      exit;
    }

    include __DIR__ . '/../views/transactions/create.php';
  }
}
