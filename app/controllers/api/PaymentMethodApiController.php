<?php
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/ApiGuard.php';
require_once __DIR__ . '/../../models/PaymentMethod.php';

class PaymentMethodApiController {
  public function list() {
    ApiGuard::requireLogin();
    
    $filters = [
      'is_active' => isset($_GET['is_active']) ? (int)$_GET['is_active'] : 1
    ];
    
    $items = PaymentMethod::list($filters);
    Response::success($items);
  }
}
