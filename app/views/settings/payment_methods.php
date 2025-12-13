<?php
$title = __('setting.payment_methods');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('setting.payment_methods') ?></h2>

<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('setting.payment_methods') ?></h3>
  
  <table>
    <tr>
      <th>ID</th>
      <th><?= __('field.payment') ?> (中文)</th>
      <th><?= __('field.payment') ?> (Tiếng Việt)</th>
      <th><?= __('list.actions') ?></th>
    </tr>
    <?php foreach ($paymentMethods as $pm): ?>
    <tr>
      <td><?= $pm['id'] ?></td>
      <td><?= htmlspecialchars($pm['name_zh']) ?></td>
      <td><?= htmlspecialchars($pm['name_vi']) ?></td>
      <td>
        <button onclick="editPaymentMethod(<?= htmlspecialchars(json_encode($pm)) ?>)" class="btn" style="padding: 4px 8px; font-size: 12px;">
          <?= __('btn.edit') ?>
        </button>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  
  <div style="margin-top: 20px;">
    <button onclick="showAddForm()" class="btn btn-success"><?= __('btn.save') ?> <?= __('field.payment') ?></button>
  </div>
</div>

<!-- 添加/编辑表单 -->
<div class="card" id="payment-form" style="display: none;">
  <h3 id="form-title"><?= __('btn.save') ?> <?= __('field.payment') ?></h3>
  <form method="post" id="payment-form-element">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <input type="hidden" name="action" id="form-action" value="create">
    <input type="hidden" name="id" id="form-id">
    
    <div class="form-group">
      <label><?= __('field.payment') ?> (中文)</label>
      <input type="text" name="name_zh" id="form-name-zh" required>
    </div>
    
    <div class="form-group">
      <label><?= __('field.payment') ?> (Tiếng Việt)</label>
      <input type="text" name="name_vi" id="form-name-vi" required>
    </div>
    
    <div class="form-group">
      <button type="submit" class="btn btn-success"><?= __('btn.save') ?></button>
      <button type="button" onclick="hideForm()" class="btn"><?= __('btn.cancel') ?></button>
    </div>
  </form>
</div>

<script>
function showAddForm() {
  document.getElementById('payment-form').style.display = 'block';
  document.getElementById('form-title').textContent = '<?= __('btn.save') ?> <?= __('field.payment') ?>';
  document.getElementById('form-action').value = 'create';
  document.getElementById('form-id').value = '';
  document.getElementById('form-name-zh').value = '';
  document.getElementById('form-name-vi').value = '';
}

function editPaymentMethod(pm) {
  document.getElementById('payment-form').style.display = 'block';
  document.getElementById('form-title').textContent = '<?= __('btn.edit') ?> <?= __('field.payment') ?>';
  document.getElementById('form-action').value = 'update';
  document.getElementById('form-id').value = pm.id;
  document.getElementById('form-name-zh').value = pm.name_zh;
  document.getElementById('form-name-vi').value = pm.name_vi;
}

function hideForm() {
  document.getElementById('payment-form').style.display = 'none';
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>

