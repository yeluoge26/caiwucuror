<?php
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/ApiGuard.php';
require_once __DIR__ . '/../../models/Transaction.php';

class ReportApiController {
  public function dashboard() {
    ApiGuard::requireLogin();
    
    $from = $_GET['from'] ?? date('Y-m-01');
    $to = $_GET['to'] ?? date('Y-m-d');
    
    // 今日统计
    $today = date('Y-m-d');
    $todayIncome = Transaction::getSummary([
      'from' => $today . ' 00:00:00',
      'to' => $today . ' 23:59:59',
      'type' => 'income',
      'status' => 'approved'
    ]);
    $todayExpense = Transaction::getSummary([
      'from' => $today . ' 00:00:00',
      'to' => $today . ' 23:59:59',
      'type' => 'expense',
      'status' => 'approved'
    ]);
    
    // 本月统计
    $monthIncome = Transaction::getSummary([
      'from' => $from . ' 00:00:00',
      'to' => $to . ' 23:59:59',
      'type' => 'income',
      'status' => 'approved'
    ]);
    $monthExpense = Transaction::getSummary([
      'from' => $from . ' 00:00:00',
      'to' => $to . ' 23:59:59',
      'type' => 'expense',
      'status' => 'approved'
    ]);
    
    Response::success([
      'today' => [
        'income' => $todayIncome,
        'expense' => $todayExpense,
        'net' => $todayIncome - $todayExpense
      ],
      'month' => [
        'income' => $monthIncome,
        'expense' => $monthExpense,
        'net' => $monthIncome - $monthExpense
      ],
      'period' => [
        'from' => $from,
        'to' => $to
      ]
    ]);
  }

  public function summary() {
    ApiGuard::requireLogin();
    
    $from = $_GET['from'] ?? date('Y-m-01');
    $to = $_GET['to'] ?? date('Y-m-d');
    
    $summary = Transaction::getSummary([
      'from' => $from . ' 00:00:00',
      'to' => $to . ' 23:59:59',
      'status' => 'approved'
    ]);
    
    Response::success($summary);
  }

  public function trends() {
    ApiGuard::requireLogin();
    
    $days = min(30, max(7, (int)($_GET['days'] ?? 7)));
    $endDate = date('Y-m-d');
    $startDate = date('Y-m-d', strtotime("-{$days} days"));
    
    $trends = [];
    $currentDate = $startDate;
    while ($currentDate <= $endDate) {
      $dayIncome = Transaction::getSummary([
        'from' => $currentDate . ' 00:00:00',
        'to' => $currentDate . ' 23:59:59',
        'type' => 'income',
        'status' => 'approved'
      ]);
      $dayExpense = Transaction::getSummary([
        'from' => $currentDate . ' 00:00:00',
        'to' => $currentDate . ' 23:59:59',
        'type' => 'expense',
        'status' => 'approved'
      ]);
      
      $trends[] = [
        'date' => $currentDate,
        'income' => $dayIncome,
        'expense' => $dayExpense,
        'net' => $dayIncome - $dayExpense
      ];
      
      $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
    }
    
    Response::success($trends);
  }
}
