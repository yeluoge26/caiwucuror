<?php
require_once __DIR__ . '/../../core/Csrf.php';
require_once __DIR__ . '/../../core/Auth.php';

$title = __('shift.list');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('shift.list') ?></h2>

<div class="card">
  <form method="get" style="margin-bottom: 20px;">
    <input type="hidden" name="r" value="shifts/list">
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 12px;">
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('shift.shift_date') ?></label>
        <input type="date" name="shift_date" value="<?= htmlspecialchars($_GET['shift_date'] ?? '') ?>">
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('shift.from_date') ?></label>
        <input type="date" name="from_date" value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>">
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('shift.to_date') ?></label>
        <input type="date" name="to_date" value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>">
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('shift.shift_type') ?></label>
        <select name="shift_type">
          <option value=""><?= __('list.filter') ?></option>
          <option value="morning" <?= ($_GET['shift_type'] ?? '') === 'morning' ? 'selected' : '' ?>><?= __('shift.type_morning') ?></option>
          <option value="afternoon" <?= ($_GET['shift_type'] ?? '') === 'afternoon' ? 'selected' : '' ?>><?= __('shift.type_afternoon') ?></option>
          <option value="evening" <?= ($_GET['shift_type'] ?? '') === 'evening' ? 'selected' : '' ?>><?= __('shift.type_evening') ?></option>
        </select>
      </div>
      
      <div class="form-group" style="margin-bottom: 0;">
        <label><?= __('shift.is_confirmed') ?></label>
        <select name="is_confirmed">
          <option value=""><?= __('list.filter') ?></option>
          <option value="1" <?= ($_GET['is_confirmed'] ?? '') === '1' ? 'selected' : '' ?>><?= __('shift.confirmed') ?></option>
          <option value="0" <?= ($_GET['is_confirmed'] ?? '') === '0' ? 'selected' : '' ?>><?= __('shift.not_confirmed') ?></option>
        </select>
      </div>
    </div>
    
    <div>
      <button type="submit" class="btn"><?= __('btn.search') ?></button>
      <a href="/index.php?r=shifts/list" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success">
  <?php
  $lang = I18n::current();
  $success = intval($_GET['success'] ?? 0);
  $failed = intval($_GET['failed'] ?? 0);
  if ($lang === 'zh') {
    echo "排班完成：成功 {$success} 条，失败 {$failed} 条";
  } else {
    echo "Lập lịch hoàn tất: Thành công {$success} bản ghi, Thất bại {$failed} bản ghi";
  }
  ?>
</div>
<?php endif; ?>

