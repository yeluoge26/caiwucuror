<?php
$title = __('btn.void');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('btn.void') ?></h2>

<div class="card">
  <div class="alert alert-error">
    <?= __('tx.void_confirm') ?>
  </div>
  
  <table>
    <tr>
      <th style="width: 150px;">ID</th>
      <td><?= $transaction['id'] ?></td>
    </tr>
    <tr>
      <th><?= __('tx.type') ?></th>
      <td><?= $transaction['type'] === 'income' ? __('tx.income') : __('tx.expense') ?></td>
    </tr>
    <tr>
      <th><?= __('tx.amount') ?></th>
      <td><?= number_format($transaction['amount'], 0, ',', '.') ?> <?= $transaction['currency'] ?></td>
    </tr>
    <tr>
      <th><?= __('field.time') ?></th>
      <td><?= date('d/m/Y H:i', strtotime($transaction['occurred_at'])) ?></td>
    </tr>
  </table>
  
  <form method="post" style="margin-top: 20px;">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <button type="submit" class="btn btn-danger"><?= __('btn.void') ?></button>
    <a href="/index.php?r=transactions/view&id=<?= $transaction['id'] ?>" class="btn"><?= __('btn.cancel') ?></a>
  </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

