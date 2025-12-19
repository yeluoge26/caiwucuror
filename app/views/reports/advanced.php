<?php
$title = __('report.advanced');
include __DIR__ . '/../layout/header.php';
$lang = I18n::current();
?>

<h2 style="margin-bottom: 20px;"><?= __('report.advanced') ?></h2>

<!-- 时间范围选择 -->
<div class="card">
  <form method="get" id="report-filter-form" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
    <input type="hidden" name="r" value="advancedReports/index">
    
    <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
      <label><?= __('report.range') ?></label>
      <select name="range" id="range-select" onchange="updateDateInputs()">
        <option value="today" <?= $range === 'today' ? 'selected' : '' ?>><?= __('report.today') ?></option>
        <option value="week" <?= $range === 'week' ? 'selected' : '' ?>><?= __('report.week') ?></option>
        <option value="month" <?= $range === 'month' ? 'selected' : '' ?>><?= __('report.month') ?></option>
        <option value="year" <?= $range === 'year' ? 'selected' : '' ?>><?= __('report.year') ?></option>
        <option value="custom" <?= $range === 'custom' ? 'selected' : '' ?>><?= __('report.custom') ?></option>
      </select>
    </div>
    
    <div class="form-group" id="custom-date-group" style="margin-bottom: 0; display: <?= $range === 'custom' ? 'block' : 'none' ?>;">
      <label><?= __('field.time') ?> (<?= __('report.from') ?>)</label>
      <input type="date" name="from" id="date-from" value="<?= htmlspecialchars($from ?? '') ?>">
    </div>
    
    <div class="form-group" id="custom-date-to-group" style="margin-bottom: 0; display: <?= $range === 'custom' ? 'block' : 'none' ?>;">
      <label><?= __('report.to') ?></label>
      <input type="date" name="to" id="date-to" value="<?= htmlspecialchars($to ?? '') ?>">
    </div>
    
    <div>
      <button type="submit" class="btn btn-success"><?= __('btn.search') ?></button>
    </div>
  </form>
</div>

<!-- 汇总统计 -->
<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('report.summary') ?></h3>
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
    <div style="text-align: center; padding: 20px; background: #e8f5e9; border-radius: 8px;">
      <div style="font-size: 14px; color: #666; margin-bottom: 8px;"><?= __('tx.income') ?></div>
      <div class="kpi income" style="font-size: 28px;">
        <?= number_format($summary['income'] ?? 0, 0, ',', '.') ?> ₫
      </div>
    </div>
    <div style="text-align: center; padding: 20px; background: #ffebee; border-radius: 8px;">
      <div style="font-size: 14px; color: #666; margin-bottom: 8px;"><?= __('tx.expense') ?></div>
      <div class="kpi expense" style="font-size: 28px;">
        <?= number_format($summary['expense'] ?? 0, 0, ',', '.') ?> ₫
      </div>
    </div>
    <div style="text-align: center; padding: 20px; background: #e3f2fd; border-radius: 8px;">
      <div style="font-size: 14px; color: #666; margin-bottom: 8px;"><?= __('dashboard.net') ?></div>
      <div class="kpi net" style="font-size: 28px;">
        <?= number_format(($summary['income'] ?? 0) - ($summary['expense'] ?? 0), 0, ',', '.') ?> ₫
      </div>
    </div>
  </div>
</div>

<!-- 趋势图表 -->
<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('report.trend_chart') ?></h3>
  <canvas id="trend-chart" style="max-height: 300px;"></canvas>
</div>

<!-- 分类统计 -->
<?php if (!empty($byCategory)): ?>
<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('report.by_category') ?></h3>
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;" class="category-grid">
    <div>
      <canvas id="category-chart" style="max-height: 300px;"></canvas>
    </div>
    <div style="overflow-x: auto;">
      <table style="width: 100%;">
        <tr>
          <th><?= __('field.category') ?></th>
          <th><?= __('tx.income') ?></th>
          <th><?= __('tx.expense') ?></th>
        </tr>
        <?php foreach ($byCategory as $item): ?>
        <tr>
          <td><?= htmlspecialchars($lang === 'zh' ? ($item['name_zh'] ?? '') : ($item['name_vi'] ?? '')) ?></td>
          <td style="color: #27ae60;"><?= number_format($item['income'] ?? 0, 0, ',', '.') ?> ₫</td>
          <td style="color: #e74c3c;"><?= number_format($item['expense'] ?? 0, 0, ',', '.') ?> ₫</td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</div>

<style>
@media (max-width: 768px) {
  .category-grid { grid-template-columns: 1fr !important; }
}
</style>

<!-- 支付方式统计 -->
<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('report.by_payment') ?></h3>
  <canvas id="payment-chart" style="max-height: 300px;"></canvas>
</div>

<!-- 供应商统计 -->
<?php if (!empty($byVendor)): ?>
<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('report.by_vendor') ?></h3>
  <table style="width: 100%;">
    <tr>
      <th><?= __('field.vendor') ?></th>
      <th><?= __('report.transaction_count') ?></th>
      <th><?= __('report.total_amount') ?></th>
    </tr>
    <?php foreach ($byVendor as $item): ?>
    <tr>
      <td><?= htmlspecialchars($item['name']) ?></td>
      <td><?= $item['transaction_count'] ?></td>
      <td style="font-weight: bold;"><?= number_format($item['total_amount'] ?? 0, 0, ',', '.') ?> ₫</td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php endif; ?>

