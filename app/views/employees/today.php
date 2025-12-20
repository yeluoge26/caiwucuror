<?php
require_once __DIR__ . '/../../core/Csrf.php';

$title = __('employee.today_onduty', '今日在岗员工');
$show_back = true;
include __DIR__ . '/../layout/h5_header.php';

$currentLang = I18n::current();
?>

<!-- 今日在岗统计 -->
<div class="h5-card" style="background: #f0f7ff; border: 1px solid #3498db;">
  <div style="text-align: center;">
    <div style="font-size: 14px; color: #6b7280; margin-bottom: 4px;">👥 <?= __('employee.today_onduty', '今日在岗员工') ?></div>
    <div style="font-size: 24px; font-weight: bold; color: #3498db;">
      <?= count($employeesOnDuty) ?> <?= __('employee.people', '人') ?>
    </div>
  </div>
</div>

<?php if (empty($employeesOnDuty)): ?>
<div class="h5-card">
  <div style="text-align: center; padding: 40px 20px; color: #999;">
    <div style="font-size: 48px; margin-bottom: 16px;">👥</div>
    <div><?= __('employee.no_onduty_today', '今日暂无在岗员工') ?></div>
  </div>
</div>
<?php else: ?>
<?php foreach ($employeesOnDuty as $item): 
  $employee = $item['employee'];
  $shifts = $item['shifts'];
  $confirmedCount = $item['confirmed_count'];
  $totalCount = $item['total_count'];
