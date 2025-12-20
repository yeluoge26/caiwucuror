<?php
$title = __('cash_closing.create', '现金日结');
$show_back = true;
include __DIR__ . '/../layout/h5_header.php';

// 检查是否已提交
$isSubmitted = isset($existing) && $existing && $existing['status'] === 'submitted';
$isApproved = isset($existing) && $existing && $existing['status'] === 'approved';
?>

<?php if (isset($error)): ?>
<div class="h5-card" style="background: #fee; border: 1px solid #fcc;">
  <div style="color: #c33;"><?= htmlspecialchars($error) ?></div>
</div>
<?php endif; ?>

<?php if ($isSubmitted || $isApproved): ?>
<div class="h5-card" style="background: #fff3cd; border: 1px solid #ffc107;">
  <div style="text-align: center; color: #856404;">
    <div style="font-size: 18px; margin-bottom: 8px;">
      <?= $isApproved ? '✅' : '⏳' ?>
      <?= $isApproved ? __('cash_closing.status_approved', '已审核通过') : __('cash_closing.status_submitted', '已提交，等待审核') ?>
    </div>
    <div style="font-size: 14px;">
      <?= __('cash_closing.cannot_edit', '不允许再次编辑') ?>
    </div>
  </div>
</div>
<?php endif; ?>

<form method="post" id="cash-closing-form">
  <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
  <input type="hidden" name="date" value="<?= htmlspecialchars($data['date']) ?>">

  <!-- 今日收款总览（系统计算，只读） -->
  <div class="h5-card">
    <div class="h5-card-title">📊 <?= __('cash_closing.today_summary', '今日收款汇总') ?></div>
    <div class="amount-display">
      <div class="amount-row">
        <span class="amount-label"><?= __('cash_closing.cash_income', '现金收入') ?></span>
        <span class="amount-value amount-positive">
          + <?= number_format($theoretical['income'], 0, ',', '.') ?> ₫
        </span>
      </div>
      <div class="amount-row">
        <span class="amount-label"><?= __('cash_closing.cash_expense', '现金支出') ?></span>
        <span class="amount-value amount-negative">
          - <?= number_format($theoretical['expense'], 0, ',', '.') ?> ₫
        </span>
      </div>
      <div class="amount-row" style="border-top: 2px solid #e5e7eb; margin-top: 8px; padding-top: 12px;">
        <span class="amount-label"><?= __('cash_closing.theoretical_balance', '理论现金') ?></span>
        <span class="amount-value" style="font-size: 20px;">
          = <?= number_format($theoretical['balance'], 0, ',', '.') ?> ₫
        </span>
      </div>
    </div>
    <div class="h5-hint" style="margin-top: 8px;">
      <?= __('cash_closing.system_calculated', '这是系统根据今日现金流水自动计算的金额') ?>
    </div>
  </div>

  <!-- 现金对账（核心填写区） -->
  <div class="h5-card">
    <div class="h5-card-title">🧮 <?= __('cash_closing.cash_reconciliation', '现金对账') ?></div>
    
    <div class="h5-form-group">
      <label><?= __('cash_closing.cash_counted', '实盘现金') ?>（<?= __('field.required', '必填') ?>）</label>
      <input type="number" 
             name="cash_counted" 
             id="cash-counted" 
             step="1000" 
             min="0" 
             value="<?= htmlspecialchars($data['cash_counted']) ?>" 
             required
             <?= ($isSubmitted || $isApproved) ? 'readonly' : '' ?>
             placeholder="<?= __('cash_closing.enter_amount', '请输入现金金额') ?>">
      <small><?= __('cash_closing.cash_counted_hint', '只能输入数字，不可负数') ?></small>
    </div>

    <!-- 实时显示差额 -->
    <div id="difference-display" style="display: none; margin-top: 12px;">
      <div class="amount-display">
        <div class="amount-row">
          <span class="amount-label"><?= __('cash_closing.system_cash', '系统现金') ?></span>
          <span class="amount-value" id="system-cash">
            <?= number_format($theoretical['balance'], 0, ',', '.') ?> ₫
          </span>
        </div>
        <div class="amount-row">
          <span class="amount-label"><?= __('cash_closing.counted_cash', '实盘现金') ?></span>
          <span class="amount-value" id="counted-cash">0 ₫</span>
        </div>
        <div class="amount-row" style="border-top: 2px solid #e5e7eb; margin-top: 8px; padding-top: 12px;">
          <span class="amount-label"><?= __('cash_closing.difference', '差额') ?></span>
          <span class="amount-value" id="difference-value" style="font-size: 20px;">0 ₫</span>
        </div>
      </div>
    </div>
  </div>

  <!-- 差额说明（条件显示） -->
  <div class="h5-card" id="difference-note-card" style="display: none;">
    <div class="h5-card-title">✏️ <?= __('cash_closing.difference_note', '差额说明') ?>（<?= __('field.required', '必填') ?>）</div>
    <div class="h5-form-group">
      <textarea name="cash_note" 
                id="cash-note" 
                rows="4" 
                placeholder="<?= __('cash_closing.difference_note_hint', '如：找零错误 / 顾客未付清 / 临时垫付') ?>"></textarea>
      <small class="h5-hint required"><?= __('cash_closing.difference_note_required', '差额不为0时必须填写说明') ?></small>
    </div>
  </div>

  <!-- 提交按钮（固定底部） -->
  <?php if (!$isSubmitted && !$isApproved): ?>
  <div class="fixed-bottom-btn">
    <button type="submit" class="h5-btn h5-btn-success" id="submit-btn" disabled>
      ✅ <?= __('cash_closing.submit', '提交今日交账') ?>
    </button>
  </div>
  <?php endif; ?>
