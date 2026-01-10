<?php
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/ApiGuard.php';
require_once __DIR__ . '/../../core/DB.php';
require_once __DIR__ . '/../../models/Asset.php';

class AssetApiController {
  public function list() {
    ApiGuard::requireLogin();
    
    $filters = [
      'category' => $_GET['category'] ?? null,
      'subcategory' => $_GET['subcategory'] ?? null,
      'search' => $_GET['search'] ?? null,
      'page' => max(1, (int)($_GET['page'] ?? 1)),
      'limit' => min(100, max(1, (int)($_GET['limit'] ?? 20)))
    ];
    
    $items = Asset::all();
    
    // 应用筛选
    if (!empty($filters['category'])) {
      $items = array_filter($items, function($item) use ($filters) {
        return $item['category'] === $filters['category'];
      });
    }
    if (!empty($filters['subcategory'])) {
      $items = array_filter($items, function($item) use ($filters) {
        return $item['subcategory'] === $filters['subcategory'];
      });
    }
    if (!empty($filters['search'])) {
      $search = strtolower($filters['search']);
      $items = array_filter($items, function($item) use ($search) {
        return strpos(strtolower($item['name']), $search) !== false ||
               strpos(strtolower($item['location'] ?? ''), $search) !== false;
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
    
    $asset = Asset::find($id);
    if (!$asset) {
      Response::error('Asset not found', 404);
    }
    
    // 获取附件（如果有asset_attachments表）
    $stmt = DB::conn()->prepare("SELECT * FROM asset_attachments WHERE asset_id = ? ORDER BY id DESC");
    $stmt->execute([$id]);
    $attachments = $stmt->fetchAll();
    $asset['attachments'] = $attachments;
    
    Response::success($asset);
  }

  public function create() {
    ApiGuard::requireLogin();
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    $payload = [
      'category' => $data['category'] ?? '',
      'subcategory' => $data['subcategory'] ?? null,
      'name' => $data['name'] ?? '',
      'floor' => $data['floor'] ?? null,
      'location' => $data['location'] ?? null,
      'price' => floatval($data['price'] ?? 0),
      'quantity' => intval($data['quantity'] ?? 1),
      'acquired_at' => $data['acquired_at'] ?? date('Y-m-d'),
      'note' => $data['note'] ?? null,
      'created_by' => Auth::user()['id']
    ];
    
    if (empty($payload['name']) || empty($payload['category'])) {
      Response::error('Name and category are required', 400);
    }
    
    $id = Asset::create($payload);
    if ($id) {
      Response::success(['id' => $id], 'Asset created successfully', 201);
    } else {
      Response::error('Failed to create asset', 500);
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
    $allowedFields = ['category', 'subcategory', 'name', 'floor', 'location', 'price', 'quantity', 'acquired_at', 'note'];
    foreach ($allowedFields as $field) {
      if (isset($data[$field])) {
        $payload[$field] = $data[$field];
      }
    }
    
    if (empty($payload)) {
      Response::error('No fields to update', 400);
    }
    
    if (Asset::update($id, $payload)) {
      Response::success(['id' => $id], 'Asset updated successfully');
    } else {
      Response::error('Failed to update asset', 500);
    }
  }

  public function void() {
    ApiGuard::requireRole(['owner']);
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
      Response::error('ID is required', 400);
    }
    
    if (Asset::void($id)) {
      Response::success(['id' => $id], 'Asset voided successfully');
    } else {
      Response::error('Failed to void asset', 500);
    }
  }
}
