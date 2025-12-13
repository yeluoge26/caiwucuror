<?php
$title = __('setting.categories');
include __DIR__ . '/../layout/header.php';
$lang = I18n::current();
?>

<h2 style="margin-bottom: 20px;"><?= __('setting.categories') ?></h2>

<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('setting.categories') ?></h3>
  
  <table>
    <tr>
      <th>ID</th>
      <th><?= __('tx.type') ?></th>
      <th><?= __('field.category') ?> (中文)</th>
      <th><?= __('field.category') ?> (Tiếng Việt)</th>
      <th><?= __('list.actions') ?></th>
    </tr>
    <?php foreach ($categories as $cat): ?>
    <tr>
      <td><?= $cat['id'] ?></td>
      <td>
        <span class="badge <?= $cat['type'] === 'income' ? 'badge-income' : ($cat['type'] === 'expense' ? 'badge-expense' : 'badge-approved') ?>">
          <?php
          if ($cat['type'] === 'income') echo __('tx.income');
          elseif ($cat['type'] === 'expense') echo __('tx.expense');
          else echo 'Both';
          ?>
        </span>
      </td>
      <td><?= htmlspecialchars($cat['name_zh']) ?></td>
      <td><?= htmlspecialchars($cat['name_vi']) ?></td>
      <td>
        <button onclick="editCategory(<?= htmlspecialchars(json_encode($cat)) ?>)" class="btn" style="padding: 4px 8px; font-size: 12px;">
          <?= __('btn.edit') ?>
        </button>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  
  <div style="margin-top: 20px;">
    <button onclick="showAddForm()" class="btn btn-success"><?= __('btn.save') ?> <?= __('field.category') ?></button>
  </div>
</div>

<!-- 添加/编辑表单 -->
<div class="card" id="category-form" style="display: none;">
  <h3 id="form-title"><?= __('btn.save') ?> <?= __('field.category') ?></h3>
  <form method="post" id="category-form-element">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <input type="hidden" name="action" id="form-action" value="create">
    <input type="hidden" name="id" id="form-id">
    
    <div class="form-group">
      <label><?= __('tx.type') ?></label>
      <select name="type" id="form-type" required>
        <option value="income"><?= __('tx.income') ?></option>
        <option value="expense"><?= __('tx.expense') ?></option>
        <option value="both">Both</option>
      </select>
    </div>
    
    <div class="form-group">
      <label><?= __('field.category') ?> (中文)</label>
      <input type="text" name="name_zh" id="form-name-zh" required>
    </div>
    
    <div class="form-group">
      <label><?= __('field.category') ?> (Tiếng Việt)</label>
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
  document.getElementById('category-form').style.display = 'block';
  document.getElementById('form-title').textContent = '<?= __('btn.save') ?> <?= __('field.category') ?>';
  document.getElementById('form-action').value = 'create';
  document.getElementById('form-id').value = '';
  document.getElementById('form-type').value = 'income';
  document.getElementById('form-name-zh').value = '';
  document.getElementById('form-name-vi').value = '';
}

function editCategory(cat) {
  document.getElementById('category-form').style.display = 'block';
  document.getElementById('form-title').textContent = '<?= __('btn.edit') ?> <?= __('field.category') ?>';
  document.getElementById('form-action').value = 'update';
  document.getElementById('form-id').value = cat.id;
  document.getElementById('form-type').value = cat.type;
  document.getElementById('form-name-zh').value = cat.name_zh;
  document.getElementById('form-name-vi').value = cat.name_vi;
}

function hideForm() {
  document.getElementById('category-form').style.display = 'none';
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>

