<?php
$title = __('asset.add');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('asset.add') ?></h2>

<div class="card">
  <?php if (!empty($error)): ?>
  <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <div class="form-group">
      <label><?= __('asset.store') ?></label>
      <select name="category" id="store-select" required>
        <option value="coffee"><?= __('asset.category_coffee') ?></option>
        <option value="office"><?= __('asset.category_office') ?></option>
        <option value="whiskey"><?= __('asset.category_whiskey') ?></option>
      </select>
    </div>
    <div class="form-group">
      <label><?= __('asset.subcategory') ?></label>
      <select name="subcategory" id="subcategory-select"></select>
    </div>
    <div class="form-group">
      <label><?= __('asset.name') ?> (<?= __('asset.optional') ?>)</label>
      <input type="text" name="name">
    </div>
    <div class="form-group">
      <label><?= __('asset.floor') ?></label>
      <select name="floor">
        <option value="1F">1F</option>
        <option value="Mezzanine"><?= __('asset.floor_mezzanine') ?></option>
        <option value="2F">2F</option>
        <option value="3F">3F</option>
        <option value="4F">4F</option>
      </select>
    </div>
    <div class="form-group">
      <label><?= __('asset.location') ?></label>
      <input type="text" name="location" placeholder="<?= __('asset.location_hint') ?>">
    </div>
    <div class="form-group">
      <label><?= __('asset.price') ?> (₫)</label>
      <input type="number" name="price" min="0" step="0.01" required>
    </div>
    <div class="form-group">
      <label><?= __('asset.quantity') ?></label>
      <input type="number" name="quantity" min="1" step="1" value="1" required>
    </div>
    <div class="form-group">
      <label><?= __('asset.acquired_at') ?></label>
      <input type="date" name="acquired_at" value="<?= date('Y-m-d') ?>" required>
    </div>
    <div class="form-group">
      <label><?= __('field.note') ?></label>
      <textarea name="note"></textarea>
    </div>
    <div class="form-group">
      <label><?= __('asset.photos') ?></label>
      <input type="file" name="asset_images[]" accept="image/*" multiple>
      <div style="font-size: 12px; color: #666; margin-top: 6px;"><?= __('asset.photo_hint') ?></div>
    </div>
    <div class="form-group">
      <button type="submit" class="btn btn-success"><?= __('btn.save') ?></button>
      <a href="/index.php?r=assets/list" class="btn" style="background:#95a5a6;"><?= __('nav.assets_list') ?></a>
    </div>
  </form>
</div>

<script>
  const subcategoryOptions = <?= json_encode([
    'coffee' => [
      __('asset.sub_coffee_bar'),
      __('asset.sub_coffee_machine'),
      __('asset.sub_coffee_furniture'),
      __('asset.sub_coffee_decor'),
      __('asset.sub_coffee_storage'),
      __('asset.sub_coffee_consumable'),
    ],
    'office' => [
      __('asset.sub_office_furniture'),
      __('asset.sub_office_it'),
      __('asset.sub_office_meeting'),
      __('asset.sub_office_locker'),
      __('asset.sub_office_network'),
      __('asset.sub_office_cleaning'),
    ],
    'whiskey' => [
      __('asset.sub_whisky_cabinet'),
      __('asset.sub_whisky_bar'),
      __('asset.sub_whisky_av'),
      __('asset.sub_whisky_seat'),
      __('asset.sub_whisky_glass'),
      __('asset.sub_whisky_decor'),
    ],
  ]) ?>;

  function renderSubcategories(store) {
    const select = document.getElementById('subcategory-select');
    select.innerHTML = '';
    (subcategoryOptions[store] || []).forEach(text => {
      const opt = document.createElement('option');
      opt.value = text;
      opt.textContent = text;
      select.appendChild(opt);
    });
  }

  const storeSelect = document.getElementById('store-select');
  storeSelect.addEventListener('change', function() {
    renderSubcategories(this.value);
  });
  renderSubcategories(storeSelect.value);
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
