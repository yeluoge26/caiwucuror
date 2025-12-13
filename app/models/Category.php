<?php
require_once __DIR__ . '/../core/DB.php';

class Category {
  public static function find($id) {
    $stmt = DB::conn()->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }

  public static function all($filters = []) {
    $where = [];
    $params = [];

    if (!empty($filters['type'])) {
      $where[] = "(`type` = ? OR `type` = 'both')";
      $params[] = $filters['type'];
    }

    if (isset($filters['is_active'])) {
      $where[] = "is_active = ?";
      $params[] = $filters['is_active'];
    }

    $sql = "SELECT * FROM categories";
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY `type`, id";

    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }

  public static function create($data) {
    $stmt = DB::conn()->prepare("
      INSERT INTO categories (`type`, name_zh, name_vi, parent_id, is_active)
      VALUES (?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
      $data['type'],
      $data['name_zh'],
      $data['name_vi'],
      $data['parent_id'] ?? null,
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
    $stmt = DB::conn()->prepare("UPDATE categories SET " . implode(', ', $fields) . " WHERE id = ?");
    return $stmt->execute($params);
  }

  public static function delete($id) {
    $stmt = DB::conn()->prepare("DELETE FROM categories WHERE id = ?");
    return $stmt->execute([$id]);
  }
}

