<?php
require_once __DIR__ . '/../../models/Inspection.php';
require_once __DIR__ . '/../../models/CashClosing.php';

$title = __('manager.dashboard');
$show_back = false; // 首页不显示返回按钮
include __DIR__ . '/../layout/h5_header.php';

// 如果控制器没有传递这些变量，则计算它们
if (!isset($inspectionCount)) {
  $today = date('Y-m-d');
  $todayInspections = Inspection::list(['date' => $today]);
  $confirmedInspections = array_filter($todayInspections, function($item) {
    return $item['reviewed_status'] === 'confirmed';
  });
  $inspectionCount = count($confirmedInspections);
  $inspectionStatus = 'red';
  if ($inspectionCount >= 2) {
    $inspectionStatus = 'green';
  } elseif ($inspectionCount == 1) {
    $inspectionStatus = 'yellow';
  }
}

if (!isset($theoretical)) {
  $previousClosing = CashClosing::getPreviousClosing(date('Y-m-d'));
  $openingCash = $previousClosing ? floatval($previousClosing['cash_counted']) : 0;
  $theoretical = CashClosing::calculateTheoreticalBalance(date('Y-m-d'), $openingCash);
}
?>

<!-- 顶部信息区 -->
<div class="top-info">
  <div class="greeting">👋 <?= __('manager.greeting', '早安，店长') ?></div>
  <div class="date">📅 <?= date('Y-m-d', strtotime('now')) ?>（<?= ['日', '一', '二', '三', '四', '五', '六'][date('w')] ?>）</div>
</div>

<!-- 今日核心待办（第一优先级） -->
<div class="h5-card" style="margin-bottom: 16px;">
  <div class="h5-card-title">⭐ <?= __('manager.core_tasks', '今日核心待办') ?></div>
  
  <!-- 巡店卡 -->
  <a href="/index.php?r=inspections/create" class="status-card" style="text-decoration: none; color: inherit; margin-bottom: 12px;">
    <div class="status-card-header">
      <span>🔍</span>
      <span class="status-card-title"><?= __('manager.today_inspections', '今日巡店') ?></span>
    </div>
    <div class="status-card-body status-<?= $inspectionStatus ?>">
      <?php
      $statusEmoji = [
        'green' => '🟢',
        'yellow' => '🟡',
        'red' => '🔴'
      ];
      echo $statusEmoji[$inspectionStatus];
      ?>
      <?= $inspectionCount ?> / 2
    </div>
    <div class="status-card-footer">
      <?php
      if ($inspectionCount >= 2) {
        echo __('manager.inspections_complete', '已完成');
      } elseif ($inspectionCount == 1) {
        echo __('manager.inspections_partial', '还需 1 次巡店');
      } else {
        echo __('manager.inspections_none', '未巡店');
      }
      ?>
    </div>
  </a>
  
  <!-- 现金卡 -->
  <a href="/index.php?r=cash_closings/create" class="status-card" style="text-decoration: none; color: inherit;">
    <div class="status-card-header">
      <span>💸</span>
      <span class="status-card-title"><?= __('manager.today_cash', '今日现金') ?></span>
    </div>
    <div class="status-card-body status-<?= $cashStatus ?>">
      <?php
      $cashEmoji = [
        'green' => '🟢',
        'yellow' => '🟡',
        'orange' => '🟠',
        'red' => '🔴'
      ];
      echo $cashEmoji[$cashStatus];
      ?>
      <?= $cashStatusText ?>
    </div>
    <div class="status-card-footer">
      <?= __('cash_closing.theoretical_balance', '理论现金') ?>: 
      <?= number_format($theoretical['balance'], 0, ',', '.') ?> ₫
    </div>
  </a>
</div>

<!-- 今日执行 & 异常（第二优先级） -->
<div class="h5-card" style="margin-bottom: 16px;">
  <div class="h5-card-title">📋 <?= __('manager.today_tasks', '今日执行') ?></div>
  
  <!-- 今日执行卡 -->
  <a href="/index.php?r=tasks/list" class="status-card" style="text-decoration: none; color: inherit; margin-bottom: 12px;">
    <div class="status-card-header">
      <span>✅</span>
      <span class="status-card-title"><?= __('manager.today_tasks', '今日执行') ?></span>
    </div>
    <div class="status-card-body" style="color: #3498db;">
      <?= count($todayTasks) ?> <?= __('manager.tasks_pending', '项') ?>
    </div>
    <div class="status-card-footer">
      <?= __('manager.today_in_office', '今日在岗') ?>: 
      <?php
      // 计算今日在岗员工数
      $todayOnDuty = 0;
      foreach ($weekShifts as $shift) {
        if ($shift['shift_date'] === date('Y-m-d')) {
          $todayOnDuty++;
        }
      }
      echo $todayOnDuty > 0 ? $todayOnDuty : '0';
      ?> <?= __('manager.people', '人') ?>
    </div>
  </a>
  
  <!-- 今日问题卡 -->
  <a href="/index.php?r=tasks/create?type=issue" class="status-card" style="text-decoration: none; color: inherit;">
    <div class="status-card-header">
      <span>⚠</span>
      <span class="status-card-title"><?= __('manager.today_issues', '今日问题') ?></span>
    </div>
    <div class="status-card-body" style="color: #e74c3c;">
      0 <?= __('manager.issues_recorded', '条') ?>
    </div>
    <div class="status-card-footer">
      <?= __('manager.record_issue', '记录问题') ?>
    </div>
  </a>
</div>

<!-- 本周排班状态（只读） -->
<?php if (!empty($weekShifts)): ?>
<div class="h5-card">
  <div class="status-card-header">
    <span>👥</span>
    <span class="status-card-title"><?= __('manager.week_schedule', '本周排班') ?></span>
  </div>
  <div class="status-card-body" style="color: #27ae60; font-size: 18px;">
    🟢 <?= __('manager.schedule_published', '已发布') ?>
  </div>
  <div class="status-card-footer">
    <?= __('manager.period', '周期') ?>: <?= date('m/d', strtotime($weekStart)) ?> - <?= date('m/d', strtotime($weekEnd)) ?>
  </div>
  <a href="/index.php?r=shifts/schedule" class="h5-btn" style="margin-top: 12px;"><?= __('manager.view_schedule', '查看排班') ?></a>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../layout/h5_footer.php'; ?>
