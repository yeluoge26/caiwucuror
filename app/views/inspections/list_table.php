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
          <?php 
          // 检查是否有照片路径
          $photoPath = $row['thumb_path'] ?? null;
          $photoCount = $row['photo_count'] ?? 0;
          
          if (!empty($photoPath)): 
            // 确保路径以 / 开头（如果路径是 uploads/inspections/xxx.jpg，需要加上 /）
            $photoUrl = (strpos($photoPath, '/') === 0) ? $photoPath : '/' . $photoPath;
          ?>
          <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
            <a href="<?= htmlspecialchars($photoUrl) ?>" target="_blank" style="display: inline-block;">
              <img src="<?= htmlspecialchars($photoUrl) ?>" alt="照片" style="max-width: 50px; max-height: 50px; border-radius: 4px; object-fit: cover; border: 1px solid #ddd;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
              <span style="display: none; color: #e74c3c; font-size: 11px;">图片加载失败</span>
            </a>
            <div style="display: flex; flex-direction: column; gap: 2px;">
              <a href="/index.php?r=inspections/view&id=<?= $row['id'] ?>" style="font-size: 12px; color: #3498db; text-decoration: none;">
                <?= __('inspection.view_photo', '查看照片') ?>
              </a>
              <?php if ($photoCount > 1): ?>
              <span style="font-size: 11px; color: #666;">共 <?= $photoCount ?> 张</span>
              <?php endif; ?>
            </div>
          </div>
          <?php else: ?>
          <span style="color: #999;">-</span>
          <?php endif; ?>
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
