<?php
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/ApiGuard.php';
require_once __DIR__ . '/../../models/Vendor.php';

class VendorApiController {
  public function list() {
    ApiGuard::requireLogin();
    
    $filters = [
      'is_active' => isset($_GET['is_active']) ? (int)$_GET['is_active'] : 1,
      'search' => $_GET['search'] ?? null
    ];
    
    $items = Vendor::list($filters);
    Response::success($items);
  }
}
