<?php
require_once __DIR__ . '/../core/DB.php';

class User {
  public static function find($id) {
    $stmt = DB::conn()->prepare("
      SELECT u.*, r.`key` as role_key, r.name_zh as role_name_zh, r.name_vi as role_name_vi
      FROM users u
      JOIN roles r ON u.role_id = r.id
      WHERE u.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }

  public static function findByUsername($username) {
    $stmt = DB::conn()->prepare("
      SELECT u.*, r.`key` as role_key, r.name_zh as role_name_zh, r.name_vi as role_name_vi
      FROM users u
      JOIN roles r ON u.role_id = r.id
      WHERE u.username = ?
    ");
    $stmt->execute([$username]);
    return $stmt->fetch();
  }

  public static function all() {
    $stmt = DB::conn()->query("
      SELECT u.*, r.`key` as role_key, r.name_zh as role_name_zh, r.name_vi as role_name_vi
      FROM users u
      JOIN roles r ON u.role_id = r.id
      ORDER BY u.id DESC
    ");
    return $stmt->fetchAll();
  }

  public static function create($data) {
    $stmt = DB::conn()->prepare("
      INSERT INTO users (username, password_hash, display_name, role_id, is_active)
      VALUES (?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
      $data['username'],
      password_hash($data['password'], PASSWORD_DEFAULT),
      $data['display_name'],
      $data['role_id'],
      $data['is_active'] ?? 1
    ]);
  }

  public static function update($id, $data) {
    $fields = [];
    $params = [];
    foreach ($data as $key => $value) {
      if ($key === 'password') {
        $fields[] = "password_hash = ?";
        $params[] = password_hash($value, PASSWORD_DEFAULT);
      } else {
        $fields[] = "$key = ?";
        $params[] = $value;
      }
    }
    $params[] = $id;
    $stmt = DB::conn()->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?");
    return $stmt->execute($params);
  }
}

