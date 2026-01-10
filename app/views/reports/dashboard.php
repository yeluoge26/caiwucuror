<?php
$title = __('nav.dashboard');
include __DIR__ . '/../layout/header.php';
$lang = I18n::current();
?>

<div class="page-header">
  <h1><?= __('nav.dashboard') ?></h1>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px;">
  <!-- 今日统计 -->
  <div class="kpi-card">
    <div class="kpi-label">📅 <?= __('dashboard.today') ?></div>
    <div style="display: flex; flex-direction: column; gap: 12px;">
      <div>
        <div style="font-size: 12px; color: #6B7280; margin-bottom: 4px;"><?= __('tx.income') ?></div>
        <div class="kpi-value income">+ <?= number_format($today['income'] ?? 0, 0, ',', '.') ?> ₫</div>
      </div>
      <div>
        <div style="font-size: 12px; color: #6B7280; margin-bottom: 4px;"><?= __('tx.expense') ?></div>
        <div class="kpi-value expense">- <?= number_format($today['expense'] ?? 0, 0, ',', '.') ?> ₫</div>
      </div>
      <div style="padding-top: 12px; border-top: 1px solid rgba(139, 69, 19, 0.1);">
        <div style="font-size: 12px; color: #6B7280; margin-bottom: 4px;"><?= __('dashboard.net') ?></div>
        <div class="kpi-value net">
          <?= number_format(($today['income'] ?? 0) - ($today['expense'] ?? 0), 0, ',', '.') ?> ₫
        </div>
      </div>
    </div>
  </div>

  <!-- 本月统计 -->
  <div class="kpi-card">
    <div class="kpi-label">📊 <?= __('dashboard.month') ?></div>
    <div style="display: flex; flex-direction: column; gap: 12px;">
      <div>
        <div style="font-size: 12px; color: #6B7280; margin-bottom: 4px;"><?= __('tx.income') ?></div>
        <div class="kpi-value income">+ <?= number_format($month['income'] ?? 0, 0, ',', '.') ?> ₫</div>
      </div>
      <div>
        <div style="font-size: 12px; color: #6B7280; margin-bottom: 4px;"><?= __('tx.expense') ?></div>
        <div class="kpi-value expense">- <?= number_format($month['expense'] ?? 0, 0, ',', '.') ?> ₫</div>
      </div>
      <div style="padding-top: 12px; border-top: 1px solid rgba(139, 69, 19, 0.1);">
        <div style="font-size: 12px; color: #6B7280; margin-bottom: 4px;"><?= __('dashboard.net') ?></div>
        <div class="kpi-value net">
          <?= number_format(($month['income'] ?? 0) - ($month['expense'] ?? 0), 0, ',', '.') ?> ₫
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 7天趋势 -->
<div class="card">
  <div class="card-title">📈 <?= __('dashboard.trend') ?> (7 <?= __('today') ?>)</div>
  <div class="table-responsive">
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
</div>

<!-- 最近流水 -->
<div class="card">
  <div class="card-title">🧾 <?= __('dashboard.latest') ?></div>
  <div class="table-responsive">
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
  </div>
  <div style="margin-top: 16px;">
    <a href="/index.php?r=transactions/list" class="btn btn-primary"><?= __('nav.list') ?></a>
  </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
