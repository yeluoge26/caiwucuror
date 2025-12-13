<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?= __('nav.list') ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<h2><?= __('nav.list') ?></h2>

<a href="/index.php?r=transactions/create"><?= __('nav.add') ?></a>

<table border="1" width="100%">
<tr>
  <th>ID</th>
  <th><?= __('tx.income') ?>/<?= __('tx.expense') ?></th>
  <th><?= __('field.amount') ?></th>
  <th><?= __('field.time') ?></th>
  <th><?= __('field.note') ?></th>
</tr>

<?php foreach ($items as $row): ?>
<tr>
  <td><?= $row['id'] ?></td>
  <td><?= $row['type'] ?></td>
  <td><?= number_format($row['amount']) ?></td>
  <td><?= $row['occurred_at'] ?></td>
  <td><?= htmlspecialchars($row['note']) ?></td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>
