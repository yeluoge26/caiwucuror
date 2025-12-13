<?php
$title = __('nav.dashboard');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('nav.dashboard') ?></h2>

<!-- 今日统计 -->
<div class="card">
  <h3 style="margin-bottom: 16px;">📅 <?= __('dashboard.today') ?></h3>
  <div class="kpi income">+ <?= number_format($today['income'] ?? 0, 0, ',', '.') ?> ₫</div>
  <div class="kpi expense">- <?= number_format($today['expense'] ?? 0, 0, ',', '.') ?> ₫</div>
  <div class="kpi net">
    = <?= number_format(($today['income'] ?? 0) - ($today['expense'] ?? 0), 0, ',', '.') ?> ₫
  </div>
</div>

<!-- 本月统计 -->
<div class="card">
  <h3 style="margin-bottom: 16px;">📊 <?= __('dashboard.month') ?></h3>
  <div class="kpi income">+ <?= number_format($month['income'] ?? 0, 0, ',', '.') ?> ₫</div>
  <div class="kpi expense">- <?= number_format($month['expense'] ?? 0, 0, ',', '.') ?> ₫</div>
  <div class="kpi net">
    = <?= number_format(($month['income'] ?? 0) - ($month['expense'] ?? 0), 0, ',', '.') ?> ₫
  </div>
</div>

<!-- 7天趋势 -->
<div class="card">
  <h3 style="margin-bottom: 16px;">📈 <?= __('dashboard.trend') ?> (7 <?= __('today') ?>)</h3>
  <table>
    <tr>
      <th><?= __('field.time') ?></th>
      <th><?= __('tx.income') ?></th>
      <th><?= __('tx.expense') ?></th>
      <th><?= __('dashboard.net') ?></th>
    </tr>
    <?php if (empty($trend)): ?>
    <tr>
      <td colspan="4" style="text-align: center; color: #999;"><?= __('list.no_data') ?></td>
    </tr>
    <?php else: ?>
    <?php foreach ($trend as $d): ?>
    <tr>
      <td><?= date('d/m/Y', strtotime($d['day'])) ?></td>
      <td style="color: #27ae60;">+ <?= number_format($d['income'], 0, ',', '.') ?> ₫</td>
      <td style="color: #e74c3c;">- <?= number_format($d['expense'], 0, ',', '.') ?> ₫</td>
      <td style="font-weight: bold;">
        <?= number_format($d['income'] - $d['expense'], 0, ',', '.') ?> ₫
      </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
  </table>
</div>

<!-- 最近流水 -->
<div class="card">
  <h3 style="margin-bottom: 16px;">🧾 <?= __('dashboard.latest') ?></h3>
  <table>
    <tr>
      <th>ID</th>
      <th><?= __('tx.type') ?></th>
      <th><?= __('tx.amount') ?></th>
      <th><?= __('tx.category') ?></th>
      <th><?= __('field.time') ?></th>
    </tr>
    <?php if (empty($latest)): ?>
    <tr>
      <td colspan="5" style="text-align: center; color: #999;"><?= __('list.no_data') ?></td>
    </tr>
    <?php else: ?>
    <?php foreach ($latest as $row): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td>
        <span class="badge <?= $row['type'] === 'income' ? 'badge-income' : 'badge-expense' ?>">
          <?= $row['type'] === 'income' ? __('tx.income') : __('tx.expense') ?>
        </span>
      </td>
      <td style="font-weight: bold;">
        <?= number_format($row['amount'], 0, ',', '.') ?> ₫
      </td>
      <td>
        <?php
        $lang = I18n::current();
        $categoryName = $lang === 'zh' ? ($row['category_name_zh'] ?? '') : ($row['category_name_vi'] ?? '');
        echo htmlspecialchars($categoryName);
        ?>
      </td>
      <td><?= date('d/m/Y H:i', strtotime($row['occurred_at'])) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
  </table>
  <div style="margin-top: 12px;">
    <a href="/index.php?r=transactions/list" class="btn"><?= __('nav.list') ?></a>
  </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

