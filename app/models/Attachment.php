<?php
require_once __DIR__ . '/../core/DB.php';

class Attachment {
  public static function create($transactionId, $filePath, $fileType, $uploadedBy) {
    $stmt = DB::conn()->prepare("
      INSERT INTO attachments (transaction_id, file_path, file_type, uploaded_by)
      VALUES (?, ?, ?, ?)
    ");
    return $stmt->execute([$transactionId, $filePath, $fileType, $uploadedBy]);
  }

  public static function listByTransaction($transactionId) {
    $stmt = DB::conn()->prepare("
      SELECT id, file_path, file_type, created_at
      FROM attachments
      WHERE transaction_id = ?
      ORDER BY id DESC
    ");
    $stmt->execute([$transactionId]);
    return $stmt->fetchAll();
  }

  // 处理并保存上传的图片附件，返回错误信息数组（如果有）
  public static function saveUploads($transactionId, $files, $uploadedBy) {
    if (empty($files) || empty($files['name'])) {
      return [];
    }

    $uploadDir = __DIR__ . '/../../public/uploads';
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0755, true);
    }

    $allowed = [
      'image/jpeg' => 'jpg',
      'image/png' => 'png',
      'image/gif' => 'gif',
      'image/webp' => 'webp',
    ];
    $maxSize = 5 * 1024 * 1024; // 5MB
    $errors = [];
    $fileCount = is_array($files['name']) ? count($files['name']) : 0;
    
    // MIME 类型检测函数（带回退机制）
    $detectMime = function($filePath) use ($allowed) {
      // 方法1: 尝试使用 finfo 类
      if (class_exists('finfo')) {
        try {
          $finfo = new finfo(FILEINFO_MIME_TYPE);
          $mime = $finfo->file($filePath);
          if ($mime && isset($allowed[$mime])) {
            return $mime;
          }
        } catch (Exception $e) {
          error_log("finfo detection failed: " . $e->getMessage());
        }
      }
      
      // 方法2: 尝试使用 mime_content_type 函数
      if (function_exists('mime_content_type')) {
        $mime = @mime_content_type($filePath);
        if ($mime && isset($allowed[$mime])) {
          return $mime;
        }
      }
      
      // 方法3: 从文件扩展名推断（回退方案）
      return null; // 返回 null，让调用者从文件名推断
    };

    for ($i = 0; $i < $fileCount; $i++) {
      if (empty($files['name'][$i])) {
        continue;
      }
      if ($files['error'][$i] !== UPLOAD_ERR_OK) {
        $errors[] = 'Upload error code: ' . $files['error'][$i];
        continue;
      }
      if ($files['size'][$i] > $maxSize) {
        $errors[] = 'File too large: ' . $files['name'][$i];
        continue;
      }
      if (!is_uploaded_file($files['tmp_name'][$i])) {
        $errors[] = 'Invalid upload source';
        continue;
      }

      // 检测 MIME 类型
      $mime = $detectMime($files['tmp_name'][$i]);
      
      // 如果无法检测 MIME 类型，尝试从文件扩展名推断
      if (!$mime) {
        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
        $extToMime = [
          'jpg' => 'image/jpeg',
          'jpeg' => 'image/jpeg',
          'png' => 'image/png',
          'gif' => 'image/gif',
          'webp' => 'image/webp'
        ];
        if (isset($extToMime[$ext])) {
          $mime = $extToMime[$ext];
        } else {
          $errors[] = 'Unsupported file type: ' . $files['name'][$i] . ' (extension: ' . $ext . ')';
          continue;
        }
      }
      
      if (!isset($allowed[$mime])) {
        $errors[] = 'Unsupported file type: ' . $mime;
        continue;
      }

      try {
        $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
      } catch (Exception $e) {
        $errors[] = 'Filename generation failed';
        continue;
      }

      $targetPath = $uploadDir . '/' . $filename;
      if (!move_uploaded_file($files['tmp_name'][$i], $targetPath)) {
        $errors[] = 'Failed to save file: ' . $files['name'][$i];
        continue;
      }

      // 保存数据库记录，路径存储为相对 public 的路径
      self::create($transactionId, 'uploads/' . $filename, $mime, $uploadedBy);
    }

    return $errors;
  }
}
