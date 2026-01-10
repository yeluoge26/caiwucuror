<?php
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/ApiGuard.php';
require_once __DIR__ . '/../../models/Employee.php';
require_once __DIR__ . '/../../models/Shift.php';

class EmployeeApiController {
  public function list() {
    ApiGuard::requireLogin();
    
    $filters = [
      'status' => $_GET['status'] ?? null,
      'role_id' => $_GET['role_id'] ?? null,
      'search' => $_GET['search'] ?? null,
      'page' => max(1, (int)($_GET['page'] ?? 1)),
      'limit' => min(100, max(1, (int)($_GET['limit'] ?? 20)))
    ];
    
    $offset = ($filters['page'] - 1) * $filters['limit'];
    $filters['offset'] = $offset;
    
    $items = Employee::list($filters);
    $total = Employee::count($filters);
    $totalPages = ceil($total / $filters['limit']);
    
    Response::success([
      'items' => $items,
      'pagination' => [
        'page' => $filters['page'],
        'limit' => $filters['limit'],
        'total' => $total,
        'totalPages' => $totalPages
      ]
    ]);
  }

  public function view() {
    ApiGuard::requireLogin();
    $id = $_GET['id'] ?? null;
    if (!$id) {
      Response::error('ID is required', 400);
    }
    
    $employee = Employee::find($id);
    if (!$employee) {
      Response::error('Employee not found', 404);
    }
    
    Response::success($employee);
  }

  public function create() {
    ApiGuard::requireRole(['owner', 'manager']);
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    $payload = [
      'name' => $data['name'] ?? '',
      'phone' => $data['phone'] ?? null,
      'email' => $data['email'] ?? null,
      'role_id' => $data['role_id'] ?? null,
      'status' => $data['status'] ?? 'active',
      'employment_type' => $data['employment_type'] ?? 'full_time',
      'hire_date' => $data['hire_date'] ?? null,
      'note' => $data['note'] ?? null,
      'created_by' => Auth::user()['id']
    ];
    
    if (empty($payload['name'])) {
      Response::error('Name is required', 400);
    }
    
    $id = Employee::create($payload);
    if ($id) {
      Response::success(['id' => $id], 'Employee created successfully', 201);
    } else {
      Response::error('Failed to create employee', 500);
    }
  }

  public function update() {
    ApiGuard::requireRole(['owner', 'manager']);
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
      Response::error('ID is required', 400);
    }
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    $payload = [];
    $allowedFields = ['name', 'phone', 'email', 'role_id', 'status', 'employment_type', 'hire_date', 'resign_date', 'note'];
    foreach ($allowedFields as $field) {
      if (isset($data[$field])) {
        $payload[$field] = $data[$field];
      }
    }
    
    if (empty($payload)) {
      Response::error('No fields to update', 400);
    }
    
    if (Employee::update($id, $payload)) {
      Response::success(['id' => $id], 'Employee updated successfully');
    } else {
      Response::error('Failed to update employee', 500);
    }
  }

  public function delete() {
    ApiGuard::requireRole(['owner']);
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
      Response::error('ID is required', 400);
    }
    
    if (Employee::delete($id)) {
      Response::success(['id' => $id], 'Employee deleted successfully');
    } else {
      Response::error('Failed to delete employee', 500);
    }
  }

  public function today() {
    ApiGuard::requireLogin();
    
    $today = date('Y-m-d');
    $employees = Employee::getTodayOnDuty($today);
    
    Response::success($employees);
  }
}
