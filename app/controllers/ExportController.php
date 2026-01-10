<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/I18n.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/PaymentMethod.php';
require_once __DIR__ . '/../models/Vendor.php';

class ExportController {
  public function excel() {
    Auth::requireLogin();
    
    $user = Auth::user();
    $filters = [
      'type' => $_GET['type'] ?? null,
      'from' => $_GET['from'] ?? null,
      'to' => $_GET['to'] ?? null,
      'category_id' => $_GET['category_id'] ?? null,
      'payment_method_id' => $_GET['payment_method_id'] ?? null,
      'created_by' => $_GET['created_by'] ?? null,
      'search' => $_GET['search'] ?? null,
      'status' => $_GET['status'] ?? null,
      'page' => 1,
      'per_page' => 10000 // 导出时允许更多数据
    ];
    
    // 默认只导出已审核的交易，除非用户是owner且明确指定
    if ($user['role_key'] !== 'owner' || !isset($_GET['status'])) {
      $filters['status'] = 'approved';
    }
    
    $items = Transaction::list($filters);
    $lang = I18n::current();
    
    // 设置Excel头
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="transactions_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');
    
    echo '<html><head><meta charset="utf-8"></head><body>';
    echo '<table border="1">';
    
    // 表头
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>' . __('tx.type') . '</th>';
    echo '<th>' . __('tx.amount') . '</th>';
    echo '<th>' . __('field.currency') . '</th>';
    echo '<th>' . __('tx.category') . '</th>';
    echo '<th>' . __('field.payment') . '</th>';
    echo '<th>' . __('field.vendor') . '</th>';
    echo '<th>' . __('field.time') . '</th>';
    echo '<th>' . __('field.note') . '</th>';
    echo '<th>' . __('tx.status') . '</th>';
    echo '<th>' . __('tx.create') . '</th>';
    echo '</tr>';
    
    // 数据行
    foreach ($items as $row) {
      echo '<tr>';
      echo '<td>' . htmlspecialchars($row['id']) . '</td>';
      echo '<td>' . ($row['type'] === 'income' ? __('tx.income') : __('tx.expense')) . '</td>';
      echo '<td>' . number_format($row['amount'], 2, '.', '') . '</td>';
      echo '<td>' . htmlspecialchars($row['currency']) . '</td>';
      echo '<td>' . htmlspecialchars($lang === 'zh' ? ($row['category_name_zh'] ?? '') : ($row['category_name_vi'] ?? '')) . '</td>';
      echo '<td>' . htmlspecialchars($lang === 'zh' ? ($row['payment_name_zh'] ?? '') : ($row['payment_name_vi'] ?? '')) . '</td>';
      echo '<td>' . htmlspecialchars($row['vendor_name'] ?? '') . '</td>';
      echo '<td>' . date('Y-m-d H:i:s', strtotime($row['occurred_at'])) . '</td>';
      echo '<td>' . htmlspecialchars($row['note'] ?? '') . '</td>';
      $status = $row['status'] === 'approved' ? __('tx.approved') : 
                ($row['status'] === 'pending' ? __('tx.pending') : __('tx.void'));
      echo '<td>' . htmlspecialchars($status) . '</td>';
      echo '<td>' . htmlspecialchars($row['creator_name'] ?? '') . '</td>';
      echo '</tr>';
    }
    
    echo '</table>';
    echo '</body></html>';
    exit;
  }
  
  public function csv() {
    Auth::requireLogin();
    
    $user = Auth::user();
    $filters = [
      'type' => $_GET['type'] ?? null,
      'from' => $_GET['from'] ?? null,
      'to' => $_GET['to'] ?? null,
      'category_id' => $_GET['category_id'] ?? null,
      'payment_method_id' => $_GET['payment_method_id'] ?? null,
      'created_by' => $_GET['created_by'] ?? null,
      'search' => $_GET['search'] ?? null,
      'status' => $_GET['status'] ?? null,
      'page' => 1,
      'per_page' => 10000
    ];
    
    // 默认只导出已审核的交易，除非用户是owner且明确指定
    if ($user['role_key'] !== 'owner' || !isset($_GET['status'])) {
      $filters['status'] = 'approved';
    }
    
    $items = Transaction::list($filters);
    $lang = I18n::current();
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="transactions_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // 添加BOM以支持Excel正确显示中文
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // 表头
    fputcsv($output, [
      'ID', __('tx.type'), __('tx.amount'), __('field.currency'),
      __('tx.category'), __('field.payment'), __('field.vendor'),
      __('field.time'), __('field.note'), __('tx.status'), __('tx.create')
    ]);
    
    // 数据行
    foreach ($items as $row) {
      fputcsv($output, [
        $row['id'],
        $row['type'] === 'income' ? __('tx.income') : __('tx.expense'),
        number_format($row['amount'], 2, '.', ''),
        $row['currency'],
        $lang === 'zh' ? ($row['category_name_zh'] ?? '') : ($row['category_name_vi'] ?? ''),
        $lang === 'zh' ? ($row['payment_name_zh'] ?? '') : ($row['payment_name_vi'] ?? ''),
        $row['vendor_name'] ?? '',
        date('Y-m-d H:i:s', strtotime($row['occurred_at'])),
        $row['note'] ?? '',
        $row['status'] === 'approved' ? __('tx.approved') : 
          ($row['status'] === 'pending' ? __('tx.pending') : __('tx.void')),
        $row['creator_name'] ?? ''
      ]);
    }
    
    fclose($output);
    exit;
  }
}

