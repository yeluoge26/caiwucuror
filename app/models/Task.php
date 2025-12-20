<?php
require_once __DIR__ . '/../core/DB.php';

class Task {
  public static function find($id) {
    $stmt = DB::conn()->prepare("
      SELECT t.*,
        r.name_zh as assign_role_name_zh, r.name_vi as assign_role_name_vi,
        u1.display_name as assign_user_name,
        u2.display_name as creator_name,
        u3.display_name as completed_by_name,
        u4.display_name as approved_by_name
      FROM tasks t
      LEFT JOIN roles r ON t.assign_role_id = r.id
      LEFT JOIN users u1 ON t.assign_user_id = u1.id
      LEFT JOIN users u2 ON t.created_by = u2.id
      LEFT JOIN users u3 ON t.completed_by = u3.id
      LEFT JOIN users u4 ON t.approved_by = u4.id
      WHERE t.id = ?
    ");
    $stmt->execute([$id]);
    $result = $stmt->fetch();
    if ($result && !isset($result['approved_by_name'])) {
      $result['approved_by_name'] = null;
    }
    return $result;
  }

  public static function create($data) {
    $sql = "INSERT INTO tasks
      (title, `type`, description, due_date, store, assign_type, assign_role_id, assign_user_id,
       require_photo, require_attachment, penalty_amount, reward_amount, currency, status, created_by)
      VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = DB::conn()->prepare($sql);
    $result = $stmt->execute([
      $data['title'],
      $data['type'] ?? 'temporary',
      $data['description'] ?? null,
      $data['due_date'] ?? null,
      $data['store'] ?? null,
      $data['assign_type'] ?? 'user',
      $data['assign_role_id'] ?? null,
      $data['assign_user_id'] ?? null,
      $data['require_photo'] ?? 0,
      $data['require_attachment'] ?? 0,
      $data['penalty_amount'] ?? null,
      $data['reward_amount'] ?? null,
      $data['currency'] ?? 'VND',
      $data['status'] ?? 'pending',
      $data['created_by'],
    ]);

    if ($result) {
      return DB::conn()->lastInsertId();
    }
    return false;
  }

  public static function list($filters = []) {
    $where = [];
    $params = [];

    if (!empty($filters['status'])) {
      $where[] = 't.status = ?';
      $params[] = $filters['status'];
    }

    if (!empty($filters['type'])) {
      $where[] = 't.`type` = ?';
      $params[] = $filters['type'];
    }

    if (!empty($filters['store'])) {
      $where[] = 't.store = ?';
      $params[] = $filters['store'];
    }

    // 支持查询分配给指定用户或角色的任务（用于今日任务等场景）
    if (isset($filters['assigned_to_user_id']) && isset($filters['assigned_to_role_id'])) {
      $where[] = '(t.assign_user_id = ? OR t.assign_role_id = ?)';
      $params[] = $filters['assigned_to_user_id'];
      $params[] = $filters['assigned_to_role_id'];
    } elseif (!empty($filters['assign_user_id'])) {
      $where[] = 't.assign_user_id = ?';
      $params[] = $filters['assign_user_id'];
    }

    if (!empty($filters['assign_role_id'])) {
      $where[] = 't.assign_role_id = ?';
      $params[] = $filters['assign_role_id'];
    }

    if (!empty($filters['created_by'])) {
      $where[] = 't.created_by = ?';
      $params[] = $filters['created_by'];
    }

    if (!empty($filters['search'])) {
      $where[] = '(t.title LIKE ? OR t.description LIKE ?)';
      $search = '%' . $filters['search'] . '%';
      $params[] = $search;
      $params[] = $search;
    }

    $sql = "SELECT t.*,
      r.name_zh as assign_role_name_zh, r.name_vi as assign_role_name_vi,
      u1.display_name as assign_user_name,
      u2.display_name as creator_name,
      u3.display_name as completed_by_name
      FROM tasks t
      LEFT JOIN roles r ON t.assign_role_id = r.id
      LEFT JOIN users u1 ON t.assign_user_id = u1.id
      LEFT JOIN users u2 ON t.created_by = u2.id
      LEFT JOIN users u3 ON t.completed_by = u3.id";
    
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY t.created_at DESC";
    
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

    if (!empty($filters['type'])) {
      $where[] = '`type` = ?';
      $params[] = $filters['type'];
    }

    if (!empty($filters['store'])) {
      $where[] = 'store = ?';
      $params[] = $filters['store'];
    }

    // 支持查询分配给指定用户或角色的任务（用于今日任务等场景）
    if (isset($filters['assigned_to_user_id']) && isset($filters['assigned_to_role_id'])) {
      $where[] = '(assign_user_id = ? OR assign_role_id = ?)';
      $params[] = $filters['assigned_to_user_id'];
      $params[] = $filters['assigned_to_role_id'];
    } elseif (!empty($filters['assign_user_id'])) {
      $where[] = 'assign_user_id = ?';
      $params[] = $filters['assign_user_id'];
    }

    if (!empty($filters['assign_role_id'])) {
      $where[] = 'assign_role_id = ?';
      $params[] = $filters['assign_role_id'];
    }

    if (!empty($filters['search'])) {
      $where[] = '(title LIKE ? OR description LIKE ?)';
      $search = '%' . $filters['search'] . '%';
      $params[] = $search;
      $params[] = $search;
    }

    $sql = "SELECT COUNT(*) as total FROM tasks";
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }

    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result ? (int)$result['total'] : 0;
  }

  public static function update($id, $data) {
    $fields = [];
    $params = [];
    foreach ($data as $key => $value) {
      $fields[] = "$key = ?";
      $params[] = $value;
    }
    $params[] = $id;
    $stmt = DB::conn()->prepare("UPDATE tasks SET " . implode(', ', $fields) . " WHERE id = ?");
    return $stmt->execute($params);
  }

  public static function complete($id, $userId, $note = null, $attachments = []) {
    $data = [
      'status' => 'completed',
      'completed_at' => date('Y-m-d H:i:s'),
      'completed_by' => $userId,
      'completion_note' => $note
    ];
    return self::update($id, $data);
  }

  public static function approve($id, $userId, $note = null) {
    $data = [
      'status' => 'approved',
      'approved_at' => date('Y-m-d H:i:s'),
      'approved_by' => $userId,
      'approval_note' => $note
    ];
    return self::update($id, $data);
  }

  public static function reject($id, $userId, $note = null) {
    $data = [
      'status' => 'rejected',
      'approved_at' => date('Y-m-d H:i:s'),
      'approved_by' => $userId,
      'approval_note' => $note
    ];
    return self::update($id, $data);
  }

  public static function start($id) {
    return self::update($id, ['status' => 'in_progress']);
  }
}

