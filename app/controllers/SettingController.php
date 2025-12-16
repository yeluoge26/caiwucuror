<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/PaymentMethod.php';
require_once __DIR__ . '/../models/Vendor.php';
require_once __DIR__ . '/../models/User.php';

class SettingController {
  public function categories() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $categories = Category::all();
    $error = null;
    $success = $_SESSION['success_message'] ?? null;
    unset($_SESSION['success_message']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create') {
          Category::create([
            'type' => $_POST['type'],
            'name_zh' => $_POST['name_zh'],
            'name_vi' => $_POST['name_vi'],
            'parent_id' => !empty($_POST['parent_id']) ? $_POST['parent_id'] : null,
            'is_active' => 1
          ]);
          $success = __('setting.category_created');
        } elseif ($action === 'update') {
          Category::update($_POST['id'], [
            'type' => $_POST['type'],
            'name_zh' => $_POST['name_zh'],
            'name_vi' => $_POST['name_vi'],
            'parent_id' => !empty($_POST['parent_id']) ? $_POST['parent_id'] : null,
            'is_active' => $_POST['is_active'] ?? 1
          ]);
          $success = __('setting.category_updated');
        } elseif ($action === 'delete') {
          Category::delete($_POST['id']);
          $success = __('setting.category_deleted');
        }
        
        // 使用session存储成功消息
        $_SESSION['success_message'] = $success ?? __('setting.category_updated');
        header('Location: /index.php?r=settings/categories');
        exit;
      }
    }

    include __DIR__ . '/../views/settings/categories.php';
  }

  public function paymentMethods() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $paymentMethods = PaymentMethod::all();
    $error = null;
    $success = $_SESSION['success_message'] ?? null;
    unset($_SESSION['success_message']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create') {
          PaymentMethod::create([
            'name_zh' => $_POST['name_zh'],
            'name_vi' => $_POST['name_vi'],
            'is_active' => 1
          ]);
          $success = __('setting.payment_method_created');
        } elseif ($action === 'update') {
          PaymentMethod::update($_POST['id'], [
            'name_zh' => $_POST['name_zh'],
            'name_vi' => $_POST['name_vi'],
            'is_active' => $_POST['is_active'] ?? 1
          ]);
          $success = __('setting.payment_method_updated');
        } elseif ($action === 'delete') {
          PaymentMethod::delete($_POST['id']);
          $success = __('setting.payment_method_deleted');
        }
        
        $_SESSION['success_message'] = $success ?? __('setting.payment_method_updated');
        header('Location: /index.php?r=settings/paymentMethods');
        exit;
      }
    }

    include __DIR__ . '/../views/settings/payment_methods.php';
  }

  public function vendors() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $vendors = Vendor::all();
    $error = null;
    $success = $_SESSION['success_message'] ?? null;
    unset($_SESSION['success_message']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create') {
          Vendor::create([
            'name' => $_POST['name'],
            'phone' => $_POST['phone'] ?? null,
            'note' => $_POST['note'] ?? null,
            'is_active' => 1
          ]);
          $success = __('setting.vendor_created');
        } elseif ($action === 'update') {
          Vendor::update($_POST['id'], [
            'name' => $_POST['name'],
            'phone' => $_POST['phone'] ?? null,
            'note' => $_POST['note'] ?? null,
            'is_active' => $_POST['is_active'] ?? 1
          ]);
          $success = __('setting.vendor_updated');
        } elseif ($action === 'delete') {
          Vendor::delete($_POST['id']);
          $success = __('setting.vendor_deleted');
        }
        
        $_SESSION['success_message'] = $success ?? __('setting.vendor_updated');
        header('Location: /index.php?r=settings/vendors');
        exit;
      }
    }

    include __DIR__ . '/../views/settings/vendors.php';
  }

  public function addVendorQuick() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant', 'staff']); // 员工也可以快速添加
    
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      echo json_encode(['success' => false, 'error' => 'Invalid request']);
      exit;
    }
    
    if (!Csrf::check($_POST['_csrf'] ?? '')) {
      echo json_encode(['success' => false, 'error' => __('csrf.invalid')]);
      exit;
    }
    
    $name = trim($_POST['name'] ?? '');
    if (empty($name)) {
      echo json_encode(['success' => false, 'error' => __('field.vendor') . ' ' . __('field.required')]);
      exit;
    }
    
    // 检查是否已存在
    $existing = Vendor::all(['is_active' => 1]);
    foreach ($existing as $v) {
      if (strtolower(trim($v['name'])) === strtolower($name)) {
        echo json_encode(['success' => true, 'id' => $v['id'], 'message' => __('vendor.already_exists')]);
        exit;
      }
    }
    
    $vendorId = Vendor::create([
      'name' => $name,
      'phone' => $_POST['phone'] ?? null,
      'note' => $_POST['note'] ?? null,
      'is_active' => 1
    ]);
    
    if ($vendorId) {
      $newVendor = Vendor::find($vendorId);
      echo json_encode(['success' => true, 'id' => $newVendor['id'], 'name' => $newVendor['name']]);
    } else {
      echo json_encode(['success' => false, 'error' => __('vendor.add_failed')]);
    }
    exit;
  }
}

