<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../core/DB.php';
require_once __DIR__ . '/../models/Inspection.php';
require_once __DIR__ . '/../models/InspectionPhoto.php';

class InspectionsController {
  private function uploadPhotos($files, $userId) {
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
    $dir = __DIR__ . '/../../public/uploads/inspections';
    if (!is_dir($dir)) {
      // 确保父目录存在
      $parentDir = dirname($dir);
      if (!is_dir($parentDir)) {
        if (!@mkdir($parentDir, 0755, true) && !is_dir($parentDir)) {
          error_log("Failed to create parent directory: $parentDir");
          return $paths; // 返回空数组，避免继续处理
        }
      }
      // 尝试创建目录
      if (!@mkdir($dir, 0755, true) && !is_dir($dir)) {
        error_log("Failed to create directory: $dir. Please create it manually with proper permissions.");
        // 尝试使用 0777 权限（在某些服务器上可能需要）
        if (!@mkdir($dir, 0777, true) && !is_dir($dir)) {
          return $paths; // 返回空数组，避免继续处理
        }
      }
    }
    // 检查目录是否可写
    if (!is_writable($dir)) {
      error_log("Directory is not writable: $dir");
      return $paths;
    }
    // 兼容单文件和多文件上传
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
      $target = $dir . '/' . $name;
      if (move_uploaded_file($tmpNames[$i], $target)) {
        $paths[] = ['path' => 'uploads/inspections/' . $name, 'mime' => $mime];
      }
    }
    return $paths;
  }

  public function list() {
    Auth::requireLogin();
    $date = $_GET['date'] ?? date('Y-m-d');
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        Inspection::create([
          'store' => $_POST['store'] ?? 'coffee',
          'floor' => $_POST['floor'] ?? '1F',
          'visit_no' => (int)($_POST['visit_no'] ?? 1),
          'room' => $_POST['room'] ?? 'general',
          'status' => $_POST['status'] ?? 'ok',
          'note' => $_POST['note'] ?? null,
          'created_by' => Auth::user()['id'],
          'spot_date' => $_POST['spot_date'] ?? date('Y-m-d'),
        ]);
        $inspId = DB::conn()->lastInsertId();
        // 兼容单图字段 photo 与多图 photos[]
        $fileInput = $_FILES['photos'] ?? ($_FILES['photo'] ?? null);
        $photos = $this->uploadPhotos($fileInput, Auth::user()['id']);
        foreach ($photos as $p) {
          InspectionPhoto::create($inspId, $p['path'], $p['mime'], Auth::user()['id']);
        }
        header('Location: /index.php?r=inspections/list&date=' . urlencode($_POST['spot_date'] ?? $date));
        exit;
      }
    }

    $items = Inspection::list(['date' => $date]);
    include __DIR__ . '/../views/inspections/list.php';
  }

  public function create() {
    Auth::requireLogin();
    $date = $_GET['date'] ?? date('Y-m-d');
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $spotDate = $_POST['spot_date'] ?? date('Y-m-d');
        
        // 禁止回填历史巡店（spot_date 不能是过去日期）
        if (strtotime($spotDate) < strtotime(date('Y-m-d'))) {
          $error = __('inspection.no_past_date');
        } else {
          // 照片必填验证
          $fileInput = $_FILES['photos'] ?? ($_FILES['photo'] ?? null);
          $photos = $this->uploadPhotos($fileInput, Auth::user()['id']);
          
          if (empty($photos)) {
            $error = __('inspection.photo_required');
          } else {
            Inspection::create([
              'store' => $_POST['store'] ?? 'coffee',
              'floor' => $_POST['floor'] ?? '1F',
              'visit_no' => (int)($_POST['visit_no'] ?? 1),
              'room' => $_POST['room'] ?? 'general',
              'status' => $_POST['status'] ?? 'ok',
              'note' => $_POST['note'] ?? null,
              'created_by' => Auth::user()['id'],
              'spot_date' => $spotDate,
            ]);
            $inspId = DB::conn()->lastInsertId();
            foreach ($photos as $p) {
              InspectionPhoto::create($inspId, $p['path'], $p['mime'], Auth::user()['id']);
            }
            header('Location: /index.php?r=inspections/list&date=' . urlencode($spotDate));
            exit;
          }
        }
      }
    }

    include __DIR__ . '/../views/inspections/create.php';
  }

  public function view() {
    Auth::requireLogin();
    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=inspections/list');
      exit;
    }
    $item = Inspection::find($id);
    if (!$item) {
      header('Location: /index.php?r=inspections/list');
      exit;
    }
    $photos = InspectionPhoto::listByInspection($id);
    include __DIR__ . '/../views/inspections/view.php';
  }

  public function review() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);
    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=inspections/list');
      exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        die('CSRF invalid');
      }
      $status = $_POST['reviewed_status'] ?? 'confirmed';
      $note = $_POST['review_note'] ?? null;
      Inspection::review($id, $status, $note, Auth::user()['id']);
    }
    header('Location: /index.php?r=inspections/view&id=' . $id);
    exit;
  }
}
