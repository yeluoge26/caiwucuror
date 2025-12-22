<?php
$title = __('setting.vendors');
include __DIR__ . '/../layout/header.php';
$current = $_GET['r'] ?? '';
?>

<h2 style="margin-bottom: 20px;"><?= __('setting.vendors') ?></h2>

<?php if (isset($error) && $error): ?>
<div class="alert alert-error" style="margin-bottom: 16px;">
  <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<?php if (isset($success) && $success): ?>
<div class="alert alert-success" style="margin-bottom: 16px;">
  <?= htmlspecialchars($success) ?>
</div>
<?php endif; ?>

<div class="card" style="padding: 12px; margin-bottom: 16px;">
  <div style="display: flex; gap: 8px; flex-wrap: wrap;">
    <a class="btn <?= $current === 'settings/categories' ? 'btn-success' : '' ?>" href="/index.php?r=settings/categories"><?= __('setting.categories') ?></a>
    <a class="btn <?= $current === 'settings/paymentMethods' ? 'btn-success' : '' ?>" href="/index.php?r=settings/paymentMethods"><?= __('setting.payment_methods') ?></a>
    <a class="btn <?= $current === 'settings/vendors' ? 'btn-success' : '' ?>" href="/index.php?r=settings/vendors"><?= __('setting.vendors') ?></a>
    <?php if (Auth::user()['role_key'] === 'owner'): ?>
    <a class="btn <?= $current === 'settings/users' ? 'btn-success' : '' ?>" href="/index.php?r=settings/users"><?= __('setting.users') ?></a>
    <?php endif; ?>
  </div>
</div>

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
        <form method="post" style="display:inline-block; margin-left:6px;" onsubmit="return confirm('<?= __('btn.delete') ?>?');">
          <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= $vendor['id'] ?>">
          <button type="submit" class="btn btn-danger" style="padding: 4px 8px; font-size: 12px;"><?= __('btn.delete') ?></button>
        </form>
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
