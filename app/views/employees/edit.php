<?php
$title = __('employee.edit');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('employee.edit') ?></h2>

<?php if ($error): ?>
<div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
  <form method="post">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    
    <div class="form-group">
      <label><?= __('employee.name') ?> *</label>
      <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? $employee['name']) ?>" required>
    </div>

    <div class="form-group">
      <label><?= __('employee.role') ?> *</label>
      <select name="role_id" required>
        <option value=""><?= __('list.filter') ?></option>
        <?php
        $lang = I18n::current();
        foreach ($roles as $r):
          $name = $lang === 'zh' ? $r['name_zh'] : $r['name_vi'];
        ?>
        <option value="<?= $r['id'] ?>" <?= ($_POST['role_id'] ?? $employee['role_id']) == $r['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($name) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
      <div class="form-group">
        <label><?= __('employee.phone') ?></label>
        <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? $employee['phone'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label><?= __('employee.email') ?></label>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $employee['email'] ?? '') ?>">
      </div>
    </div>

    <div class="form-group">
      <label><?= __('employee.address') ?></label>
      <textarea name="address" rows="2"><?= htmlspecialchars($_POST['address'] ?? $employee['address'] ?? '') ?></textarea>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
      <div class="form-group">
        <label><?= __('employee.status') ?></label>
        <select name="status">
          <option value="active" <?= ($_POST['status'] ?? $employee['status']) === 'active' ? 'selected' : '' ?>>
            <?= __('employee.status_active') ?>
          </option>
          <option value="inactive" <?= ($_POST['status'] ?? $employee['status']) === 'inactive' ? 'selected' : '' ?>>
            <?= __('employee.status_inactive') ?>
          </option>
          <option value="resigned" <?= ($_POST['status'] ?? $employee['status']) === 'resigned' ? 'selected' : '' ?>>
            <?= __('employee.status_resigned') ?>
          </option>
        </select>
      </div>

      <div class="form-group">
        <label><?= __('employee.employment_type') ?></label>
        <select name="employment_type">
          <option value="full_time" <?= ($_POST['employment_type'] ?? ($employee['employment_type'] ?? 'full_time')) === 'full_time' ? 'selected' : '' ?>>
            <?= __('employee.employment_type_full_time') ?>
          </option>
          <option value="part_time" <?= ($_POST['employment_type'] ?? ($employee['employment_type'] ?? '')) === 'part_time' ? 'selected' : '' ?>>
            <?= __('employee.employment_type_part_time') ?>
          </option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label><?= __('employee.hire_date') ?></label>
      <input type="date" name="hire_date" value="<?= htmlspecialchars($_POST['hire_date'] ?? ($employee['hire_date'] ?? '')) ?>">
    </div>

    <div class="form-group">
      <label><?= __('employee.resign_date') ?></label>
      <input type="date" name="resign_date" value="<?= htmlspecialchars($_POST['resign_date'] ?? ($employee['resign_date'] ?? '')) ?>">
    </div>

    <div class="form-group">
      <label><?= __('field.note') ?></label>
      <textarea name="note" rows="3"><?= htmlspecialchars($_POST['note'] ?? $employee['note'] ?? '') ?></textarea>
    </div>

    <div style="margin-top: 20px;">
      <button type="submit" class="btn btn-success"><?= __('btn.submit') ?></button>
      <a href="/index.php?r=employees/list" class="btn" style="background: #95a5a6;"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

