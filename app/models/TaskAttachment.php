<?php
require_once __DIR__ . '/../core/DB.php';

class TaskAttachment {
  public static function create($taskId, $filePath, $fileType, $uploadedBy) {
    $stmt = DB::conn()->prepare("
      INSERT INTO task_attachments (task_id, file_path, file_type, uploaded_by)
      VALUES (?, ?, ?, ?)
    ");
    return $stmt->execute([$taskId, $filePath, $fileType, $uploadedBy]);
  }

  public static function listByTask($taskId) {
    $stmt = DB::conn()->prepare("
      SELECT id, file_path, file_type, uploaded_by, created_at
      FROM task_attachments
      WHERE task_id = ?
      ORDER BY id DESC
    ");
    $stmt->execute([$taskId]);
    return $stmt->fetchAll();
  }

  public static function saveUploads($taskId, $files, $uploadedBy) {
    if (empty($files) || empty($files['name'])) {
      return [];
    }

    $uploadDir = __DIR__ . '/../../public/uploads/tasks';
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0755, true);
    }

    $allowed = [
      'image/jpeg' => 'jpg',
      'image/png' => 'png',
      'image/gif' => 'gif',
      'image/webp' => 'webp',
      'application/pdf' => 'pdf',
      'application/msword' => 'doc',
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
    ];
    $maxSize = 10 * 1024 * 1024; // 10MB
    $errors = [];
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

    $names = is_array($files['name']) ? $files['name'] : [$files['name']];
    $tmpNames = is_array($files['tmp_name']) ? $files['tmp_name'] : [$files['tmp_name']];
    $sizes = is_array($files['size']) ? $files['size'] : [$files['size']];
    $errs = is_array($files['error']) ? $files['error'] : [$files['error']];
    $count = count($names);

    for ($i = 0; $i < $count; $i++) {
      if (empty($names[$i]) || $errs[$i] !== UPLOAD_ERR_OK) {
        continue;
      }

      if ($sizes[$i] > $maxSize) {
        $errors[] = 'File too large: ' . $names[$i];
        continue;
      }

      if (!is_uploaded_file($tmpNames[$i])) {
        continue;
      }

      $mime = $detectMime($tmpNames[$i]);
      if (!isset($allowed[$mime])) {
        $errors[] = 'Unsupported file type: ' . $mime;
        continue;
      }

      $filename = 'task_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
      $targetPath = $uploadDir . '/' . $filename;

      if (move_uploaded_file($tmpNames[$i], $targetPath)) {
        self::create($taskId, 'uploads/tasks/' . $filename, $mime, $uploadedBy);
      } else {
        $errors[] = 'Failed to save file: ' . $names[$i];
      }
    }

    return $errors;
  }
}

