Dashboard 要展示什么（P0）
顶部 KPI（最重要）

今日收入

今日支出

今日净额

本月收入 / 支出 / 净额

下方

最近 7 天：收入 / 支出趋势（先用列表，后面再接图表）

今日最新 10 笔流水

2️⃣ Dashboard 统计 SQL（核心）
今日统计
SELECT
  SUM(CASE WHEN type='income' THEN amount ELSE 0 END) AS income_today,
  SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) AS expense_today
FROM transactions
WHERE status='approved'
  AND DATE(occurred_at) = CURDATE();

本月统计
SELECT
  SUM(CASE WHEN type='income' THEN amount ELSE 0 END) AS income_month,
  SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) AS expense_month
FROM transactions
WHERE status='approved'
  AND DATE_FORMAT(occurred_at,'%Y-%m') = DATE_FORMAT(CURDATE(),'%Y-%m');

最近 7 天汇总
SELECT
  DATE(occurred_at) AS day,
  SUM(CASE WHEN type='income' THEN amount ELSE 0 END) AS income,
  SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) AS expense
FROM transactions
WHERE status='approved'
  AND occurred_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
GROUP BY day
ORDER BY day ASC;

最近流水
SELECT *
FROM transactions
WHERE status='approved'
ORDER BY occurred_at DESC
LIMIT 10;

3️⃣ ReportController.php（Dashboard 控制器）

📁 app/controllers/ReportController.php

<?php
class ReportController {

  public function dashboard() {
    Auth::requireLogin();

    $db = DB::conn();

    // 今日
    $today = $db->query("
      SELECT
        SUM(CASE WHEN type='income' THEN amount ELSE 0 END) income,
        SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) expense
      FROM transactions
      WHERE status='approved'
        AND DATE(occurred_at)=CURDATE()
    ")->fetch();

    // 本月
    $month = $db->query("
      SELECT
        SUM(CASE WHEN type='income' THEN amount ELSE 0 END) income,
        SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) expense
      FROM transactions
      WHERE status='approved'
        AND DATE_FORMAT(occurred_at,'%Y-%m')=DATE_FORMAT(CURDATE(),'%Y-%m')
    ")->fetch();

    // 7 天
    $trend = $db->query("
      SELECT
        DATE(occurred_at) day,
        SUM(CASE WHEN type='income' THEN amount ELSE 0 END) income,
        SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) expense
      FROM transactions
      WHERE status='approved'
        AND occurred_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
      GROUP BY day
      ORDER BY day
    ")->fetchAll();

    // 最近流水
    $latest = $db->query("
      SELECT * FROM transactions
      WHERE status='approved'
      ORDER BY occurred_at DESC
      LIMIT 10
    ")->fetchAll();

    include __DIR__ . '/../views/reports/dashboard.php';
  }
}

4️⃣ Dashboard 页面（H5）

📁 app/views/reports/dashboard.php

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?= __('nav.dashboard') ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  body { font-family: system-ui; margin: 12px; }
  .card { padding:12px; border:1px solid #ddd; margin-bottom:10px; }
  .kpi { font-size:20px; font-weight:bold; }
  table { width:100%; border-collapse:collapse; }
  td,th { border:1px solid #ddd; padding:6px; font-size:13px; }
</style>
</head>
<body>

<h2><?= __('nav.dashboard') ?></h2>

<!-- 今日 -->
<div class="card">
  <h3>📅 <?= __('today') ?? 'Hôm nay / Today' ?></h3>
  <div class="kpi">+ <?= number_format($today['income'] ?? 0) ?> ₫</div>
  <div class="kpi">- <?= number_format($today['expense'] ?? 0) ?> ₫</div>
  <div class="kpi">
    = <?= number_format(($today['income'] ?? 0) - ($today['expense'] ?? 0)) ?> ₫
  </div>
</div>

<!-- 本月 -->
<div class="card">
  <h3>📊 <?= __('month') ?? 'Tháng này / This month' ?></h3>
  <div>+ <?= number_format($month['income'] ?? 0) ?> ₫</div>
  <div>- <?= number_format($month['expense'] ?? 0) ?> ₫</div>
  <div>
    = <?= number_format(($month['income'] ?? 0) - ($month['expense'] ?? 0)) ?> ₫
  </div>
</div>

<!-- 7 天趋势 -->
<div class="card">
  <h3>📈 7 Days</h3>
  <table>
    <tr>
      <th>Date</th>
      <th><?= __('tx.income') ?></th>
      <th><?= __('tx.expense') ?></th>
      <th>Net</th>
    </tr>
    <?php foreach ($trend as $d): ?>
    <tr>
      <td><?= $d['day'] ?></td>
      <td><?= number_format($d['income']) ?></td>
      <td><?= number_format($d['expense']) ?></td>
      <td><?= number_format($d['income'] - $d['expense']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

<!-- 最近流水 -->
<div class="card">
  <h3>🧾 <?= __('nav.list') ?></h3>
  <table>
    <tr>
      <th>ID</th>
      <th>Type</th>
      <th>Amount</th>
      <th>Time</th>
    </tr>
    <?php foreach ($latest as $row): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= $row['type'] ?></td>
      <td><?= number_format($row['amount']) ?></td>
      <td><?= $row['occurred_at'] ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

</body>
</html>

5️⃣ 路由访问方式
/index.php?r=reports/dashboard