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
    // 计算week_start_date（如果未提供）
    if (empty($data['week_start_date']) && !empty($data['shift_date'])) {
      $shiftDate = new DateTime($data['shift_date']);
      $weekday = (int)$shiftDate->format('w'); // 0=Sunday, 1=Monday, etc.
      $daysToMonday = ($weekday == 0) ? 6 : ($weekday - 1);
      $monday = clone $shiftDate;
      $monday->modify("-{$daysToMonday} days");
      $data['week_start_date'] = $monday->format('Y-m-d');
    }

    $sql = "INSERT INTO shifts
      (shift_date, week_start_date, shift_type, employee_id, manager_id, note, created_by, is_published)
      VALUES (?,?,?,?,?,?,?,?)";

    $stmt = DB::conn()->prepare($sql);
    $result = $stmt->execute([
      $data['shift_date'],
      $data['week_start_date'] ?? null,
      $data['shift_type'],
      $data['employee_id'],
      $data['manager_id'] ?? null,
      $data['note'] ?? null,
      $data['created_by'],
      isset($data['is_published']) ? (int)$data['is_published'] : 0,
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

    if (!empty($filters['week_start_date'])) {
      $where[] = 's.week_start_date = ?';
      $params[] = $filters['week_start_date'];
    }

    if (isset($filters['is_published'])) {
      $where[] = 's.is_published = ?';
      $params[] = (int)$filters['is_published'];
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
    
    // 记录确认状态：只有 confirmed 状态才标记为已确认
    // 但所有非 pending 状态都应该记录确认时间和确认人（表示已处理）
    $isConfirmed = ($status === 'confirmed') ? 1 : 0;
    $confirmedAt = ($status !== 'pending') ? date('Y-m-d H:i:s') : null;
    $confirmedBy = ($status !== 'pending') ? $userId : null;
    
    // 如果从非 pending 状态改回 pending，清除确认信息
    if ($status === 'pending') {
      $confirmedAt = null;
      $confirmedBy = null;
    }
    
    $data = [
      'status' => $status,
      'is_confirmed' => $isConfirmed,
      'confirmed_at' => $confirmedAt,
      'confirmed_by' => $confirmedBy
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

  // 获取指定周的排班数据（按员工和日期组织）
  public static function getWeeklySchedule($weekStartDate) {
    $weekEndDate = date('Y-m-d', strtotime($weekStartDate . ' +6 days'));
    $shifts = self::list([
      'from_date' => $weekStartDate,
      'to_date' => $weekEndDate
    ]);

    // 按员工ID和日期组织数据
    $schedule = [];
    foreach ($shifts as $shift) {
      $empId = $shift['employee_id'];
      $date = $shift['shift_date'];
      if (!isset($schedule[$empId])) {
        $schedule[$empId] = [
          'employee_id' => $empId,
          'employee_name' => $shift['employee_name'],
          'days' => []
        ];
      }
      if (!isset($schedule[$empId]['days'][$date])) {
        $schedule[$empId]['days'][$date] = [];
      }
      $schedule[$empId]['days'][$date][] = $shift;
    }
    return $schedule;
  }

  // 批量创建或更新周排数据
  public static function saveWeeklySchedule($weekStartDate, $scheduleData, $userId, $isPublished = false) {
    DB::conn()->beginTransaction();
    try {
      // 删除本周未发布的排班（如果发布，则保留）
      if (!$isPublished) {
        $stmt = DB::conn()->prepare("DELETE FROM shifts WHERE week_start_date = ? AND is_published = 0");
        $stmt->execute([$weekStartDate]);
      }

      // 保存新的排班数据
      foreach ($scheduleData as $empId => $days) {
        foreach ($days as $date => $shiftTypes) {
          // 删除该员工该日期的现有排班（如果未发布）
          if (!$isPublished) {
            $stmt = DB::conn()->prepare("DELETE FROM shifts WHERE shift_date = ? AND employee_id = ? AND is_published = 0");
            $stmt->execute([$date, $empId]);
          }

          // 创建新的排班记录
          foreach ($shiftTypes as $shiftType) {
            if ($shiftType && $shiftType !== 'rest') {
              self::create([
                'shift_date' => $date,
                'week_start_date' => $weekStartDate,
                'shift_type' => $shiftType,
                'employee_id' => $empId,
                'created_by' => $userId,
                'is_published' => $isPublished ? 1 : 0
              ]);
            }
          }
        }
      }

      DB::conn()->commit();
      return true;
    } catch (Exception $e) {
      DB::conn()->rollBack();
      error_log("Shift::saveWeeklySchedule error: " . $e->getMessage());
      return false;
    }
  }

  // 发布周排
  public static function publishWeeklySchedule($weekStartDate) {
    $stmt = DB::conn()->prepare("UPDATE shifts SET is_published = 1 WHERE week_start_date = ?");
    return $stmt->execute([$weekStartDate]);
  }

  // 检查周排是否已发布
  public static function isWeekPublished($weekStartDate) {
    $stmt = DB::conn()->prepare("SELECT COUNT(*) as count FROM shifts WHERE week_start_date = ? AND is_published = 1 LIMIT 1");
    $stmt->execute([$weekStartDate]);
    $result = $stmt->fetch();
    return $result && $result['count'] > 0;
  }
}

