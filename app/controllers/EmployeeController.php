<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../core/DB.php';

class EmployeeController {
  public function list() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    // 分页参数
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 20;
    $offset = ($page - 1) * $perPage;

    $filters = [
      'status' => $_GET['status'] ?? null,
      'role_id' => $_GET['role_id'] ?? null,
      'search' => $_GET['search'] ?? null,
      'limit' => $perPage,
      'offset' => $offset
    ];

    $items = Employee::list($filters);
    $total = Employee::count($filters);
    $totalPages = ceil($total / $perPage);

    $roles = DB::conn()->query("SELECT id, `key`, name_zh, name_vi FROM roles ORDER BY id ASC")->fetchAll();

    include __DIR__ . '/../views/employees/list.php';
  }

  public function create() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager']);

    $roles = DB::conn()->query("SELECT id, `key`, name_zh, name_vi FROM roles ORDER BY id ASC")->fetchAll();
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $data = [
          'name' => $_POST['name'] ?? '',
          'role_id' => $_POST['role_id'] ?? null,
          'phone' => $_POST['phone'] ?? null,
          'email' => $_POST['email'] ?? null,
          'address' => $_POST['address'] ?? null,
          'status' => $_POST['status'] ?? 'active',
          'employment_type' => $_POST['employment_type'] ?? 'full_time',
          'hire_date' => $_POST['hire_date'] ?? null,
          'resign_date' => $_POST['resign_date'] ?? null,
          'note' => $_POST['note'] ?? null,
          'created_by' => Auth::user()['id'],
        ];

        $employeeId = Employee::create($data);
        if ($employeeId) {
          header('Location: /index.php?r=employees/list');
          exit;
        } else {
          $error = __('employee.create_failed');
        }
      }
    }

    include __DIR__ . '/../views/employees/create.php';
  }

  public function edit() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager']);

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=employees/list');
      exit;
    }

    $employee = Employee::find($id);
    if (!$employee) {
      header('Location: /index.php?r=employees/list');
      exit;
    }

    $roles = DB::conn()->query("SELECT id, `key`, name_zh, name_vi FROM roles ORDER BY id ASC")->fetchAll();
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $data = [
          'name' => $_POST['name'] ?? '',
          'role_id' => $_POST['role_id'] ?? null,
          'phone' => $_POST['phone'] ?? null,
          'email' => $_POST['email'] ?? null,
          'address' => $_POST['address'] ?? null,
          'status' => $_POST['status'] ?? 'active',
          'employment_type' => $_POST['employment_type'] ?? 'full_time',
          'hire_date' => $_POST['hire_date'] ?? null,
          'resign_date' => $_POST['resign_date'] ?? null,
          'note' => $_POST['note'] ?? null,
        ];

        if (Employee::update($id, $data)) {
          header('Location: /index.php?r=employees/list');
          exit;
        } else {
          $error = __('employee.update_failed');
        }
      }
    }

    include __DIR__ . '/../views/employees/edit.php';
  }

  public function view() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=employees/list');
      exit;
    }

    $employee = Employee::find($id);
    if (!$employee) {
      header('Location: /index.php?r=employees/list');
      exit;
    }

    include __DIR__ . '/../views/employees/view.php';
  }

  public function today() {
    Auth::requireLogin();
    
    require_once __DIR__ . '/../models/Shift.php';
    
    $today = date('Y-m-d');
    
    // 获取今日所有班次
    $todayShifts = Shift::list([
      'shift_date' => $today
    ]);
    
    // 按员工分组，统计每个员工的班次信息
    $employeesOnDuty = [];
    foreach ($todayShifts as $shift) {
      $employeeId = $shift['employee_id'];
      if (!isset($employeesOnDuty[$employeeId])) {
        $employee = Employee::find($employeeId);
        if ($employee) {
          $employeesOnDuty[$employeeId] = [
            'employee' => $employee,
            'shifts' => [],
            'confirmed_count' => 0,
            'total_count' => 0
          ];
        }
      }
      
      if (isset($employeesOnDuty[$employeeId])) {
        $employeesOnDuty[$employeeId]['shifts'][] = $shift;
        $employeesOnDuty[$employeeId]['total_count']++;
        if ($shift['is_confirmed']) {
          $employeesOnDuty[$employeeId]['confirmed_count']++;
        }
      }
    }
    
    // 转换为数组并排序
    $employeesOnDuty = array_values($employeesOnDuty);
    
    include __DIR__ . '/../views/employees/today.php';
  }
}

