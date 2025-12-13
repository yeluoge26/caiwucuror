<?php
class Transaction {

  public static function create($data) {
    $sql = "INSERT INTO transactions
      (`type`, amount, category_id, payment_method_id, vendor_id,
       occurred_at, note, created_by)
      VALUES (?,?,?,?,?,?,?,?)";

    $stmt = DB::conn()->prepare($sql);
    return $stmt->execute([
      $data['type'],
      $data['amount'],
      $data['category_id'],
      $data['payment_method_id'],
      $data['vendor_id'] ?? null,
      $data['occurred_at'],
      $data['note'] ?? null,
      $data['created_by'],
    ]);
  }

  public static function list($filters = []) {
    $where = [];
    $params = [];

    if (!empty($filters['type'])) {
      $where[] = '`type`=?';
      $params[] = $filters['type'];
    }

    if (!empty($filters['from'])) {
      $where[] = 'occurred_at>=?';
      $params[] = $filters['from'];
    }

    if (!empty($filters['to'])) {
      $where[] = 'occurred_at<=?';
      $params[] = $filters['to'];
    }

    $sql = "SELECT * FROM transactions";
    if ($where) $sql .= " WHERE " . implode(' AND ', $where);
    $sql .= " ORDER BY occurred_at DESC LIMIT 200";

    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }
}
