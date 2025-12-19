<?php
$title = __('btn.edit');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('btn.edit') ?> <?= __('tx.create') ?></h2>

<div class="card">
  <?php if (isset($error)): ?>
  <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  
  <form method="post" enctype="multipart/form-data" id="transaction-form">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    
    <div class="form-group">
      <label><?= __('tx.type') ?></label>
      <select name="type" id="type-select" required>
        <option value="income" <?= $transaction['type'] === 'income' ? 'selected' : '' ?>><?= __('tx.income') ?></option>
        <option value="expense" <?= $transaction['type'] === 'expense' ? 'selected' : '' ?>><?= __('tx.expense') ?></option>
      </select>
    </div>
    
    <div class="form-group">
      <label><?= __('field.amount') ?> (₫)</label>
      <input type="number" name="amount" id="amount-input" step="0.01" min="0.01" value="<?= htmlspecialchars($transaction['amount']) ?>" required>
      <small style="color: #666; display: block; margin-top: 4px;"><?= __('tx.amount_hint') ?></small>
    </div>
    
    <div class="form-group">
      <label><?= __('field.currency') ?></label>
      <select name="currency">
        <option value="VND" <?= $transaction['currency'] === 'VND' ? 'selected' : '' ?>>VND</option>
        <option value="USD" <?= $transaction['currency'] === 'USD' ? 'selected' : '' ?>>USD</option>
        <option value="CNY" <?= $transaction['currency'] === 'CNY' ? 'selected' : '' ?>>CNY</option>
      </select>
    </div>
    
    <div class="form-group">
      <label><?= __('field.category') ?></label>
      <select name="category_id" required>
        <option value="">-- <?= __('field.category') ?> --</option>
        <?php
        $lang = I18n::current();
        foreach ($categories as $cat):
          if ($cat['type'] === 'both' || $cat['type'] === $transaction['type']):
            $name = $lang === 'zh' ? $cat['name_zh'] : $cat['name_vi'];
        ?>
        <option value="<?= $cat['id'] ?>" 
                data-type="<?= $cat['type'] ?>"
                <?= $transaction['category_id'] == $cat['id'] ? 'selected' : '' ?>>
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
        <option value="<?= $pm['id'] ?>" <?= $transaction['payment_method_id'] == $pm['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($name) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    
    <div class="form-group" id="vendor-group" style="display: <?= $transaction['type'] === 'expense' ? 'block' : 'none' ?>;">
      <label><?= __('field.vendor') ?></label>
      <div style="display: flex; gap: 8px; align-items: flex-end;">
        <div style="flex: 1;">
          <select name="vendor_id" id="vendor-select">
            <option value="">-- <?= __('field.vendor') ?> --</option>
            <?php foreach ($vendors as $vendor): ?>
            <option value="<?= $vendor['id'] ?>" <?= ($transaction['vendor_id'] ?? null) == $vendor['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($vendor['name']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="button" onclick="showAddVendorModal()" class="btn" style="background: #27ae60; white-space: nowrap;">
          + <?= __('btn.add_vendor') ?>
        </button>
      </div>
    </div>
    
    <!-- 快速添加供应商模态框 -->
    <div id="vendor-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
      <div class="card" style="max-width: 500px; width: 90%; margin: 20px;">
        <h3 style="margin-bottom: 16px;"><?= __('btn.add_vendor') ?></h3>
        <form id="quick-vendor-form" onsubmit="return addVendorQuick(event)">
          <div class="form-group">
            <label><?= __('field.vendor') ?> *</label>
            <input type="text" id="quick-vendor-name" required>
          </div>
          <div class="form-group">
            <label>Phone</label>
            <input type="text" id="quick-vendor-phone">
          </div>
          <div class="form-group">
            <label><?= __('field.note') ?></label>
            <textarea id="quick-vendor-note" rows="3"></textarea>
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-success"><?= __('btn.save') ?></button>
            <button type="button" onclick="hideAddVendorModal()" class="btn"><?= __('btn.cancel') ?></button>
          </div>
        </form>
      </div>
    </div>
    
    <div class="form-group">
      <label><?= __('field.time') ?></label>
      <input type="datetime-local" name="occurred_at" 
             value="<?= date('Y-m-d\TH:i', strtotime($transaction['occurred_at'])) ?>" required>
    </div>
    
    <div class="form-group">
      <label><?= __('field.note') ?></label>
      <textarea name="note"><?= htmlspecialchars($transaction['note'] ?? '') ?></textarea>
    </div>
    
    <div class="form-group">
      <label><?= __('field.attachments') ?></label>
      <input type="file" name="attachments[]" id="file-input" accept="image/*" multiple 
             style="display: none;" onchange="handleFileSelect(event)">
      <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 8px;">
        <button type="button" onclick="document.getElementById('file-input').click()" 
                class="btn" style="background: #3498db;">
          <?= __('btn.select_images') ?>
        </button>
        <span id="file-count" style="line-height: 38px; color: #666;"></span>
      </div>
      <div id="image-preview" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 8px; margin-top: 8px;"></div>
      <small style="color: #666; display: block; margin-top: 4px;">
        <?= __('field.attachments_hint') ?>
      </small>
    </div>
    
    <div class="form-group">
      <button type="submit" class="btn btn-success"><?= __('btn.save') ?></button>
      <a href="/index.php?r=transactions/view&id=<?= $transaction['id'] ?>" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<script>
document.getElementById('type-select').addEventListener('change', function() {
  const type = this.value;
  const categorySelect = document.querySelector('select[name="category_id"]');
  const vendorGroup = document.getElementById('vendor-group');
  
  if (type === 'expense') {
    vendorGroup.style.display = 'block';
  } else {
    vendorGroup.style.display = 'none';
  }
  
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

// 金额验证
document.getElementById('transaction-form').addEventListener('submit', function(e) {
  const amount = parseFloat(document.getElementById('amount-input').value);
  if (isNaN(amount) || amount <= 0) {
    e.preventDefault();
    alert('<?= __('tx.amount_invalid') ?>');
    document.getElementById('amount-input').focus();
    return false;
  }
});

// 图片上传处理
let selectedFiles = [];

function handleFileSelect(event) {
  const files = Array.from(event.target.files);
  const maxFiles = 5;
  const maxSize = 5 * 1024 * 1024; // 5MB
  
  if (selectedFiles.length + files.length > maxFiles) {
    alert('<?= __('upload.max_files') ?>');
    return;
  }
  
  files.forEach(file => {
    if (file.size > maxSize) {
      alert('<?= __('upload.file_too_large') ?>: ' + file.name);
      return;
    }
    
    if (!file.type.startsWith('image/')) {
      alert('<?= __('upload.invalid_type') ?>: ' + file.name);
      return;
    }
    
    selectedFiles.push(file);
    previewImage(file);
  });
  
  updateFileCount();
}

function previewImage(file) {
  const reader = new FileReader();
  reader.onload = function(e) {
    const preview = document.getElementById('image-preview');
    const div = document.createElement('div');
    div.style.position = 'relative';
    div.style.aspectRatio = '1';
    div.style.overflow = 'hidden';
    div.style.borderRadius = '4px';
    div.style.border = '1px solid #ddd';
    
    const img = document.createElement('img');
    img.src = e.target.result;
    img.style.width = '100%';
    img.style.height = '100%';
    img.style.objectFit = 'cover';
    
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.innerHTML = '×';
    removeBtn.style.position = 'absolute';
    removeBtn.style.top = '4px';
    removeBtn.style.right = '4px';
    removeBtn.style.background = 'rgba(231, 76, 60, 0.9)';
    removeBtn.style.color = 'white';
    removeBtn.style.border = 'none';
    removeBtn.style.borderRadius = '50%';
    removeBtn.style.width = '24px';
    removeBtn.style.height = '24px';
    removeBtn.style.cursor = 'pointer';
    removeBtn.style.fontSize = '18px';
    removeBtn.style.lineHeight = '1';
    removeBtn.onclick = function() {
      const index = selectedFiles.indexOf(file);
      if (index > -1) {
        selectedFiles.splice(index, 1);
        div.remove();
        updateFileCount();
        updateFileInput();
      }
    };
    
    div.appendChild(img);
    div.appendChild(removeBtn);
    preview.appendChild(div);
  };
  reader.readAsDataURL(file);
}

function updateFileCount() {
  const count = selectedFiles.length;
  const countEl = document.getElementById('file-count');
  if (count > 0) {
    countEl.textContent = '<?= __('upload.selected') ?>: ' + count;
  } else {
    countEl.textContent = '';
  }
}

function updateFileInput() {
  const input = document.getElementById('file-input');
  const dt = new DataTransfer();
  selectedFiles.forEach(file => dt.items.add(file));
  input.files = dt.files;
}

// 快速添加供应商
function showAddVendorModal() {
  document.getElementById('vendor-modal').style.display = 'flex';
  document.getElementById('quick-vendor-name').focus();
}

function hideAddVendorModal() {
  document.getElementById('vendor-modal').style.display = 'none';
  document.getElementById('quick-vendor-form').reset();
}

function addVendorQuick(e) {
  e.preventDefault();
  const name = document.getElementById('quick-vendor-name').value.trim();
  const phone = document.getElementById('quick-vendor-phone').value.trim();
  const note = document.getElementById('quick-vendor-note').value.trim();
  
  if (!name) {
    alert('<?= __('field.vendor') ?> <?= __('field.required') ?>');
    return false;
  }
  
  const formData = new FormData();
  formData.append('action', 'create');
  formData.append('name', name);
  formData.append('phone', phone);
  formData.append('note', note);
  formData.append('_csrf', '<?= Csrf::token() ?>');
  
  fetch('/index.php?r=settings/addVendorQuick', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const select = document.getElementById('vendor-select');
      const option = document.createElement('option');
      option.value = data.id;
      option.textContent = data.name;
      option.selected = true;
      select.appendChild(option);
      hideAddVendorModal();
    } else {
      alert(data.error || '<?= __('vendor.add_failed') ?>');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('<?= __('vendor.add_failed') ?>');
  });
  
  return false;
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>

