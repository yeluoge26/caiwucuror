<?php
$title = __('asset.detail');
include __DIR__ . '/../layout/header.php';
?>

<h2 style="margin-bottom: 20px;"><?= __('inspection.detail') ?></h2>

<div class="card">
  <table>
    <tr><th style="width:160px;">ID</th><td><?= $item['id'] ?></td></tr>
    <tr><th><?= __('material.store') ?></th><td><?= htmlspecialchars($item['store']) ?></td></tr>
    <tr><th><?= __('inspection.floor') ?></th><td><?= htmlspecialchars($item['floor']) ?></td></tr>
    <tr><th><?= __('inspection.visit_no') ?></th><td><?= $item['visit_no'] == 2 ? __('inspection.visit_second') : __('inspection.visit_first') ?></td></tr>
    <tr><th><?= __('inspection.room') ?></th><td>
      <?php
      $roomMap = [
        'store' => __('inspection.room_store'),
        'restroom' => __('inspection.room_restroom'),
        'stair' => __('inspection.room_stair')
      ];
      echo htmlspecialchars($roomMap[$item['room']] ?? $item['room']);
      ?>
    </td></tr>
    <tr><th><?= __('inspection.status') ?></th><td><?= $item['status'] === 'ok' ? __('inspection.ok') : __('inspection.issue') ?></td></tr>
    <tr><th><?= __('inspection.photo') ?></th><td>
      <?php if (!empty($photos)): ?>
        <?php foreach ($photos as $p): ?>
          <a href="/<?= htmlspecialchars($p['file_path']) ?>" target="_blank" style="margin-right:6px;"><?= __('inspection.view_photo') ?></a>
        <?php endforeach; ?>
      <?php else: ?>-<?php endif; ?>
    </td></tr>
    <tr><th><?= __('field.note') ?></th><td><?= nl2br(htmlspecialchars($item['note'] ?? '')) ?></td></tr>
    <tr><th><?= __('tx.create') ?></th><td><?= htmlspecialchars($item['creator_name'] ?? '') ?> @ <?= date('Y-m-d', strtotime($item['spot_date'])) ?></td></tr>
    <tr><th><?= __('inspection.review_status') ?></th><td>
      <?php
      if ($item['reviewed_status'] === 'confirmed') echo __('inspection.review_ok');
      elseif ($item['reviewed_status'] === 'rejected') echo __('inspection.review_reject');
      else echo __('inspection.pending');
      ?>
    </td></tr>
    <tr><th><?= __('inspection.review_note') ?></th><td><?= nl2br(htmlspecialchars($item['review_note'] ?? '')) ?></td></tr>
    <tr><th><?= __('inspection.review_by') ?></th><td><?= htmlspecialchars($item['reviewer_name'] ?? '-') ?></td></tr>
  </table>

  <div style="margin-top:12px;">
    <a href="/index.php?r=inspections/list" class="btn"><?= __('btn.cancel') ?></a>
  </div>
</div>

<?php if (in_array(Auth::user()['role_key'], ['owner', 'manager', 'accountant']) && $item['reviewed_status'] === 'pending'): ?>
<div class="card">
  <h3 style="margin-bottom: 12px;"><?= __('inspection.review') ?></h3>
  <form method="post" action="/index.php?r=inspections/review&id=<?= $item['id'] ?>">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <div class="form-group">
      <label><?= __('inspection.review_status') ?></label>
      <select name="reviewed_status" required>
        <option value="confirmed"><?= __('inspection.review_ok') ?></option>
        <option value="rejected"><?= __('inspection.review_reject') ?></option>
      </select>
    </div>
    <div class="form-group">
      <label><?= __('inspection.review_note') ?></label>
      <textarea name="review_note"></textarea>
    </div>
    <div class="form-group">
      <button type="submit" class="btn btn-success"><?= __('btn.save') ?></button>
    </div>
  </form>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>
