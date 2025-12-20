<?php
require_once __DIR__ . '/../core/DB.php';

class Inspection {
  public static function list($filters = []) {
    $where = [];
    $params = [];
    if (!empty($filters['date'])) {
      $where[] = 'i.spot_date = ?';
      $params[] = $filters['date'];
    }
    if (!empty($filters['created_by'])) {
      $where[] = 'i.created_by = ?';
      $params[] = $filters['created_by'];
    }
    $sql = "SELECT i.*, u.display_name as creator_name, r.display_name as reviewer_name
            , (SELECT file_path FROM inspection_photos p WHERE p.inspection_id = i.id ORDER BY p.id DESC LIMIT 1) as thumb_path
            , (SELECT COUNT(*) FROM inspection_photos p WHERE p.inspection_id = i.id) as photo_count
            FROM inspections i
            LEFT JOIN users u ON i.created_by = u.id
            LEFT JOIN users r ON i.reviewed_by = r.id";
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY i.spot_date DESC, i.id DESC";
    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    
    // 调试：记录查询结果
    error_log("Inspection::list - Found " . count($results) . " inspections");
    foreach ($results as $idx => $row) {
      error_log("Inspection::list - Inspection ID: {$row['id']}, thumb_path: " . ($row['thumb_path'] ?? 'NULL') . ", photo_count: " . ($row['photo_count'] ?? 0));
    }
    
    return $results;
  }

  public static function find($id) {
    $stmt = DB::conn()->prepare("
      SELECT i.*, u.display_name as creator_name, r.display_name as reviewer_name
      FROM inspections i
      LEFT JOIN users u ON i.created_by = u.id
      LEFT JOIN users r ON i.reviewed_by = r.id
      WHERE i.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }

  public static function create($data) {
    $stmt = DB::conn()->prepare("
      INSERT INTO inspections (store, floor, visit_no, room, status, note, created_by, spot_date)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
      $data['store'],
      $data['floor'],
      $data['visit_no'],
      $data['room'],
      $data['status'],
      $data['note'] ?? null,
      $data['created_by'],
      $data['spot_date'],
    ]);
  }

  public static function review($id, $status, $note, $userId) {
    $stmt = DB::conn()->prepare("
      UPDATE inspections
      SET reviewed_status = ?, review_note = ?, reviewed_by = ?, reviewed_at = NOW()
      WHERE id = ?
    ");
    return $stmt->execute([$status, $note, $userId, $id]);
  }

  /**
   * 批量审批通过所有状态为ok且待审批的巡店任务
   * @param int $userId 审批人ID
   * @return array ['success' => bool, 'count' => int, 'message' => string]
   */
  public static function batchApproveOk($userId) {
    $stmt = DB::conn()->prepare("
      UPDATE inspections
      SET reviewed_status = 'confirmed', 
          reviewed_by = ?, 
          reviewed_at = NOW()
      WHERE status = 'ok' 
        AND reviewed_status = 'pending'
    ");
    $result = $stmt->execute([$userId]);
    $count = $stmt->rowCount();
    return [
      'success' => $result,
      'count' => $count,
      'message' => $result ? "成功审批通过 {$count} 条巡店记录" : "批量审批失败"
    ];
  }
}
