<?php
$title = __('equipment_rental.detail');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('equipment_rental.detail') ?></h2>

<div class="card">
  <table>
    <tr>
      <th style="width: 160px;">ID</th>
      <td><?= $rental['id'] ?></td>
    </tr>
    <tr>
      <th><?= __('equipment_rental.customer_name') ?></th>
      <td><?= htmlspecialchars($rental['customer_name']) ?></td>
    </tr>
    <tr>
      <th><?= __('equipment_rental.equipment_name') ?></th>
      <td><?= htmlspecialchars($rental['equipment_name']) ?></td>
    </tr>
    <tr>
      <th><?= __('equipment_rental.equipment_serial') ?></th>
      <td><?= htmlspecialchars($rental['equipment_serial'] ?? '-') ?></td>
    </tr>
    <tr>
      <th><?= __('equipment_rental.start_date') ?></th>
      <td><?= date('Y-m-d', strtotime($rental['start_date'])) ?></td>
    </tr>
    <tr>
      <th><?= __('equipment_rental.end_date') ?></th>
      <td><?= date('Y-m-d', strtotime($rental['end_date'])) ?></td>
    </tr>
    <tr>
      <th><?= __('equipment_rental.deposit') ?></th>
      <td><?= number_format($rental['deposit'], 0) ?> ₫</td>
    </tr>
    <tr>
      <th><?= __('equipment_rental.deposit_payment_method') ?></th>
      <td><?= htmlspecialchars(I18n::current() === 'zh' ? ($rental['payment_name_zh'] ?? '-') : ($rental['payment_name_vi'] ?? '-')) ?></td>
    </tr>
    <tr>
      <th><?= __('equipment_rental.id_card_photo') ?></th>
      <td>
        <?php if (!empty($rental['id_card_photo'])): ?>
        <a href="/<?= htmlspecialchars($rental['id_card_photo']) ?>" target="_blank">
          <img src="/<?= htmlspecialchars($rental['id_card_photo']) ?>" alt="<?= __('equipment_rental.id_card_photo') ?>" style="max-width: 300px; max-height: 300px; border: 1px solid #ddd; border-radius: 4px;">
        </a>
        <?php else: ?>
        <span style="color: #999;">-</span>
        <?php endif; ?>
      </td>
    </tr>
    <tr>
      <th><?= __('equipment_rental.status') ?></th>
      <td>
        <span class="badge <?= $rental['status'] === 'active' ? 'badge-approved' : ($rental['status'] === 'returned' ? 'badge-void' : 'badge-pending') ?>">
          <?php
          if ($rental['status'] === 'active') echo __('equipment_rental.status_active');
          elseif ($rental['status'] === 'returned') echo __('equipment_rental.status_returned');
          else echo __('equipment_rental.status_cancelled');
          ?>
        </span>
      </td>
    </tr>
    <tr>
      <th><?= __('field.note') ?></th>
      <td><?= nl2br(htmlspecialchars($rental['note'] ?? '')) ?></td>
    </tr>
    <tr>
      <th><?= __('tx.create') ?></th>
      <td><?= htmlspecialchars($rental['creator_name'] ?? '') ?> @ <?= date('Y-m-d H:i:s', strtotime($rental['created_at'])) ?></td>
    </tr>
  </table>

  <div style="margin-top: 20px; display: flex; gap: 12px;">
    <a href="/index.php?r=equipment_rentals/list" class="btn" style="background: #95a5a6;"><?= __('btn.back') ?></a>
    <?php if ($rental['status'] === 'active'): ?>
    <form method="post" action="/index.php?r=equipment_rentals/returnEquipment&id=<?= $rental['id'] ?>" style="display: inline;">
      <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
      <button type="submit" class="btn btn-success" onclick="return confirm('<?= __('equipment_rental.confirm_return') ?>');">
        <?= __('equipment_rental.return') ?>
      </button>
    </form>
    <form method="post" action="/index.php?r=equipment_rentals/cancel&id=<?= $rental['id'] ?>" style="display: inline;">
      <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
      <button type="submit" class="btn btn-danger" onclick="return confirm('<?= __('equipment_rental.confirm_cancel') ?>');">
        <?= __('equipment_rental.cancel') ?>
      </button>
    </form>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
