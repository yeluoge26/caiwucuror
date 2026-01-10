<?php
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/ApiGuard.php';
require_once __DIR__ . '/../../models/Task.php';

class TaskApiController {
  public function list() {
    ApiGuard::requireLogin();
    
    $filters = [
      'status' => $_GET['status'] ?? null,
      'store' => $_GET['store'] ?? null,
      'assigned_to_user_id' => $_GET['assigned_to_user_id'] ?? null,
      'assigned_to_role_id' => $_GET['assigned_to_role_id'] ?? null,
      'created_by' => $_GET['created_by'] ?? null,
      'search' => $_GET['search'] ?? null,
      'page' => max(1, (int)($_GET['page'] ?? 1)),
      'limit' => min(100, max(1, (int)($_GET['limit'] ?? 20)))
    ];
    
    $offset = ($filters['page'] - 1) * $filters['limit'];
    $filters['offset'] = $offset;
    
    $items = Task::list($filters);
    $total = Task::count($filters);
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
    
    $task = Task::find($id);
    if (!$task) {
      Response::error('Task not found', 404);
    }
    
    Response::success($task);
  }

  public function create() {
    ApiGuard::requireRole(['owner', 'manager']);
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    $payload = [
      'title' => $data['title'] ?? '',
      'description' => $data['description'] ?? null,
      'store' => $data['store'] ?? 'coffee',
      'priority' => $data['priority'] ?? 'normal',
      'status' => $data['status'] ?? 'pending',
      'assign_user_id' => $data['assign_user_id'] ?? null,
      'assign_role_id' => $data['assign_role_id'] ?? null,
      'due_date' => $data['due_date'] ?? null,
      'created_by' => Auth::user()['id']
    ];
    
    if (empty($payload['title'])) {
      Response::error('Title is required', 400);
    }
    
    $id = Task::create($payload);
    if ($id) {
      Response::success(['id' => $id], 'Task created successfully', 201);
    } else {
      Response::error('Failed to create task', 500);
    }
  }

  public function update() {
    ApiGuard::requireLogin();
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
      Response::error('ID is required', 400);
    }
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    $payload = [];
    $allowedFields = ['title', 'description', 'store', 'priority', 'status', 'assign_user_id', 'assign_role_id', 'due_date'];
    foreach ($allowedFields as $field) {
      if (isset($data[$field])) {
        $payload[$field] = $data[$field];
      }
    }
    
    if (empty($payload)) {
      Response::error('No fields to update', 400);
    }
    
    if (Task::update($id, $payload)) {
      Response::success(['id' => $id], 'Task updated successfully');
    } else {
      Response::error('Failed to update task', 500);
    }
  }

  public function approve() {
    ApiGuard::requireRole(['owner', 'manager']);
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
      Response::error('ID is required', 400);
    }
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $approved = ($data['approved'] ?? true) === true;
    $note = $data['note'] ?? null;
    
    if (Task::approve($id, Auth::user()['id'], $approved, $note)) {
      Response::success(['id' => $id, 'approved' => $approved], 'Task approved successfully');
    } else {
      Response::error('Failed to approve task', 500);
    }
  }
}
