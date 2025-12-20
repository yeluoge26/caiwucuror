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
          $isConfirmed = $shift['is_confirmed'];
          $shiftId = $shift['id'];
          
          $typeText = '';
          if ($type === 'morning') {
            $typeText = __('shift.morning', '早班');
          } elseif ($type === 'afternoon') {
            $typeText = __('shift.afternoon', '中班');
          } elseif ($type === 'evening') {
            $typeText = __('shift.evening', '晚班');
          }
        ?>
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px; background: #f9fafb; border-radius: 6px;">
          <div style="display: flex; align-items: center; gap: 8px;">
            <span class="shift-icon-<?= $shiftId ?>" style="font-size: 16px;"><?= $isConfirmed ? '✅' : '⏳' ?></span>
            <span style="font-size: 13px; font-weight: 600;"><?= htmlspecialchars($typeText) ?></span>
          </div>
          <button 
            type="button" 
            class="shift-confirm-btn" 
            data-shift-id="<?= $shiftId ?>"
            data-confirmed="<?= $isConfirmed ? '1' : '0' ?>"
            style="padding: 6px 12px; font-size: 12px; border-radius: 6px; border: none; cursor: pointer; <?= $isConfirmed ? 'background: #27ae60; color: white;' : 'background: #3498db; color: white;' ?>">
            <?= $isConfirmed ? __('shift.cancel_confirm', '取消确认') : __('shift.confirm_attendance', '确认到岗') ?>
          </button>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    
    <?php if (!empty($employee['phone'])): ?>
    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
      <span style="font-size: 13px; color: #6b7280;"><?= __('employee.phone', '电话') ?>:</span>
      <span style="font-size: 13px;">
        <a href="tel:<?= htmlspecialchars($employee['phone']) ?>" style="color: #3498db; text-decoration: none;">
          <?= htmlspecialchars($employee['phone']) ?>
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
  const confirmButtons = document.querySelectorAll('.shift-confirm-btn');
  
  confirmButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      const shiftId = this.getAttribute('data-shift-id');
      const currentConfirmed = this.getAttribute('data-confirmed') === '1';
      const newConfirmed = !currentConfirmed;
      
      // 禁用按钮，防止重复点击
      this.disabled = true;
      const originalText = this.textContent;
      this.textContent = '<?= __('btn.processing', '处理中...') ?>';
      
      // 创建 FormData
      const formData = new FormData();
      formData.append('_csrf', '<?= Csrf::token() ?>');
      formData.append('shift_id', shiftId);
      formData.append('confirmed', newConfirmed ? '1' : '0');
      
      // 发送请求
      fetch('/index.php?r=employees/confirmShift', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // 更新按钮状态
          this.setAttribute('data-confirmed', newConfirmed ? '1' : '0');
          this.textContent = newConfirmed ? '<?= __('shift.cancel_confirm', '取消确认') ?>' : '<?= __('shift.confirm_attendance', '确认到岗') ?>';
          this.style.background = newConfirmed ? '#27ae60' : '#3498db';
          
          // 更新图标
          const iconSpan = this.closest('.h5-card').querySelector('.shift-icon-' + shiftId);
          if (iconSpan) {
            iconSpan.textContent = newConfirmed ? '✅' : '⏳';
          }
          
          // 刷新页面以更新统计
          setTimeout(() => {
            window.location.reload();
          }, 500);
        } else {
          alert('<?= __('error.operation_failed', '操作失败') ?>');
          this.disabled = false;
          this.textContent = originalText;
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('<?= __('error.operation_failed', '操作失败') ?>: ' + error.message);
        this.disabled = false;
        this.textContent = originalText;
      });
    });
  });
});
</script>

<?php include __DIR__ . '/../layout/h5_footer.php'; ?>

