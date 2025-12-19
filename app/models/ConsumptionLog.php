<?php
require_once __DIR__ . '/../core/DB.php';

class ConsumptionLog {
  public static function create($data) {
    $stmt = DB::conn()->prepare("
      INSERT INTO consumption_logs (drink_id, quantity, occurred_at, note, created_by)
      VALUES (?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
      $data['drink_id'],
      $data['quantity'],
      $data['occurred_at'],
      $data['note'] ?? null,
      $data['created_by'],
    ]);
  }
}
