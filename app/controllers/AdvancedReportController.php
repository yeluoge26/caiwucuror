<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/PaymentMethod.php';
require_once __DIR__ . '/../models/Vendor.php';

class AdvancedReportController {
  public function index() {
    Auth::requireLogin();
    
    // 获取时间范围
    $range = $_GET['range'] ?? 'month';
    $from = null;
    $to = date('Y-m-d 23:59:59');
    
    switch ($range) {
      case 'today':
        $from = date('Y-m-d 00:00:00');
        break;
      case 'week':
        $from = date('Y-m-d 00:00:00', strtotime('-6 days'));
        break;
      case 'month':
        $from = date('Y-m-01 00:00:00');
        $to = date('Y-m-t 23:59:59');
        break;
      case 'year':
        $from = date('Y-01-01 00:00:00');
        $to = date('Y-12-31 23:59:59');
        break;
      case 'custom':
        $from = $_GET['from'] ?? date('Y-m-01 00:00:00');
        $to = $_GET['to'] ?? date('Y-m-t 23:59:59');
        break;
    }
    
    // 获取统计数据
    $summary = Transaction::getSummary([
      'from' => $from,
      'to' => $to,
      'status' => 'approved'
    ]) ?: ['income' => 0, 'expense' => 0];
    
    // 趋势数据（按天）
    $trend = Transaction::getTrendByDate($from, $to) ?: [];
    
    // 分类统计
    $byCategory = Transaction::getByCategory([
      'from' => $from,
      'to' => $to
    ]) ?: [];
    
    // 支付方式统计
    $byPayment = Transaction::getByPaymentMethod([
      'from' => $from,
      'to' => $to
    ]) ?: [];
    
    // 供应商统计（支出）
    $byVendor = Transaction::getByVendor([
      'from' => $from,
      'to' => $to
    ]) ?: [];
    
    // 月度对比（最近3个月）
    $monthlyCompare = Transaction::getMonthlyCompare(3) ?: [];
    
    include __DIR__ . '/../views/reports/advanced.php';
  }
  
  public function chartData() {
    Auth::requireLogin();
    
    $type = $_GET['type'] ?? 'trend';
    $from = $_GET['from'] ?? date('Y-m-01 00:00:00');
    $to = $_GET['to'] ?? date('Y-m-t 23:59:59');
    
    $data = [];
    
    switch ($type) {
      case 'trend':
        $data = Transaction::getTrendByDate($from, $to);
        break;
      case 'category':
        $data = Transaction::getByCategory(['from' => $from, 'to' => $to]);
        break;
      case 'payment':
        $data = Transaction::getByPaymentMethod(['from' => $from, 'to' => $to]);
        break;
      case 'vendor':
        $data = Transaction::getByVendor(['from' => $from, 'to' => $to]);
        break;
    }
    
    Response::json($data);
  }
}

