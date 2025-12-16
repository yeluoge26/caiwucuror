<?php
require_once __DIR__ . '/../core/DB.php';

class Transaction {
  public static function find($id) {
    $stmt = DB::conn()->prepare("
      SELECT t.*, 
        c.name_zh as category_name_zh, c.name_vi as category_name_vi,
        p.name_zh as payment_name_zh, p.name_vi as payment_name_vi,
        v.name as vendor_name,
        u.display_name as creator_name
      FROM transactions t
      LEFT JOIN categories c ON t.category_id = c.id
      LEFT JOIN payment_methods p ON t.payment_method_id = p.id
      LEFT JOIN vendors v ON t.vendor_id = v.id
      LEFT JOIN users u ON t.created_by = u.id
      WHERE t.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }

  public static function create($data) {
    $sql = "INSERT INTO transactions
      (`type`, amount, currency, category_id, payment_method_id, vendor_id,
       occurred_at, note, status, created_by)
      VALUES (?,?,?,?,?,?,?,?,?,?)";

    $stmt = DB::conn()->prepare($sql);
    $result = $stmt->execute([
      $data['type'],
      $data['amount'],
      $data['currency'] ?? 'VND',
      $data['category_id'],
      $data['payment_method_id'],
      $data['vendor_id'] ?? null,
      $data['occurred_at'],
      $data['note'] ?? null,
      $data['status'] ?? 'approved',
      $data['created_by'],
    ]);

    if ($result) {
      return DB::conn()->lastInsertId();
    }
    return false;
  }

  public static function list($filters = []) {
    $where = ['status != ?'];
    $params = ['void'];

    // 默认只显示已审核的交易，除非明确指定status或用户是owner
    if (!isset($filters['status']) && empty($filters['show_pending'])) {
      $where[] = 't.status = ?';
      $params[] = 'approved';
    } elseif (!empty($filters['status'])) {
      $where[] = 't.status = ?';
      $params[] = $filters['status'];
    }

    if (!empty($filters['type'])) {
      $where[] = 't.`type`=?';
      $params[] = $filters['type'];
    }

    if (!empty($filters['from'])) {
      $where[] = 't.occurred_at>=?';
      $params[] = $filters['from'];
    }

    if (!empty($filters['to'])) {
      $where[] = 't.occurred_at<=?';
      $params[] = $filters['to'];
    }

    if (!empty($filters['category_id'])) {
      $where[] = 't.category_id=?';
      $params[] = $filters['category_id'];
    }

    if (!empty($filters['payment_method_id'])) {
      $where[] = 't.payment_method_id=?';
      $params[] = $filters['payment_method_id'];
    }

    if (!empty($filters['vendor_id'])) {
      $where[] = 't.vendor_id=?';
      $params[] = $filters['vendor_id'];
    }

    if (!empty($filters['created_by'])) {
      $where[] = 't.created_by=?';
      $params[] = $filters['created_by'];
    }

    if (!empty($filters['search'])) {
      $where[] = '(t.note LIKE ? OR v.name LIKE ?)';
      $search = '%' . $filters['search'] . '%';
      $params[] = $search;
      $params[] = $search;
    }

    $sql = "SELECT t.*, 
      c.name_zh as category_name_zh, c.name_vi as category_name_vi,
      p.name_zh as payment_name_zh, p.name_vi as payment_name_vi,
      v.name as vendor_name,
      u.display_name as creator_name
      FROM transactions t
      LEFT JOIN categories c ON t.category_id = c.id
      LEFT JOIN payment_methods p ON t.payment_method_id = p.id
      LEFT JOIN vendors v ON t.vendor_id = v.id
      LEFT JOIN users u ON t.created_by = u.id";
    
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY t.occurred_at DESC";
    
    // 分页支持
    $page = $filters['page'] ?? 1;
    $perPage = $filters['per_page'] ?? 20;
    $offset = ($page - 1) * $perPage;
    $sql .= " LIMIT " . intval($perPage) . " OFFSET " . intval($offset);

    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }
  
  public static function count($filters = []) {
    $where = ['status != ?'];
    $params = ['void'];

    // 默认只统计已审核的交易，除非明确指定status或用户是owner
    if (!isset($filters['status']) && empty($filters['show_pending'])) {
      $where[] = 'status = ?';
      $params[] = 'approved';
    } elseif (!empty($filters['status'])) {
      $where[] = 'status = ?';
      $params[] = $filters['status'];
    }

    if (!empty($filters['type'])) {
      $where[] = '`type`=?';
      $params[] = $filters['type'];
    }

    if (!empty($filters['from'])) {
      $where[] = 'occurred_at>=?';
      $params[] = $filters['from'];
    }

    if (!empty($filters['to'])) {
      $where[] = 'occurred_at<=?';
      $params[] = $filters['to'];
    }

    if (!empty($filters['category_id'])) {
      $where[] = 'category_id=?';
      $params[] = $filters['category_id'];
    }

    if (!empty($filters['payment_method_id'])) {
      $where[] = 'payment_method_id=?';
      $params[] = $filters['payment_method_id'];
    }

    if (!empty($filters['vendor_id'])) {
      $where[] = 'vendor_id=?';
      $params[] = $filters['vendor_id'];
    }

    if (!empty($filters['created_by'])) {
      $where[] = 'created_by=?';
      $params[] = $filters['created_by'];
    }

    if (!empty($filters['search'])) {
      // 搜索备注和供应商名称
      $where[] = '(note LIKE ? OR EXISTS (SELECT 1 FROM vendors v WHERE v.id = transactions.vendor_id AND v.name LIKE ?))';
      $search = '%' . $filters['search'] . '%';
      $params[] = $search;
      $params[] = $search;
    }

    $sql = "SELECT COUNT(*) as total FROM transactions";
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }

    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
  }

