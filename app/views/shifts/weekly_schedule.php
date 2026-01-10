<?php
$title = __('shift.weekly_schedule');
include __DIR__ . '/../layout/header.php';
?>

<h2><?= __('shift.weekly_title') ?></h2>

<?php if ($error): ?>
<div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="card">
  <form method="post" id="weeklyScheduleForm">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <input type="hidden" name="week_start" value="<?= htmlspecialchars($selectedWeekStart) ?>">
    <input type="hidden" name="schedule_data" id="scheduleData">

    <div class="form-group" style="margin-bottom: 20px;">
      <label><?= __('shift.select_week') ?></label>
      <input type="week" name="week" value="<?= htmlspecialchars(date('Y-\WW', strtotime($selectedWeekStart))) ?>" 
             onchange="location.href='/index.php?r=shifts/weeklySchedule&week='+this.value" 
             style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
      <span style="margin-left: 10px; color: #666;">
        <?= date('Y-m-d', strtotime($selectedWeekStart)) ?> - <?= date('Y-m-d', strtotime($selectedWeekStart . ' +6 days')) ?>
      </span>
      <?php if ($isPublished): ?>
      <span style="margin-left: 10px; color: #27ae60; font-weight: bold;"><?= __('shift.published') ?></span>
      <?php else: ?>
      <span style="margin-left: 10px; color: #95a5a6;"><?= __('shift.draft') ?></span>
      <?php endif; ?>
    </div>

    <?php if ($isPublished): ?>
    <div class="alert alert-warning" style="margin-bottom: 20px;">
      <?= __('shift.cannot_edit_published') ?>
    </div>
    <?php endif; ?>

    <div style="margin-bottom: 20px;">
      <button type="button" class="btn" onclick="copyLastWeek()" <?= $isPublished ? 'disabled' : '' ?>>
        <?= __('shift.copy_last_week') ?>
      </button>
      <button type="button" class="btn" onclick="saveDraft()" <?= $isPublished ? 'disabled' : '' ?>>
        <?= __('shift.save_draft') ?>
      </button>
      <button type="button" class="btn btn-success" onclick="publishSchedule()" <?= $isPublished ? 'disabled' : '' ?>>
        <?= __('shift.publish') ?>
      </button>
      <a href="/index.php?r=shifts/list" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
    </div>

    <div style="overflow-x: auto;">
      <table id="weeklyTable" style="width: 100%; border-collapse: collapse; min-width: 800px;">
        <thead>
          <tr>
            <th style="padding: 12px; border: 1px solid #ddd; background: #f8f9fa; text-align: left;">
              <?= __('shift.employee') ?>
            </th>
            <?php foreach ($weekDays as $day): ?>
            <th style="padding: 12px; border: 1px solid #ddd; background: #f8f9fa; text-align: center; min-width: 100px;">
              <?= $day['day_name'] ?><br>
              <small><?= $day['day_num'] ?></small>
            </th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($employees as $emp): ?>
          <tr>
            <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">
              <?= htmlspecialchars($emp['name']) ?>
            </td>
            <?php foreach ($weekDays as $day): ?>
            <td style="padding: 8px; border: 1px solid #ddd; text-align: center; cursor: pointer;"
                data-employee-id="<?= $emp['id'] ?>"
                data-date="<?= $day['date'] ?>"
                onclick="toggleShift(this)"
                class="shift-cell">
              <span class="shift-display"><?= __('shift.rest') ?></span>
            </td>
            <?php endforeach; ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </form>
</div>

<style>
.shift-cell {
  user-select: none;
  transition: background-color 0.2s;
}
.shift-cell:hover:not([data-published="1"]) {
  background-color: #f0f0f0;
}
.shift-cell.rest {
  background-color: #ecf0f1;
}
.shift-cell.morning {
  background-color: #fff9e6;
}
.shift-cell.evening {
  background-color: #e6f3ff;
}
.shift-display {
  font-weight: bold;
  font-size: 14px;
}
</style>

<script>
const shiftCycle = ['rest', 'morning', 'evening'];
const shiftLabels = {
  'rest': '<?= __('shift.rest') ?>',
  'morning': '<?= __('shift.morning') ?>',
  'evening': '<?= __('shift.evening') ?>'
};

// 初始化表格数据
const scheduleData = <?= json_encode($schedule) ?>;
const weekDays = <?= json_encode(array_column($weekDays, 'date')) ?>;

// 加载已有排班数据
document.addEventListener('DOMContentLoaded', function() {
  Object.keys(scheduleData).forEach(empId => {
    const empSchedule = scheduleData[empId];
    Object.keys(empSchedule.days || {}).forEach(date => {
      const shifts = empSchedule.days[date];
      const cell = document.querySelector(`[data-employee-id="${empId}"][data-date="${date}"]`);
      if (cell && shifts.length > 0) {
        // 如果有多个班次，显示第一个（实际应用中可能需要更复杂的显示逻辑）
        const shiftType = shifts[0].shift_type;
        updateCell(cell, shiftType === 'morning' ? 'morning' : (shiftType === 'evening' ? 'evening' : 'rest'));
      }
    });
  });
});

function toggleShift(cell) {
  if (cell.dataset.published === '1') return;
  
  const current = cell.dataset.shift || 'rest';
  const currentIndex = shiftCycle.indexOf(current);
  const nextIndex = (currentIndex + 1) % shiftCycle.length;
  const next = shiftCycle[nextIndex];
  
  updateCell(cell, next);
}

function updateCell(cell, shiftType) {
  cell.dataset.shift = shiftType;
  cell.className = 'shift-cell ' + shiftType;
  cell.querySelector('.shift-display').textContent = shiftLabels[shiftType];
}

function getScheduleData() {
  const data = {};
  document.querySelectorAll('.shift-cell').forEach(cell => {
    const empId = cell.dataset.employeeId;
    const date = cell.dataset.date;
    const shiftType = cell.dataset.shift || 'rest';
    
    if (shiftType !== 'rest') {
      if (!data[empId]) data[empId] = {};
      if (!data[empId][date]) data[empId][date] = [];
      data[empId][date].push(shiftType);
    }
  });
  return data;
}

function copyLastWeek() {
  if (confirm('<?= __('shift.copy_last_week') ?>?')) {
    const form = document.getElementById('weeklyScheduleForm');
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'copy_last_week';
    form.appendChild(actionInput);
    form.submit();
  }
}

function saveDraft() {
  const data = getScheduleData();
  document.getElementById('scheduleData').value = JSON.stringify(data);
  
  const form = document.getElementById('weeklyScheduleForm');
  const actionInput = document.createElement('input');
  actionInput.type = 'hidden';
  actionInput.name = 'action';
  actionInput.value = 'save_draft';
  form.appendChild(actionInput);
  form.submit();
}

function publishSchedule() {
  if (confirm('<?= __('shift.publish_confirm') ?>')) {
    const data = getScheduleData();
    document.getElementById('scheduleData').value = JSON.stringify(data);
    
    const form = document.getElementById('weeklyScheduleForm');
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'publish';
    form.appendChild(actionInput);
    form.submit();
  }
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
