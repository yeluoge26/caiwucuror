<?php
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/ApiGuard.php';
require_once __DIR__ . '/../../models/Shift.php';
require_once __DIR__ . '/../../models/Employee.php';

class ShiftApiController {
  public function list() {
    ApiGuard::requireLogin();
    
    $filters = [
      'shift_date' => $_GET['shift_date'] ?? null,
      'from_date' => $_GET['from_date'] ?? null,
      'to_date' => $_GET['to_date'] ?? null,
      'shift_type' => $_GET['shift_type'] ?? null,
      'employee_id' => $_GET['employee_id'] ?? null,
      'status' => $_GET['status'] ?? null,
      'is_confirmed' => isset($_GET['is_confirmed']) ? (int)$_GET['is_confirmed'] : null,
      'page' => max(1, (int)($_GET['page'] ?? 1)),
      'limit' => min(100, max(1, (int)($_GET['limit'] ?? 20)))
    ];
    
    $offset = ($filters['page'] - 1) * $filters['limit'];
    $filters['offset'] = $offset;
    
    $items = Shift::list($filters);
    $total = Shift::count($filters);
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
    
    $shift = Shift::find($id);
    if (!$shift) {
      Response::error('Shift not found', 404);
    }
    
    Response::success($shift);
  }

  public function create() {
    ApiGuard::requireRole(['owner', 'manager']);
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    $payload = [
      'shift_date' => $data['shift_date'] ?? date('Y-m-d'),
      'shift_type' => $data['shift_type'] ?? 'morning',
      'employee_id' => $data['employee_id'] ?? null,
      'manager_id' => $data['manager_id'] ?? null,
      'note' => $data['note'] ?? null,
      'created_by' => Auth::user()['id']
    ];
    
    if (empty($payload['employee_id'])) {
      Response::error('Employee ID is required', 400);
    }
    
    $id = Shift::create($payload);
    if ($id) {
      Response::success(['id' => $id], 'Shift created successfully', 201);
    } else {
      Response::error('Failed to create shift', 500);
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
    $allowedFields = ['shift_date', 'shift_type', 'employee_id', 'manager_id', 'note'];
    foreach ($allowedFields as $field) {
      if (isset($data[$field])) {
        $payload[$field] = $data[$field];
      }
    }
    
    if (empty($payload)) {
      Response::error('No fields to update', 400);
    }
    
    if (Shift::update($id, $payload)) {
      Response::success(['id' => $id], 'Shift updated successfully');
    } else {
      Response::error('Failed to update shift', 500);
    }
  }

  public function confirmShift() {
    ApiGuard::requireLogin();
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $shiftId = $data['shift_id'] ?? $_GET['shift_id'] ?? null;
    $status = $data['status'] ?? 'confirmed';
    
    if (!$shiftId) {
      Response::error('Shift ID is required', 400);
    }
    
    $shift = Shift::find($shiftId);
    if (!$shift) {
      Response::error('Shift not found', 404);
    }
    
    // 检查权限：只有老板可以修改已提交的状态
    $user = Auth::user();
    $isOwner = ($user['role_key'] ?? '') === 'owner';
    $confirmedBy = $shift['confirmed_by'] ?? null;
    $currentStatus = $shift['status'] ?? 'pending';
    $isSubmitted = ($currentStatus !== 'pending' && !empty($confirmedBy));
    
    if ($isSubmitted && !$isOwner) {
      Response::error('Cannot edit submitted shift', 403);
    }
    
    if (Shift::updateStatus($shiftId, $status, Auth::user()['id'])) {
      Response::success(['id' => $shiftId, 'status' => $status], 'Shift status updated successfully');
    } else {
      Response::error('Failed to update shift status', 500);
    }
  }

  public function weeklySchedule() {
    ApiGuard::requireRole(['owner', 'manager']);
    
    $weekStart = $_GET['week'] ?? null;
    if ($weekStart) {
      $weekStart = date('Y-m-d', strtotime($weekStart . ' monday this week'));
    } else {
      $today = date('Y-m-d');
      $weekday = (int)date('w', strtotime($today));
      $daysToMonday = ($weekday == 0) ? 6 : ($weekday - 1);
      $weekStart = date('Y-m-d', strtotime($today . " -{$daysToMonday} days"));
    }
    
    $employees = Employee::active();
    $schedule = Shift::getWeeklySchedule($weekStart);
    $isPublished = Shift::isWeekPublished($weekStart);
    
    // 生成周一到周日的日期数组
    $weekDays = [];
    for ($i = 0; $i < 7; $i++) {
      $date = date('Y-m-d', strtotime($weekStart . " +{$i} days"));
      $weekDays[] = [
        'date' => $date,
        'day_name' => date('D', strtotime($date)),
        'day_num' => date('d', strtotime($date))
      ];
    }
    
    Response::success([
      'week_start' => $weekStart,
      'is_published' => $isPublished,
      'employees' => $employees,
      'schedule' => $schedule,
      'week_days' => $weekDays
    ]);
  }

  public function saveWeeklySchedule() {
    ApiGuard::requireRole(['owner', 'manager']);
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $weekStart = $data['week_start'] ?? null;
    $scheduleData = $data['schedule_data'] ?? [];
    $action = $data['action'] ?? 'save_draft'; // save_draft, publish
    
    if (!$weekStart) {
      Response::error('Week start date is required', 400);
    }
    
    $isPublished = ($action === 'publish');
    
    // 检查是否已发布
    if ($isPublished && Shift::isWeekPublished($weekStart)) {
      Response::error('This week schedule is already published', 400);
    }
    
    if (Shift::saveWeeklySchedule($weekStart, $scheduleData, Auth::user()['id'], $isPublished)) {
      Response::success([
        'week_start' => $weekStart,
        'is_published' => $isPublished
      ], 'Weekly schedule saved successfully');
    } else {
      Response::error('Failed to save weekly schedule', 500);
    }
  }

  public function copyLastWeek() {
    ApiGuard::requireRole(['owner', 'manager']);
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $weekStart = $data['week_start'] ?? null;
    
    if (!$weekStart) {
      Response::error('Week start date is required', 400);
    }
    
    $lastWeekStart = date('Y-m-d', strtotime($weekStart . ' -7 days'));
    $lastWeekSchedule = Shift::getWeeklySchedule($lastWeekStart);
    
    // 转换为保存格式
    $scheduleData = [];
    foreach ($lastWeekSchedule as $empSchedule) {
      $empId = $empSchedule['employee_id'];
      foreach ($empSchedule['days'] ?? [] as $date => $shifts) {
        // 将日期调整为本周
        $dayOfWeek = (int)date('w', strtotime($date));
        $daysToMonday = ($dayOfWeek == 0) ? 6 : ($dayOfWeek - 1);
        $newDate = date('Y-m-d', strtotime($weekStart . " +{$daysToMonday} days"));
        
        if (!isset($scheduleData[$empId])) {
          $scheduleData[$empId] = [];
        }
        if (!isset($scheduleData[$empId][$newDate])) {
          $scheduleData[$empId][$newDate] = [];
        }
        foreach ($shifts as $shift) {
          $scheduleData[$empId][$newDate][] = $shift['shift_type'];
        }
      }
    }
    
    if (Shift::saveWeeklySchedule($weekStart, $scheduleData, Auth::user()['id'], false)) {
      Response::success([
        'week_start' => $weekStart
      ], 'Last week schedule copied successfully');
    } else {
      Response::error('Failed to copy last week schedule', 500);
    }
  }
}
