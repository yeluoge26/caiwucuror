<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../models/Shift.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../core/DB.php';

class ShiftController {
  public function list() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    // 分页参数
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 50;
    $offset = ($page - 1) * $perPage;

    $filters = [
      'shift_date' => $_GET['shift_date'] ?? null,
      'from_date' => $_GET['from_date'] ?? null,
      'to_date' => $_GET['to_date'] ?? null,
      'shift_type' => $_GET['shift_type'] ?? null,
      'employee_id' => $_GET['employee_id'] ?? null,
      'is_confirmed' => isset($_GET['is_confirmed']) ? (int)$_GET['is_confirmed'] : null,
      'limit' => $perPage,
      'offset' => $offset
    ];

    $items = Shift::list($filters);
    $total = Shift::count($filters);
    $totalPages = ceil($total / $perPage);

    // 获取当前用户信息，判断是否是老板
    $user = Auth::user();
    $isOwner = ($user['role_key'] ?? '') === 'owner';

    $employees = Employee::active();
    // 获取老板和店长作为负责人选项
    $ownerRole = DB::conn()->query("SELECT id FROM roles WHERE `key` = 'owner' LIMIT 1")->fetch();
    $managerRole = DB::conn()->query("SELECT id FROM roles WHERE `key` = 'manager' LIMIT 1")->fetch();
    $managerRoleIds = [];
    if ($ownerRole) $managerRoleIds[] = $ownerRole['id'];
    if ($managerRole) $managerRoleIds[] = $managerRole['id'];
    $managers = [];
    if (!empty($managerRoleIds)) {
      foreach ($managerRoleIds as $roleId) {
        $managers = array_merge($managers, Employee::list(['role_id' => $roleId]));
      }
    }

