<?php
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/ApiGuard.php';
require_once __DIR__ . '/../../models/Category.php';

class CategoryApiController {
  public function list() {
    ApiGuard::requireLogin();
    
    $filters = [
      'type' => $_GET['type'] ?? null,
      'is_active' => isset($_GET['is_active']) ? (int)$_GET['is_active'] : 1
    ];
    
    $items = Category::list($filters);
    Response::success($items);
  }
}
