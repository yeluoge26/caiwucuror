<div class="card">
  <h3 style="margin-bottom: 12px;"><?= __('inspection.list') ?></h3>
  <form method="get" style="margin-bottom:12px;">
    <input type="hidden" name="r" value="inspections/list">
    <label><?= __('field.time') ?></label>
    <input type="date" name="date" value="<?= htmlspecialchars($date ?? '') ?>">
    <button class="btn" type="submit" style="padding:6px 10px; font-size:12px;"><?= __('btn.search') ?></button>
  </form>
  <div class="table-scroll">
    <table>
      <tr>
        <th>ID</th>
        <th><?= __('material.store') ?></th>
        <th><?= __('inspection.floor') ?></th>
        <th><?= __('inspection.visit_no') ?></th>
        <th><?= __('inspection.room') ?></th>
        <th><?= __('inspection.status') ?></th>
        <th><?= __('inspection.photo') ?></th>
        <th><?= __('field.note') ?></th>
        <th><?= __('inspection.manage') ?></th>
        <th><?= __('inspection.review_status') ?></th>
        <th><?= __('list.actions') ?></th>
      </tr>
      <?php if (empty($items)): ?>
      <tr><td colspan="11" style="text-align:center; color:#888;"><?= __('list.no_data') ?></td></tr>
      <?php else: ?>
      <?php foreach ($items as $row): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['store']) ?></td>
        <td><?= htmlspecialchars($row['floor']) ?></td>
        <td><?= $row['visit_no'] == 2 ? __('inspection.visit_second') : __('inspection.visit_first') ?></td>
        <td>
          <?php
            $roomMap = [
              'store' => __('inspection.room_store'),
              'restroom' => __('inspection.room_restroom'),
              'stair' => __('inspection.room_stair')
            ];
            echo htmlspecialchars($roomMap[$row['room']] ?? $row['room']);
          ?>
        </td>
        <td>
          <span class="badge <?= $row['status'] === 'ok' ? 'badge-approved' : 'badge-void' ?>">
            <?= $row['status'] === 'ok' ? __('inspection.ok') : __('inspection.issue') ?>
          </span>
        </td>
        <td>
          <?php if (!empty($row['thumb_path'])): ?>
          <a href="/<?= htmlspecialchars($row['thumb_path']) ?>" target="_blank"><?= __('inspection.view_photo') ?></a>
          <?php else: ?>-<?php endif; ?>
        </td>
        <td><?= htmlspecialchars($row['note'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['creator_name'] ?? '') ?> @ <?= date('Y-m-d', strtotime($row['spot_date'])) ?></td>
        <td>
          <span class="badge">
            <?php
              if ($row['reviewed_status'] === 'confirmed') echo __('inspection.review_ok');
              elseif ($row['reviewed_status'] === 'rejected') echo __('inspection.review_reject');
              else echo __('inspection.pending');
            ?>
          </span>
        </td>
        <td>
          <a href="/index.php?r=inspections/view&id=<?= $row['id'] ?>" class="btn" style="padding:6px 10px; font-size:12px;"><?= __('inspection.review') ?></a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </table>
  </div>
</div>
