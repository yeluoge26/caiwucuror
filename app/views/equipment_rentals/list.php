<?php
$title = __('equipment_rental.list');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('equipment_rental.list') ?></h2>

<div style="margin-bottom: 12px;">
  <a href="/index.php?r=equipment_rentals/create" class="btn btn-success"><?= __('equipment_rental.create') ?></a>
</div>

<!-- 筛选表单 -->
<div class="card" style="margin-bottom: 20px;">
  <form method="get" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
    <input type="hidden" name="r" value="equipment_rentals/list">
    <div class="form-group" style="flex: 1; min-width: 150px;">
      <label><?= __('equipment_rental.customer_name') ?></label>
      <input type="text" name="customer_name" value="<?= htmlspecialchars($_GET['customer_name'] ?? '') ?>" placeholder="<?= __('equipment_rental.customer_name') ?>">
    </div>
    <div class="form-group" style="flex: 1; min-width: 150px;">
      <label><?= __('equipment_rental.equipment_name') ?></label>
      <input type="text" name="equipment_name" value="<?= htmlspecialchars($_GET['equipment_name'] ?? '') ?>" placeholder="<?= __('equipment_rental.equipment_name') ?>">
    </div>
    <div class="form-group" style="flex: 1; min-width: 120px;">
      <label><?= __('equipment_rental.status') ?></label>
      <select name="status">
        <option value=""><?= __('list.all') ?></option>
        <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>><?= __('equipment_rental.status_active') ?></option>
        <option value="returned" <?= ($_GET['status'] ?? '') === 'returned' ? 'selected' : '' ?>><?= __('equipment_rental.status_returned') ?></option>
        <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>><?= __('equipment_rental.status_cancelled') ?></option>
      </select>
    </div>
    <div class="form-group" style="flex: 1; min-width: 120px;">
      <label><?= __('field.time') ?> (<?= __('report.from') ?>)</label>
      <input type="date" name="from_date" value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>">
    </div>
    <div class="form-group" style="flex: 1; min-width: 120px;">
      <label><?= __('field.time') ?> (<?= __('report.to') ?>)</label>
      <input type="date" name="to_date" value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>">
    </div>
    <div class="form-group">
      <button type="submit" class="btn"><?= __('btn.search') ?></button>
      <a href="/index.php?r=equipment_rentals/list" class="btn" style="background: #95a5a6;"><?= __('btn.clear') ?></a>
    </div>
  </form>
</div>

<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('equipment_rental.list') ?></h3>
  <div class="table-scroll">
    <table>
      <tr>
        <th>ID</th>
        <th><?= __('equipment_rental.customer_name') ?></th>
        <th><?= __('equipment_rental.equipment_name') ?></th>
        <th><?= __('equipment_rental.equipment_serial') ?></th>
        <th><?= __('equipment_rental.start_date') ?></th>
        <th><?= __('equipment_rental.end_date') ?></th>
        <th><?= __('equipment_rental.deposit') ?></th>
        <th><?= __('equipment_rental.deposit_payment_method') ?></th>
        <th><?= __('equipment_rental.status') ?></th>
        <th><?= __('tx.create') ?></th>
        <th><?= __('list.actions') ?></th>
      </tr>
      <?php if (empty($items)): ?>
      <tr>
        <td colspan="11" style="text-align: center; color: #888;"><?= __('list.no_data') ?></td>
      </tr>
      <?php else: ?>
      <?php foreach ($items as $row): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['customer_name']) ?></td>
        <td><?= htmlspecialchars($row['equipment_name']) ?></td>
        <td><?= htmlspecialchars($row['equipment_serial'] ?? '-') ?></td>
        <td><?= date('Y-m-d', strtotime($row['start_date'])) ?></td>
        <td><?= date('Y-m-d', strtotime($row['end_date'])) ?></td>
        <td><?= number_format($row['deposit'], 0) ?> ₫</td>
        <td><?= htmlspecialchars(I18n::current() === 'zh' ? ($row['payment_name_zh'] ?? '-') : ($row['payment_name_vi'] ?? '-')) ?></td>
        <td>
          <span class="badge <?= $row['status'] === 'active' ? 'badge-approved' : ($row['status'] === 'returned' ? 'badge-void' : 'badge-pending') ?>">
            <?php
            if ($row['status'] === 'active') echo __('equipment_rental.status_active');
            elseif ($row['status'] === 'returned') echo __('equipment_rental.status_returned');
            else echo __('equipment_rental.status_cancelled');
            ?>
          </span>
        </td>
        <td><?= htmlspecialchars($row['creator_name'] ?? '') ?></td>
        <td>
          <a href="/index.php?r=equipment_rentals/view&id=<?= $row['id'] ?>" class="btn" style="padding: 6px 10px; font-size: 12px;"><?= __('btn.view') ?></a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </table>
  </div>

  <?php if ($totalPages > 1): ?>
  <div style="margin-top: 20px; text-align: center;">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="?r=equipment_rentals/list&page=<?= $i ?>&<?= http_build_query(array_diff_key($_GET, ['page' => '', 'r' => ''])) ?>" 
         class="btn <?= $i === $page ? 'btn-success' : '' ?>" 
         style="padding: 6px 10px; font-size: 12px; margin: 0 2px;">
        <?= $i ?>
      </a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
