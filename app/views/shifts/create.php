<?php
$title = __('shift.create');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('shift.create') ?></h2>

<?php if ($error): ?>
<div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
  <form method="post">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    
    <div class="form-group">
      <label><?= __('shift.shift_date') ?> *</label>
      <input type="date" name="shift_date" value="<?= htmlspecialchars($_POST['shift_date'] ?? date('Y-m-d')) ?>" required>
    </div>

    <div class="form-group">
      <label><?= __('shift.shift_type') ?> *</label>
      <select name="shift_type" required>
        <option value="morning" <?= ($_POST['shift_type'] ?? 'morning') === 'morning' ? 'selected' : '' ?>>
          <?= __('shift.type_morning') ?>
        </option>
        <option value="afternoon" <?= ($_POST['shift_type'] ?? '') === 'afternoon' ? 'selected' : '' ?>>
          <?= __('shift.type_afternoon') ?>
        </option>
        <option value="evening" <?= ($_POST['shift_type'] ?? '') === 'evening' ? 'selected' : '' ?>>
          <?= __('shift.type_evening') ?>
        </option>
      </select>
    </div>

    <div class="form-group">
      <label><?= __('shift.employee') ?> *</label>
      <select name="employee_id" required>
        <option value=""><?= __('list.filter') ?></option>
        <?php foreach ($employees as $emp): ?>
        <option value="<?= $emp['id'] ?>" <?= ($_POST['employee_id'] ?? '') == $emp['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($emp['name']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label><?= __('shift.manager') ?></label>
      <select name="manager_id">
        <option value=""><?= __('list.filter') ?></option>
        <?php foreach ($managers as $mgr): ?>
        <option value="<?= $mgr['id'] ?>" <?= ($_POST['manager_id'] ?? '') == $mgr['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($mgr['name']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label><?= __('field.note') ?></label>
      <textarea name="note" rows="3"><?= htmlspecialchars($_POST['note'] ?? '') ?></textarea>
    </div>

    <div style="margin-top: 20px;">
      <button type="submit" class="btn btn-success"><?= __('btn.submit') ?></button>
      <a href="/index.php?r=shifts/list" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

