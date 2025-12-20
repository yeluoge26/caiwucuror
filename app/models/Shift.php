<?php
require_once __DIR__ . '/../core/DB.php';

class Shift {
  public static function find($id) {
    $stmt = DB::conn()->prepare("
      SELECT s.*,
        e.name as employee_name,
        m.name as manager_name,
        u1.display_name as creator_name,
        u2.display_name as confirmed_by_name
      FROM shifts s
      LEFT JOIN employees e ON s.employee_id = e.id
      LEFT JOIN employees m ON s.manager_id = m.id
      LEFT JOIN users u1 ON s.created_by = u1.id
      LEFT JOIN users u2 ON s.confirmed_by = u2.id
      WHERE s.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }

  public static function create($data) {
    $sql = "INSERT INTO shifts
      (shift_date, shift_type, employee_id, manager_id, note, created_by)
      VALUES (?,?,?,?,?,?)";

    $stmt = DB::conn()->prepare($sql);
    $result = $stmt->execute([
      $data['shift_date'],
      $data['shift_type'],
      $data['employee_id'],
      $data['manager_id'] ?? null,
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
    $stmt = DB::conn()->prepare("UPDATE shifts SET " . implode(', ', $fields) . " WHERE id = ?");
    return $stmt->execute($params);
  }

  public static function list($filters = []) {
    $where = [];
    $params = [];

    if (!empty($filters['shift_date'])) {
      $where[] = 's.shift_date = ?';
      $params[] = $filters['shift_date'];
    }

    if (!empty($filters['from_date'])) {
      $where[] = 's.shift_date >= ?';
      $params[] = $filters['from_date'];
    }

    if (!empty($filters['to_date'])) {
      $where[] = 's.shift_date <= ?';
      $params[] = $filters['to_date'];
    }

    if (!empty($filters['shift_type'])) {
      $where[] = 's.shift_type = ?';
      $params[] = $filters['shift_type'];
    }

    if (!empty($filters['employee_id'])) {
      $where[] = 's.employee_id = ?';
      $params[] = $filters['employee_id'];
    }

    if (!empty($filters['manager_id'])) {
      $where[] = 's.manager_id = ?';
      $params[] = $filters['manager_id'];
    }

    if (isset($filters['is_confirmed'])) {
      $where[] = 's.is_confirmed = ?';
      $params[] = $filters['is_confirmed'];
    }

    if (!empty($filters['status'])) {
      $where[] = 's.status = ?';
      $params[] = $filters['status'];
    }

    $sql = "SELECT s.*,
      e.name as employee_name,
      e.phone as phone,
      r.name_zh as role_name_zh, r.name_vi as role_name_vi,
      m.name as manager_name,
      u1.display_name as creator_name,
      u2.display_name as confirmed_by_name
      FROM shifts s
      LEFT JOIN employees e ON s.employee_id = e.id
      LEFT JOIN roles r ON e.role_id = r.id
      LEFT JOIN employees m ON s.manager_id = m.id
      LEFT JOIN users u1 ON s.created_by = u1.id
      LEFT JOIN users u2 ON s.confirmed_by = u2.id";
    
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY s.shift_date DESC, s.shift_type ASC";
    
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

    if (!empty($filters['shift_date'])) {
      $where[] = 'shift_date = ?';
      $params[] = $filters['shift_date'];
    }

    if (!empty($filters['from_date'])) {
      $where[] = 'shift_date >= ?';
      $params[] = $filters['from_date'];
    }

    if (!empty($filters['to_date'])) {
      $where[] = 'shift_date <= ?';
      $params[] = $filters['to_date'];
    }

    if (!empty($filters['employee_id'])) {
      $where[] = 'employee_id = ?';
      $params[] = $filters['employee_id'];
    }

    $sql = "SELECT COUNT(*) as total FROM shifts";
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }

    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result ? (int)$result['total'] : 0;
  }

  public static function confirm($id, $userId, $confirmed = true) {
    $data = [
      'is_confirmed' => $confirmed ? 1 : 0,
      'status' => $confirmed ? 'confirmed' : 'pending',
      'confirmed_at' => $confirmed ? date('Y-m-d H:i:s') : null,
      'confirmed_by' => $confirmed ? $userId : null
    ];
    return self::update($id, $data);
  }

  public static function updateStatus($id, $status, $userId) {
    $validStatuses = ['pending', 'confirmed', 'late', 'leave', 'off', 'abnormal'];
    if (!in_array($status, $validStatuses)) {
      return false;
    }
    
    $data = [
      'status' => $status,
      'is_confirmed' => ($status === 'confirmed') ? 1 : 0,
      'confirmed_at' => ($status !== 'pending') ? date('Y-m-d H:i:s') : null,
      'confirmed_by' => ($status !== 'pending') ? $userId : null
    ];
    return self::update($id, $data);
  }

  public static function getByDateRange($fromDate, $toDate, $employeeId = null) {
    $filters = [
      'from_date' => $fromDate,
      'to_date' => $toDate
    ];
    if ($employeeId) {
      $filters['employee_id'] = $employeeId;
    }
    return self::list($filters);
  }
}

