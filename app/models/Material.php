<?php
require_once __DIR__ . '/../core/DB.php';

class Material {
  public static function all() {
    $stmt = DB::conn()->query("
      SELECT m.*, u.display_name as creator_name
      FROM materials m
      LEFT JOIN users u ON m.created_by = u.id
      ORDER BY m.category ASC, m.name ASC
    ");
    return $stmt->fetchAll();
  }

  // 分组展示：相同原料名称只展示一次，聚合库存
  public static function allGroupedByName() {
    $stmt = DB::conn()->query("
      SELECT 
        m.name,
        GROUP_CONCAT(DISTINCT m.store SEPARATOR '/') as store_list,
        m.category,
        m.unit,
        SUM(m.quantity) as quantity,
        SUM(m.min_quantity) as min_quantity,
        MIN(m.created_at) as created_at,
        MIN(m.note) as note
      FROM materials m
      GROUP BY m.name, m.category, m.unit
      ORDER BY m.name ASC
    ");
    return $stmt->fetchAll();
  }

  public static function create($data) {
    $stmt = DB::conn()->prepare("
      INSERT INTO materials (name, category, store, unit, quantity, min_quantity, note, created_by)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
      $data['name'],
      $data['category'],
      $data['store'],
      $data['unit'],
      $data['quantity'],
      $data['min_quantity'],
      $data['note'] ?? null,
      $data['created_by'],
    ]);
  }

  public static function addOrIncrement($data) {
    $stmt = DB::conn()->prepare("
      SELECT id, quantity FROM materials
      WHERE name = ? AND category = ? AND store = ? AND unit = ?
      LIMIT 1
    ");
    $stmt->execute([$data['name'], $data['category'], $data['store'], $data['unit']]);
    $existing = $stmt->fetch();
    if ($existing) {
      $newQty = $existing['quantity'] + $data['quantity'];
      self::update($existing['id'], ['quantity' => $newQty]);
      return $existing['id'];
    }
    self::create($data);
    return DB::conn()->lastInsertId();
  }

  public static function update($id, $data) {
    $fields = [];
    $params = [];
    foreach ($data as $k => $v) {
      $fields[] = "$k = ?";
      $params[] = $v;
    }
    $params[] = $id;
    $stmt = DB::conn()->prepare("UPDATE materials SET " . implode(',', $fields) . " WHERE id = ?");
    return $stmt->execute($params);
  }

  public static function deduct($materialId, $amount) {
    $stmt = DB::conn()->prepare("UPDATE materials SET quantity = GREATEST(quantity - ?, 0) WHERE id = ?");
    return $stmt->execute([$amount, $materialId]);
  }
}
