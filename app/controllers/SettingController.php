<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/PaymentMethod.php';
require_once __DIR__ . '/../models/Vendor.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/DB.php';

class SettingController {
  public function categories() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $categories = Category::all();
    $error = null;
    $success = null;

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
    $success = null;

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
    $success = null;

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
        
        header('Location: /index.php?r=settings/vendors');
        exit;
      }
    }

    include __DIR__ . '/../views/settings/vendors.php';
  }

  public function users() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $roles = DB::conn()->query("SELECT id, `key`, name_zh, name_vi FROM roles ORDER BY id ASC")->fetchAll();
    $users = User::all();
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'create') {
          User::create([
            'username' => $_POST['username'],
            'password' => $_POST['password'],
            'display_name' => $_POST['display_name'],
            'role_id' => $_POST['role_id'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0
          ]);
        } elseif ($action === 'update') {
          $data = [
            'display_name' => $_POST['display_name'],
            'role_id' => $_POST['role_id'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0
          ];
          if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
          }
          User::update($_POST['id'], $data);
        }
        header('Location: /index.php?r=settings/users');
        exit;
      }
    }

    include __DIR__ . '/../views/settings/users.php';
  }
}
