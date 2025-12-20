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
    if ($inspectionCount >= 2) {
      $inspectionStatus = 'green';
    } elseif ($inspectionCount == 1) {
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

    // 今日任务 - 获取分配给当前用户的任务
    $allTasks = Task::list([
      'assign_user_id' => $user['id']
    ]);
    // 过滤出今日到期的任务
    $todayTasks = array_filter($allTasks, function($task) use ($today) {
      if (empty($task['due_date'])) return false;
      $dueDate = date('Y-m-d', strtotime($task['due_date']));
      return $dueDate === $today && in_array($task['status'], ['pending', 'in_progress']);
    });

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

