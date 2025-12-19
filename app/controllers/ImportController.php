<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/PaymentMethod.php';
require_once __DIR__ . '/../models/Vendor.php';

class ImportController {
  public function index() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);
    
    $error = null;
    $success = null;
    $imported = 0;
    $failed = 0;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } elseif (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        
        if ($ext === 'csv') {
          $result = $this->importCsv($file);
          $imported = $result['imported'];
          $failed = $result['failed'];
          if ($imported > 0) {
            $success = sprintf(__('import.success'), $imported);
            if ($failed > 0) {
              $error = sprintf(__('import.partial'), $failed);
            }
          } else {
            $error = __('import.failed');
          }
        } else {
          $error = __('import.invalid_format');
        }
      } else {
        $error = __('import.no_file');
      }
    }
    
    include __DIR__ . '/../views/import/index.php';
  }
  
  private function importCsv($file) {
    $imported = 0;
    $failed = 0;
    $lang = I18n::current();
    
    // 获取所有分类和支付方式用于匹配
    $categories = Category::all(['is_active' => 1]);
    $paymentMethods = PaymentMethod::all(['is_active' => 1]);
    $vendors = Vendor::all(['is_active' => 1]);
    
    // 创建映射
    $categoryMap = [];
    foreach ($categories as $cat) {
      $name = $lang === 'zh' ? $cat['name_zh'] : $cat['name_vi'];
      $categoryMap[strtolower(trim($name))] = $cat['id'];
    }
    
    $paymentMap = [];
    foreach ($paymentMethods as $pm) {
      $name = $lang === 'zh' ? $pm['name_zh'] : $pm['name_vi'];
      $paymentMap[strtolower(trim($name))] = $pm['id'];
    }
    
    $vendorMap = [];
    foreach ($vendors as $v) {
      $vendorMap[strtolower(trim($v['name']))] = $v['id'];
    }
    
    $handle = fopen($file, 'r');
    if (!$handle) {
      return ['imported' => 0, 'failed' => 0];
    }
    
    // 跳过表头
    $header = fgetcsv($handle);
    
    while (($row = fgetcsv($handle)) !== false) {
      if (count($row) < 8) {
        $failed++;
        continue;
      }
      
      // 解析CSV行（简化版，假设格式：ID,类型,金额,货币,分类,支付方式,供应商,时间,备注,状态）
      $type = strtolower(trim($row[1] ?? ''));
      $type = ($type === __('tx.income') || $type === 'income' || $type === '收入' || $type === 'thu') ? 'income' : 'expense';
      
      $amount = floatval($row[2] ?? 0);
      if ($amount <= 0) {
        $failed++;
        continue;
      }
      
      $currency = trim($row[3] ?? 'VND');
      $categoryName = strtolower(trim($row[4] ?? ''));
      $paymentName = strtolower(trim($row[5] ?? ''));
      $vendorName = trim($row[6] ?? '');
      $occurredAt = trim($row[7] ?? date('Y-m-d H:i:s'));
      $note = trim($row[8] ?? '');
      
      // 匹配分类
      $categoryId = $categoryMap[$categoryName] ?? null;
      if (!$categoryId) {
        $failed++;
        continue;
      }
      
      // 匹配支付方式
      $paymentId = $paymentMap[$paymentName] ?? null;
      if (!$paymentId) {
        $failed++;
        continue;
      }
      
      // 匹配供应商（可选）
      $vendorId = null;
      if ($vendorName && isset($vendorMap[strtolower($vendorName)])) {
        $vendorId = $vendorMap[strtolower($vendorName)];
      }
      
      // 创建交易
      $data = [
        'type' => $type,
        'amount' => $amount,
        'currency' => $currency,
        'category_id' => $categoryId,
        'payment_method_id' => $paymentId,
        'vendor_id' => $vendorId,
        'occurred_at' => $occurredAt ?: date('Y-m-d H:i:s'),
        'note' => $note,
        'created_by' => Auth::user()['id'],
        'status' => 'approved'
      ];
      
      if (Transaction::create($data)) {
        $imported++;
      } else {
        $failed++;
      }
    }
    
    fclose($handle);
    
    return ['imported' => $imported, 'failed' => $failed];
  }
}

