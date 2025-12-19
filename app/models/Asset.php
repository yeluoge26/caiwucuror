<?php
require_once __DIR__ . '/../core/DB.php';

class Asset {
  public static function all() {
    $stmt = DB::conn()->query("
      SELECT a.*, u.display_name as creator_name,
        (SELECT file_path FROM asset_attachments att WHERE att.asset_id = a.id ORDER BY att.id DESC LIMIT 1) as thumb_path
      FROM assets a
      LEFT JOIN users u ON a.created_by = u.id
      ORDER BY a.acquired_at DESC, a.id DESC
    ");
    return $stmt->fetchAll();
  }

  public static function create($data) {
    $stmt = DB::conn()->prepare("
      INSERT INTO assets (category, subcategory, name, floor, location, price, quantity, acquired_at, note, created_by)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $ok = $stmt->execute([
      $data['category'],
      $data['subcategory'] ?? null,
      $data['name'],
      $data['floor'] ?? null,
      $data['location'] ?? null,
      $data['price'],
      $data['quantity'] ?? 0,
      $data['acquired_at'],
      $data['note'] ?? null,
      $data['created_by'],
    ]);
    return $ok ? DB::conn()->lastInsertId() : false;
  }

  public static function find($id) {
    $stmt = DB::conn()->prepare("
      SELECT a.*, u.display_name as creator_name
      FROM assets a
      LEFT JOIN users u ON a.created_by = u.id
      WHERE a.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }

  public static function update($id, $data) {
    $fields = [];
    $params = [];
    foreach ($data as $k => $v) {
      $fields[] = "$k = ?";
      $params[] = $v;
    }
    $params[] = $id;
    $stmt = DB::conn()->prepare("UPDATE assets SET " . implode(', ', $fields) . " WHERE id = ?");
    return $stmt->execute($params);
  }

  public static function void($id) {
    $stmt = DB::conn()->prepare("UPDATE assets SET status='void' WHERE id = ?");
    return $stmt->execute([$id]);
  }
}
