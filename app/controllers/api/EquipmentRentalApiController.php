<?php
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/ApiGuard.php';
require_once __DIR__ . '/../../models/EquipmentRental.php';

class EquipmentRentalApiController {
  public function list() {
    ApiGuard::requireLogin();
    
    $filters = [
      'status' => $_GET['status'] ?? null,
      'customer_name' => $_GET['customer_name'] ?? null,
      'equipment_name' => $_GET['equipment_name'] ?? null,
      'from_date' => $_GET['from_date'] ?? null,
      'to_date' => $_GET['to_date'] ?? null,
      'page' => max(1, (int)($_GET['page'] ?? 1)),
      'limit' => min(100, max(1, (int)($_GET['limit'] ?? 20)))
    ];
    
    $offset = ($filters['page'] - 1) * $filters['limit'];
    $filters['offset'] = $offset;
    $filters['limit'] = $filters['limit'];
    
    $items = EquipmentRental::list($filters);
    $total = EquipmentRental::count($filters);
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
    
    $rental = EquipmentRental::find($id);
    if (!$rental) {
      Response::error('Equipment rental not found', 404);
    }
    
    Response::success($rental);
  }

  public function create() {
    ApiGuard::requireLogin();
    
    // 支持FormData和JSON两种方式
    $data = [];
    if ($_SERVER['CONTENT_TYPE'] && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
      $data = json_decode(file_get_contents('php://input'), true) ?? [];
    } else {
      $data = $_POST;
    }
    
    $payload = [
      'customer_name' => $data['customer_name'] ?? '',
      'equipment_name' => $data['equipment_name'] ?? '',
      'equipment_serial' => $data['equipment_serial'] ?? null,
      'start_date' => $data['start_date'] ?? date('Y-m-d'),
      'end_date' => $data['end_date'] ?? null,
      'deposit' => floatval($data['deposit'] ?? 0),
      'deposit_payment_method_id' => $data['deposit_payment_method_id'] ?? null,
      'note' => $data['note'] ?? null,
      'created_by' => Auth::user()['id'],
      'status' => 'active'
    ];
    
    if (empty($payload['customer_name']) || empty($payload['equipment_name'])) {
      Response::error('Customer name and equipment name are required', 400);
    }
    
    if (empty($payload['end_date'])) {
      Response::error('End date is required', 400);
    }
    
    // 验证日期
    if (strtotime($payload['end_date']) < strtotime($payload['start_date'])) {
      Response::error('End date must be after start date', 400);
    }
    
    // 处理ID照片上传
    $idCardPhoto = null;
    if (!empty($_FILES['id_card_photo'])) {
      $file = $_FILES['id_card_photo'];
      if ($file['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $mime = mime_content_type($file['tmp_name']);
        if (in_array($mime, $allowed)) {
          $dir = __DIR__ . '/../../../public/uploads/equipment_rentals';
          if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
          }
          $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
          $filename = 'id_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
          if (move_uploaded_file($file['tmp_name'], $dir . '/' . $filename)) {
            $idCardPhoto = 'uploads/equipment_rentals/' . $filename;
          }
        }
      }
    }
    
    if ($idCardPhoto) {
      $payload['id_card_photo'] = $idCardPhoto;
    }
    
    $id = EquipmentRental::create($payload);
    if ($id) {
      Response::success(['id' => $id], 'Equipment rental created successfully', 201);
    } else {
      Response::error('Failed to create equipment rental', 500);
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
    $allowedFields = ['customer_name', 'equipment_name', 'equipment_serial', 'start_date', 'end_date', 'deposit', 'deposit_payment_method_id', 'note'];
    foreach ($allowedFields as $field) {
      if (isset($data[$field])) {
        $payload[$field] = $data[$field];
      }
    }
    
    if (empty($payload)) {
      Response::error('No fields to update', 400);
    }
    
    if (EquipmentRental::update($id, $payload)) {
      Response::success(['id' => $id], 'Equipment rental updated successfully');
    } else {
      Response::error('Failed to update equipment rental', 500);
    }
  }

  public function returnEquipment() {
    ApiGuard::requireLogin();
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
      Response::error('ID is required', 400);
    }
    
    $rental = EquipmentRental::find($id);
    if (!$rental) {
      Response::error('Equipment rental not found', 404);
    }
    
    if ($rental['status'] !== 'active') {
      Response::error('Only active rentals can be returned', 400);
    }
    
    if (EquipmentRental::returnEquipment($id)) {
      Response::success(['id' => $id], 'Equipment returned successfully');
    } else {
      Response::error('Failed to return equipment', 500);
    }
  }

  public function cancel() {
    ApiGuard::requireLogin();
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
      Response::error('ID is required', 400);
    }
    
    $rental = EquipmentRental::find($id);
    if (!$rental) {
      Response::error('Equipment rental not found', 404);
    }
    
    if ($rental['status'] !== 'active') {
      Response::error('Only active rentals can be cancelled', 400);
    }
    
    if (EquipmentRental::cancel($id)) {
      Response::success(['id' => $id], 'Equipment rental cancelled successfully');
    } else {
      Response::error('Failed to cancel equipment rental', 500);
    }
  }
}
