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
    if (!is_dir($dir)) mkdir($dir, 0755, true);
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
    $filters = [];
    if (!empty($_GET['date'])) $filters['date'] = $_GET['date'];
    $items = Inspection::list($filters);
    Response::json(['data' => $items]);
  }

  public function view() {
    ApiGuard::requireLogin();
    $id = $_GET['id'] ?? null;
    if (!$id) Response::json(['error' => 'id_required'], 400);
    $item = Inspection::find($id);
    if (!$item) Response::json(['error' => 'not_found'], 404);
    $photos = InspectionPhoto::listByInspection($id);
    Response::json(['data' => $item, 'photos' => $photos]);
  }

  public function create() {
    ApiGuard::requireLogin();
    $payload = [
      'store' => $_POST['store'] ?? 'coffee',
      'floor' => $_POST['floor'] ?? '1F',
      'visit_no' => (int)($_POST['visit_no'] ?? 1),
      'room' => $_POST['room'] ?? 'general',
      'status' => $_POST['status'] ?? 'ok',
      'note' => $_POST['note'] ?? null,
      'created_by' => Auth::user()['id'],
      'spot_date' => $_POST['spot_date'] ?? date('Y-m-d'),
    ];
    Inspection::create($payload);
    $id = DB::conn()->lastInsertId();
    $files = $_FILES['photos'] ?? ($_FILES['photo'] ?? null);
    $photos = $this->uploadPhotos($files);
    foreach ($photos as $p) {
      InspectionPhoto::create($id, $p['path'], $p['mime'], Auth::user()['id']);
    }
    Response::json(['data' => ['id' => $id]]);
  }

  public function review() {
    ApiGuard::requireRole(['owner', 'manager', 'accountant']);
    $id = $_GET['id'] ?? null;
    if (!$id) Response::json(['error' => 'id_required'], 400);
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $status = $data['status'] ?? 'confirmed';
    $note = $data['note'] ?? null;
    Inspection::review($id, $status, $note, Auth::user()['id']);
    Response::json(['data' => true]);
  }
}
