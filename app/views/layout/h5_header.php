<!doctype html>
<html lang="<?= I18n::current() ?>">
<head>
<meta charset="utf-8">
<title><?= $title ?? __('app.title') ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
<meta name="description" content="<?= __('app.title') ?>">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<link rel="stylesheet" href="/assets/css/h5-style.css">
</head>
<body>
<?php if (Auth::check()): ?>
<div class="h5-header">
  <?php if (isset($show_back) && $show_back): ?>
  <a href="javascript:history.back()" class="h5-back-btn">←</a>
  <?php endif; ?>
  <div class="h5-title"><?= $title ?? __('app.title') ?></div>
</div>
<div class="h5-content">
<?php else: ?>
<div class="h5-content" style="padding-top: 0;">
<?php endif; ?>