  public static function update($id, $data) {
    $fields = [];
    $params = [];
    foreach ($data as $key => $value) {
      $fields[] = "$key = ?";
      $params[] = $value;
    }
    $params[] = $id;
    $stmt = DB::conn()->prepare("UPDATE transactions SET " . implode(', ', $fields) . " WHERE id = ?");
    return $stmt->execute($params);
  }

  public static function void($id) {
    $stmt = DB::conn()->prepare("UPDATE transactions SET status = 'void' WHERE id = ?");
    return $stmt->execute([$id]);
  }

  public static function getSummary($filters = []) {
    $where = ['status = ?'];
    $params = ['approved'];

    if (!empty($filters['from'])) {
      $where[] = 'occurred_at>=?';
      $params[] = $filters['from'];
    }

    if (!empty($filters['to'])) {
      $where[] = 'occurred_at<=?';
      $params[] = $filters['to'];
    }

    $sql = "SELECT 
      SUM(CASE WHEN `type`='income' THEN amount ELSE 0 END) as income,
      SUM(CASE WHEN `type`='expense' THEN amount ELSE 0 END) as expense
      FROM transactions";
    
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }

    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
  }

  public static function getTrend($days = 7) {
    // 只统计已审核的交易
    $stmt = DB::conn()->prepare("
      SELECT
        DATE(occurred_at) as day,
        SUM(CASE WHEN `type`='income' THEN amount ELSE 0 END) as income,
        SUM(CASE WHEN `type`='expense' THEN amount ELSE 0 END) as expense
      FROM transactions
      WHERE status='approved'
        AND occurred_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
      GROUP BY day
      ORDER BY day ASC
    ");
    $stmt->execute([$days - 1]);
    return $stmt->fetchAll();
  }

  public static function getByCategory($filters = []) {
    $where = ['t.status = ?'];
    $params = ['approved'];

    if (!empty($filters['type'])) {
      $where[] = 't.`type`=?';
      $params[] = $filters['type'];
    }

    if (!empty($filters['from'])) {
      $where[] = 't.occurred_at>=?';
      $params[] = $filters['from'];
    }

    if (!empty($filters['to'])) {
      $where[] = 't.occurred_at<=?';
      $params[] = $filters['to'];
    }

    $sql = "SELECT 
      c.id, c.name_zh, c.name_vi,
      SUM(CASE WHEN t.`type`='income' THEN t.amount ELSE 0 END) as income,
      SUM(CASE WHEN t.`type`='expense' THEN t.amount ELSE 0 END) as expense
      FROM categories c
      LEFT JOIN transactions t ON c.id = t.category_id";
    
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " GROUP BY c.id, c.name_zh, c.name_vi
      HAVING income > 0 OR expense > 0
      ORDER BY (income + expense) DESC";

    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }

  public static function getTrendByDate($from, $to) {
    $stmt = DB::conn()->prepare("
      SELECT
        DATE(occurred_at) as day,
        SUM(CASE WHEN `type`='income' THEN amount ELSE 0 END) as income,
        SUM(CASE WHEN `type`='expense' THEN amount ELSE 0 END) as expense
      FROM transactions
      WHERE status='approved'
        AND occurred_at >= ?
        AND occurred_at <= ?
      GROUP BY day
      ORDER BY day ASC
    ");
    $stmt->execute([$from, $to]);
    return $stmt->fetchAll();
  }

  public static function getByPaymentMethod($filters = []) {
    $where = ['t.status = ?'];
    $params = ['approved'];

    if (!empty($filters['from'])) {
      $where[] = 't.occurred_at>=?';
      $params[] = $filters['from'];
    }

    if (!empty($filters['to'])) {
      $where[] = 't.occurred_at<=?';
      $params[] = $filters['to'];
    }

    $sql = "SELECT 
      p.id, p.name_zh, p.name_vi,
      SUM(CASE WHEN t.`type`='income' THEN t.amount ELSE 0 END) as income,
      SUM(CASE WHEN t.`type`='expense' THEN t.amount ELSE 0 END) as expense
      FROM payment_methods p
      LEFT JOIN transactions t ON p.id = t.payment_method_id";
    
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " GROUP BY p.id, p.name_zh, p.name_vi
      HAVING income > 0 OR expense > 0
      ORDER BY (income + expense) DESC";

    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }

  public static function getByVendor($filters = []) {
    $where = ['t.status = ?', 't.vendor_id IS NOT NULL'];
    $params = ['approved'];

    if (!empty($filters['from'])) {
      $where[] = 't.occurred_at>=?';
      $params[] = $filters['from'];
    }

    if (!empty($filters['to'])) {
      $where[] = 't.occurred_at<=?';
      $params[] = $filters['to'];
    }

    $sql = "SELECT 
      v.id, v.name,
      COUNT(t.id) as transaction_count,
      SUM(t.amount) as total_amount
      FROM vendors v
      INNER JOIN transactions t ON v.id = t.vendor_id";
    
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " GROUP BY v.id, v.name
      ORDER BY total_amount DESC
      LIMIT 20";

    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }

  public static function getMonthlyCompare($months = 3) {
    $stmt = DB::conn()->prepare("
      SELECT
        DATE_FORMAT(occurred_at, '%Y-%m') as month,
        SUM(CASE WHEN `type`='income' THEN amount ELSE 0 END) as income,
        SUM(CASE WHEN `type`='expense' THEN amount ELSE 0 END) as expense
      FROM transactions
      WHERE status='approved'
        AND occurred_at >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
      GROUP BY month
      ORDER BY month ASC
    ");
    $stmt->execute([$months - 1]);
    return $stmt->fetchAll();
  }
}

