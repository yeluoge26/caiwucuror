<?php
require_once __DIR__ . '/../core/DB.php';

class VoidRequest {
  public static function create($transactionId, $userId, $reason = null) {
    $stmt = DB::conn()->prepare("
      INSERT INTO void_requests (transaction_id, requested_by, reason)
      VALUES (?, ?, ?)
    ");
    return $stmt->execute([$transactionId, $userId, $reason]);
  }

  public static function latestByTransaction($transactionId) {
    $stmt = DB::conn()->prepare("
      SELECT vr.*, u.display_name
      FROM void_requests vr
      JOIN users u ON vr.requested_by = u.id
      WHERE vr.transaction_id = ?
      ORDER BY vr.id DESC
      LIMIT 1
    ");
    $stmt->execute([$transactionId]);
    return $stmt->fetch();
  }
}
