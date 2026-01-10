<?php
require_once __DIR__ . '/../core/DB.php';

class CashClosing {
  public static function find($id) {
    $stmt = DB::conn()->prepare("
      SELECT c.*,
        u1.display_name as creator_name,
        u2.display_name as reviewer_name
      FROM cash_closings c
      LEFT JOIN users u1 ON c.created_by = u1.id
      LEFT JOIN users u2 ON c.reviewed_by = u2.id
      WHERE c.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }

  public static function findByDate($date) {
    $stmt = DB::conn()->prepare("
      SELECT c.*,
        u1.display_name as creator_name,
        u2.display_name as reviewer_name
      FROM cash_closings c
      LEFT JOIN users u1 ON c.created_by = u1.id
      LEFT JOIN users u2 ON c.reviewed_by = u2.id
      WHERE c.date = ?
    ");
    $stmt->execute([$date]);
    return $stmt->fetch();
  }

  public static function create($data) {
    $sql = "INSERT INTO cash_closings
      (date, created_by, cash_counted, cash_note, status)
      VALUES (?,?,?,?,?)";

    $stmt = DB::conn()->prepare($sql);
    $result = $stmt->execute([
      $data['date'],
      $data['created_by'],
      $data['cash_counted'],
      $data['cash_note'] ?? null,
      $data['status'] ?? 'draft',
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
    $stmt = DB::conn()->prepare("UPDATE cash_closings SET " . implode(', ', $fields) . " WHERE id = ?");
    return $stmt->execute($params);
  }

  public static function submit($id) {
    return self::update($id, [
      'status' => 'submitted',
      'submitted_at' => date('Y-m-d H:i:s')
    ]);
  }

  public static function review($id, $status, $note, $userId) {
    return self::update($id, [
      'status' => $status,
      'reviewed_by' => $userId,
      'reviewed_at' => date('Y-m-d H:i:s'),
      'review_note' => $note
    ]);
  }

  public static function list($filters = []) {
    $where = [];
    $params = [];

    if (!empty($filters['date'])) {
      $where[] = 'c.date = ?';
      $params[] = $filters['date'];
    }

    if (!empty($filters['from_date'])) {
      $where[] = 'c.date >= ?';
      $params[] = $filters['from_date'];
    }

    if (!empty($filters['to_date'])) {
      $where[] = 'c.date <= ?';
      $params[] = $filters['to_date'];
    }

    if (!empty($filters['status'])) {
      $where[] = 'c.status = ?';
      $params[] = $filters['status'];
    }

    if (!empty($filters['created_by'])) {
      $where[] = 'c.created_by = ?';
      $params[] = $filters['created_by'];
    }

    $sql = "SELECT c.*,
      u1.display_name as creator_name,
      u2.display_name as reviewer_name
      FROM cash_closings c
      LEFT JOIN users u1 ON c.created_by = u1.id
      LEFT JOIN users u2 ON c.reviewed_by = u2.id";
    
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY c.date DESC, c.id DESC";
    
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

    if (!empty($filters['date'])) {
      $where[] = 'date = ?';
      $params[] = $filters['date'];
    }

    if (!empty($filters['status'])) {
      $where[] = 'status = ?';
      $params[] = $filters['status'];
    }

    $sql = "SELECT COUNT(*) as total FROM cash_closings";
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }

    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result ? (int)$result['total'] : 0;
  }

  /**
   * 计算指定日期的理论现金余额
   * 需要从transactions表统计现金收入和支出
   */
  public static function calculateTheoreticalBalance($date, $openingCash = 0) {
    require_once __DIR__ . '/Transaction.php';
    require_once __DIR__ . '/PaymentMethod.php';
    
    // 获取现金支付方式ID
    $cashPaymentMethod = PaymentMethod::findByName('现金', 'Tiền mặt');
    if (!$cashPaymentMethod) {
      return ['income' => 0, 'expense' => 0, 'balance' => $openingCash];
    }

    $cashPaymentId = $cashPaymentMethod['id'];

    // 计算现金收入
    $income = Transaction::getSummary([
      'from' => $date . ' 00:00:00',
      'to' => $date . ' 23:59:59',
      'payment_method_id' => $cashPaymentId,
      'type' => 'income',
      'status' => 'approved'
    ]);

    // 计算现金支出
    $expense = Transaction::getSummary([
      'from' => $date . ' 00:00:00',
      'to' => $date . ' 23:59:59',
      'payment_method_id' => $cashPaymentId,
      'type' => 'expense',
      'status' => 'approved'
    ]);

    $cashIncome = $income['income'] ?? 0;
    $cashExpense = $expense['expense'] ?? 0;
    $balance = $openingCash + $cashIncome - $cashExpense;

    return [
      'income' => $cashIncome,
      'expense' => $cashExpense,
      'balance' => $balance
    ];
  }

  /**
   * 获取上一日的日结记录，用于计算期初现金
   */
  public static function getPreviousClosing($date) {
    $stmt = DB::conn()->prepare("
      SELECT * FROM cash_closings
      WHERE date < ? AND status = 'approved'
      ORDER BY date DESC
      LIMIT 1
    ");
    $stmt->execute([$date]);
    return $stmt->fetch();
  }
}






