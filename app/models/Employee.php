<?php
require_once __DIR__ . '/../core/DB.php';

class Employee {
  public static function find($id) {
    $stmt = DB::conn()->prepare("
      SELECT e.*,
        r.name_zh as role_name_zh, r.name_vi as role_name_vi,
        u.display_name as creator_name
      FROM employees e
      LEFT JOIN roles r ON e.role_id = r.id
      LEFT JOIN users u ON e.created_by = u.id
      WHERE e.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }

  public static function create($data) {
    $sql = "INSERT INTO employees
      (name, role_id, phone, email, address, status, hire_date, resign_date, note, created_by)
      VALUES (?,?,?,?,?,?,?,?,?,?)";

    $stmt = DB::conn()->prepare($sql);
    $result = $stmt->execute([
      $data['name'],
      $data['role_id'],
      $data['phone'] ?? null,
      $data['email'] ?? null,
      $data['address'] ?? null,
      $data['status'] ?? 'active',
      $data['hire_date'] ?? null,
      $data['resign_date'] ?? null,
      $data['note'] ?? null,
      $data['created_by'],
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
    $stmt = DB::conn()->prepare("UPDATE employees SET " . implode(', ', $fields) . " WHERE id = ?");
    return $stmt->execute($params);
  }

  public static function list($filters = []) {
    $where = [];
    $params = [];

    if (!empty($filters['status'])) {
      $where[] = 'e.status = ?';
      $params[] = $filters['status'];
    }

    if (!empty($filters['role_id'])) {
      $where[] = 'e.role_id = ?';
      $params[] = $filters['role_id'];
    }

    if (!empty($filters['search'])) {
      $where[] = '(e.name LIKE ? OR e.phone LIKE ? OR e.email LIKE ?)';
      $search = '%' . $filters['search'] . '%';
      $params[] = $search;
      $params[] = $search;
      $params[] = $search;
    }

    $sql = "SELECT e.*,
      r.name_zh as role_name_zh, r.name_vi as role_name_vi,
      u.display_name as creator_name
      FROM employees e
      LEFT JOIN roles r ON e.role_id = r.id
      LEFT JOIN users u ON e.created_by = u.id";
    
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY e.name ASC";
    
    if (isset($filters['limit'])) {
      $sql .= " LIMIT " . intval($filters['limit']);
      if (isset($filters['offset'])) {
        $sql .= " OFFSET " . intval($filters['offset']);
      }
    }

    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }

  public static function count($filters = []) {
    $where = [];
    $params = [];

    if (!empty($filters['status'])) {
      $where[] = 'status = ?';
      $params[] = $filters['status'];
    }

    if (!empty($filters['role_id'])) {
      $where[] = 'role_id = ?';
      $params[] = $filters['role_id'];
    }

    if (!empty($filters['search'])) {
      $where[] = '(name LIKE ? OR phone LIKE ? OR email LIKE ?)';
      $search = '%' . $filters['search'] . '%';
      $params[] = $search;
      $params[] = $search;
      $params[] = $search;
    }

    $sql = "SELECT COUNT(*) as total FROM employees";
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }

    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result ? (int)$result['total'] : 0;
  }

  public static function all($filters = []) {
    return self::list($filters);
  }

  public static function active() {
    return self::list(['status' => 'active']);
  }
}