</form>

<script>
const cashCountedInput = document.getElementById('cash-counted');
const differenceDisplay = document.getElementById('difference-display');
const differenceNoteCard = document.getElementById('difference-note-card');
const cashNoteInput = document.getElementById('cash-note');
const submitBtn = document.getElementById('submit-btn');
const systemCash = <?= $theoretical['balance'] ?>;

function updateDifference() {
  const counted = parseFloat(cashCountedInput.value) || 0;
  const difference = counted - systemCash;
  
  if (counted > 0) {
    // 显示差额
    differenceDisplay.style.display = 'block';
    document.getElementById('counted-cash').textContent = counted.toLocaleString('vi-VN') + ' ₫';
    
    const diffValue = document.getElementById('difference-value');
    diffValue.textContent = (difference >= 0 ? '+' : '') + difference.toLocaleString('vi-VN') + ' ₫';
    diffValue.className = 'amount-value ' + (difference >= 0 ? 'amount-positive' : 'amount-negative');
    
    // 如果差额不为0，显示说明输入框
    if (Math.abs(difference) > 0.01) {
      differenceNoteCard.style.display = 'block';
      cashNoteInput.required = true;
    } else {
      differenceNoteCard.style.display = 'none';
      cashNoteInput.required = false;
    }
    
    // 更新提交按钮状态
    updateSubmitButton();
  } else {
    differenceDisplay.style.display = 'none';
    differenceNoteCard.style.display = 'none';
    cashNoteInput.required = false;
    updateSubmitButton();
  }
}

function updateSubmitButton() {
  const counted = parseFloat(cashCountedInput.value) || 0;
  const difference = Math.abs((counted - systemCash));
  const hasNote = cashNoteInput.value.trim().length > 0;
  
  if (counted > 0) {
    if (difference > 0.01 && !hasNote) {
      submitBtn.disabled = true;
    } else {
      submitBtn.disabled = false;
    }
  } else {
    submitBtn.disabled = true;
  }
}

cashCountedInput.addEventListener('input', updateDifference);
cashNoteInput.addEventListener('input', updateSubmitButton);

// 表单提交验证
document.getElementById('cash-closing-form').addEventListener('submit', function(e) {
  const counted = parseFloat(cashCountedInput.value) || 0;
  const difference = Math.abs((counted - systemCash));
  const hasNote = cashNoteInput.value.trim().length > 0;
  
  if (counted <= 0) {
    e.preventDefault();
    alert('<?= __('cash_closing.enter_amount', '请输入实盘现金金额') ?>');
    return false;
  }
  
  if (difference > 0.01 && !hasNote) {
    e.preventDefault();
    alert('<?= __('cash_closing.difference_note_required', '差额不为0时必须填写说明') ?>');
    cashNoteInput.focus();
    return false;
  }
});

// 初始化
updateDifference();
</script>

<?php include __DIR__ . '/../layout/h5_footer.php'; ?>
