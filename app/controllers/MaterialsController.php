<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../models/Material.php';
require_once __DIR__ . '/../models/MaterialAttachment.php';

class MaterialsController {
  public function list() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $items = Material::allGroupedByName();
    include __DIR__ . '/../views/materials/list.php';
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
          'name' => $_POST['name'] ?? '',
          'category' => $_POST['category'] ?? '',
          'store' => $_POST['store'] ?? 'coffee',
          'unit' => $_POST['unit'] ?? '',
          'quantity' => $_POST['quantity'] ?? 0,
          'min_quantity' => $_POST['min_quantity'] ?? 0,
          'note' => $_POST['note'] ?? null,
          'created_by' => Auth::user()['id'],
        ];
        $materialId = Material::addOrIncrement($data);
        if ($materialId) {
          $fileInput = $_FILES['material_images'] ?? ($_FILES['material_image'] ?? ($_FILES['photo'] ?? null));
          if (!empty($fileInput)) {
            MaterialAttachment::saveUploads($materialId, $fileInput, Auth::user()['id']);
          }
        }
        header('Location: /index.php?r=materials/list');
        exit;
      }
    }

    include __DIR__ . '/../views/materials/create.php';
  }
}
