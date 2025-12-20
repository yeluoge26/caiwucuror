<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Inspection.php';
require_once __DIR__ . '/../models/CashClosing.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Shift.php';
require_once __DIR__ . '/../models/Employee.php';

class ManagerController {
  public function dashboard() {
    Auth::requireLogin();
    Auth::requireRole(['manager']);

    $today = date('Y-m-d');
    $user = Auth::user();

    // 今日巡店状态 - 统计所有今日的巡店记录（包括pending状态），因为店长自己创建的应该立即计入
    $todayInspections = Inspection::list(['date' => $today]);
    $inspectionCount = count($todayInspections);
    $inspectionStatus = 'red';
    if ($inspectionCount >= 24) {
      $inspectionStatus = 'green';
    } elseif ($inspectionCount >= 12) {
      $inspectionStatus = 'yellow';
    }

    // 今日现金日结状态
    $todayClosing = CashClosing::findByDate($today);
    $cashStatus = 'red';
    $cashStatusText = __('cash_closing.not_submitted');
    if ($todayClosing) {
      if ($todayClosing['status'] === 'approved') {
        $cashStatus = 'green';
        $cashStatusText = __('cash_closing.status_approved');
      } elseif ($todayClosing['status'] === 'submitted') {
        // 检查是否有差额且无说明
        $previousClosing = CashClosing::getPreviousClosing($today);
        $openingCash = $previousClosing ? floatval($previousClosing['cash_counted']) : 0;
        $theoretical = CashClosing::calculateTheoreticalBalance($today, $openingCash);
        $difference = floatval($todayClosing['cash_counted']) - $theoretical['balance'];
        
        if (abs($difference) > 0.01 && empty($todayClosing['cash_note'])) {
          $cashStatus = 'orange';
          $cashStatusText = __('cash_closing.difference_no_note');
        } else {
          $cashStatus = 'yellow';
          $cashStatusText = __('cash_closing.status_submitted');
        }
      } elseif ($todayClosing['status'] === 'draft') {
        $cashStatus = 'yellow';
        $cashStatusText = __('cash_closing.status_draft');
      }
    }

    // 今日任务 - 获取分配给当前用户或当前用户角色的待处理任务
    // 包括：1. 直接分配给当前用户的任务 (assign_user_id)
    //      2. 分配给当前用户角色的任务 (assign_role_id)
    // 显示所有待处理（pending/in_progress）的任务
    $allTasks = Task::list([
      'assigned_to_user_id' => $user['id'],
      'assigned_to_role_id' => $user['role_id']
    ]);
    // 过滤出待处理的任务（pending 或 in_progress）
    $todayTasks = array_filter($allTasks, function($task) {
      return in_array($task['status'], ['pending', 'in_progress']);
    });

    // 今日问题 - 统计今日创建的问题任务（通过标题或描述中包含"问题"关键词来识别）
    // 只统计待处理状态的问题（pending 或 in_progress），已完成和已审批的不计入
    $today = date('Y-m-d');
    $todayIssues = array_filter($allTasks, function($task) use ($today) {
      // 检查任务状态，只统计待处理的问题
      if (!in_array($task['status'], ['pending', 'in_progress'])) {
        return false;
      }
      
      // 检查任务标题或描述中是否包含"问题"关键词
      $title = mb_strtolower($task['title'] ?? '', 'UTF-8');
      $description = mb_strtolower($task['description'] ?? '', 'UTF-8');
      $hasIssueKeyword = (
        mb_strpos($title, '问题') !== false || 
        mb_strpos($title, 'issue') !== false ||
        mb_strpos($description, '问题') !== false || 
        mb_strpos($description, 'issue') !== false
      );
      
      // 检查是否是今天创建的
      $createdDate = date('Y-m-d', strtotime($task['created_at'] ?? ''));
      
      return $hasIssueKeyword && $createdDate === $today;
    });
    $todayIssuesCount = count($todayIssues);

    // 本周排班（周一到周日）
    $weekStart = date('Y-m-d', strtotime('monday this week'));
    $weekEnd = date('Y-m-d', strtotime('sunday this week'));
    $weekShifts = Shift::list([
      'from_date' => $weekStart,
      'to_date' => $weekEnd
    ]);

    // 按日期和员工组织排班数据
    $shiftsByDate = [];
    foreach ($weekShifts as $shift) {
      $date = $shift['shift_date'];
      if (!isset($shiftsByDate[$date])) {
        $shiftsByDate[$date] = [];
      }
      $shiftsByDate[$date][] = $shift;
    }

    include __DIR__ . '/../views/manager/dashboard.php';
  }
}

