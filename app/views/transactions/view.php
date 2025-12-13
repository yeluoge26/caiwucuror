<?php
$title = __('tx.view');
include __DIR__ . '/../layout/header.php';
$lang = I18n::current();
?>

<h2 style="margin-bottom: 20px;"><?= __('tx.view') ?></h2>

<div class="card">
  <table>
    <tr>
      <th style="width: 150px;">ID</th>
      <td><?= $transaction['id'] ?></td>
    </tr>
    <tr>
      <th><?= __('tx.type') ?></th>
      <td>
        <span class="badge <?= $transaction['type'] === 'income' ? 'badge-income' : 'badge-expense' ?>">
          <?= $transaction['type'] === 'income' ? __('tx.income') : __('tx.expense') ?>
        </span>
      </td>
    </tr>
    <tr>
      <th><?= __('tx.amount') ?></th>
      <td style="font-size: 20px; font-weight: bold;">
        <?= number_format($transaction['amount'], 0, ',', '.') ?> <?= $transaction['currency'] ?>
      </td>
    </tr>
    <tr>
      <th><?= __('tx.category') ?></th>
      <td><?= htmlspecialchars($lang === 'zh' ? ($transaction['category_name_zh'] ?? '') : ($transaction['category_name_vi'] ?? '')) ?></td>
    </tr>
    <tr>
      <th><?= __('field.payment') ?></th>
      <td><?= htmlspecialchars($lang === 'zh' ? ($transaction['payment_name_zh'] ?? '') : ($transaction['payment_name_vi'] ?? '')) ?></td>
    </tr>
    <?php if ($transaction['vendor_name']): ?>
    <tr>
      <th><?= __('field.vendor') ?></th>
      <td><?= htmlspecialchars($transaction['vendor_name']) ?></td>
    </tr>
    <?php endif; ?>
    <tr>
      <th><?= __('field.time') ?></th>
      <td><?= date('d/m/Y H:i:s', strtotime($transaction['occurred_at'])) ?></td>
    </tr>
    <tr>
      <th><?= __('tx.status') ?></th>
      <td>
        <span class="badge badge-<?= $transaction['status'] ?>">
          <?php
          if ($transaction['status'] === 'approved') echo __('tx.approved');
          elseif ($transaction['status'] === 'pending') echo __('tx.pending');
          else echo __('tx.void');
          ?>
        </span>
      </td>
    </tr>
    <tr>
      <th><?= __('field.note') ?></th>
      <td><?= nl2br(htmlspecialchars($transaction['note'] ?? '')) ?></td>
    </tr>
    <tr>
      <th><?= __('tx.create') ?></th>
      <td><?= htmlspecialchars($transaction['creator_name'] ?? '') ?></td>
    </tr>
  </table>
  
  <div style="margin-top: 20px;">
    <a href="/index.php?r=transactions/list" class="btn"><?= __('btn.cancel') ?></a>
    <?php if (in_array($user['role_key'], ['owner', 'accountant']) && $transaction['status'] !== 'void'): ?>
    <a href="/index.php?r=transactions/void&id=<?= $transaction['id'] ?>" class="btn btn-danger">
      <?= __('btn.void') ?>
    </a>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

