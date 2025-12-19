<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('inspection.new') ?></h3>
  <?php if (!empty($error)): ?>
  <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

    <div class="form-group">
      <label><?= __('material.store') ?></label>
      <select name="store" required>
        <option value="coffee"><?= __('asset.category_coffee') ?></option>
        <option value="office"><?= __('asset.category_office') ?></option>
        <option value="whiskey"><?= __('asset.category_whiskey') ?></option>
      </select>
    </div>

    <div class="form-group">
      <label><?= __('inspection.floor') ?></label>
      <select name="floor" required>
        <option value="1F">1F</option>
        <option value="2F">2F</option>
        <option value="3F">3F</option>
        <option value="4F">4F</option>
      </select>
    </div>

    <div class="form-group">
      <label><?= __('inspection.visit_no') ?></label>
      <select name="visit_no" required>
        <option value="1"><?= __('inspection.visit_first') ?></option>
        <option value="2"><?= __('inspection.visit_second') ?></option>
      </select>
    </div>

    <div class="form-group">
      <label><?= __('inspection.room') ?></label>
      <select name="room" required>
        <option value="store"><?= __('inspection.room_store') ?></option>
        <option value="restroom"><?= __('inspection.room_restroom') ?></option>
        <option value="stair"><?= __('inspection.room_stair') ?></option>
      </select>
    </div>

    <div class="form-group">
      <label><?= __('inspection.status') ?></label>
      <select name="status" required>
        <option value="ok"><?= __('inspection.ok') ?></option>
        <option value="issue"><?= __('inspection.issue') ?></option>
      </select>
    </div>

    <div class="form-group">
      <label><?= __('field.time') ?></label>
      <input type="date" name="spot_date" value="<?= htmlspecialchars($date ?? date('Y-m-d')) ?>" required>
    </div>

    <div class="form-group">
      <label><?= __('inspection.photo') ?></label>
      <input type="file" name="photos[]" accept="image/*" multiple>
      <div style="font-size: 12px; color: #666; margin-top: 6px;"><?= __('asset.photo_hint') ?></div>
    </div>

    <div class="form-group">
      <label><?= __('field.note') ?></label>
      <input type="text" name="note" placeholder="<?= __('inspection.note_hint') ?>">
    </div>

    <div class="form-group">
      <button type="submit" class="btn btn-success"><?= __('btn.save') ?></button>
      <a href="/index.php?r=inspections/list" class="btn" style="background:#95a5a6;"><?= __('nav.inspections') ?></a>
    </div>
  </form>
</div>
