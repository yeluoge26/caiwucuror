<?php
$title = __('cash_closing.create');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('cash_closing.create') ?></h2>

<div class="card">
  <?php if (isset($error)): ?>
  <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  
  <form method="post">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    
    <div class="form-group">
      <label><?= __('cash_closing.date') ?></label>
      <input type="date" name="date" value="<?= htmlspecialchars($data['date']) ?>" readonly class="readonly">
      <small><?= __('cash_closing.date_hint') ?></small>
    </div>

    <div class="card" style="background: #f5f5f5; margin-bottom: 16px;">
      <h3 style="margin-bottom: 12px;"><?= __('cash_closing.theoretical_calculation') ?></h3>
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
      </table>
    </div>
    
    <div class="form-group">
      <label><?= __('cash_closing.cash_counted') ?> *</label>
      <input type="number" name="cash_counted" step="0.01" min="0" 
             value="<?= htmlspecialchars($data['cash_counted']) ?>" required>
      <small><?= __('cash_closing.cash_counted_hint') ?></small>
    </div>

    <?php
    $difference = floatval($data['cash_counted']) - $theoretical['balance'];
    if (abs($difference) > 0.01):
    ?>
    <div class="alert" style="background: <?= abs($difference) > 10000 ? '#fee' : '#fff3cd'; ?>; border: 1px solid <?= abs($difference) > 10000 ? '#fcc' : '#ffc107'; ?>;">
      <strong><?= __('cash_closing.difference') ?>:</strong> 
      <span style="color: <?= $difference > 0 ? '#27ae60' : '#e74c3c'; ?>; font-weight: bold;">
        <?= $difference > 0 ? '+' : '' ?><?= number_format($difference, 0, ',', '.') ?> ₫
      </span>
    </div>
    <?php endif; ?>
    
    <div class="form-group">
      <label><?= __('cash_closing.cash_note') ?> <?= abs($difference) > 0.01 ? '*' : '' ?></label>
      <textarea name="cash_note" rows="4" 
                <?= abs($difference) > 0.01 ? 'required' : '' ?>><?= htmlspecialchars($data['cash_note'] ?? '') ?></textarea>
      <small><?= __('cash_closing.cash_note_hint') ?></small>
    </div>
    
    <div class="form-group">
      <button type="submit" class="btn btn-success"><?= __('btn.save') ?></button>
      <a href="/index.php?r=cash_closings/list" class="btn"><?= __('btn.cancel') ?></a>
    </div>
  </form>
</div>

<script>
document.querySelector('input[name="cash_counted"]').addEventListener('input', function() {
  const counted = parseFloat(this.value) || 0;
  const theoretical = <?= $theoretical['balance'] ?>;
  const difference = counted - theoretical;
  
  // 动态显示差额
  let diffAlert = document.querySelector('.difference-alert');
  if (!diffAlert && Math.abs(difference) > 0.01) {
    diffAlert = document.createElement('div');
    diffAlert.className = 'alert difference-alert';
    diffAlert.style.background = Math.abs(difference) > 10000 ? '#fee' : '#fff3cd';
    diffAlert.style.border = '1px solid ' + (Math.abs(difference) > 10000 ? '#fcc' : '#ffc107');
    this.parentElement.after(diffAlert);
  }
  
  if (diffAlert) {
    if (Math.abs(difference) > 0.01) {
      diffAlert.innerHTML = '<strong><?= __('cash_closing.difference') ?>:</strong> ' +
        '<span style="color: ' + (difference > 0 ? '#27ae60' : '#e74c3c') + '; font-weight: bold;">' +
        (difference > 0 ? '+' : '') + difference.toLocaleString('vi-VN') + ' ₫</span>';
      diffAlert.style.display = 'block';
      
      // 如果差额不为0，要求填写说明
      const noteField = document.querySelector('textarea[name="cash_note"]');
      if (noteField) {
        noteField.required = true;
      }
    } else {
      diffAlert.style.display = 'none';
      const noteField = document.querySelector('textarea[name="cash_note"]');
      if (noteField) {
        noteField.required = false;
      }
    }
  }
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>

