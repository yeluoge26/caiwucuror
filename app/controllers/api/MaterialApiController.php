<?php
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/ApiGuard.php';
require_once __DIR__ . '/../../models/Material.php';

class MaterialApiController {
  public function list() {
    ApiGuard::requireLogin();
    
    $filters = [
      'category' => $_GET['category'] ?? null,
      'store' => $_GET['store'] ?? null,
      'search' => $_GET['search'] ?? null,
      'grouped' => isset($_GET['grouped']) ? (bool)$_GET['grouped'] : false,
      'page' => max(1, (int)($_GET['page'] ?? 1)),
      'limit' => min(100, max(1, (int)($_GET['limit'] ?? 20)))
    ];
    
    if ($filters['grouped']) {
      $items = Material::allGroupedByName();
    } else {
      $items = Material::all();
    }
    
    // 应用筛选
    if (!empty($filters['category'])) {
      $items = array_filter($items, function($item) use ($filters) {
        return $item['category'] === $filters['category'];
      });
    }
    if (!empty($filters['store'])) {
      $items = array_filter($items, function($item) use ($filters) {
        if ($filters['grouped']) {
          return strpos($item['store_list'] ?? '', $filters['store']) !== false;
        }
        return $item['store'] === $filters['store'];
      });
    }
    if (!empty($filters['search'])) {
      $search = strtolower($filters['search']);
      $items = array_filter($items, function($item) use ($search) {
        return strpos(strtolower($item['name']), $search) !== false;
      });
    }
    
    $items = array_values($items);
    $total = count($items);
    $offset = ($filters['page'] - 1) * $filters['limit'];
    $items = array_slice($items, $offset, $filters['limit']);
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
    
    $material = Material::find($id);
    if (!$material) {
      Response::error('Material not found', 404);
    }
    
    Response::success($material);
  }

  public function create() {
    ApiGuard::requireLogin();
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    $payload = [
      'name' => $data['name'] ?? '',
      'category' => $data['category'] ?? '',
      'store' => $data['store'] ?? 'coffee',
      'unit' => $data['unit'] ?? 'g',
      'quantity' => floatval($data['quantity'] ?? 0),
      'min_quantity' => floatval($data['min_quantity'] ?? 0),
      'note' => $data['note'] ?? null,
      'created_by' => Auth::user()['id']
    ];
    
    if (empty($payload['name']) || empty($payload['category'])) {
      Response::error('Name and category are required', 400);
    }
    
    if (Material::create($payload)) {
      Response::success(null, 'Material created successfully', 201);
    } else {
      Response::error('Failed to create material', 500);
    }
  }

  public function update() {
    ApiGuard::requireLogin();
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
      Response::error('ID is required', 400);
    }
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    $payload = [];
    $allowedFields = ['name', 'category', 'store', 'unit', 'quantity', 'min_quantity', 'note'];
    foreach ($allowedFields as $field) {
      if (isset($data[$field])) {
        $payload[$field] = $data[$field];
      }
    }
    
    if (empty($payload)) {
      Response::error('No fields to update', 400);
    }
    
    if (Material::update($id, $payload)) {
      Response::success(['id' => $id], 'Material updated successfully');
    } else {
      Response::error('Failed to update material', 500);
    }
  }

  public function consume() {
    ApiGuard::requireLogin();
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $materialId = $data['material_id'] ?? null;
    $quantity = floatval($data['quantity'] ?? 0);
    $note = $data['note'] ?? null;
    
    if (!$materialId || $quantity <= 0) {
      Response::error('Material ID and quantity are required', 400);
    }
    
    $material = Material::find($materialId);
    if (!$material) {
      Response::error('Material not found', 404);
    }
    
    if ($material['quantity'] < $quantity) {
      Response::error('Insufficient quantity', 400);
    }
    
    $newQuantity = $material['quantity'] - $quantity;
    if (Material::update($materialId, ['quantity' => $newQuantity])) {
      Response::success([
        'material_id' => $materialId,
        'old_quantity' => $material['quantity'],
        'new_quantity' => $newQuantity,
        'consumed' => $quantity
      ], 'Material consumed successfully');
    } else {
      Response::error('Failed to consume material', 500);
    }
  }
}
