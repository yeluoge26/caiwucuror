<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../models/EquipmentRental.php';
require_once __DIR__ . '/../models/PaymentMethod.php';

class EquipmentRentalController {
  public function list() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 50;
    $offset = ($page - 1) * $perPage;

    $filters = [
      'status' => $_GET['status'] ?? null,
      'customer_name' => $_GET['customer_name'] ?? null,
      'equipment_name' => $_GET['equipment_name'] ?? null,
      'from_date' => $_GET['from_date'] ?? null,
      'to_date' => $_GET['to_date'] ?? null,
      'limit' => $perPage,
      'offset' => $offset
    ];

    $items = EquipmentRental::list($filters);
    $total = EquipmentRental::count($filters);
    $totalPages = ceil($total / $perPage);

    include __DIR__ . '/../views/equipment_rentals/list.php';
  }

  public function create() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $error = null;
    $paymentMethods = PaymentMethod::all(['is_active' => 1]);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        $error = __('csrf.invalid');
      } else {
        // 处理证件照片上传
        $idCardPhoto = null;
        if (!empty($_FILES['id_card_photo']) && $_FILES['id_card_photo']['error'] === UPLOAD_ERR_OK) {
          $uploadDir = __DIR__ . '/../../public/uploads/equipment_rentals/';
          if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
          }
          
          $ext = pathinfo($_FILES['id_card_photo']['name'], PATHINFO_EXTENSION);
          $fileName = 'id_card_' . time() . '_' . uniqid() . '.' . $ext;
          $filePath = $uploadDir . $fileName;
          
          if (move_uploaded_file($_FILES['id_card_photo']['tmp_name'], $filePath)) {
            $idCardPhoto = 'uploads/equipment_rentals/' . $fileName;
          } else {
            $error = __('equipment_rental.upload_failed');
          }
        }

        if (!$error) {
          $data = [
            'customer_name' => $_POST['customer_name'] ?? '',
            'equipment_name' => $_POST['equipment_name'] ?? '',
            'equipment_serial' => $_POST['equipment_serial'] ?? null,
            'start_date' => $_POST['start_date'] ?? date('Y-m-d'),
            'end_date' => $_POST['end_date'] ?? date('Y-m-d'),
            'deposit' => floatval($_POST['deposit'] ?? 0),
            'deposit_payment_method_id' => $_POST['deposit_payment_method_id'] ? intval($_POST['deposit_payment_method_id']) : null,
            'id_card_photo' => $idCardPhoto,
            'note' => $_POST['note'] ?? null,
            'created_by' => Auth::user()['id'],
          ];

          // 验证日期
          if (strtotime($data['end_date']) < strtotime($data['start_date'])) {
            $error = __('equipment_rental.end_date_before_start');
          } else {
            $rentalId = EquipmentRental::create($data);
            if ($rentalId) {
              header('Location: /index.php?r=equipment_rentals/view&id=' . $rentalId);
              exit;
            } else {
              $error = __('equipment_rental.create_failed');
            }
          }
        }
      }
    }

    include __DIR__ . '/../views/equipment_rentals/create.php';
  }

  public function view() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=equipment_rentals/list');
      exit;
    }

    $rental = EquipmentRental::find($id);
    if (!$rental) {
      header('Location: /index.php?r=equipment_rentals/list');
      exit;
    }

    include __DIR__ . '/../views/equipment_rentals/view.php';
  }

  public function returnEquipment() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=equipment_rentals/list');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        die('CSRF invalid');
      }

      EquipmentRental::returnEquipment($id);
      header('Location: /index.php?r=equipment_rentals/view&id=' . $id);
      exit;
    }

    header('Location: /index.php?r=equipment_rentals/view&id=' . $id);
    exit;
  }

  public function cancel() {
    Auth::requireLogin();
    Auth::requireRole(['owner', 'manager', 'accountant']);

    $id = $_GET['id'] ?? null;
    if (!$id) {
      header('Location: /index.php?r=equipment_rentals/list');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!Csrf::check($_POST['_csrf'] ?? '')) {
        die('CSRF invalid');
      }

      EquipmentRental::cancel($id);
      header('Location: /index.php?r=equipment_rentals/view&id=' . $id);
      exit;
    }

    header('Location: /index.php?r=equipment_rentals/view&id=' . $id);
    exit;
  }
}
