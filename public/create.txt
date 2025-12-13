<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?= __('nav.add') ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<h2><?= __('nav.add') ?></h2>

<form method="post">
<input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

<select name="type">
  <option value="income"><?= __('tx.income') ?></option>
  <option value="expense"><?= __('tx.expense') ?></option>
</select><br>

<label><?= __('field.amount') ?></label>
<input name="amount" type="number" step="0.01" required><br>

<label><?= __('field.category') ?></label>
<input name="category_id" required><br>

<label><?= __('field.payment') ?></label>
<input name="payment_method_id" required><br>

<label><?= __('field.time') ?></label>
<input name="occurred_at" type="datetime-local" required><br>

<label><?= __('field.note') ?></label>
<textarea name="note"></textarea><br>

<button type="submit"><?= __('btn.save') ?></button>
</form>

</body>
</html>
