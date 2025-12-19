<?php
$title = __('shift.confirm');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('shift.confirm') ?></h2>

<div class="card">
  <div style="margin-bottom: 20px;">
    <h3><?= __('shift.shift_info') ?></h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
      <div>
        <strong><?= __('shift.shift_date') ?>:</strong>
        <?= date('Y-m-d', strtotime($shift['shift_date'])) ?>
      </div>
      <div>
        <strong><?= __('shift.shift_type') ?>:</strong>
        <?php
        $typeText = [
          'morning' => __('shift.type_morning'),
          'afternoon' => __('shift.type_afternoon'),
          'evening' => __('shift.type_evening')
        ];
        echo $typeText[$shift['shift_type']] ?? $shift['shift_type'];
        ?>
      </div>
      <div>
        <strong><?= __('shift.employee') ?>:</strong>
        <?= htmlspecialchars($shift['employee_name'] ?? '') ?>
      </div>
      <div>
        <strong><?= __('shift.manager') ?>:</strong>
        <?= htmlspecialchars($shift['manager_name'] ?? '-') ?>
      </div>
    </div>
  </div>

  <form method="post">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    
    <div class="form-group">
      <label>
        <input type="checkbox" name="confirmed" value="1" <?= $shift['is_confirmed'] ? 'checked' : '' ?>>
        <?= __('shift.confirm_attendance') ?>
      </label>
    </div>

    <div style="margin-top: 20px;">
      <button type="submit" class="btn btn-success"><?= __('btn.submit') ?></button>
      <a href="/index.php?r=shifts/list" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

