<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../core/DB.php';
require_once __DIR__ . '/../models/Inspection.php';
require_once __DIR__ . '/../models/InspectionPhoto.php';

class InspectionsController {
  private function uploadPhotos($files, $userId) {
    $paths = [];
    if (empty($files) || empty($files['name'])) {
      error_log("uploadPhotos: No files provided or files array is empty");
      return $paths;
    }
    
    // 调试：记录接收到的文件信息
    error_log("uploadPhotos: Received files - " . json_encode([
      'has_name' => isset($files['name']),
      'name_type' => gettype($files['name']),
      'name_value' => is_array($files['name']) ? count($files['name']) . ' files' : $files['name'],
      'has_tmp_name' => isset($files['tmp_name']),
      'has_error' => isset($files['error']),
      'error_value' => is_array($files['error']) ? json_encode($files['error']) : $files['error']
    ], JSON_UNESCAPED_UNICODE));
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
    
    $uploadErrors = [
      UPLOAD_ERR_INI_SIZE => '文件大小超过 upload_max_filesize',
      UPLOAD_ERR_FORM_SIZE => '文件大小超过表单 MAX_FILE_SIZE',
      UPLOAD_ERR_PARTIAL => '文件只有部分被上传',
      UPLOAD_ERR_NO_FILE => '没有文件被上传',
      UPLOAD_ERR_NO_TMP_DIR => '找不到临时文件夹',
      UPLOAD_ERR_CANT_WRITE => '文件写入失败',
      UPLOAD_ERR_EXTENSION => 'PHP扩展阻止了文件上传'
    ];
    
    for ($i = 0; $i < $count; $i++) {
      if (empty($names[$i])) {
        error_log("Upload skipped: empty filename at index $i");
        continue;
      }
      
      if ($errors[$i] !== UPLOAD_ERR_OK) {
        $errorMsg = $uploadErrors[$errors[$i]] ?? "未知错误 ({$errors[$i]})";
        error_log("Upload error for file '{$names[$i]}': {$errorMsg}");
        continue;
      }
      
      if (!is_uploaded_file($tmpNames[$i])) {
        error_log("Upload skipped: '{$names[$i]}' is not a valid uploaded file");
        continue;
      }
      
      if ($sizes[$i] > $maxSize) {
        error_log("Upload skipped: '{$names[$i]}' size ({$sizes[$i]} bytes) exceeds max size ({$maxSize} bytes)");
        continue;
      }
      
      // 检测 MIME 类型
      $mime = $detectMime($tmpNames[$i]);
      
      // 如果无法检测 MIME 类型，尝试从文件扩展名推断
      if (!$mime) {
        error_log("MIME type detection failed for '{$names[$i]}', trying file extension");
        $ext = strtolower(pathinfo($names[$i], PATHINFO_EXTENSION));
        $extToMime = [
          'jpg' => 'image/jpeg',
          'jpeg' => 'image/jpeg',
          'png' => 'image/png',
          'gif' => 'image/gif',
          'webp' => 'image/webp'
        ];
        if (isset($extToMime[$ext])) {
          $mime = $extToMime[$ext];
          error_log("Using MIME type from extension: {$mime}");
        } else {
          error_log("Upload skipped: '{$names[$i]}' MIME type could not be detected and extension '{$ext}' is not recognized");
          continue;
        }
      }
      
      if (!isset($allowed[$mime])) {
        error_log("Upload skipped: '{$names[$i]}' has unsupported MIME type '{$mime}'");
        continue;
      }
      
      $name = 'inspect_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
      $target = $dir . '/' . $name;
      
      error_log("Attempting to move file: tmp='{$tmpNames[$i]}', target='{$target}', tmp_exists=" . (file_exists($tmpNames[$i]) ? 'yes' : 'no') . ", tmp_readable=" . (is_readable($tmpNames[$i]) ? 'yes' : 'no'));
      
      if (move_uploaded_file($tmpNames[$i], $target)) {
        $paths[] = ['path' => 'uploads/inspections/' . $name, 'mime' => $mime];
        error_log("Upload success: '{$names[$i]}' saved as '{$name}' at '{$target}'");
        
        // 验证文件是否真的存在
        if (file_exists($target)) {
          error_log("File verified: '{$target}' exists, size: " . filesize($target) . " bytes");
        } else {
          error_log("WARNING: File '{$target}' does not exist after move_uploaded_file!");
        }
      } else {
        $lastError = error_get_last();
        error_log("Upload failed: failed to move '{$names[$i]}' to '{$target}'. Error: " . ($lastError['message'] ?? 'unknown'));
        error_log("Directory writable: " . (is_writable($dir) ? 'yes' : 'no'));
        error_log("Target directory exists: " . (is_dir($dir) ? 'yes' : 'no'));
      }
    }
    
    if (empty($paths) && $count > 0) {
      error_log("No files were successfully uploaded out of {$count} attempted upload(s)");
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
    error_log("InspectionsController::list - Loaded " . count($items) . " inspections for date: {$date}");
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
          // 照片可选，不强制要求
          error_log("InspectionsController::create - POST data received");
          error_log("InspectionsController::create - _POST keys: " . implode(', ', array_keys($_POST)));
          error_log("InspectionsController::create - _FILES keys: " . implode(', ', array_keys($_FILES)));
          
          // 详细记录 _FILES 内容
          if (!empty($_FILES)) {
            error_log("InspectionsController::create - _FILES content: " . json_encode([
              'keys' => array_keys($_FILES),
              'photos_exists' => isset($_FILES['photos']),
              'photos_content' => isset($_FILES['photos']) ? [
                'name' => $_FILES['photos']['name'] ?? 'not set',
                'tmp_name' => isset($_FILES['photos']['tmp_name']) ? (is_array($_FILES['photos']['tmp_name']) ? count($_FILES['photos']['tmp_name']) . ' files' : 'single file') : 'not set',
                'error' => $_FILES['photos']['error'] ?? 'not set',
                'size' => $_FILES['photos']['size'] ?? 'not set'
              ] : 'not set'
            ], JSON_UNESCAPED_UNICODE));
          } else {
            error_log("InspectionsController::create - _FILES is empty!");
          }
          
          if (isset($_FILES['photos'])) {
            error_log("InspectionsController::create - _FILES['photos'] exists: " . json_encode([
              'name' => is_array($_FILES['photos']['name']) ? count($_FILES['photos']['name']) . ' files' : $_FILES['photos']['name'],
              'error' => is_array($_FILES['photos']['error']) ? $_FILES['photos']['error'] : $_FILES['photos']['error']
            ], JSON_UNESCAPED_UNICODE));
          }
          if (isset($_FILES['photo'])) {
            error_log("InspectionsController::create - _FILES['photo'] exists: " . json_encode([
              'name' => $_FILES['photo']['name'],
              'error' => $_FILES['photo']['error']
            ], JSON_UNESCAPED_UNICODE));
          }
          $fileInput = $_FILES['photos'] ?? ($_FILES['photo'] ?? null);
          $photos = $this->uploadPhotos($fileInput, Auth::user()['id']);
          error_log("InspectionsController::create - uploadPhotos returned " . count($photos) . " photos");
          
          $result = Inspection::create([
            'store' => $_POST['store'] ?? 'coffee',
            'floor' => $_POST['floor'] ?? '1F',
            'visit_no' => (int)($_POST['visit_no'] ?? 1),
            'room' => $_POST['room'] ?? 'general',
            'status' => $_POST['status'] ?? 'ok',
            'note' => $_POST['note'] ?? null,
            'created_by' => Auth::user()['id'],
            'spot_date' => $spotDate,
          ]);
          
          if (!$result) {
            $error = __('inspection.create_failed', '创建失败');
          } else {
            $inspId = DB::conn()->lastInsertId();
            // 如果有照片，保存照片
            if (!empty($photos)) {
              error_log("InspectionsController::create - Saving " . count($photos) . " photos for inspection ID: {$inspId}");
              foreach ($photos as $p) {
                $result = InspectionPhoto::create($inspId, $p['path'], $p['mime'], Auth::user()['id']);
                error_log("InspectionsController::create - Photo saved: {$p['path']}, result: " . ($result ? 'success' : 'failed'));
              }
            } else {
              error_log("InspectionsController::create - No photos to save for inspection ID: {$inspId}");
            }
            
            // 检查是否是 AJAX 请求
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            
            error_log("InspectionsController::create - isAjax: " . ($isAjax ? 'yes' : 'no'));
            error_log("InspectionsController::create - photos count: " . count($photos));
            error_log("InspectionsController::create - inspection_id: {$inspId}");
            
            if ($isAjax) {
              // AJAX 请求：返回 JSON 响应
              header('Content-Type: application/json; charset=utf-8');
              $response = [
                'success' => true,
                'message' => __('inspection.create_success', '创建成功'),
                'inspection_id' => $inspId,
                'photo_count' => count($photos),
                'photos' => $photos
              ];
              error_log("InspectionsController::create - JSON response: " . json_encode($response, JSON_UNESCAPED_UNICODE));
              echo json_encode($response, JSON_UNESCAPED_UNICODE);
              exit;
            } else {
              // 普通表单提交：重定向
              $redirectUrl = '/index.php?r=inspections/list&date=' . urlencode($spotDate);
              header('Location: ' . $redirectUrl);
              exit;
            }
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

  /**
   * 批量审批通过所有状态为ok的巡店任务（仅老板可用）
   */
  public function batchApprove() {
    Auth::requireLogin();
    Auth::requireRole(['owner']);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        die('CSRF invalid');
      }
      
      $result = Inspection::batchApproveOk(Auth::user()['id']);
      
      // 检查是否是 AJAX 请求
      $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
      
      if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        $message = $result['success'] 
          ? str_replace('{count}', $result['count'], __('inspection.batch_approve_success'))
          : __('inspection.batch_approve_failed');
        echo json_encode([
          'success' => $result['success'],
          'count' => $result['count'],
          'message' => $message
        ], JSON_UNESCAPED_UNICODE);
        exit;
      } else {
        // 普通表单提交：重定向并显示消息
        $date = $_GET['date'] ?? date('Y-m-d');
        $redirectUrl = '/index.php?r=inspections/list&date=' . urlencode($date);
        if ($result['success']) {
          $redirectUrl .= '&msg=' . urlencode($result['message']);
        } else {
          $redirectUrl .= '&error=' . urlencode($result['message']);
        }
        header('Location: ' . $redirectUrl);
        exit;
      }
    }
    
    // GET 请求：重定向到列表页
    header('Location: /index.php?r=inspections/list');
    exit;
  }
}

