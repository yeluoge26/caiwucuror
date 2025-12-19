<?php
$title = __('material.add');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('material.add') ?></h2>

<div class="card">
  <?php if (!empty($error)): ?>
  <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <div class="form-group">
      <label><?= __('material.name') ?></label>
      <input type="text" name="name" id="name-input" required>
      <div style="margin-top:8px;">
        <label style="font-size:13px; color:#555;"><?= __('material.name_common') ?></label>
        <select id="name-select" style="margin-left:8px; padding:8px; border:1px solid #ddd; border-radius:4px;">
          <option value="other"><?= __('material.other') ?></option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label><?= __('material.store') ?></label>
      <select name="store" required>
        <option value="coffee"><?= __('asset.category_coffee') ?></option>
        <option value="office"><?= __('asset.category_office') ?></option>
        <option value="whiskey"><?= __('asset.category_whiskey') ?></option>
      </select>
    </div>
    <div class="form-group">
      <label><?= __('material.category') ?></label>
      <select name="category" required>
        <option value="bean"><?= __('material.cat_bean') ?></option>
        <option value="milk"><?= __('material.cat_milk') ?></option>
        <option value="syrup"><?= __('material.cat_syrup') ?></option>
        <option value="consumable"><?= __('material.cat_consumable') ?></option>
      </select>
    </div>
    <div class="form-group">
      <label><?= __('material.unit') ?></label>
      <select name="unit" required>
        <option value="g">g</option>
        <option value="kg">kg</option>
        <option value="L">L</option>
        <option value="pcs"><?= __('material.unit_pcs') ?></option>
      </select>
    </div>
    <div class="form-group">
      <label><?= __('material.quantity') ?></label>
      <input type="number" name="quantity" step="0.01" min="0" required>
    </div>
    <div class="form-group">
      <label><?= __('material.min_quantity') ?></label>
      <input type="number" name="min_quantity" step="0.01" min="0" value="0">
    </div>
    <div class="form-group">
      <label><?= __('field.note') ?></label>
      <textarea name="note"></textarea>
    </div>
    <div class="form-group">
      <label><?= __('asset.photos') ?></label>
      <input type="file" name="material_images[]" accept="image/*" multiple>
      <div style="font-size:12px;color:#666;margin-top:6px;"><?= __('asset.photo_hint') ?></div>
    </div>
    <div class="form-group">
      <button type="submit" class="btn btn-success"><?= __('btn.save') ?></button>
      <a href="/index.php?r=materials/list" class="btn" style="background:#95a5a6;"><?= __('nav.materials_list') ?></a>
    </div>
  </form>
</div>

<script>
  const nameOptions = <?= json_encode([
    'coffee' => [
      'bean' => ['阿拉比卡豆', '罗布斯塔豆', '拼配豆', '脱因咖啡豆'],
      'milk' => ['鲜牛奶', '燕麦奶', '椰奶'],
      'syrup' => ['香草糖浆', '焦糖糖浆', '榛果糖浆', '抹茶粉', '可可粉'],
      'consumable' => ['8oz杯', '12oz杯', '吸管', '纸巾', '杯盖']
    ],
    'office' => [
      'bean' => ['速溶咖啡粉', '挂耳咖啡'],
      'milk' => ['常温奶', '奶精球'],
      'syrup' => ['红茶包', '绿茶包', '巧克力粉'],
      'consumable' => ['纸杯', '纸巾', '搅拌棒']
    ],
    'whiskey' => [
      'bean' => ['咖啡豆'], // rare, keep placeholder
      'milk' => ['牛奶'],
      'syrup' => ['苏打水', '汤力水', '糖浆', '柠檬汁'],
      'consumable' => ['威士忌杯', '高脚杯', '冰块', '纸巾']
    ]
  ]) ?>;

  const storeSelect = document.querySelector('select[name="store"]');
  const categorySelect = document.querySelector('select[name="category"]');
  const nameSelect = document.getElementById('name-select');
  const nameInput = document.getElementById('name-input');

  function renderNames() {
    const store = storeSelect.value;
    const cat = categorySelect.value;
    const list = (nameOptions[store] && nameOptions[store][cat]) || [];
    nameSelect.innerHTML = '';
    list.forEach(item => {
      const opt = document.createElement('option');
      opt.value = item;
      opt.textContent = item;
      nameSelect.appendChild(opt);
    });
    const otherOpt = document.createElement('option');
    otherOpt.value = 'other';
    otherOpt.textContent = '<?= __('material.other') ?>';
    nameSelect.appendChild(otherOpt);
    nameSelect.value = list[0] || 'other';
    if (nameSelect.value !== 'other') {
      nameInput.value = nameSelect.value;
    }
  }

  nameSelect.addEventListener('change', function() {
    if (this.value === 'other') {
      nameInput.value = '';
      nameInput.focus();
    } else {
      nameInput.value = this.value;
    }
  });

  storeSelect.addEventListener('change', renderNames);
  categorySelect.addEventListener('change', renderNames);
  renderNames();
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
