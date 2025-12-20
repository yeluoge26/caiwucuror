<?php
$title = __('tx.create');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('tx.create') ?></h2>

<div class="card">
  <?php if (isset($error)): ?>
  <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    
    <div class="form-group">
      <label><?= __('tx.type') ?></label>
      <?php $selectedType = $_POST['type'] ?? 'income'; ?>
      <select name="type" id="type-select" required>
        <option value="income" <?= $selectedType === 'income' ? 'selected' : '' ?>><?= __('tx.income') ?></option>
        <option value="expense" <?= $selectedType === 'expense' ? 'selected' : '' ?>><?= __('tx.expense') ?></option>
      </select>
    </div>
    
    <div class="form-group">
      <label><?= __('field.amount') ?> (₫)</label>
      <input type="number" name="amount" step="0.01" min="0" required>
    </div>
    
    <div class="form-group">
      <label><?= __('field.currency') ?></label>
      <select name="currency">
        <option value="VND">VND</option>
        <option value="USD">USD</option>
        <option value="CNY">CNY</option>
      </select>
    </div>
    
    <div class="form-group">
      <label><?= __('field.category') ?></label>
      <select name="category_id" id="category-select" required>
        <option value="">-- <?= __('field.category') ?> --</option>
        <?php
        $lang = I18n::current();
        foreach ($categories as $cat):
          $name = $lang === 'zh' ? $cat['name_zh'] : $cat['name_vi'];
        ?>
        <option value="<?= $cat['id'] ?>" 
                data-type="<?= $cat['type'] ?>"
                <?= ($_POST['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($name) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    
    <div class="form-group">
      <label><?= __('field.payment') ?></label>
      <select name="payment_method_id" required>
        <option value="">-- <?= __('field.payment') ?> --</option>
        <?php
        foreach ($paymentMethods as $pm):
          $name = $lang === 'zh' ? $pm['name_zh'] : $pm['name_vi'];
        ?>
        <option value="<?= $pm['id'] ?>" <?= ($_POST['payment_method_id'] ?? '') == $pm['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($name) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    
    <div class="form-group" id="vendor-group" style="display: none;">
      <label><?= __('field.vendor') ?></label>
      <select name="vendor_id">
        <option value="">-- <?= __('field.vendor') ?> --</option>
        <?php foreach ($vendors as $vendor): ?>
        <option value="<?= $vendor['id'] ?>" <?= ($_POST['vendor_id'] ?? '') == $vendor['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($vendor['name']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    
    <div class="form-group">
      <label><?= __('field.time') ?></label>
      <input type="datetime-local" name="occurred_at" 
             value="<?= $_POST['occurred_at'] ?? date('Y-m-d\TH:i') ?>" required>
    </div>

    <div class="form-group" id="payer-group" style="display: none;">
      <label><?= __('field.payer') ?></label>
      <input type="text" name="payer" id="payer-input" value="<?= htmlspecialchars($_POST['payer'] ?? Auth::user()['display_name'] ?? '') ?>">
    </div>
    
    <div class="form-group">
      <label><?= __('field.note') ?></label>
      <textarea name="note"><?= htmlspecialchars($_POST['note'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
      <label><?= __('attachment.upload') ?></label>
      <input type="file" name="attachments[]" accept="image/*" multiple>
      <div style="font-size: 12px; color: #666; margin-top: 6px;">
        <?= __('attachment.upload_hint') ?>
      </div>
    </div>
    
    <div class="form-group">
      <button type="submit" class="btn btn-success"><?= __('btn.save') ?></button>
      <a href="/index.php?r=transactions/list" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<script>
document.getElementById('type-select').addEventListener('change', function() {
  const type = this.value;
  const categorySelect = document.getElementById('category-select');
  const vendorGroup = document.getElementById('vendor-group');
  const payerGroup = document.getElementById('payer-group');
  
  // 显示/隐藏供应商字段
  if (type === 'expense') {
    vendorGroup.style.display = 'block';
    payerGroup.style.display = 'block';
    // 如果付款人字段为空，设置为当前登录用户
    const payerInput = payerGroup.querySelector('input[name="payer"]');
    if (payerInput && !payerInput.value.trim()) {
      payerInput.value = '<?= htmlspecialchars(Auth::user()["display_name"] ?? "", ENT_QUOTES) ?>';
    }
  } else {
    vendorGroup.style.display = 'none';
    payerGroup.style.display = 'none';
    // 清除供应商选择
    const vendorSelect = vendorGroup.querySelector('select[name="vendor_id"]');
    if (vendorSelect) vendorSelect.value = '';
    const payerInput = payerGroup.querySelector('input[name="payer"]');
    if (payerInput) payerInput.value = '';
  }
  
  // 保存当前选中的分类ID
  const currentCategoryId = categorySelect.value;
  
  // 过滤分类选项
  let hasVisibleOption = false;
  Array.from(categorySelect.options).forEach(option => {
    if (option.value === '') {
      option.style.display = '';
      return;
    }
    const catType = option.dataset.type;
    if (catType === 'both' || catType === type) {
      option.style.display = '';
      hasVisibleOption = true;
    } else {
      option.style.display = 'none';
    }
  });
  
  // 如果当前选中的分类不在可见选项中，重置选择
  if (currentCategoryId) {
    const selectedOption = categorySelect.querySelector(`option[value="${currentCategoryId}"]`);
    if (selectedOption && selectedOption.style.display === 'none') {
      categorySelect.value = '';
    }
  }
});

// 初始化
document.getElementById('type-select').dispatchEvent(new Event('change'));
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
