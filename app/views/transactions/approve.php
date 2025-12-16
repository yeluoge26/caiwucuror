<?php
$title = __('tx.approve');
include __DIR__ . '/../layout/header.php';
$lang = I18n::current();
?>

<h2 style="margin-bottom: 20px;"><?= __('tx.approve') ?></h2>

<div class="card">
  <div class="alert alert-success">
    <?= __('tx.approve_confirm') ?>
  </div>
  
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
      <th><?= __('field.time') ?></th>
      <td><?= date('d/m/Y H:i', strtotime($transaction['occurred_at'])) ?></td>
    </tr>
    <tr>
      <th><?= __('field.note') ?></th>
      <td><?= htmlspecialchars($transaction['note'] ?? '') ?></td>
    </tr>
  </table>
  
  <form method="post" style="margin-top: 20px;">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <button type="submit" class="btn btn-success"><?= __('tx.approve') ?></button>
    <a href="/index.php?r=transactions/view&id=<?= $transaction['id'] ?>" class="btn"><?= __('btn.cancel') ?></a>
  </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

