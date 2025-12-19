<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/PaymentMethod.php';
require_once __DIR__ . '/../models/Vendor.php';
require_once __DIR__ . '/../models/Attachment.php';
require_once __DIR__ . '/../models/VoidRequest.php';

class TransactionController {
  public function list() {
    Auth::requireLogin();

    // 分页参数
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 50; // 每页显示50条
    $offset = ($page - 1) * $perPage;

    $filters = [
      'type' => $_GET['type'] ?? null,
      'from' => $_GET['from'] ?? null,
      'to' => $_GET['to'] ?? null,
      'category_id' => $_GET['category_id'] ?? null,
      'payment_method_id' => $_GET['payment_method_id'] ?? null,
      'created_by' => $_GET['created_by'] ?? null,
      'search' => $_GET['search'] ?? null,
      'limit' => $perPage,
      'offset' => $offset
    ];

    $items = Transaction::list($filters);
    $total = Transaction::count($filters);
    $totalPages = ceil($total / $perPage);

    $categories = Category::all(['is_active' => 1]);
    $paymentMethods = PaymentMethod::all(['is_active' => 1]);
    $user = Auth::user();

    include __DIR__ . '/../views/transactions/list.php';
  }

  public function create() {
    Auth::requireLogin();

    $categories = Category::all(['is_active' => 1]);
    $paymentMethods = PaymentMethod::all(['is_active' => 1]);
    $vendors = Vendor::all(['is_active' => 1]);
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $data = [
          'type' => $_POST['type'] ?? '',
          'amount' => $_POST['amount'] ?? 0,
          'currency' => $_POST['currency'] ?? 'VND',
          'category_id' => $_POST['category_id'] ?? null,
          'payment_method_id' => $_POST['payment_method_id'] ?? null,
          'vendor_id' => !empty($_POST['vendor_id']) ? $_POST['vendor_id'] : null,
          'payer' => $_POST['payer'] ?? null,
          'occurred_at' => $_POST['occurred_at'] ?? date('Y-m-d H:i:s'),
          'note' => $_POST['note'] ?? null,
          'created_by' => Auth::user()['id'],
          'status' => 'approved'
        ];

        $txId = Transaction::create($data);
        if ($txId) {
          // 上传凭证图片（可选）
          if (!empty($_FILES['attachments'])) {
            Attachment::saveUploads($txId, $_FILES['attachments'], Auth::user()['id'] ?? null);
          }
          header('Location: /index.php?r=transactions/list');
          exit;
        } else {
          $error = __('transaction.create_failed');
        }
      }
    }

    include __DIR__ . '/../views/transactions/create.php';
  }

  public function view() {
    Auth::requireLogin();

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=transactions/list');
      exit;
    }

    $transaction = Transaction::find($id);
    $attachments = Attachment::listByTransaction($id);
    $latestVoidRequest = VoidRequest::latestByTransaction($id);
    if (!$transaction) {
      header('Location: /index.php?r=transactions/list');
      exit;
    }

    $user = Auth::user();
    include __DIR__ . '/../views/transactions/view.php';
  }

  public function void() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'accountant']);

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=transactions/list');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        die('CSRF invalid');
      }
      Transaction::void($id);
      header('Location: /index.php?r=transactions/list');
      exit;
    }

    $transaction = Transaction::find($id);
    include __DIR__ . '/../views/transactions/void.php';
  }

  public function requestVoid() {
    Auth::requireLogin();
    $user = Auth::user();

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=transactions/list');
      exit;
    }

    $transaction = Transaction::find($id);
    if (!$transaction || $transaction['status'] === 'void') {
      header('Location: /index.php?r=transactions/list');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        die('CSRF invalid');
      }
      $reason = $_POST['reason'] ?? null;
      VoidRequest::create($id, $user['id'], $reason);
      header('Location: /index.php?r=transactions/view&id=' . $id);
      exit;
    }

    header('Location: /index.php?r=transactions/view&id=' . $id);
  }

  public function export() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    // 获取筛选条件（与列表页相同）
    $filters = [
      'type' => $_GET['type'] ?? null,
      'from' => $_GET['from'] ?? null,
      'to' => $_GET['to'] ?? null,
      'category_id' => $_GET['category_id'] ?? null,
      'payment_method_id' => $_GET['payment_method_id'] ?? null,
      'created_by' => $_GET['created_by'] ?? null,
      'search' => $_GET['search'] ?? null,
    ];
    // 导出时不限制数量
    $items = Transaction::list($filters);

    $lang = I18n::current();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="transactions_' . date('Ymd_His') . '.csv"');
    
    // 输出 BOM 以支持 Excel 正确显示中文
    echo "\xEF\xBB\xBF";
    
    $out = fopen('php://output', 'w');
    
    // CSV 表头（与导入格式一致）
    $headers = [
      $lang === 'zh' ? '类型' : 'Loại',
      $lang === 'zh' ? '金额' : 'Số tiền',
      $lang === 'zh' ? '货币' : 'Tiền tệ',
      $lang === 'zh' ? '分类' : 'Danh mục',
      $lang === 'zh' ? '支付方式' : 'Phương thức thanh toán',
      $lang === 'zh' ? '供应商' : 'Nhà cung cấp',
      $lang === 'zh' ? '付款人' : 'Người trả',
      $lang === 'zh' ? '发生时间' : 'Thời gian',
      $lang === 'zh' ? '备注' : 'Ghi chú'
    ];
    fputcsv($out, $headers);

    // 输出数据
    foreach ($items as $row) {
      fputcsv($out, [
        $row['type'] === 'income' ? ($lang === 'zh' ? '收入' : 'Thu nhập') : ($lang === 'zh' ? '支出' : 'Chi tiêu'),
        $row['amount'],
        $row['currency'],
        $lang === 'zh' ? ($row['category_name_zh'] ?? '') : ($row['category_name_vi'] ?? ''),
        $lang === 'zh' ? ($row['payment_name_zh'] ?? '') : ($row['payment_name_vi'] ?? ''),
        $row['vendor_name'] ?? '',
        $row['payer'] ?? '',
        $row['occurred_at'],
        $row['note'] ?? ''
      ]);
    }
    fclose($out);
    exit;
  }

  public function import() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['transaction_csv'])) {
      header('Location: /index.php?r=transactions/list&import=0&failed=1');
      exit;
    }

    if (!Csrf::check($_POST['_csrf'] ?? '')) {
      header('Location: /index.php?r=transactions/list&import=0&failed=1');
      exit;
    }

    if ($_FILES['transaction_csv']['error'] !== UPLOAD_ERR_OK) {
      header('Location: /index.php?r=transactions/list&import=0&failed=1');
      exit;
    }

    $file = $_FILES['transaction_csv']['tmp_name'];
    $handle = fopen($file, 'r');
    if (!$handle) {
      header('Location: /index.php?r=transactions/list&import=0&failed=1');
      exit;
    }

    // 跳过表头
    $header = fgetcsv($handle);
    if (!$header) {
      fclose($handle);
      header('Location: /index.php?r=transactions/list&import=0&failed=1');
      exit;
    }

    $lang = I18n::current();
    $success = 0;
    $failed = 0;
    $errors = [];

    // 获取所有分类、支付方式、供应商用于匹配
    $categories = Category::all(['is_active' => 1]);
    $paymentMethods = PaymentMethod::all(['is_active' => 1]);
    $vendors = Vendor::all(['is_active' => 1]);

    // 创建映射表（同时支持中越双语）
    $categoryMap = [];
    foreach ($categories as $cat) {
      $nameZh = strtolower(trim($cat['name_zh']));
      $nameVi = strtolower(trim($cat['name_vi']));
      $categoryMap[$nameZh] = $cat['id'];
      $categoryMap[$nameVi] = $cat['id'];
    }

    $paymentMap = [];
    foreach ($paymentMethods as $pm) {
      $nameZh = strtolower(trim($pm['name_zh']));
      $nameVi = strtolower(trim($pm['name_vi']));
      $paymentMap[$nameZh] = $pm['id'];
      $paymentMap[$nameVi] = $pm['id'];
    }

    $vendorMap = [];
    foreach ($vendors as $v) {
      $vendorMap[strtolower(trim($v['name']))] = $v['id'];
    }

    while (($row = fgetcsv($handle)) !== false) {
      if (count($row) < 6) {
        $failed++;
        continue;
      }

      // CSV 格式：类型,金额,货币,分类,支付方式,供应商(可选),付款人(可选),发生时间,备注
      [$type, $amount, $currency, $categoryName, $paymentName, $vendorName, $payer, $occurredAt, $note] = array_pad($row, 9, null);

      // 验证必填字段
      if (empty($type) || empty($amount) || empty($categoryName) || empty($paymentName)) {
        $failed++;
        continue;
      }

      // 验证类型
      $type = strtolower(trim($type));
      if (!in_array($type, ['income', 'expense', '收入', '支出', 'thu nhập', 'chi tiêu'])) {
        $failed++;
        continue;
      }
      // 转换中文/越南语类型为英文
      if (in_array($type, ['收入', 'thu nhập'])) {
        $type = 'income';
      } elseif (in_array($type, ['支出', 'chi tiêu'])) {
        $type = 'expense';
      }

      // 匹配分类（支持中越双语）
      $categoryKey = strtolower(trim($categoryName));
      $categoryId = $categoryMap[$categoryKey] ?? null;
      if (!$categoryId) {
        $failed++;
        continue;
      }

      // 匹配支付方式（支持中越双语）
      $paymentKey = strtolower(trim($paymentName));
      $paymentMethodId = $paymentMap[$paymentKey] ?? null;
      if (!$paymentMethodId) {
        $failed++;
        continue;
      }

      // 匹配供应商（可选）
      $vendorId = null;
      if (!empty($vendorName)) {
        $vendorId = $vendorMap[strtolower(trim($vendorName))] ?? null;
      }

      // 解析金额
      $amount = str_replace([',', '.'], '', $amount);
      if (!is_numeric($amount) || $amount <= 0) {
        $failed++;
        continue;
      }

      // 解析时间
      if (empty($occurredAt)) {
        $occurredAt = date('Y-m-d H:i:s');
      } else {
        // 尝试解析各种日期格式
        $timestamp = strtotime($occurredAt);
        if ($timestamp === false) {
          $occurredAt = date('Y-m-d H:i:s');
        } else {
          $occurredAt = date('Y-m-d H:i:s', $timestamp);
        }
      }

      $data = [
        'type' => $type,
        'amount' => $amount,
        'currency' => $currency ?: 'VND',
        'category_id' => $categoryId,
        'payment_method_id' => $paymentMethodId,
        'vendor_id' => $vendorId,
        'payer' => $payer ?: null,
        'occurred_at' => $occurredAt,
        'note' => $note ?: null,
        'created_by' => Auth::user()['id'],
        'status' => 'approved'
      ];

      $txId = Transaction::create($data);
      if ($txId) {
        $success++;
      } else {
        $failed++;
      }
    }
    fclose($handle);
    header('Location: /index.php?r=transactions/list&import=1&success=' . $success . '&failed=' . $failed);
    exit;
  }
}
