<?php
require_once __DIR__ . '/../core/DB.php';

class Vendor {
  public static function find($id) {
    $stmt = DB::conn()->prepare("SELECT * FROM vendors WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }

  public static function all($filters = []) {
    $where = [];
    $params = [];

    if (isset($filters['is_active'])) {
      $where[] = "is_active = ?";
      $params[] = $filters['is_active'];
    }

    $sql = "SELECT * FROM vendors";
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY name";

    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }

  public static function create($data) {
    $stmt = DB::conn()->prepare("
      INSERT INTO vendors (name, phone, note, is_active)
      VALUES (?, ?, ?, ?)
    ");
    $result = $stmt->execute([
      $data['name'],
      $data['phone'] ?? null,
      $data['note'] ?? null,
      $data['is_active'] ?? 1
    ]);
    
    if ($result) {
      return DB::conn()->lastInsertId();
    }
    return false;
  }

  public static function update($id, $data) {
    $fields = [];
    $params = [];
    foreach ($data as $key => $value) {
      $fields[] = "$key = ?";
      $params[] = $value;
    }
    $params[] = $id;
    $stmt = DB::conn()->prepare("UPDATE vendors SET " . implode(', ', $fields) . " WHERE id = ?");
    return $stmt->execute($params);
  }

  public static function delete($id) {
    // 软删除，避免外键引用报错
    $stmt = DB::conn()->prepare("UPDATE vendors SET is_active = 0 WHERE id = ?");
    return $stmt->execute([$id]);
  }
}
