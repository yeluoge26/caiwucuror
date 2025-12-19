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
    <?php if (!empty($transaction['payer'])): ?>
    <tr>
      <th><?= __('field.payer') ?></th>
      <td><?= htmlspecialchars($transaction['payer']) ?></td>
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
    <h3 style="margin-bottom: 10px;">📎 <?= __('attachment.title') ?></h3>
    <?php if (!empty($attachments)): ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px;">
      <?php foreach ($attachments as $att): ?>
      <div style="background: #fff; border: 1px solid #eee; border-radius: 6px; padding: 8px; text-align: center;">
        <a href="/<?= htmlspecialchars($att['file_path']) ?>" target="_blank">
          <img src="/<?= htmlspecialchars($att['file_path']) ?>" alt="attachment" style="max-width: 100%; max-height: 140px; object-fit: contain;">
        </a>
        <div style="font-size: 12px; color: #666; margin-top: 6px;">
          <?= date('Y-m-d H:i', strtotime($att['created_at'])) ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="color: #888; font-size: 14px;"><?= __('attachment.none') ?></div>
    <?php endif; ?>
  </div>
  
  <div style="margin-top: 20px;">
    <a href="/index.php?r=transactions/list" class="btn"><?= __('btn.cancel') ?></a>
    <?php if (in_array($user['role_key'], ['owner', 'accountant']) && $transaction['status'] !== 'void'): ?>
    <a href="/index.php?r=transactions/void&id=<?= $transaction['id'] ?>" class="btn btn-danger">
      <?= __('btn.void') ?>
    </a>
    <?php elseif ($transaction['status'] !== 'void'): ?>
    <form method="post" action="/index.php?r=transactions/requestVoid&id=<?= $transaction['id'] ?>" style="display: inline-block;">
      <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
      <input type="text" name="reason" placeholder="<?= __('void.reason_placeholder') ?>" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 220px;">
      <button type="submit" class="btn btn-danger" style="padding: 8px 12px;"><?= __('void.request') ?></button>
    </form>
    <?php endif; ?>
  </div>

  <?php if (!empty($latestVoidRequest)): ?>
  <div style="margin-top: 16px; padding: 12px; background: #fff6e5; border: 1px solid #f5d7a8; border-radius: 6px;">
    <div style="font-weight: 600; margin-bottom: 6px;"><?= __('void.last_request') ?></div>
    <div style="font-size: 14px; color: #555;">
      <?= htmlspecialchars($latestVoidRequest['display_name'] ?? '') ?> @ <?= date('Y-m-d H:i', strtotime($latestVoidRequest['created_at'])) ?>
    </div>
    <?php if (!empty($latestVoidRequest['reason'])): ?>
    <div style="font-size: 14px; color: #555; margin-top: 4px;"><?= nl2br(htmlspecialchars($latestVoidRequest['reason'])) ?></div>
    <?php endif; ?>
    <div style="font-size: 12px; color: #a66; margin-top: 4px;"><?= __('void.pending_tip') ?></div>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
