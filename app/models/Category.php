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
    // 检查是否被交易使用
    $checkStmt = DB::conn()->prepare("SELECT COUNT(*) as count FROM transactions WHERE category_id = ? AND status != 'void'");
    $checkStmt->execute([$id]);
    $result = $checkStmt->fetch();
    
    if ($result && $result['count'] > 0) {
      return ['error' => true, 'message' => '该分类正在被交易使用，无法删除'];
    }
    
    // 检查是否有子分类
    $childStmt = DB::conn()->prepare("SELECT COUNT(*) as count FROM categories WHERE parent_id = ?");
    $childStmt->execute([$id]);
    $childResult = $childStmt->fetch();
    
    if ($childResult && $childResult['count'] > 0) {
      return ['error' => true, 'message' => '该分类下有子分类，无法删除'];
    }
    
    // 真正删除
    $stmt = DB::conn()->prepare("DELETE FROM categories WHERE id = ?");
    return $stmt->execute([$id]) ? ['error' => false] : ['error' => true, 'message' => '删除失败'];
  }
}
