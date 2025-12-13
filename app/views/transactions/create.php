<?php
$title = __('tx.create');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('tx.create') ?></h2>

<div class="card">
  <?php if (isset($error)): ?>
  <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  
  <form method="post">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    
    <div class="form-group">
      <label><?= __('tx.type') ?></label>
      <select name="type" id="type-select" required>
        <option value="income"><?= __('tx.income') ?></option>
        <option value="expense"><?= __('tx.expense') ?></option>
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
      <select name="category_id" required>
        <option value="">-- <?= __('field.category') ?> --</option>
        <?php
        $lang = I18n::current();
        foreach ($categories as $cat):
          if ($cat['type'] === 'both' || $cat['type'] === ($_POST['type'] ?? 'income')):
            $name = $lang === 'zh' ? $cat['name_zh'] : $cat['name_vi'];
        ?>
        <option value="<?= $cat['id'] ?>" 
                data-type="<?= $cat['type'] ?>"
                <?= ($_POST['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($name) ?>
        </option>
        <?php endif; endforeach; ?>
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
    
    <div class="form-group">
      <label><?= __('field.note') ?></label>
      <textarea name="note"><?= htmlspecialchars($_POST['note'] ?? '') ?></textarea>
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
  const categorySelect = document.querySelector('select[name="category_id"]');
  const vendorGroup = document.getElementById('vendor-group');
  
  // 显示/隐藏供应商字段
  if (type === 'expense') {
    vendorGroup.style.display = 'block';
  } else {
    vendorGroup.style.display = 'none';
  }
  
  // 过滤分类选项
  Array.from(categorySelect.options).forEach(option => {
    if (option.value === '') return;
    const catType = option.dataset.type;
    if (catType === 'both' || catType === type) {
      option.style.display = '';
    } else {
      option.style.display = 'none';
    }
  });
});

// 初始化
document.getElementById('type-select').dispatchEvent(new Event('change'));
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>