<!-- 月度对比 -->
<?php if (!empty($monthlyCompare)): ?>
<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('report.monthly_compare') ?></h3>
  <canvas id="monthly-chart" style="max-height: 300px;"></canvas>
</div>
<?php endif; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// 时间范围选择
function updateDateInputs() {
  const range = document.getElementById('range-select').value;
  const customGroup = document.getElementById('custom-date-group');
  const customToGroup = document.getElementById('custom-date-to-group');
  
  if (range === 'custom') {
    customGroup.style.display = 'block';
    customToGroup.style.display = 'block';
  } else {
    customGroup.style.display = 'none';
    customToGroup.style.display = 'none';
  }
}

// 趋势图表
<?php if (!empty($trend)): ?>
<?php
$trendLabels = [];
$trendIncome = [];
$trendExpense = [];
foreach ($trend as $d) {
  $trendLabels[] = "'" . date('d/m', strtotime($d['day'])) . "'";
  $trendIncome[] = $d['income'] ?? 0;
  $trendExpense[] = $d['expense'] ?? 0;
}
?>
const trendCtx = document.getElementById('trend-chart').getContext('2d');
const trendData = {
  labels: [<?php echo implode(',', $trendLabels); ?>],
  datasets: [{
    label: '<?= __('tx.income') ?>',
    data: [<?php echo implode(',', $trendIncome); ?>],
    borderColor: '#27ae60',
    backgroundColor: 'rgba(39, 174, 96, 0.1)',
    tension: 0.4
  }, {
    label: '<?= __('tx.expense') ?>',
    data: [<?php echo implode(',', $trendExpense); ?>],
    borderColor: '#e74c3c',
    backgroundColor: 'rgba(231, 76, 60, 0.1)',
    tension: 0.4
  }]
};
new Chart(trendCtx, {
  type: 'line',
  data: trendData,
  options: {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: { position: 'top' }
    },
    scales: {
      y: { beginAtZero: true }
    }
  }
});
<?php endif; ?>

// 分类饼图
<?php if (!empty($byCategory)): ?>
<?php
$categoryLabels = [];
$categoryData = [];
foreach ($byCategory as $item) {
  $categoryLabels[] = "'" . htmlspecialchars($lang === 'zh' ? ($item['name_zh'] ?? '') : ($item['name_vi'] ?? ''), ENT_QUOTES) . "'";
  $categoryData[] = ($item['income'] ?? 0) + ($item['expense'] ?? 0);
}
?>
const categoryCtx = document.getElementById('category-chart').getContext('2d');
const categoryChartData = {
  labels: [<?php echo implode(',', $categoryLabels); ?>],
  datasets: [{
    data: [<?php echo implode(',', $categoryData); ?>],
    backgroundColor: [
      '#3498db', '#e74c3c', '#f39c12', '#27ae60', '#9b59b6',
      '#1abc9c', '#34495e', '#e67e22', '#16a085', '#c0392b'
    ]
  }]
};
new Chart(categoryCtx, {
  type: 'pie',
  data: categoryChartData,
  options: {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: { position: 'right' }
    }
  }
});
<?php endif; ?>

// 支付方式图表
<?php if (!empty($byPayment)): ?>
<?php
$paymentLabels = [];
$paymentData = [];
foreach ($byPayment as $item) {
  $paymentLabels[] = "'" . htmlspecialchars($lang === 'zh' ? ($item['name_zh'] ?? '') : ($item['name_vi'] ?? ''), ENT_QUOTES) . "'";
  $paymentData[] = ($item['income'] ?? 0) + ($item['expense'] ?? 0);
}
?>
const paymentCtx = document.getElementById('payment-chart').getContext('2d');
const paymentChartData = {
  labels: [<?php echo implode(',', $paymentLabels); ?>],
  datasets: [{
    label: '<?= __('tx.amount') ?>',
    data: [<?php echo implode(',', $paymentData); ?>],
    backgroundColor: [
      '#3498db', '#e74c3c', '#f39c12', '#27ae60', '#9b59b6'
    ]
  }]
};
new Chart(paymentCtx, {
  type: 'bar',
  data: paymentChartData,
  options: {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: { beginAtZero: true }
    }
  }
});

<?php if (!empty($monthlyCompare)): ?>
// 月度对比图表
<?php
$monthlyLabels = [];
$monthlyIncome = [];
$monthlyExpense = [];
foreach ($monthlyCompare as $m) {
  $monthlyLabels[] = "'" . $m['month'] . "'";
  $monthlyIncome[] = $m['income'] ?? 0;
  $monthlyExpense[] = $m['expense'] ?? 0;
}
?>
const monthlyCtx = document.getElementById('monthly-chart').getContext('2d');
const monthlyData = {
  labels: [<?php echo implode(',', $monthlyLabels); ?>],
  datasets: [{
    label: '<?= __('tx.income') ?>',
    data: [<?php echo implode(',', $monthlyIncome); ?>],
    backgroundColor: '#27ae60'
  }, {
    label: '<?= __('tx.expense') ?>',
    data: [<?php echo implode(',', $monthlyExpense); ?>],
    backgroundColor: '#e74c3c'
  }]
};
new Chart(monthlyCtx, {
  type: 'bar',
  data: monthlyData,
  options: {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: { position: 'top' }
    },
    scales: {
      y: { beginAtZero: true }
    }
  }
});
<?php endif; ?>
<?php endif; ?>
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>

