<?php
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/ApiGuard.php';
require_once __DIR__ . '/../../models/Inspection.php';
require_once __DIR__ . '/../../models/CashClosing.php';
require_once __DIR__ . '/../../models/Task.php';
require_once __DIR__ . '/../../models/Shift.php';

class ManagerApiController {
  public function dashboard() {
    ApiGuard::requireRole(['manager']);
    
    $today = date('Y-m-d');
    $user = Auth::user();
    
    // 今日巡店状态
    $todayInspections = Inspection::list([
      'date' => $today,
      'created_by' => $user['id']
    ]);
    $inspectionCount = count($todayInspections);
    $inspectionStatus = 'red';
    if ($inspectionCount >= 2) {
      $inspectionStatus = 'green';
    } elseif ($inspectionCount == 1) {
      $inspectionStatus = 'yellow';
    }
    
    // 今日现金日结状态
    $todayClosing = CashClosing::findByDate($today);
    $cashStatus = 'red';
    $cashStatusText = 'not_submitted';
    if ($todayClosing) {
      if ($todayClosing['status'] === 'approved') {
        $cashStatus = 'green';
        $cashStatusText = 'approved';
      } elseif ($todayClosing['status'] === 'submitted') {
        $previousClosing = CashClosing::getPreviousClosing($today);
        $openingCash = $previousClosing ? floatval($previousClosing['cash_counted']) : 0;
        $theoretical = CashClosing::calculateTheoreticalBalance($today, $openingCash);
        $difference = floatval($todayClosing['cash_counted']) - $theoretical['balance'];
        
        if (abs($difference) > 0.01 && empty($todayClosing['cash_note'])) {
          $cashStatus = 'orange';
          $cashStatusText = 'difference_no_note';
        } else {
          $cashStatus = 'yellow';
          $cashStatusText = 'submitted';
        }
      } elseif ($todayClosing['status'] === 'draft') {
        $cashStatus = 'yellow';
        $cashStatusText = 'draft';
      }
    }
    
    // 今日任务
    $allTasks = Task::list([
      'assigned_to_user_id' => $user['id'],
      'assigned_to_role_id' => $user['role_id']
    ]);
    $todayTasks = array_filter($allTasks, function($task) {
      return in_array($task['status'], ['pending', 'in_progress']);
    });
    
    // 今日问题
    $todayIssues = array_filter($allTasks, function($task) use ($today) {
      if (!in_array($task['status'], ['pending', 'in_progress'])) {
        return false;
      }
      $title = mb_strtolower($task['title'] ?? '', 'UTF-8');
      $description = mb_strtolower($task['description'] ?? '', 'UTF-8');
      $hasIssueKeyword = (
        mb_strpos($title, '问题') !== false || 
        mb_strpos($title, 'issue') !== false ||
        mb_strpos($description, '问题') !== false || 
        mb_strpos($description, 'issue') !== false
      );
      $createdDate = date('Y-m-d', strtotime($task['created_at'] ?? ''));
      return $hasIssueKeyword && $createdDate === $today;
    });
    
    // 本周排班
    $weekStart = date('Y-m-d', strtotime('monday this week'));
    $weekEnd = date('Y-m-d', strtotime('sunday this week'));
    $weekShifts = Shift::list([
      'from_date' => $weekStart,
      'to_date' => $weekEnd
    ]);
    
    Response::success([
      'inspections' => [
        'count' => $inspectionCount,
        'status' => $inspectionStatus,
        'required' => 2
      ],
      'cash_closing' => [
        'status' => $cashStatus,
        'status_text' => $cashStatusText,
        'data' => $todayClosing
      ],
      'tasks' => [
        'count' => count($todayTasks),
        'items' => array_values($todayTasks)
      ],
      'issues' => [
        'count' => count($todayIssues),
        'items' => array_values($todayIssues)
      ],
      'shifts' => [
        'week_start' => $weekStart,
        'week_end' => $weekEnd,
        'count' => count($weekShifts),
        'items' => $weekShifts
      ]
    ]);
  }
}
