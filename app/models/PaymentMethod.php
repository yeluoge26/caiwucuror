<?php
require_once __DIR__ . '/../core/DB.php';

class PaymentMethod {
  public static function find($id) {
    $stmt = DB::conn()->prepare("SELECT * FROM payment_methods WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }

  public static function findByName($nameZh, $nameVi = null) {
    $stmt = DB::conn()->prepare("SELECT * FROM payment_methods WHERE name_zh = ? OR name_vi = ? LIMIT 1");
    $stmt->execute([$nameZh, $nameVi ?: $nameZh]);
    return $stmt->fetch();
  }

  public static function all($filters = []) {
    $where = [];
    $params = [];

    if (isset($filters['is_active'])) {
      $where[] = "is_active = ?";
      $params[] = $filters['is_active'];
    }

    $sql = "SELECT * FROM payment_methods";
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY id";

    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }

  public static function create($data) {
    $stmt = DB::conn()->prepare("
      INSERT INTO payment_methods (name_zh, name_vi, is_active)
      VALUES (?, ?, ?)
    ");
    return $stmt->execute([
      $data['name_zh'],
      $data['name_vi'],
      $data['is_active'] ?? 1
    ]);
  }

  public static function update($id, $data) {
    $fields = [];
    $params = [];
    foreach ($data as $key => $value) {
      $fields[] = "$key = ?";
      $params[] = $value;
    }
    $params[] = $id;
    $stmt = DB::conn()->prepare("UPDATE payment_methods SET " . implode(', ', $fields) . " WHERE id = ?");
    return $stmt->execute($params);
  }

  public static function delete($id) {
    // 检查是否被交易使用
    $checkStmt = DB::conn()->prepare("SELECT COUNT(*) as count FROM transactions WHERE payment_method_id = ? AND status != 'void'");
    $checkStmt->execute([$id]);
    $result = $checkStmt->fetch();
    
    if ($result && $result['count'] > 0) {
      return ['error' => true, 'message' => '该支付方式正在被交易使用，无法删除'];
    }
    
    // 真正删除
    $stmt = DB::conn()->prepare("DELETE FROM payment_methods WHERE id = ?");
    return $stmt->execute([$id]) ? ['error' => false] : ['error' => true, 'message' => '删除失败'];
  }
}
