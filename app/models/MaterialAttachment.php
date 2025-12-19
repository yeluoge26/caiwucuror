<?php
require_once __DIR__ . '/../core/DB.php';

class MaterialAttachment {
  public static function create($materialId, $filePath, $fileType, $uploadedBy) {
    $stmt = DB::conn()->prepare("
      INSERT INTO material_attachments (material_id, file_path, file_type, uploaded_by)
      VALUES (?, ?, ?, ?)
    ");
    return $stmt->execute([$materialId, $filePath, $fileType, $uploadedBy]);
  }

  public static function listByMaterial($materialId) {
    $stmt = DB::conn()->prepare("
      SELECT id, file_path, file_type, created_at
      FROM material_attachments
      WHERE material_id = ?
      ORDER BY id DESC
    ");
    $stmt->execute([$materialId]);
    return $stmt->fetchAll();
  }

  public static function saveUploads($materialId, $files, $uploadedBy) {
    if (empty($files) || empty($files['name'])) return [];

    $uploadDir = __DIR__ . '/../../public/uploads/materials';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

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
    $errors = [];
    $names = is_array($files['name']) ? $files['name'] : [$files['name']];
    $tmpNames = is_array($files['tmp_name']) ? $files['tmp_name'] : [$files['tmp_name']];
    $sizes = is_array($files['size']) ? $files['size'] : [$files['size']];
    $errs = is_array($files['error']) ? $files['error'] : [$files['error']];
    $count = count($names);
    $finfo = new finfo(FILEINFO_MIME_TYPE);

    for ($i = 0; $i < $count; $i++) {
      if (empty($names[$i])) continue;
      if ($errs[$i] !== UPLOAD_ERR_OK) { $errors[] = 'Upload error'; continue; }
      if ($sizes[$i] > $maxSize) { $errors[] = 'File too large'; continue; }
      if (!is_uploaded_file($tmpNames[$i])) { $errors[] = 'Invalid upload'; continue; }
      $mime = $detectMime($tmpNames[$i]);
      if (!isset($allowed[$mime])) { $errors[] = 'Unsupported type'; continue; }
      try {
        $filename = 'mat_' . $materialId . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
      } catch (Exception $e) {
        $errors[] = 'Name gen failed';
        continue;
      }
      $target = $uploadDir . '/' . $filename;
      if (!move_uploaded_file($tmpNames[$i], $target)) {
        $errors[] = 'Save failed';
        continue;
      }
      self::create($materialId, 'uploads/materials/' . $filename, $mime, $uploadedBy);
    }
    return $errors;
  }
}
