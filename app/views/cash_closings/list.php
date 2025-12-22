<?php
$title = __('cash_closing.list');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('cash_closing.list') ?></h2>

<div class="card">
  <form method="get" action="/index.php?r=cash_closings/list" style="margin-bottom: 16px;">
    <input type="hidden" name="r" value="cash_closings/list">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
      <div>
        <label><?= __('field.from_date') ?></label>
        <input type="date" name="from_date" value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>">
      </div>
      <div>
        <label><?= __('field.to_date') ?></label>
        <input type="date" name="to_date" value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>">
      </div>
      <div>
        <label><?= __('cash_closing.status') ?></label>
        <select name="status">
          <option value=""><?= __('field.all') ?></option>
          <option value="draft" <?= ($_GET['status'] ?? '') === 'draft' ? 'selected' : '' ?>><?= __('cash_closing.status_draft') ?></option>
          <option value="submitted" <?= ($_GET['status'] ?? '') === 'submitted' ? 'selected' : '' ?>><?= __('cash_closing.status_submitted') ?></option>
          <option value="approved" <?= ($_GET['status'] ?? '') === 'approved' ? 'selected' : '' ?>><?= __('cash_closing.status_approved') ?></option>
          <option value="rejected" <?= ($_GET['status'] ?? '') === 'rejected' ? 'selected' : '' ?>><?= __('cash_closing.status_rejected') ?></option>
        </select>
      </div>
      <div style="display: flex; align-items: flex-end;">
        <button type="submit" class="btn"><?= __('btn.search') ?></button>
        <a href="/index.php?r=cash_closings/create" class="btn btn-success" style="margin-left: 8px;"><?= __('cash_closing.create') ?></a>
      </div>
    </div>
  </form>

  <table>
    <tr>
      <th><?= __('cash_closing.date') ?></th>
      <th><?= __('cash_closing.cash_counted') ?></th>
      <th><?= __('cash_closing.status') ?></th>
      <th><?= __('field.creator') ?></th>
      <th><?= __('field.reviewer') ?></th>
      <th><?= __('field.actions') ?></th>
    </tr>
    <?php if (empty($items)): ?>
    <tr>
      <td colspan="6" style="text-align: center; color: #999;"><?= __('list.no_data') ?></td>
    </tr>
    <?php else: ?>
    <?php foreach ($items as $item): ?>
    <tr>
      <td><?= date('Y-m-d', strtotime($item['date'])) ?></td>
      <td style="font-weight: bold;"><?= number_format($item['cash_counted'], 0, ',', '.') ?> ₫</td>
      <td>
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
      </td>
      <td><?= htmlspecialchars($item['creator_name'] ?? '') ?></td>
      <td><?= htmlspecialchars($item['reviewer_name'] ?? '-') ?></td>
      <td>
        <a href="/index.php?r=cash_closings/view&id=<?= $item['id'] ?>" class="btn btn-sm"><?= __('btn.view') ?></a>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
  </table>

  <?php if ($totalPages > 1): ?>
  <div style="margin-top: 16px; text-align: center;">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="?r=cash_closings/list&page=<?= $i ?><?= !empty($_GET['from_date']) ? '&from_date=' . urlencode($_GET['from_date']) : '' ?><?= !empty($_GET['to_date']) ? '&to_date=' . urlencode($_GET['to_date']) : '' ?><?= !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '' ?>" 
         class="btn btn-sm <?= $i == $page ? 'btn-primary' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>



