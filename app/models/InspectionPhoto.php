<?php
require_once __DIR__ . '/../core/DB.php';

class InspectionPhoto {
  public static function listByInspection($id) {
    $stmt = DB::conn()->prepare("
      SELECT id, file_path, file_type, created_at
      FROM inspection_photos
      WHERE inspection_id = ?
      ORDER BY id DESC
    ");
    $stmt->execute([$id]);
    return $stmt->fetchAll();
  }

  public static function create($inspectionId, $path, $type, $userId) {
    error_log("InspectionPhoto::create - inspectionId: {$inspectionId}, path: {$path}, type: {$type}, userId: {$userId}");
    $stmt = DB::conn()->prepare("
      INSERT INTO inspection_photos (inspection_id, file_path, file_type, uploaded_by)
      VALUES (?, ?, ?, ?)
    ");
    $result = $stmt->execute([$inspectionId, $path, $type, $userId]);
    $insertId = DB::conn()->lastInsertId();
    error_log("InspectionPhoto::create - Result: " . ($result ? 'success' : 'failed') . ", insertId: {$insertId}");
    
    // 验证插入的数据
    if ($result) {
      $verify = DB::conn()->prepare("SELECT id, file_path FROM inspection_photos WHERE id = ?");
      $verify->execute([$insertId]);
      $verifyData = $verify->fetch();
      error_log("InspectionPhoto::create - Verified data: " . json_encode($verifyData, JSON_UNESCAPED_UNICODE));
    }
    
    return $result;
  }
}
