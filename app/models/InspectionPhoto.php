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
    $stmt = DB::conn()->prepare("
      INSERT INTO inspection_photos (inspection_id, file_path, file_type, uploaded_by)
      VALUES (?, ?, ?, ?)
    ");
    return $stmt->execute([$inspectionId, $path, $type, $userId]);
  }
}