    include __DIR__ . '/../views/shifts/list.php';
  }

  public function create() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager']);

    $employees = Employee::active();
    // 获取老板和店长作为负责人选项
    $ownerRole = DB::conn()->query("SELECT id FROM roles WHERE `key` = 'owner' LIMIT 1")->fetch();
    $managerRole = DB::conn()->query("SELECT id FROM roles WHERE `key` = 'manager' LIMIT 1")->fetch();
    $managerRoleIds = [];
    if ($ownerRole) $managerRoleIds[] = $ownerRole['id'];
    if ($managerRole) $managerRoleIds[] = $managerRole['id'];
    $managers = [];
    if (!empty($managerRoleIds)) {
      foreach ($managerRoleIds as $roleId) {
        $managers = array_merge($managers, Employee::list(['role_id' => $roleId]));
      }
    }
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $data = [
          'shift_date' => $_POST['shift_date'] ?? date('Y-m-d'),
          'shift_type' => $_POST['shift_type'] ?? 'morning',
          'employee_id' => $_POST['employee_id'] ?? null,
          'manager_id' => !empty($_POST['manager_id']) ? $_POST['manager_id'] : null,
          'note' => $_POST['note'] ?? null,
          'created_by' => Auth::user()['id'],
        ];

        $shiftId = Shift::create($data);
        if ($shiftId) {
          header('Location: /index.php?r=shifts/list');
          exit;
        } else {
          $error = __('shift.create_failed');
        }
      }
    }

    include __DIR__ . '/../views/shifts/create.php';
  }

  public function schedule() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager']);

    $date = $_GET['date'] ?? date('Y-m-d');
    $employees = Employee::active();
    // 获取老板和店长作为负责人选项
    $ownerRole = DB::conn()->query("SELECT id FROM roles WHERE `key` = 'owner' LIMIT 1")->fetch();
    $managerRole = DB::conn()->query("SELECT id FROM roles WHERE `key` = 'manager' LIMIT 1")->fetch();
    $managerRoleIds = [];
    if ($ownerRole) $managerRoleIds[] = $ownerRole['id'];
    if ($managerRole) $managerRoleIds[] = $managerRole['id'];
    $managers = [];
    if (!empty($managerRoleIds)) {
      foreach ($managerRoleIds as $roleId) {
        $managers = array_merge($managers, Employee::list(['role_id' => $roleId]));
      }
    }
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $shiftDate = $_POST['shift_date'] ?? $date;
        $shifts = $_POST['shifts'] ?? [];
        $success = 0;
        $failed = 0;

        foreach ($shifts as $shift) {
          if (empty($shift['employee_id']) || empty($shift['shift_type'])) {
            continue;
          }

          $data = [
            'shift_date' => $shiftDate,
            'shift_type' => $shift['shift_type'],
            'employee_id' => $shift['employee_id'],
            'manager_id' => !empty($shift['manager_id']) ? $shift['manager_id'] : null,
            'note' => $shift['note'] ?? null,
            'created_by' => Auth::user()['id'],
          ];

          // 检查是否已存在
          $existing = Shift::list([
            'shift_date' => $shiftDate,
            'employee_id' => $shift['employee_id'],
            'shift_type' => $shift['shift_type']
          ]);

          if (empty($existing)) {
            if (Shift::create($data)) {
              $success++;
            } else {
              $failed++;
            }
          } else {
            // 更新现有班次
            if (Shift::update($existing[0]['id'], [
              'manager_id' => $data['manager_id'],
              'note' => $data['note']
            ])) {
              $success++;
            } else {
              $failed++;
            }
          }
        }

        header('Location: /index.php?r=shifts/list&shift_date=' . urlencode($shiftDate) . '&success=' . $success . '&failed=' . $failed);
        exit;
      }
    }

    // 获取当天的班次
    $existingShifts = Shift::list(['shift_date' => $date]);

    include __DIR__ . '/../views/shifts/schedule.php';
  }

  public function confirm() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager']);

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=shifts/list');
      exit;
    }

    $shift = Shift::find($id);
    if (!$shift) {
      header('Location: /index.php?r=shifts/list');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        die('CSRF invalid');
      }

      $confirmed = isset($_POST['confirmed']) && $_POST['confirmed'] === '1';
      Shift::confirm($id, Auth::user()['id'], $confirmed);
      header('Location: /index.php?r=shifts/list');
      exit;
    }

    include __DIR__ . '/../views/shifts/confirm.php';
  }

  public function quickConfirm() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager']);

    $id = $_GET['id'] ?? null;
    $confirmed = isset($_GET['confirmed']) && $_GET['confirmed'] === '1';
    
    if ($id) {
      Shift::confirm($id, Auth::user()['id'], $confirmed);
    }
    
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/index.php?r=shifts/list'));
    exit;
  }

  public function edit() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager']);

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=shifts/list');
      exit;
    }

    $shift = Shift::find($id);
    if (!$shift) {
      header('Location: /index.php?r=shifts/list');
      exit;
    }

    $employees = Employee::active();
    // 获取老板和店长作为负责人选项
    $ownerRole = DB::conn()->query("SELECT id FROM roles WHERE `key` = 'owner' LIMIT 1")->fetch();
    $managerRole = DB::conn()->query("SELECT id FROM roles WHERE `key` = 'manager' LIMIT 1")->fetch();
    $managerRoleIds = [];
    if ($ownerRole) $managerRoleIds[] = $ownerRole['id'];
    if ($managerRole) $managerRoleIds[] = $managerRole['id'];
    $managers = [];
    if (!empty($managerRoleIds)) {
      foreach ($managerRoleIds as $roleId) {
        $managers = array_merge($managers, Employee::list(['role_id' => $roleId]));
      }
    }
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $data = [
          'shift_date' => $_POST['shift_date'] ?? $shift['shift_date'],
          'shift_type' => $_POST['shift_type'] ?? $shift['shift_type'],
          'employee_id' => $_POST['employee_id'] ?? $shift['employee_id'],
          'manager_id' => !empty($_POST['manager_id']) ? $_POST['manager_id'] : null, // 空值设置为null，取消负责人
          'note' => $_POST['note'] ?? null,
        ];

        if (Shift::update($id, $data)) {
          header('Location: /index.php?r=shifts/list');
          exit;
        } else {
          $error = __('shift.update_failed');
        }
      }
    }

    include __DIR__ . '/../views/shifts/edit.php';
  }

  public function removeManager() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager']);

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=shifts/list');
      exit;
    }

    // 快速取消负责人
    Shift::update($id, ['manager_id' => null]);
    
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/index.php?r=shifts/list'));
    exit;
  }
}

