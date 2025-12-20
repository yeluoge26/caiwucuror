<?php
$title = __('manager.dashboard');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('manager.dashboard') ?></h2>

<!-- 今日信息卡片 -->
<div class="card" style="margin-bottom: 20px;">
  <h3 style="margin-bottom: 12px;">📅 <?= __('manager.today_info') ?></h3>
  <p style="font-size: 1.1em; color: #666;">
    <?= date('Y年m月d日', strtotime(date('Y-m-d'))) ?> 
    (<?= ['日', '一', '二', '三', '四', '五', '六'][date('w')] ?>)
  </p>
</div>

<!-- 管理清单卡片 -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px; margin-bottom: 20px;">
  
  <!-- 今日任务卡片 -->
  <div class="card" style="cursor: pointer;" onclick="window.location='/index.php?r=tasks/list'">
    <h3 style="margin-bottom: 12px;">✅ <?= __('manager.today_tasks') ?></h3>
    <div style="font-size: 2em; font-weight: bold; color: #3498db;">
      <?= count($todayTasks) ?>
    </div>
    <p style="color: #666; margin-top: 8px;">
      <?= __('manager.tasks_pending') ?>
    </p>
  </div>

  <!-- 今日巡店卡片 -->
  <a href="/index.php?r=inspections/create" class="card" style="text-decoration: none; color: inherit; cursor: pointer;">
    <h3 style="margin-bottom: 12px;">🔍 <?= __('manager.today_inspections') ?></h3>
    <div style="font-size: 2em; font-weight: bold; color: <?= $inspectionStatus === 'green' ? '#27ae60' : ($inspectionStatus === 'yellow' ? '#f39c12' : '#e74c3c'); ?>;">
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
    <p style="color: #666; margin-top: 8px;">
      <?php
      if ($inspectionCount >= 2) {
        echo __('manager.inspections_complete');
      } elseif ($inspectionCount == 1) {
        echo __('manager.inspections_partial');
      } else {
        echo __('manager.inspections_none');
      }
      ?>
    </p>
  </a>

  <!-- 今日现金日结卡片 -->
  <a href="/index.php?r=cash_closings/create" class="card" style="text-decoration: none; color: inherit; cursor: pointer;">
    <h3 style="margin-bottom: 12px;">💸 <?= __('manager.today_cash') ?></h3>
    <div style="font-size: 1.5em; font-weight: bold; color: <?= $cashStatus === 'green' ? '#27ae60' : ($cashStatus === 'yellow' ? '#f39c12' : ($cashStatus === 'orange' ? '#ff9800' : '#e74c3c')); ?>;">
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
    <?php if ($todayClosing): ?>
    <p style="color: #666; margin-top: 8px;">
      <?= __('cash_closing.cash_counted') ?>: 
      <?= number_format($todayClosing['cash_counted'], 0, ',', '.') ?> ₫
    </p>
    <?php endif; ?>
  </a>

  <!-- 本周排班卡片 -->
  <a href="/index.php?r=shifts/schedule" class="card" style="text-decoration: none; color: inherit; cursor: pointer;">
    <h3 style="margin-bottom: 12px;">👥 <?= __('manager.week_schedule') ?></h3>
    <div style="font-size: 1.2em;">
      <?= count($weekShifts) ?> <?= __('manager.shifts_scheduled') ?>
    </div>
    <p style="color: #666; margin-top: 8px;">
      <?= date('m/d', strtotime($weekStart)) ?> - <?= date('m/d', strtotime($weekEnd)) ?>
    </p>
  </a>

</div>

<!-- 本周排班预览 -->
<?php if (!empty($shiftsByDate)): ?>
<div class="card">
  <h3 style="margin-bottom: 16px;">👥 <?= __('manager.week_schedule_preview') ?></h3>
  <div style="overflow-x: auto;">
    <table style="min-width: 600px;">
      <tr>
        <th><?= __('field.date') ?></th>
        <th><?= __('shift.morning') ?></th>
        <th><?= __('shift.afternoon') ?></th>
        <th><?= __('shift.evening') ?></th>
      </tr>
      <?php
      $days = ['周一', '周二', '周三', '周四', '周五', '周六', '周日'];
      $currentDate = $weekStart;
      for ($i = 0; $i < 7; $i++):
        $dateStr = date('Y-m-d', strtotime($currentDate . ' +' . $i . ' days'));
        $dayShifts = $shiftsByDate[$dateStr] ?? [];
        $morning = array_filter($dayShifts, fn($s) => $s['shift_type'] === 'morning');
        $afternoon = array_filter($dayShifts, fn($s) => $s['shift_type'] === 'afternoon');
        $evening = array_filter($dayShifts, fn($s) => $s['shift_type'] === 'evening');
      ?>
      <tr>
        <td>
          <?= $days[$i] ?><br>
          <small style="color: #999;"><?= date('m/d', strtotime($dateStr)) ?></small>
        </td>
        <td>
          <?php foreach ($morning as $s): ?>
            <div><?= htmlspecialchars($s['employee_name'] ?? '') ?></div>
          <?php endforeach; ?>
        </td>
        <td>
          <?php foreach ($afternoon as $s): ?>
            <div><?= htmlspecialchars($s['employee_name'] ?? '') ?></div>
          <?php endforeach; ?>
        </td>
        <td>
          <?php foreach ($evening as $s): ?>
            <div><?= htmlspecialchars($s['employee_name'] ?? '') ?></div>
          <?php endforeach; ?>
        </td>
      </tr>
      <?php endfor; ?>
    </table>
  </div>
  <div style="margin-top: 12px;">
    <a href="/index.php?r=shifts/schedule" class="btn"><?= __('manager.view_full_schedule') ?></a>
  </div>
</div>
<?php endif; ?>

<!-- 今日任务列表 -->
<?php if (!empty($todayTasks)): ?>
<div class="card">
  <h3 style="margin-bottom: 16px;">✅ <?= __('manager.today_tasks_list') ?></h3>
  <table>
    <tr>
      <th><?= __('task.title') ?></th>
      <th><?= __('task.due_date') ?></th>
      <th><?= __('task.status') ?></th>
      <th><?= __('field.actions') ?></th>
    </tr>
    <?php foreach (array_slice($todayTasks, 0, 5) as $task): ?>
    <tr>
      <td><?= htmlspecialchars($task['title']) ?></td>
      <td><?= $task['due_date'] ? date('Y-m-d H:i', strtotime($task['due_date'])) : '-' ?></td>
      <td>
        <span class="badge badge-<?= $task['status'] === 'pending' ? 'warning' : 'info' ?>">
          <?= __('task.status_' . $task['status']) ?>
        </span>
      </td>
      <td>
        <a href="/index.php?r=tasks/view&id=<?= $task['id'] ?>" class="btn btn-sm"><?= __('btn.view') ?></a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php if (count($todayTasks) > 5): ?>
  <div style="margin-top: 12px;">
    <a href="/index.php?r=tasks/list" class="btn"><?= __('manager.view_all_tasks') ?></a>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>

