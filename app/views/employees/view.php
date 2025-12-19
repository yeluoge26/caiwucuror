<?php
$title = __('employee.view');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('employee.view') ?></h2>

<div class="card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h3><?= htmlspecialchars($employee['name']) ?></h3>
    <div>
      <a href="/index.php?r=employees/edit&id=<?= $employee['id'] ?>" class="btn btn-success">
        <?= __('btn.edit') ?>
      </a>
      <a href="/index.php?r=employees/list" class="btn" style="background: #95a5a6;"><?= __('btn.back') ?></a>
    </div>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; margin-bottom: 20px;">
    <div>
      <strong><?= __('employee.role') ?>:</strong>
      <?php
      $lang = I18n::current();
      $roleName = $lang === 'zh' ? ($employee['role_name_zh'] ?? '') : ($employee['role_name_vi'] ?? '');
      ?>
      <?= htmlspecialchars($roleName) ?>
    </div>
    <div>
      <strong><?= __('employee.phone') ?>:</strong>
      <?= htmlspecialchars($employee['phone'] ?? '-') ?>
    </div>
    <div>
      <strong><?= __('employee.email') ?>:</strong>
      <?= htmlspecialchars($employee['email'] ?? '-') ?>
    </div>
    <div>
      <strong><?= __('employee.status') ?>:</strong>
      <?php
      $statusClass = [
        'active' => 'badge-income',
        'inactive' => 'badge-pending',
        'resigned' => 'badge-void'
      ];
      $statusText = [
        'active' => __('employee.status_active'),
        'inactive' => __('employee.status_inactive'),
        'resigned' => __('employee.status_resigned')
      ];
      ?>
      <span class="badge <?= $statusClass[$employee['status']] ?? '' ?>">
        <?= $statusText[$employee['status']] ?? $employee['status'] ?>
      </span>
    </div>
    <div>
      <strong><?= __('employee.hire_date') ?>:</strong>
      <?= $employee['hire_date'] ? date('Y-m-d', strtotime($employee['hire_date'])) : '-' ?>
    </div>
    <?php if ($employee['resign_date']): ?>
    <div>
      <strong><?= __('employee.resign_date') ?>:</strong>
      <?= date('Y-m-d', strtotime($employee['resign_date'])) ?>
    </div>
    <?php endif; ?>
  </div>

  <?php if ($employee['address']): ?>
  <div class="form-group">
    <strong><?= __('employee.address') ?>:</strong>
    <div style="margin-top: 8px; padding: 12px; background: #f8f9fa; border-radius: 4px;">
      <?= nl2br(htmlspecialchars($employee['address'])) ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($employee['note']): ?>
  <div class="form-group">
    <strong><?= __('field.note') ?>:</strong>
    <div style="margin-top: 8px; padding: 12px; background: #f8f9fa; border-radius: 4px;">
      <?= nl2br(htmlspecialchars($employee['note'])) ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

