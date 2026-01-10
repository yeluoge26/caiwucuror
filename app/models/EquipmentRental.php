<?php
require_once __DIR__ . '/../core/DB.php';

class EquipmentRental {
  public static function find($id) {
    $stmt = DB::conn()->prepare("
      SELECT r.*, 
        p.name_zh as payment_name_zh, p.name_vi as payment_name_vi,
        u.display_name as creator_name
      FROM equipment_rentals r
      LEFT JOIN payment_methods p ON r.deposit_payment_method_id = p.id
      LEFT JOIN users u ON r.created_by = u.id
      WHERE r.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }

  public static function list($filters = []) {
    $where = [];
    $params = [];

    if (isset($filters['status'])) {
      $where[] = "r.status = ?";
      $params[] = $filters['status'];
    }

    if (isset($filters['customer_name'])) {
      $where[] = "r.customer_name LIKE ?";
      $params[] = '%' . $filters['customer_name'] . '%';
    }

    if (isset($filters['equipment_name'])) {
      $where[] = "r.equipment_name LIKE ?";
      $params[] = '%' . $filters['equipment_name'] . '%';
    }

    if (isset($filters['from_date'])) {
      $where[] = "r.start_date >= ?";
      $params[] = $filters['from_date'];
    }

    if (isset($filters['to_date'])) {
      $where[] = "r.end_date <= ?";
      $params[] = $filters['to_date'];
    }

    $sql = "
      SELECT r.*, 
        p.name_zh as payment_name_zh, p.name_vi as payment_name_vi,
        u.display_name as creator_name
      FROM equipment_rentals r
      LEFT JOIN payment_methods p ON r.deposit_payment_method_id = p.id
      LEFT JOIN users u ON r.created_by = u.id
    ";

    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }

    $sql .= " ORDER BY r.created_at DESC";

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

    if (isset($filters['status'])) {
      $where[] = "status = ?";
      $params[] = $filters['status'];
    }

    if (isset($filters['customer_name'])) {
      $where[] = "customer_name LIKE ?";
      $params[] = '%' . $filters['customer_name'] . '%';
    }

    if (isset($filters['equipment_name'])) {
      $where[] = "equipment_name LIKE ?";
      $params[] = '%' . $filters['equipment_name'] . '%';
    }

    if (isset($filters['from_date'])) {
      $where[] = "start_date >= ?";
      $params[] = $filters['from_date'];
    }

    if (isset($filters['to_date'])) {
      $where[] = "end_date <= ?";
      $params[] = $filters['to_date'];
    }

    $sql = "SELECT COUNT(*) as total FROM equipment_rentals";
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }

    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
  }

  public static function create($data) {
    $stmt = DB::conn()->prepare("
      INSERT INTO equipment_rentals 
        (customer_name, equipment_name, equipment_serial, start_date, end_date, 
         deposit, deposit_payment_method_id, id_card_photo, status, note, created_by)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $ok = $stmt->execute([
      $data['customer_name'],
      $data['equipment_name'],
      $data['equipment_serial'] ?? null,
      $data['start_date'],
      $data['end_date'],
      $data['deposit'] ?? 0,
      $data['deposit_payment_method_id'] ?? null,
      $data['id_card_photo'] ?? null,
      $data['status'] ?? 'active',
      $data['note'] ?? null,
      $data['created_by'],
    ]);
    return $ok ? DB::conn()->lastInsertId() : false;
  }

  public static function update($id, $data) {
    $fields = [];
    $params = [];
    foreach ($data as $k => $v) {
      $fields[] = "$k = ?";
      $params[] = $v;
    }
    $params[] = $id;
    $stmt = DB::conn()->prepare("UPDATE equipment_rentals SET " . implode(', ', $fields) . " WHERE id = ?");
    return $stmt->execute($params);
  }

  public static function returnEquipment($id) {
    $stmt = DB::conn()->prepare("UPDATE equipment_rentals SET status='returned' WHERE id = ?");
    return $stmt->execute([$id]);
  }

  public static function cancel($id) {
    $stmt = DB::conn()->prepare("UPDATE equipment_rentals SET status='cancelled' WHERE id = ?");
    return $stmt->execute([$id]);
  }
}
