<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/DB.php';
require_once __DIR__ . '/../models/Transaction.php';

class ReportController {
  public function dashboard() {
    Auth::requireLogin();

    // 今日统计（只统计已审核的交易）
    $today = Transaction::getSummary([
      'from' => date('Y-m-d 00:00:00'),
      'to' => date('Y-m-d 23:59:59'),
      'status' => 'approved'
    ]) ?: ['income' => 0, 'expense' => 0];

    // 本月统计（只统计已审核的交易）
    $monthStart = date('Y-m-01 00:00:00');
    $monthEnd = date('Y-m-t 23:59:59');
    $month = Transaction::getSummary([
      'from' => $monthStart,
      'to' => $monthEnd,
      'status' => 'approved'
    ]) ?: ['income' => 0, 'expense' => 0];

    // 7天趋势（只统计已审核的交易）
    $trend = Transaction::getTrend(7);

    // 最近10笔流水（只显示已审核的）
    $latest = Transaction::list(['per_page' => 10, 'page' => 1]);

    include __DIR__ . '/../views/reports/dashboard.php';
  }

  public function summary() {
    Auth::requireLogin();

    $range = $_GET['range'] ?? 'today';
    $from = null;
    $to = date('Y-m-d 23:59:59');

    switch ($range) {
      case 'today':
        $from = date('Y-m-d 00:00:00');
        break;
      case '7d':
        $from = date('Y-m-d 00:00:00', strtotime('-6 days'));
        break;
      case 'month':
        $from = date('Y-m-01 00:00:00');
        $to = date('Y-m-t 23:59:59');
        break;
    }

    $summary = Transaction::getSummary(['from' => $from, 'to' => $to]);
    Response::json($summary);
  }

  public function byCategory() {
    Auth::requireLogin();

    $type = $_GET['type'] ?? null;
    $range = $_GET['range'] ?? 'month';
    $from = null;
    $to = date('Y-m-d 23:59:59');

    switch ($range) {
      case 'today':
        $from = date('Y-m-d 00:00:00');
        break;
      case '7d':
        $from = date('Y-m-d 00:00:00', strtotime('-6 days'));
        break;
      case 'month':
        $from = date('Y-m-01 00:00:00');
        $to = date('Y-m-t 23:59:59');
        break;
    }

    $data = Transaction::getByCategory([
      'type' => $type,
      'from' => $from,
      'to' => $to
    ]);

    Response::json($data);
  }
}