?>
<div class="h5-card">
  <div style="display: flex; align-items: center; margin-bottom: 12px;">
    <div style="width: 48px; height: 48px; border-radius: 50%; background: #3498db; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; font-weight: bold; margin-right: 12px;">
      <?= mb_substr($employee['name'], 0, 1, 'UTF-8') ?>
    </div>
    <div style="flex: 1;">
      <div style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 4px;">
        <?= htmlspecialchars($employee['name']) ?>
      </div>
      <div style="font-size: 13px; color: #6b7280;">
        <?php
        if ($currentLang === 'zh') {
          echo htmlspecialchars($employee['role_name_zh'] ?? '');
        } else {
          echo htmlspecialchars($employee['role_name_vi'] ?? '');
        }
        ?>
      </div>
    </div>
    <div style="text-align: right;">
      <?php if ($confirmedCount === $totalCount && $totalCount > 0): ?>
        <span style="color: #27ae60; font-size: 20px;">✅</span>
      <?php elseif ($confirmedCount > 0): ?>
        <span style="color: #f39c12; font-size: 20px;">🟡</span>
      <?php else: ?>
        <span style="color: #e74c3c; font-size: 20px;">⏳</span>
      <?php endif; ?>
    </div>
  </div>
  
  <div style="padding-top: 12px; border-top: 1px solid #eee;">
    <div style="margin-bottom: 12px;">
      <div style="font-size: 13px; color: #6b7280; margin-bottom: 8px;"><?= __('employee.shifts', '班次') ?>:</div>
      <div style="display: flex; flex-direction: column; gap: 8px;">
        <?php foreach ($shifts as $shift): 
          $type = $shift['shift_type'];
          $currentStatus = $shift['status'] ?? 'pending';
          $shiftId = $shift['id'];
          
          $typeText = '';
          if ($type === 'morning') {
            $typeText = __('shift.morning', '早班');
          } elseif ($type === 'afternoon') {
            $typeText = __('shift.afternoon', '中班');
          } elseif ($type === 'evening') {
            $typeText = __('shift.evening', '晚班');
          }
          
          // 状态图标和颜色
          $statusIcons = [
            'pending' => '⏳',
            'confirmed' => '✅',
            'late' => '⏰',
            'leave' => '📝',
            'off' => '🏖️',
            'abnormal' => '⚠️'
          ];
          $statusColors = [
            'pending' => '#e74c3c',
            'confirmed' => '#27ae60',
            'late' => '#f39c12',
            'leave' => '#3498db',
            'off' => '#9b59b6',
            'abnormal' => '#e67e22'
          ];
          $statusIcon = $statusIcons[$currentStatus] ?? '⏳';
          $statusColor = $statusColors[$currentStatus] ?? '#666';
        ?>
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px; background: #f9fafb; border-radius: 6px; gap: 8px;">
          <div style="display: flex; align-items: center; gap: 8px; flex: 1;">
            <span class="shift-icon-<?= $shiftId ?>" style="font-size: 16px;"><?= $statusIcon ?></span>
            <span style="font-size: 13px; font-weight: 600;"><?= htmlspecialchars($typeText) ?></span>
            <span class="shift-status-<?= $shiftId ?>" style="font-size: 12px; color: <?= $statusColor ?>; font-weight: 600;">
              <?php
              $statusTexts = [
                'pending' => __('shift.status_pending', '未确认'),
                'confirmed' => __('shift.status_confirmed', '已到岗'),
                'late' => __('shift.status_late', '迟到'),
                'leave' => __('shift.status_leave', '请假'),
                'off' => __('shift.status_off', '调休'),
                'abnormal' => __('shift.status_abnormal', '打卡异常')
              ];
              echo htmlspecialchars($statusTexts[$currentStatus] ?? $currentStatus);
              ?>
            </span>
          </div>
          <select 
            class="shift-status-select" 
            data-shift-id="<?= $shiftId ?>"
            style="padding: 6px 8px; font-size: 12px; border-radius: 6px; border: 1px solid #ddd; background: white; cursor: pointer; min-width: 100px;">
            <option value="pending" <?= $currentStatus === 'pending' ? 'selected' : '' ?>><?= __('shift.status_pending', '未确认') ?></option>
            <option value="confirmed" <?= $currentStatus === 'confirmed' ? 'selected' : '' ?>><?= __('shift.status_confirmed', '已到岗') ?></option>
            <option value="late" <?= $currentStatus === 'late' ? 'selected' : '' ?>><?= __('shift.status_late', '迟到') ?></option>
            <option value="leave" <?= $currentStatus === 'leave' ? 'selected' : '' ?>><?= __('shift.status_leave', '请假') ?></option>
            <option value="off" <?= $currentStatus === 'off' ? 'selected' : '' ?>><?= __('shift.status_off', '调休') ?></option>
            <option value="abnormal" <?= $currentStatus === 'abnormal' ? 'selected' : '' ?>><?= __('shift.status_abnormal', '打卡异常') ?></option>
          </select>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    
    <?php 
    $employeePhone = $employee['phone'] ?? ($shifts[0]['phone'] ?? null);
    if (!empty($employeePhone)): 
    ?>
    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
      <span style="font-size: 13px; color: #6b7280;"><?= __('employee.phone', '电话') ?>:</span>
      <span style="font-size: 13px;">
        <a href="tel:<?= htmlspecialchars($employeePhone) ?>" style="color: #3498db; text-decoration: none;">
          <?= htmlspecialchars($employeePhone) ?>
        </a>
      </span>
    </div>
    <?php endif; ?>
    
    <div style="display: flex; justify-content: space-between;">
      <span style="font-size: 13px; color: #6b7280;"><?= __('employee.status', '状态') ?>:</span>
      <span style="font-size: 13px; font-weight: 600;">
        <?php
        if ($confirmedCount === $totalCount && $totalCount > 0) {
          echo '<span style="color: #27ae60;">' . __('employee.all_confirmed', '全部已到岗') . '</span>';
        } elseif ($confirmedCount > 0) {
          echo '<span style="color: #f39c12;">' . sprintf(__('employee.partial_confirmed', '部分到岗 (%d/%d)'), $confirmedCount, $totalCount) . '</span>';
        } else {
          echo '<span style="color: #e74c3c;">' . __('employee.not_confirmed', '未到岗') . '</span>';
        }
        ?>
      </span>
    </div>
  </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const statusSelects = document.querySelectorAll('.shift-status-select');
  
  const statusIcons = {
    'pending': '⏳',
    'confirmed': '✅',
    'late': '⏰',
    'leave': '📝',
    'off': '🏖️',
    'abnormal': '⚠️'
  };
  
  const statusColors = {
    'pending': '#e74c3c',
    'confirmed': '#27ae60',
    'late': '#f39c12',
    'leave': '#3498db',
    'off': '#9b59b6',
    'abnormal': '#e67e22'
  };
  
  const statusTexts = {
    'pending': '<?= __('shift.status_pending', '未确认') ?>',
    'confirmed': '<?= __('shift.status_confirmed', '已到岗') ?>',
    'late': '<?= __('shift.status_late', '迟到') ?>',
    'leave': '<?= __('shift.status_leave', '请假') ?>',
    'off': '<?= __('shift.status_off', '调休') ?>',
    'abnormal': '<?= __('shift.status_abnormal', '打卡异常') ?>'
  };
  
  statusSelects.forEach(select => {
    select.addEventListener('change', function() {
      const shiftId = this.getAttribute('data-shift-id');
      const newStatus = this.value;
      const originalValue = this.getAttribute('data-original-status') || this.value;
      
      // 禁用选择框，防止重复提交
      this.disabled = true;
      const originalText = this.options[this.selectedIndex].text;
      
      // 创建 FormData
      const formData = new FormData();
      formData.append('_csrf', '<?= Csrf::token() ?>');
      formData.append('shift_id', shiftId);
      formData.append('status', newStatus);
      
      // 发送请求
      fetch('/index.php?r=employees/confirmShift', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // 更新图标
          const iconSpan = document.querySelector('.shift-icon-' + shiftId);
          if (iconSpan) {
            iconSpan.textContent = statusIcons[newStatus] || '⏳';
          }
          
          // 更新状态文本
          const statusSpan = document.querySelector('.shift-status-' + shiftId);
          if (statusSpan) {
            statusSpan.textContent = statusTexts[newStatus] || newStatus;
            statusSpan.style.color = statusColors[newStatus] || '#666';
          }
          
          // 更新原始状态
          this.setAttribute('data-original-status', newStatus);
          
          // 刷新页面以更新统计
          setTimeout(() => {
            window.location.reload();
          }, 500);
        } else {
          alert('<?= __('error.operation_failed', '操作失败') ?>');
          this.value = originalValue;
          this.disabled = false;
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('<?= __('error.operation_failed', '操作失败') ?>: ' + error.message);
        this.value = originalValue;
        this.disabled = false;
      });
    });
    
    // 保存原始状态
    select.setAttribute('data-original-status', select.value);
  });
});
</script>

<?php include __DIR__ . '/../layout/h5_footer.php'; ?>

