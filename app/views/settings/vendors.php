<?php
$title = __('setting.vendors');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('setting.vendors') ?></h2>

<?php if (isset($success)): ?>
<div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('setting.vendors') ?></h3>
  
  <table>
    <tr>
      <th>ID</th>
      <th><?= __('field.vendor') ?></th>
      <th>Phone</th>
      <th><?= __('field.note') ?></th>
      <th><?= __('list.actions') ?></th>
    </tr>
    <?php foreach ($vendors as $vendor): ?>
    <tr>
      <td><?= $vendor['id'] ?></td>
      <td><?= htmlspecialchars($vendor['name']) ?></td>
      <td><?= htmlspecialchars($vendor['phone'] ?? '-') ?></td>
      <td><?= htmlspecialchars($vendor['note'] ?? '-') ?></td>
      <td>
        <button onclick="editVendor(<?= htmlspecialchars(json_encode($vendor)) ?>)" class="btn" style="padding: 4px 8px; font-size: 12px;">
          <?= __('btn.edit') ?>
        </button>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  
  <div style="margin-top: 20px;">
    <button onclick="showAddForm()" class="btn btn-success"><?= __('btn.save') ?> <?= __('field.vendor') ?></button>
  </div>
</div>

<!-- 添加/编辑表单 -->
<div class="card" id="vendor-form" style="display: none;">
  <h3 id="form-title"><?= __('btn.save') ?> <?= __('field.vendor') ?></h3>
  <form method="post" id="vendor-form-element">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <input type="hidden" name="action" id="form-action" value="create">
    <input type="hidden" name="id" id="form-id">
    
    <div class="form-group">
      <label><?= __('field.vendor') ?></label>
      <input type="text" name="name" id="form-name" required>
    </div>
    
    <div class="form-group">
      <label>Phone</label>
      <input type="text" name="phone" id="form-phone">
    </div>
    
    <div class="form-group">
      <label><?= __('field.note') ?></label>
      <textarea name="note" id="form-note"></textarea>
    </div>
    
    <div class="form-group">
      <button type="submit" class="btn btn-success"><?= __('btn.save') ?></button>
      <button type="button" onclick="hideForm()" class="btn"><?= __('btn.cancel') ?></button>
    </div>
  </form>
</div>

<script>
function showAddForm() {
  document.getElementById('vendor-form').style.display = 'block';
  document.getElementById('form-title').textContent = '<?= __('btn.save') ?> <?= __('field.vendor') ?>';
  document.getElementById('form-action').value = 'create';
  document.getElementById('form-id').value = '';
  document.getElementById('form-name').value = '';
  document.getElementById('form-phone').value = '';
  document.getElementById('form-note').value = '';
}

function editVendor(vendor) {
  document.getElementById('vendor-form').style.display = 'block';
  document.getElementById('form-title').textContent = '<?= __('btn.edit') ?> <?= __('field.vendor') ?>';
  document.getElementById('form-action').value = 'update';
  document.getElementById('form-id').value = vendor.id;
  document.getElementById('form-name').value = vendor.name;
  document.getElementById('form-phone').value = vendor.phone || '';
  document.getElementById('form-note').value = vendor.note || '';
}

function hideForm() {
  document.getElementById('vendor-form').style.display = 'none';
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>

