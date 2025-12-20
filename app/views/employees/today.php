<?php
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
    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
      <span style="font-size: 13px; color: #6b7280;"><?= __('employee.shifts', '班次') ?>:</span>
      <span style="font-size: 13px; font-weight: 600;">
        <?php
        $shiftTypes = [];
        foreach ($shifts as $shift) {
          $type = $shift['shift_type'];
          $confirmed = $shift['is_confirmed'] ? '✅' : '⏳';
          if ($type === 'morning') {
            $shiftTypes[] = $confirmed . ' ' . __('shift.morning', '早班');
          } elseif ($type === 'afternoon') {
            $shiftTypes[] = $confirmed . ' ' . __('shift.afternoon', '中班');
          } elseif ($type === 'evening') {
            $shiftTypes[] = $confirmed . ' ' . __('shift.evening', '晚班');
          }
        }
        echo implode(' / ', $shiftTypes);
        ?>
      </span>
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

<?php include __DIR__ . '/../layout/h5_footer.php'; ?>

