<?php
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/ApiGuard.php';
require_once __DIR__ . '/../../models/Transaction.php';

class TransactionApiController {
  public function list() {
    ApiGuard::requireLogin();
    $filters = [
      'type' => $_GET['type'] ?? null,
      'from' => $_GET['from'] ?? null,
      'to' => $_GET['to'] ?? null,
      'category_id' => $_GET['category_id'] ?? null,
      'payment_method_id' => $_GET['payment_method_id'] ?? null,
      'created_by' => $_GET['created_by'] ?? null,
      'search' => $_GET['search'] ?? null,
      'limit' => $_GET['limit'] ?? 100
    ];
    $items = Transaction::list($filters);
    Response::json(['data' => $items]);
  }

  public function view() {
    ApiGuard::requireLogin();
    $id = $_GET['id'] ?? null;
    if (!$id) Response::json(['error' => 'id_required'], 400);
    $item = Transaction::find($id);
    if (!$item) Response::json(['error' => 'not_found'], 404);
    Response::json(['data' => $item]);
  }

  public function create() {
    ApiGuard::requireLogin();
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $payload = [
      'type' => $data['type'] ?? '',
      'amount' => $data['amount'] ?? 0,
      'currency' => $data['currency'] ?? 'VND',
      'category_id' => $data['category_id'] ?? null,
      'payment_method_id' => $data['payment_method_id'] ?? null,
      'vendor_id' => $data['vendor_id'] ?? null,
      'payer' => $data['payer'] ?? null,
      'occurred_at' => $data['occurred_at'] ?? date('Y-m-d H:i:s'),
      'note' => $data['note'] ?? null,
      'created_by' => Auth::user()['id'],
      'status' => 'approved'
    ];
    $id = Transaction::create($payload);
    if ($id) {
      Response::json(['data' => ['id' => $id]]);
    } else {
      Response::json(['error' => 'create_failed'], 500);
    }
  }

  public function void() {
    ApiGuard::requireRole(['owner', 'accountant']);
    $id = $_GET['id'] ?? null;
    if (!$id) Response::json(['error' => 'id_required'], 400);
    Transaction::void($id);
    Response::json(['data' => true]);
  }
}
