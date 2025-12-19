<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../models/Asset.php';
require_once __DIR__ . '/../models/AssetAttachment.php';

class AssetsController {
  public function list() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $items = Asset::all();
    include __DIR__ . '/../views/assets/list.php';
  }

  public function create() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        $data = [
          'category' => $_POST['category'] ?? '',
          'subcategory' => $_POST['subcategory'] ?? null,
          'name' => $_POST['name'] ?? '',
          'floor' => $_POST['floor'] ?? null,
          'location' => $_POST['location'] ?? null,
          'price' => $_POST['price'] ?? 0,
          'quantity' => max(1, (int)($_POST['quantity'] ?? 1)),
          'acquired_at' => $_POST['acquired_at'] ?? date('Y-m-d'),
          'note' => $_POST['note'] ?? null,
          'created_by' => Auth::user()['id'],
        ];
        $assetId = Asset::create($data);
        if ($assetId) {
          $fileInput = $_FILES['asset_images'] ?? ($_FILES['asset_image'] ?? ($_FILES['photo'] ?? null));
          if (!empty($fileInput)) {
            AssetAttachment::saveUploads($assetId, $fileInput, Auth::user()['id']);
          }
        }
        header('Location: /index.php?r=assets/list');
        exit;
      }
    }

    include __DIR__ . '/../views/assets/create.php';
  }

  public function export() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $items = Asset::all();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="assets_' . date('Ymd_His') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['store', 'subcategory', 'name', 'floor', 'location', 'price', 'acquired_at', 'note']);
    foreach ($items as $row) {
      fputcsv($out, [
        $row['category'],
        $row['subcategory'],
        $row['name'],
        $row['floor'],
        $row['location'],
        $row['price'],
        $row['acquired_at'],
        $row['note'],
      ]);
    }
    fclose($out);
    exit;
  }

  public function import() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['asset_csv'])) {
      header('Location: /index.php?r=assets/list');
      exit;
    }

    if (!Csrf::check($_POST['_csrf'] ?? '')) {
      header('Location: /index.php?r=assets/list&import=0&failed=1');
      exit;
    }

    if ($_FILES['asset_csv']['error'] !== UPLOAD_ERR_OK) {
      header('Location: /index.php?r=assets/list&import=0&failed=1');
      exit;
    }

    $file = $_FILES['asset_csv']['tmp_name'];
    $handle = fopen($file, 'r');
    if (!$handle) {
      header('Location: /index.php?r=assets/list&import=0&failed=1');
      exit;
    }

    // skip header
    fgetcsv($handle);
    $allowedStores = ['coffee', 'office', 'whiskey'];
    $success = 0;
    $failed = 0;
    while (($row = fgetcsv($handle)) !== false) {
      if (count($row) < 7) {
        $failed++; continue;
      }
      [$store, $sub, $name, $floor, $location, $price, $acquired, $note] = array_pad($row, 8, null);
      if (!in_array($store, $allowedStores, true)) {
        $failed++; continue;
      }
      $data = [
        'category' => $store,
        'subcategory' => $sub ?: null,
        'name' => $name ?: '',
        'floor' => $floor ?: null,
        'location' => $location ?: null,
        'price' => is_numeric($price) ? (float)$price : 0,
        'acquired_at' => $acquired ?: date('Y-m-d'),
        'note' => $note ?: null,
        'created_by' => Auth::user()['id'],
      ];
      $assetId = Asset::create($data);
      if ($assetId) $success++; else $failed++;
    }
    fclose($handle);
    header('Location: /index.php?r=assets/list&import=1&success=' . $success . '&failed=' . $failed);
    exit;
  }

  public function view() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=assets/list');
      exit;
    }
    $asset = Asset::find($id);
    if (!$asset) {
      header('Location: /index.php?r=assets/list');
      exit;
    }
    $attachments = AssetAttachment::listByAsset($id);
    include __DIR__ . '/../views/assets/view.php';
  }

  public function void() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=assets/list');
      exit;
    }
    $asset = Asset::find($id);
    if (!$asset || $asset['status'] === 'void') {
      header('Location: /index.php?r=assets/list');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        die('CSRF invalid');
      }
      Asset::void($id);
      header('Location: /index.php?r=assets/list');
      exit;
    }

    include __DIR__ . '/../views/assets/void.php';
  }
}
