<?php
$title = __('cash_closing.view');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('cash_closing.view') ?></h2>

<?php if (isset($_GET['error'])): ?>
<div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('cash_closing.basic_info') ?></h3>
  <table>
    <tr><th><?= __('cash_closing.date') ?></th><td><?= date('Y-m-d', strtotime($item['date'])) ?></td></tr>
    <tr><th><?= __('cash_closing.cash_counted') ?></th><td style="font-weight: bold; font-size: 1.2em;"><?= number_format($item['cash_counted'], 0, ',', '.') ?> ₫</td></tr>
    <tr><th><?= __('cash_closing.status') ?></th><td>
      <?php
      $statusClass = [
        'draft' => 'badge',
        'submitted' => 'badge badge-warning',
        'approved' => 'badge badge-success',
        'rejected' => 'badge badge-error'
      ];
      $statusText = [
        'draft' => __('cash_closing.status_draft'),
        'submitted' => __('cash_closing.status_submitted'),
        'approved' => __('cash_closing.status_approved'),
        'rejected' => __('cash_closing.status_rejected')
      ];
      ?>
      <span class="<?= $statusClass[$item['status']] ?? 'badge' ?>">
        <?= $statusText[$item['status']] ?? $item['status'] ?>
      </span>
    </td></tr>
    <tr><th><?= __('field.creator') ?></th><td><?= htmlspecialchars($item['creator_name'] ?? '') ?></td></tr>
    <?php if ($item['submitted_at']): ?>
    <tr><th><?= __('cash_closing.submitted_at') ?></th><td><?= date('Y-m-d H:i:s', strtotime($item['submitted_at'])) ?></td></tr>
    <?php endif; ?>
    <?php if ($item['reviewed_by']): ?>
    <tr><th><?= __('field.reviewer') ?></th><td><?= htmlspecialchars($item['reviewer_name'] ?? '') ?></td></tr>
    <tr><th><?= __('cash_closing.reviewed_at') ?></th><td><?= date('Y-m-d H:i:s', strtotime($item['reviewed_at'])) ?></td></tr>
    <?php endif; ?>
  </table>
</div>

<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('cash_closing.cash_reconciliation') ?></h3>
  <table>
    <tr>
      <th><?= __('cash_closing.opening_cash') ?></th>
      <td><?= number_format($openingCash, 0, ',', '.') ?> ₫</td>
    </tr>
    <tr>
      <th><?= __('cash_closing.cash_income') ?></th>
      <td style="color: #27ae60;">+ <?= number_format($theoretical['income'], 0, ',', '.') ?> ₫</td>
    </tr>
    <tr>
      <th><?= __('cash_closing.cash_expense') ?></th>
      <td style="color: #e74c3c;">- <?= number_format($theoretical['expense'], 0, ',', '.') ?> ₫</td>
    </tr>
    <tr>
      <th style="font-weight: bold;"><?= __('cash_closing.theoretical_balance') ?></th>
      <td style="font-weight: bold; font-size: 1.1em;">
        = <?= number_format($theoretical['balance'], 0, ',', '.') ?> ₫
      </td>
    </tr>
    <tr>
      <th><?= __('cash_closing.cash_counted') ?></th>
      <td style="font-weight: bold;"><?= number_format($item['cash_counted'], 0, ',', '.') ?> ₫</td>
    </tr>
    <tr>
      <th style="font-weight: bold;"><?= __('cash_closing.difference') ?></th>
      <td style="font-weight: bold; font-size: 1.1em; color: <?= abs($difference) > 10000 ? '#e74c3c' : ($difference > 0 ? '#27ae60' : '#e74c3c'); ?>;">
        <?= $difference > 0 ? '+' : '' ?><?= number_format($difference, 0, ',', '.') ?> ₫
      </td>
    </tr>
  </table>
  
  <?php if ($item['cash_note']): ?>
  <div style="margin-top: 16px;">
    <strong><?= __('cash_closing.cash_note') ?>:</strong>
    <p style="margin-top: 8px; padding: 12px; background: #f5f5f5; border-radius: 4px;">
      <?= nl2br(htmlspecialchars($item['cash_note'])) ?>
    </p>
  </div>
  <?php endif; ?>
</div>

<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('cash_closing.payment_summary') ?></h3>
  <table>
    <tr>
      <th><?= __('tx.payment') ?></th>
      <th><?= __('tx.income') ?></th>
      <th><?= __('tx.expense') ?></th>
      <th><?= __('dashboard.net') ?></th>
    </tr>
    <?php foreach ($paymentSummaries as $summary): ?>
    <?php
    $lang = I18n::current();
    $pmName = $lang === 'zh' ? $summary['payment_method']['name_zh'] : $summary['payment_method']['name_vi'];
    $net = $summary['income'] - $summary['expense'];
    ?>
    <tr>
      <td><?= htmlspecialchars($pmName) ?></td>
      <td style="color: #27ae60;">+ <?= number_format($summary['income'], 0, ',', '.') ?> ₫</td>
      <td style="color: #e74c3c;">- <?= number_format($summary['expense'], 0, ',', '.') ?> ₫</td>
      <td style="font-weight: bold;"><?= number_format($net, 0, ',', '.') ?> ₫</td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

<?php if ($item['review_note']): ?>
<div class="card">
  <h3 style="margin-bottom: 16px;"><?= __('cash_closing.review_note') ?></h3>
  <p style="padding: 12px; background: #f5f5f5; border-radius: 4px;">
    <?= nl2br(htmlspecialchars($item['review_note'])) ?>
  </p>
</div>
<?php endif; ?>

<div style="margin-top: 16px;">
  <?php if ($item['status'] === 'draft' && Auth::user()['role_key'] === 'manager' && $item['created_by'] == Auth::user()['id']): ?>
  <form method="post" action="/index.php?r=cash_closings/submit&id=<?= $item['id'] ?>" style="display: inline;">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <button type="submit" class="btn btn-success"><?= __('cash_closing.submit') ?></button>
  </form>
  <?php endif; ?>
  
  <?php if ($item['status'] === 'submitted' && in_array(Auth::user()['role_key'], ['owner', 'accountant'])): ?>
  <div class="card" style="margin-top: 16px;">
    <h3 style="margin-bottom: 12px;"><?= __('cash_closing.review') ?></h3>
    <form method="post" action="/index.php?r=cash_closings/review&id=<?= $item['id'] ?>">
      <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
      <div class="form-group">
        <label><?= __('cash_closing.review_status') ?></label>
        <select name="status" required>
          <option value="approved"><?= __('cash_closing.approve') ?></option>
          <option value="rejected"><?= __('cash_closing.reject') ?></option>
        </select>
      </div>
      <div class="form-group">
        <label><?= __('cash_closing.review_note') ?> *</label>
        <textarea name="review_note" rows="3" required></textarea>
        <small><?= __('cash_closing.review_note_hint') ?></small>
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-success"><?= __('btn.save') ?></button>
      </div>
    </form>
  </div>
  <?php endif; ?>
  
  <a href="/index.php?r=cash_closings/list" class="btn"><?= __('btn.back') ?></a>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>






