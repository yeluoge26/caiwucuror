<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/PaymentMethod.php';
require_once __DIR__ . '/../models/Vendor.php';
require_once __DIR__ . '/../models/Attachment.php';

class TransactionController {
  public function list() {
    Auth::requireLogin();

    $user = Auth::user();
    $page = max(1, intval($_GET['page'] ?? 1));
    $perPage = 20;

    $filters = [
      'type' => $_GET['type'] ?? null,
      'from' => $_GET['from'] ?? null,
      'to' => $_GET['to'] ?? null,
      'category_id' => $_GET['category_id'] ?? null,
      'payment_method_id' => $_GET['payment_method_id'] ?? null,
      'vendor_id' => $_GET['vendor_id'] ?? null,
      'created_by' => $_GET['created_by'] ?? null,
      'search' => $_GET['search'] ?? null,
      'status' => $_GET['status'] ?? null,
      'page' => $page,
      'per_page' => $perPage
    ];

    // owner可以看到待审核的交易
    if ($user['role_key'] === 'owner' && isset($_GET['show_pending'])) {
      $filters['show_pending'] = true;
      unset($filters['status']);
    }

    $items = Transaction::list($filters);
    $total = Transaction::count($filters);
    $totalPages = ceil($total / $perPage);
    
    $categories = Category::all(['is_active' => 1]);
    $paymentMethods = PaymentMethod::all(['is_active' => 1]);
    $vendors = Vendor::all(['is_active' => 1]);

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
        $amount = floatval($_POST['amount'] ?? 0);
        
        // 金额验证：必须为正数
        if ($amount <= 0) {
          $error = __('tx.amount_invalid');
        } else {
          $data = [
            'type' => $_POST['type'] ?? '',
            'amount' => $amount,
            'currency' => $_POST['currency'] ?? 'VND',
            'category_id' => $_POST['category_id'] ?? null,
            'payment_method_id' => $_POST['payment_method_id'] ?? null,
            'vendor_id' => !empty($_POST['vendor_id']) ? $_POST['vendor_id'] : null,
            'occurred_at' => $_POST['occurred_at'] ?? date('Y-m-d H:i:s'),
            'note' => $_POST['note'] ?? null,
            'created_by' => Auth::user()['id'],
            'status' => 'pending' // 默认状态为待审核
          ];

          $transactionId = Transaction::create($data);
          if ($transactionId) {
            // 处理图片上传
            self::handleFileUploads($transactionId, Auth::user()['id']);
            header('Location: /index.php?r=transactions/list');
            exit;
          } else {
            $error = __('tx.create_failed');
          }
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
    if (!$transaction) {
      header('Location: /index.php?r=transactions/list');
      exit;
    }

    $attachments = Attachment::findByTransaction($id);
    $user = Auth::user();
    include __DIR__ . '/../views/transactions/view.php';
  }

  public function edit() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=transactions/list');
      exit;
    }

    $transaction = Transaction::find($id);
    if (!$transaction) {
      header('Location: /index.php?r=transactions/list');
      exit;
    }

    // 已审核或已作废的交易不允许编辑
    if ($transaction['status'] === 'void' || $transaction['status'] === 'approved') {
      header('Location: /index.php?r=transactions/view&id=' . $id);
      exit;
    }

    $categories = Category::all(['is_active' => 1]);
    $paymentMethods = PaymentMethod::all(['is_active' => 1]);
    $vendors = Vendor::all(['is_active' => 1]);
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $amount = floatval($_POST['amount'] ?? 0);
        
        // 金额验证：必须为正数
        if ($amount <= 0) {
          $error = __('tx.amount_invalid');
        } else {
          $data = [
            'type' => $_POST['type'] ?? '',
            'amount' => $amount,
            'currency' => $_POST['currency'] ?? 'VND',
            'category_id' => $_POST['category_id'] ?? null,
            'payment_method_id' => $_POST['payment_method_id'] ?? null,
            'vendor_id' => !empty($_POST['vendor_id']) ? $_POST['vendor_id'] : null,
            'occurred_at' => $_POST['occurred_at'] ?? date('Y-m-d H:i:s'),
            'note' => $_POST['note'] ?? null,
          ];

          if (Transaction::update($id, $data)) {
            // 处理图片上传
            self::handleFileUploads($id, Auth::user()['id']);
            header('Location: /index.php?r=transactions/view&id=' . $id);
            exit;
          } else {
            $error = __('tx.update_failed');
          }
        }
      }
    }

    include __DIR__ . '/../views/transactions/edit.php';
  }

  public function approve() {
    Auth::requireLogin();
    // 只有owner可以审核
    Auth::requireRole(['owner']);

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=transactions/list');
      exit;
    }

    $transaction = Transaction::find($id);
    if (!$transaction) {
      header('Location: /index.php?r=transactions/list');
      exit;
    }

    // 只能审核待审核状态的交易
    if ($transaction['status'] !== 'pending') {
      header('Location: /index.php?r=transactions/view&id=' . $id);
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        die('CSRF invalid');
      }
      Transaction::update($id, ['status' => 'approved']);
      header('Location: /index.php?r=transactions/view&id=' . $id);
      exit;
    }

    include __DIR__ . '/../views/transactions/approve.php';
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

  private static function handleFileUploads($transactionId, $userId) {
    $uploadDir = __DIR__ . '/../../public/uploads/';
    
    // 确保上传目录存在
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0755, true);
    }

    // 处理多个文件上传
    if (isset($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) {
      $fileCount = count($_FILES['attachments']['name']);
      
      for ($i = 0; $i < $fileCount; $i++) {
        if ($_FILES['attachments']['error'][$i] === UPLOAD_ERR_OK) {
          $tmpName = $_FILES['attachments']['tmp_name'][$i];
          $originalName = $_FILES['attachments']['name'][$i];
          $fileSize = $_FILES['attachments']['size'][$i];
          $fileType = $_FILES['attachments']['type'][$i];
          
          // 验证文件类型（只允许图片）
          $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
          if (!in_array($fileType, $allowedTypes)) {
            continue;
          }
          
          // 验证文件大小（最大5MB）
          if ($fileSize > 5 * 1024 * 1024) {
            continue;
          }
          
          // 生成唯一文件名
          $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
          $fileName = date('YmdHis') . '_' . uniqid() . '.' . $ext;
          $filePath = 'uploads/' . $fileName;
          $fullPath = $uploadDir . $fileName;
          
          // 移动文件
          if (move_uploaded_file($tmpName, $fullPath)) {
            // 保存到数据库
            Attachment::create([
              'transaction_id' => $transactionId,
              'file_path' => $filePath,
              'file_type' => $fileType,
              'uploaded_by' => $userId
            ]);
          }
        }
      }
    }
  }

  public function deleteAttachment() {
    Auth::requireLogin();
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
      http_response_code(400);
      die('Invalid attachment ID');
    }

    $attachment = Attachment::find($id);
    if (!$attachment) {
      http_response_code(404);
      die('Attachment not found');
    }

    // 检查权限：只能删除自己上传的附件，或owner/accountant可以删除
    $user = Auth::user();
    if ($attachment['uploaded_by'] != $user['id'] && 
        !in_array($user['role_key'], ['owner', 'accountant'])) {
      http_response_code(403);
      die('Permission denied');
    }

    if (Attachment::delete($id)) {
      header('Content-Type: application/json');
      echo json_encode(['success' => true]);
    } else {
      http_response_code(500);
      echo json_encode(['success' => false, 'error' => 'Delete failed']);
    }
    exit;
  }
}

