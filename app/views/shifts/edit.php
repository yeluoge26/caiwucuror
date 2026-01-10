<?php
$title = __('shift.edit');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('shift.edit') ?></h2>

<?php if ($error): ?>
<div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
  <form method="post">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    
    <div class="form-group">
      <label><?= __('shift.shift_date') ?> *</label>
      <input type="date" name="shift_date" value="<?= htmlspecialchars($shift['shift_date']) ?>" required>
    </div>

    <div class="form-group">
      <label><?= __('shift.shift_type') ?> *</label>
      <select name="shift_type" required>
        <option value="morning" <?= $shift['shift_type'] === 'morning' ? 'selected' : '' ?>>
          <?= __('shift.type_morning') ?>
        </option>
        <option value="afternoon" <?= $shift['shift_type'] === 'afternoon' ? 'selected' : '' ?>>
          <?= __('shift.type_afternoon') ?>
        </option>
        <option value="evening" <?= $shift['shift_type'] === 'evening' ? 'selected' : '' ?>>
          <?= __('shift.type_evening') ?>
        </option>
      </select>
    </div>

    <div class="form-group">
      <label><?= __('shift.employee') ?> *</label>
      <select name="employee_id" required>
        <option value=""><?= __('list.filter') ?></option>
        <?php foreach ($employees as $emp): ?>
        <option value="<?= $emp['id'] ?>" <?= $shift['employee_id'] == $emp['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($emp['name']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label><?= __('shift.manager') ?></label>
      <select name="manager_id">
        <option value=""><?= __('shift.no_manager') ?> (<?= __('shift.cancel_manager') ?>)</option>
        <?php foreach ($managers as $mgr): ?>
        <option value="<?= $mgr['id'] ?>" <?= ($shift['manager_id'] ?? '') == $mgr['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($mgr['name']) ?>
        </option>
        <?php endforeach; ?>
      </select>
      <small style="color: #666; margin-top: 4px; display: block;">
        <?= __('shift.manager_hint') ?>
      </small>
    </div>

    <div class="form-group">
      <label><?= __('field.note') ?></label>
      <textarea name="note" rows="3"><?= htmlspecialchars($shift['note'] ?? '') ?></textarea>
    </div>

    <div style="margin-top: 20px;">
      <button type="submit" class="btn btn-success"><?= __('btn.save') ?></button>
      <a href="/index.php?r=shifts/list" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>






