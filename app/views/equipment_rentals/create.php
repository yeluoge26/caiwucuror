<?php
$title = __('equipment_rental.create');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('equipment_rental.create') ?></h2>

<div class="card">
  <?php if (!empty($error)): ?>
  <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

    <div class="form-group">
      <label><?= __('equipment_rental.customer_name') ?> *</label>
      <input type="text" name="customer_name" value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>" required>
    </div>

    <div class="form-group">
      <label><?= __('equipment_rental.equipment_name') ?> *</label>
      <input type="text" name="equipment_name" value="<?= htmlspecialchars($_POST['equipment_name'] ?? '') ?>" required>
    </div>

    <div class="form-group">
      <label><?= __('equipment_rental.equipment_serial') ?></label>
      <input type="text" name="equipment_serial" value="<?= htmlspecialchars($_POST['equipment_serial'] ?? '') ?>" placeholder="<?= __('equipment_rental.equipment_serial_hint') ?>">
    </div>

    <div class="form-group">
      <label><?= __('equipment_rental.start_date') ?> *</label>
      <input type="date" name="start_date" value="<?= htmlspecialchars($_POST['start_date'] ?? date('Y-m-d')) ?>" required>
    </div>

    <div class="form-group">
      <label><?= __('equipment_rental.end_date') ?> *</label>
      <input type="date" name="end_date" value="<?= htmlspecialchars($_POST['end_date'] ?? date('Y-m-d', strtotime('+7 days'))) ?>" required>
    </div>

    <div class="form-group">
      <label><?= __('equipment_rental.deposit') ?> (₫)</label>
      <input type="number" name="deposit" value="<?= htmlspecialchars($_POST['deposit'] ?? '0') ?>" min="0" step="0.01" required>
    </div>

    <div class="form-group">
      <label><?= __('equipment_rental.deposit_payment_method') ?></label>
      <select name="deposit_payment_method_id">
        <option value=""><?= __('list.select') ?></option>
        <?php foreach ($paymentMethods as $pm): ?>
        <option value="<?= $pm['id'] ?>" <?= (isset($_POST['deposit_payment_method_id']) && $_POST['deposit_payment_method_id'] == $pm['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars(I18n::current() === 'zh' ? $pm['name_zh'] : $pm['name_vi']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label><?= __('equipment_rental.id_card_photo') ?></label>
      <input type="file" name="id_card_photo" accept="image/*">
      <small style="color: #666; display: block; margin-top: 4px;">
        <?= __('equipment_rental.id_card_photo_hint') ?>
      </small>
    </div>

    <div class="form-group">
      <label><?= __('field.note') ?></label>
      <textarea name="note" rows="3" placeholder="<?= __('equipment_rental.note_hint') ?>"><?= htmlspecialchars($_POST['note'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
      <button type="submit" class="btn btn-success"><?= __('btn.save') ?></button>
      <a href="/index.php?r=equipment_rentals/list" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
