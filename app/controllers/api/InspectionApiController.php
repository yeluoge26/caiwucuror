<?php
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/ApiGuard.php';
require_once __DIR__ . '/../../models/Inspection.php';
require_once __DIR__ . '/../../models/InspectionPhoto.php';
require_once __DIR__ . '/../../core/DB.php';

class InspectionApiController {
  private function uploadPhotos($files) {
    $paths = [];
    if (empty($files) || empty($files['name'])) return $paths;
    $allowed = [
      'image/jpeg' => 'jpg',
      'image/png' => 'png',
      'image/gif' => 'gif',
      'image/webp' => 'webp',
    ];
    $maxSize = 10 * 1024 * 1024;
    $detectMime = function($tmp) {
      if (class_exists('finfo')) {
        $fi = new finfo(FILEINFO_MIME_TYPE);
        return $fi->file($tmp);
      }
      if (function_exists('mime_content_type')) {
        return mime_content_type($tmp);
      }
      return null;
    };
    $dir = __DIR__ . '/../../../public/uploads/inspections';
    if (!is_dir($dir)) {
      // 确保父目录存在
      $parentDir = dirname($dir);
      if (!is_dir($parentDir)) {
        if (!@mkdir($parentDir, 0755, true) && !is_dir($parentDir)) {
          error_log("Failed to create parent directory: $parentDir");
          return $paths;
        }
      }
      // 尝试创建目录
      if (!@mkdir($dir, 0755, true) && !is_dir($dir)) {
        error_log("Failed to create directory: $dir. Please create it manually with proper permissions.");
        // 尝试使用 0777 权限
        if (!@mkdir($dir, 0777, true) && !is_dir($dir)) {
          return $paths;
        }
      }
    }
    // 检查目录是否可写
    if (!is_writable($dir)) {
      error_log("Directory is not writable: $dir");
      return $paths;
    }
    $names = is_array($files['name']) ? $files['name'] : [$files['name']];
    $tmpNames = is_array($files['tmp_name']) ? $files['tmp_name'] : [$files['tmp_name']];
    $sizes = is_array($files['size']) ? $files['size'] : [$files['size']];
    $errors = is_array($files['error']) ? $files['error'] : [$files['error']];
    $count = count($names);
    for ($i = 0; $i < $count; $i++) {
      if (empty($names[$i]) || $errors[$i] !== UPLOAD_ERR_OK) continue;
      $mime = $detectMime($tmpNames[$i]);
      if (!isset($allowed[$mime]) || $sizes[$i] > $maxSize || !is_uploaded_file($tmpNames[$i])) continue;
      $name = 'inspect_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
      if (move_uploaded_file($tmpNames[$i], $dir . '/' . $name)) {
        $paths[] = ['path' => 'uploads/inspections/' . $name, 'mime' => $mime];
      }
    }
    return $paths;
  }

  public function list() {
    ApiGuard::requireLogin();
    
    $filters = [
      'date' => $_GET['date'] ?? null,
      'from_date' => $_GET['from_date'] ?? null,
      'to_date' => $_GET['to_date'] ?? null,
      'store' => $_GET['store'] ?? null,
      'floor' => $_GET['floor'] ?? null,
      'status' => $_GET['status'] ?? null,
      'reviewed_status' => $_GET['reviewed_status'] ?? null,
      'created_by' => $_GET['created_by'] ?? null,
      'page' => max(1, (int)($_GET['page'] ?? 1)),
      'limit' => min(100, max(1, (int)($_GET['limit'] ?? 20)))
    ];
    
    $offset = ($filters['page'] - 1) * $filters['limit'];
    $filters['offset'] = $offset;
    
    $items = Inspection::list($filters);
    $total = Inspection::count($filters);
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
    
    $item = Inspection::find($id);
    if (!$item) {
      Response::error('Inspection not found', 404);
    }
    
    $photos = InspectionPhoto::listByInspection($id);
    $item['photos'] = $photos;
    
    Response::success($item);
  }

  public function create() {
    ApiGuard::requireLogin();
    
    // 支持JSON和FormData两种方式
    $data = [];
    if ($_SERVER['CONTENT_TYPE'] && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
      $data = json_decode(file_get_contents('php://input'), true) ?? [];
    } else {
      $data = $_POST;
    }
    
    $spotDate = $data['spot_date'] ?? date('Y-m-d');
    
    // 禁止回填历史巡店
    if (strtotime($spotDate) < strtotime(date('Y-m-d'))) {
      Response::error('Cannot create inspection for past dates', 400);
    }
    
    $payload = [
      'store' => $data['store'] ?? 'coffee',
      'floor' => $data['floor'] ?? '1F',
      'visit_no' => (int)($data['visit_no'] ?? 1),
      'room' => $data['room'] ?? 'general',
      'status' => $data['status'] ?? 'ok',
      'note' => $data['note'] ?? null,
      'created_by' => Auth::user()['id'],
      'spot_date' => $spotDate,
    ];
    
    // 处理文件上传
    $files = $_FILES['photos'] ?? ($_FILES['photo'] ?? null);
    $photos = $this->uploadPhotos($files);
    
    // 照片必填验证
    if (empty($photos) || count($photos) === 0) {
      Response::error('At least one photo is required', 400);
    }
    
    $id = Inspection::create($payload);
    if (!$id) {
      Response::error('Failed to create inspection', 500);
    }
    
    // 保存照片
    foreach ($photos as $p) {
      InspectionPhoto::create($id, $p['path'], $p['mime'], Auth::user()['id']);
    }
    
    Response::success(['id' => $id, 'photos' => $photos], 'Inspection created successfully', 201);
  }

  public function review() {
    ApiGuard::requireRole(['owner', 'manager', 'accountant']);
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
      Response::error('ID is required', 400);
    }
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $status = $data['status'] ?? 'confirmed';
    $note = $data['note'] ?? null;
    
    if (!in_array($status, ['confirmed', 'rejected'])) {
      Response::error('Invalid review status', 400);
    }
    
    if (Inspection::review($id, $status, $note, Auth::user()['id'])) {
      Response::success(['id' => $id, 'status' => $status], 'Inspection reviewed successfully');
    } else {
      Response::error('Failed to review inspection', 500);
    }
  }

  public function batchApprove() {
    ApiGuard::requireRole(['owner', 'manager']);
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $ids = $data['ids'] ?? [];
    
    if (empty($ids) || !is_array($ids)) {
      Response::error('IDs array is required', 400);
    }
    
    $successCount = 0;
    $failedCount = 0;
    
    foreach ($ids as $id) {
      if (Inspection::review($id, 'confirmed', null, Auth::user()['id'])) {
        $successCount++;
      } else {
        $failedCount++;
      }
    }
    
    Response::success([
      'success_count' => $successCount,
      'failed_count' => $failedCount,
      'total' => count($ids)
    ], "Batch approved: {$successCount} successful, {$failedCount} failed");
  }
}
}