<div class="card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
    <h3><?= __('shift.list') ?></h3>
    <div style="display: flex; gap: 8px;">
      <a href="/index.php?r=shifts/create" class="btn btn-success"><?= __('shift.create') ?></a>
      <a href="/index.php?r=shifts/schedule" class="btn"><?= __('shift.schedule') ?></a>
    </div>
  </div>
  <div class="table-scroll">
    <table>
      <tr>
        <th>ID</th>
        <th><?= __('shift.shift_date') ?></th>
        <th><?= __('shift.shift_type') ?></th>
        <th><?= __('shift.employee') ?></th>
        <th><?= __('shift.manager') ?></th>
        <th><?= __('shift.is_confirmed') ?></th>
        <th><?= __('shift.status') ?></th>
        <th><?= __('list.actions') ?></th>
      </tr>
      <?php if (empty($items)): ?>
      <tr>
        <td colspan="8" style="text-align: center; color: #999; padding: 40px;">
          <?= __('list.no_data') ?>
        </td>
      </tr>
      <?php else: ?>
      <?php
      $lang = I18n::current();
      foreach ($items as $row):
        $typeText = [
          'morning' => __('shift.type_morning'),
          'afternoon' => __('shift.type_afternoon'),
          'evening' => __('shift.type_evening')
        ];
      ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= date('Y-m-d', strtotime($row['shift_date'])) ?></td>
        <td><?= $typeText[$row['shift_type']] ?? $row['shift_type'] ?></td>
        <td><?= htmlspecialchars($row['employee_name'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['manager_name'] ?? '-') ?></td>
        <td>
          <?php if ($row['is_confirmed']): ?>
          <span class="badge badge-income"><?= __('shift.confirmed') ?></span>
          <?php else: ?>
          <span class="badge badge-pending"><?= __('shift.not_confirmed') ?></span>
          <?php endif; ?>
        </td>
        <td>
          <?php
          $currentStatus = $row['status'] ?? 'pending';
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
          $statusTexts = [
            'pending' => __('shift.status_pending'),
            'confirmed' => __('shift.status_confirmed'),
            'late' => __('shift.status_late'),
            'leave' => __('shift.status_leave'),
            'off' => __('shift.status_off'),
            'abnormal' => __('shift.status_abnormal')
          ];
          $statusIcon = $statusIcons[$currentStatus] ?? '⏳';
          $statusColor = $statusColors[$currentStatus] ?? '#666';
          $statusText = $statusTexts[$currentStatus] ?? __('shift.status_pending');
          ?>
          <div style="display: flex; align-items: center; gap: 6px;">
            <span style="font-size: 16px;"><?= $statusIcon ?></span>
            <span class="shift-status-<?= $row['id'] ?>" style="color: <?= $statusColor ?>; font-weight: 600;">
              <?= htmlspecialchars($statusText) ?>
            </span>
          </div>
        </td>
        <td>
          <?php
          // 判断是否已提交：状态不是pending且已确认（confirmed_by不为空）
          $confirmedBy = $row['confirmed_by'] ?? null;
          $isSubmitted = ($currentStatus !== 'pending' && !empty($confirmedBy));
          // PC页面只有老板可以修改已提交的状态
          $user = Auth::user();
          $isOwner = ($user['role_key'] ?? '') === 'owner';
          $canEdit = $isOwner || !$isSubmitted;
          ?>
          <div style="display: flex; gap: 4px; flex-wrap: wrap; align-items: center;">
            <select 
              id="shift-status-<?= $row['id'] ?>"
              name="shift_status_<?= $row['id'] ?>"
              class="shift-status-select" 
              data-shift-id="<?= $row['id'] ?>"
              data-original-status="<?= $currentStatus ?>"
              data-can-edit="<?= $canEdit ? '1' : '0' ?>"
              <?= !$canEdit ? 'disabled' : '' ?>
              style="padding: 4px 6px; font-size: 12px; border-radius: 4px; border: 1px solid #ddd; background: <?= $canEdit ? 'white' : '#f5f5f5' ?>; cursor: <?= $canEdit ? 'pointer' : 'not-allowed' ?>; min-width: 100px; <?= !$canEdit ? 'opacity: 0.6;' : '' ?>">
              <option value="pending" <?= $currentStatus === 'pending' ? 'selected' : '' ?>><?= __('shift.status_pending') ?></option>
              <option value="confirmed" <?= $currentStatus === 'confirmed' ? 'selected' : '' ?>><?= __('shift.status_confirmed') ?></option>
              <option value="late" <?= $currentStatus === 'late' ? 'selected' : '' ?>><?= __('shift.status_late') ?></option>
              <option value="leave" <?= $currentStatus === 'leave' ? 'selected' : '' ?>><?= __('shift.status_leave') ?></option>
              <option value="off" <?= $currentStatus === 'off' ? 'selected' : '' ?>><?= __('shift.status_off') ?></option>
              <option value="abnormal" <?= $currentStatus === 'abnormal' ? 'selected' : '' ?>><?= __('shift.status_abnormal') ?></option>
            </select>
            <?php if ($canEdit): ?>
            <button 
              type="button" 
              id="shift-submit-<?= $row['id'] ?>"
              class="shift-submit-btn" 
              data-shift-id="<?= $row['id'] ?>"
              aria-label="<?= __('shift.confirm_attendance') ?>"
              style="padding: 4px 8px; font-size: 12px; border-radius: 4px; border: none; cursor: pointer; background: #3498db; color: white;">
              <?= __('btn.submit') ?>
            </button>
            <?php else: ?>
            <span style="font-size: 11px; color: #999; padding: 4px 8px;">
              <?= __('shift.cannot_edit') ?>
            </span>
            <?php endif; ?>
            <a href="/index.php?r=shifts/edit&id=<?= $row['id'] ?>" 
               class="btn" style="padding: 4px 8px; font-size: 12px;">
              <?= __('btn.edit') ?>
            </a>
            <?php if ($row['manager_id']): ?>
            <a href="/index.php?r=shifts/removeManager&id=<?= $row['id'] ?>" 
               class="btn" style="padding: 4px 8px; font-size: 12px; background: #95a5a6;"
               onclick="return confirm('<?= __('shift.remove_manager_confirm') ?>')"
               title="<?= __('shift.remove_manager') ?>">
              <?= __('shift.remove_manager') ?>
            </a>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </table>
  </div>

  <?php if (isset($totalPages) && $totalPages > 1): ?>
  <div style="margin-top: 20px; display: flex; justify-content: center; align-items: center; gap: 8px; flex-wrap: wrap;">
    <?php
    $currentPage = $page ?? 1;
    $queryParams = $_GET;
    
    $buildPageUrl = function($pageNum) use ($queryParams) {
      $queryParams['page'] = $pageNum;
      $queryParams['r'] = 'shifts/list';
      return '/index.php?' . http_build_query($queryParams);
    };
    
    $lang = I18n::current();
    ?>
    
    <?php if ($currentPage > 1): ?>
      <a href="<?= htmlspecialchars($buildPageUrl(1)) ?>" class="btn" style="padding: 6px 12px;">
        <?= $lang === 'zh' ? '首页' : 'Đầu' ?>
      </a>
      <a href="<?= htmlspecialchars($buildPageUrl($currentPage - 1)) ?>" class="btn" style="padding: 6px 12px;">
        <?= $lang === 'zh' ? '上一页' : 'Trước' ?>
      </a>
    <?php endif; ?>
    
    <span style="padding: 6px 12px;">
      <?php
      if ($lang === 'zh') {
        echo "第 {$currentPage} 页 / 共 {$totalPages} 页 (共 {$total} 条)";
      } else {
        echo "Trang {$currentPage} / {$totalPages} (Tổng {$total} bản ghi)";
      }
      ?>
    </span>
    
    <?php if ($currentPage < $totalPages): ?>
      <a href="<?= htmlspecialchars($buildPageUrl($currentPage + 1)) ?>" class="btn" style="padding: 6px 12px;">
        <?= $lang === 'zh' ? '下一页' : 'Sau' ?>
      </a>
      <a href="<?= htmlspecialchars($buildPageUrl($totalPages)) ?>" class="btn" style="padding: 6px 12px;">
        <?= $lang === 'zh' ? '末页' : 'Cuối' ?>
      </a>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const statusSelects = document.querySelectorAll('.shift-status-select');
  const submitButtons = document.querySelectorAll('.shift-submit-btn');
  
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
    'pending': '<?= __('shift.status_pending') ?>',
    'confirmed': '<?= __('shift.status_confirmed') ?>',
    'late': '<?= __('shift.status_late') ?>',
    'leave': '<?= __('shift.status_leave') ?>',
    'off': '<?= __('shift.status_off') ?>',
    'abnormal': '<?= __('shift.status_abnormal') ?>'
  };
  
  // 提交按钮点击事件
  submitButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      const shiftId = this.getAttribute('data-shift-id');
      const select = document.querySelector('.shift-status-select[data-shift-id="' + shiftId + '"]');
      
      if (!select) {
        alert('<?= __('error.operation_failed') ?>');
        return;
      }
      
      // 检查是否可以编辑
      const canEdit = select.getAttribute('data-can-edit') === '1';
      if (!canEdit) {
        alert('<?= __('shift.cannot_edit') ?>');
        return;
      }
      
      const newStatus = select.value;
      const originalStatus = select.getAttribute('data-original-status') || select.value;
      
      // 如果状态没有变化，不需要提交
      if (newStatus === originalStatus) {
        return;
      }
      
      // 禁用按钮和选择框，防止重复提交
      this.disabled = true;
      select.disabled = true;
      const originalText = this.textContent;
      this.textContent = '<?= __('btn.processing') ?>';
      
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
          // 更新状态显示
          const statusSpan = document.querySelector('.shift-status-' + shiftId);
          if (statusSpan) {
            statusSpan.textContent = statusTexts[newStatus] || newStatus;
            statusSpan.style.color = statusColors[newStatus] || '#666';
            
            // 更新图标（如果存在）
            const iconSpan = statusSpan.previousElementSibling;
            if (iconSpan && iconSpan.tagName === 'SPAN') {
              iconSpan.textContent = statusIcons[newStatus] || '⏳';
            }
          }
          
          // 更新原始状态
          select.setAttribute('data-original-status', newStatus);
          
          // 如果不是老板，提交后禁用控件
          const isOwner = <?= $isOwner ? 'true' : 'false' ?>;
          if (!isOwner) {
            select.disabled = true;
            select.style.background = '#f5f5f5';
            select.style.cursor = 'not-allowed';
            select.style.opacity = '0.6';
            select.setAttribute('data-can-edit', '0');
            this.disabled = true;
            this.style.display = 'none';
            
            // 添加"已提交"提示
            const statusContainer = statusSpan.parentElement;
            if (statusContainer && !statusContainer.querySelector('.submitted-hint')) {
              const hint = document.createElement('span');
              hint.className = 'submitted-hint';
              hint.textContent = '(<?= __('shift.submitted') ?>)';
              hint.style.fontSize = '11px';
              hint.style.color = '#999';
              hint.style.marginLeft = '4px';
              statusContainer.appendChild(hint);
            }
          } else {
            // 老板可以继续编辑
            this.disabled = false;
            select.disabled = false;
            this.textContent = originalText;
          }
          
          // 刷新页面以更新统计
          setTimeout(() => {
            window.location.reload();
          }, 500);
        } else {
          alert('<?= __('error.operation_failed') ?>');
          select.value = originalStatus;
          this.disabled = false;
          select.disabled = false;
          this.textContent = originalText;
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('<?= __('error.operation_failed', '操作失败') ?>: ' + error.message);
        select.value = originalStatus;
        this.disabled = false;
        select.disabled = false;
        this.textContent = originalText;
      });
    });
  });
  
  // 保存原始状态
  statusSelects.forEach(select => {
    select.setAttribute('data-original-status', select.value);
  });
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>

