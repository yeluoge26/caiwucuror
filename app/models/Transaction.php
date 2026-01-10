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
      (`type`, amount, currency, category_id, payment_method_id, vendor_id, payer,
       occurred_at, note, status, created_by)
      VALUES (?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = DB::conn()->prepare($sql);
    $result = $stmt->execute([
      $data['type'],
      $data['amount'],
      $data['currency'] ?? 'VND',
      $data['category_id'],
      $data['payment_method_id'],
      $data['vendor_id'] ?? null,
      $data['payer'] ?? null,
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
    
    // 支持分页
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
    $where = ['status != ?'];
    $params = ['void'];

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

    $sql = "SELECT COUNT(*) as total
      FROM transactions t
      LEFT JOIN vendors v ON t.vendor_id = v.id";
    
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
    $where = ['t.status = ?', 't.`type` = ?'];
    $params = ['approved', 'expense'];

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
      LEFT JOIN transactions t ON v.id = t.vendor_id";
    
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " GROUP BY v.id, v.name
      HAVING transaction_count > 0
      ORDER BY total_amount DESC";

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

  /**
   * 计算坪效分析
   * 坪效 = 销售额 / 店铺面积（平方米）
   * 
   * @param array $filters 筛选条件（from, to）
   * @param float $storeArea 店铺面积（平方米），默认100平方米
   * @return array 包含销售额、面积、坪效等数据
   */
  public static function getSalesPerSquareMeter($filters = [], $storeArea = 100) {
    $where = ['status = ?', '`type` = ?'];
    $params = ['approved', 'income'];

    if (!empty($filters['from'])) {
      $where[] = 'occurred_at>=?';
      $params[] = $filters['from'];
    }

    if (!empty($filters['to'])) {
      $where[] = 'occurred_at<=?';
      $params[] = $filters['to'];
    }

    $sql = "SELECT 
      SUM(amount) as total_sales,
      COUNT(*) as transaction_count
      FROM transactions";
    
    if ($where) {
      $sql .= " WHERE " . implode(' AND ', $where);
    }

    $stmt = DB::conn()->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();

    $totalSales = $result['total_sales'] ?? 0;
    $salesPerSquareMeter = $storeArea > 0 ? ($totalSales / $storeArea) : 0;

    return [
      'total_sales' => $totalSales,
      'store_area' => $storeArea,
      'sales_per_square_meter' => $salesPerSquareMeter,
      'transaction_count' => $result['transaction_count'] ?? 0
    ];
  }
}
