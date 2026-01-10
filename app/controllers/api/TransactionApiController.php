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
      'status' => $_GET['status'] ?? null,
      'search' => $_GET['search'] ?? null,
      'page' => max(1, (int)($_GET['page'] ?? 1)),
      'limit' => min(100, max(1, (int)($_GET['limit'] ?? 20)))
    ];
    
    $offset = ($filters['page'] - 1) * $filters['limit'];
    $filters['offset'] = $offset;
    
    $items = Transaction::list($filters);
    $total = Transaction::count($filters);
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
    
    $item = Transaction::find($id);
    if (!$item) {
      Response::error('Transaction not found', 404);
    }
    
    Response::success($item);
  }

  public function create() {
    ApiGuard::requireLogin();
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    $payload = [
      'type' => $data['type'] ?? '',
      'amount' => floatval($data['amount'] ?? 0),
      'currency' => $data['currency'] ?? 'VND',
      'category_id' => $data['category_id'] ?? null,
      'payment_method_id' => $data['payment_method_id'] ?? null,
      'vendor_id' => $data['vendor_id'] ?? null,
      'payer' => $data['payer'] ?? null,
      'occurred_at' => $data['occurred_at'] ?? date('Y-m-d H:i:s'),
      'note' => $data['note'] ?? null,
      'created_by' => Auth::user()['id'],
      'status' => $data['status'] ?? 'approved'
    ];
    
    if (empty($payload['type']) || !in_array($payload['type'], ['income', 'expense'])) {
      Response::error('Invalid transaction type', 400);
    }
    
    if ($payload['amount'] <= 0) {
      Response::error('Amount must be greater than 0', 400);
    }
    
    $id = Transaction::create($payload);
    if ($id) {
      Response::success(['id' => $id], 'Transaction created successfully', 201);
    } else {
      Response::error('Failed to create transaction', 500);
    }
  }

  public function update() {
    ApiGuard::requireLogin();
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
      Response::error('ID is required', 400);
    }
    
    $transaction = Transaction::find($id);
    if (!$transaction) {
      Response::error('Transaction not found', 404);
    }
    
    // 只有待审核的交易可以修改
    if ($transaction['status'] !== 'pending') {
      Response::error('Only pending transactions can be updated', 400);
    }
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    $payload = [];
    $allowedFields = ['type', 'amount', 'currency', 'category_id', 'payment_method_id', 'vendor_id', 'payer', 'occurred_at', 'note'];
    foreach ($allowedFields as $field) {
      if (isset($data[$field])) {
        $payload[$field] = $data[$field];
      }
    }
    
    if (empty($payload)) {
      Response::error('No fields to update', 400);
    }
    
    if (Transaction::update($id, $payload)) {
      Response::success(['id' => $id], 'Transaction updated successfully');
    } else {
      Response::error('Failed to update transaction', 500);
    }
  }

  public function void() {
    ApiGuard::requireRole(['owner', 'accountant']);
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
      Response::error('ID is required', 400);
    }
    
    if (Transaction::void($id)) {
      Response::success(['id' => $id], 'Transaction voided successfully');
    } else {
      Response::error('Failed to void transaction', 500);
    }
  }
}
