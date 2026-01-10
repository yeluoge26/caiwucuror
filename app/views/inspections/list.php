<?php
$title = __('nav.inspections');
include __DIR__ . '/../layout/header.php';

// 检查是否是老板角色
$isOwner = (Auth::user()['role_key'] ?? '') === 'owner';
$date = $_GET['date'] ?? date('Y-m-d');
?>

<h2 style="margin-bottom: 20px;"><?= __('nav.inspections') ?></h2>

<?php if ($isOwner): ?>
<div class="card" style="margin-bottom: 20px; background: #e8f5e9; border: 1px solid #4caf50;">
  <div style="display: flex; justify-content: space-between; align-items: center;">
    <div>
      <strong style="color: #2e7d32;"><?= __('inspection.batch_approve_title') ?></strong>
      <p style="margin: 8px 0 0 0; color: #555; font-size: 13px;">
        <?= __('inspection.batch_approve_hint') ?>
      </p>
    </div>
    <form method="post" action="/index.php?r=inspections/batchApprove&date=<?= urlencode($date) ?>" id="batchApproveForm" style="margin: 0;">
      <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
      <button type="submit" class="btn btn-success" id="batchApproveBtn" style="padding: 10px 20px;">
        <?= __('inspection.batch_approve_btn') ?>
      </button>
    </form>
  </div>
</div>
<?php endif; ?>

<?php 
// 显示成功或错误消息
if (!empty($_GET['msg'])): 
?>
<div class="alert alert-success" style="margin-bottom: 20px; padding: 12px; background: #d4edda; border: 1px solid #c3e6cb; color: #155724; border-radius: 4px;">
  <?= htmlspecialchars($_GET['msg']) ?>
</div>
<?php endif; ?>

<?php if (!empty($_GET['error'])): ?>
<div class="alert alert-error" style="margin-bottom: 20px; padding: 12px; background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 4px;">
  <?= htmlspecialchars($_GET['error']) ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/list_table.php'; ?>

<?php if ($isOwner): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('batchApproveForm');
  const btn = document.getElementById('batchApproveBtn');
  
  if (form && btn) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // 确认对话框
      if (!confirm('<?= __('inspection.batch_approve_confirm') ?>')) {
        return;
      }
      
      // 禁用按钮
      btn.disabled = true;
      btn.textContent = '<?= __('btn.processing') ?>';
      
      // 发送 AJAX 请求
      const formData = new FormData(form);
      const xhr = new XMLHttpRequest();
      xhr.open('POST', form.action, true);
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      
      xhr.onload = function() {
        if (xhr.status === 200) {
          try {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
              alert(response.message || '<?= __('inspection.batch_approve_success') ?>');
              // 刷新页面
              window.location.reload();
            } else {
              alert(response.message || '<?= __('inspection.batch_approve_failed') ?>');
              btn.disabled = false;
              btn.textContent = '<?= __('inspection.batch_approve_btn') ?>';
            }
          } catch (e) {
            // 如果不是 JSON 响应，可能是重定向，直接刷新页面
            window.location.reload();
          }
        } else {
          alert('<?= __('error.operation_failed') ?>');
          btn.disabled = false;
          btn.textContent = '<?= __('inspection.batch_approve_btn') ?>';
        }
      };
      
      xhr.onerror = function() {
        alert('<?= __('error.operation_failed') ?>');
        btn.disabled = false;
        btn.textContent = '<?= __('inspection.batch_approve_btn') ?>';
      };
      
      xhr.send(formData);
    });
  }
});
</script>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>
