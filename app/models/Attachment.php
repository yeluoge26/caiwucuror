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
    $finfo = new finfo(FILEINFO_MIME_TYPE);

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

      $mime = $finfo->file($files['tmp_name'][$i]);
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
