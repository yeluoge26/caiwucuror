<?php
require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../core/Auth.php';

class ApiGuard {
  public static function requireLogin() {
    if (!Auth::check()) {
      Response::json(['error' => 'unauthorized'], 401);
    }
  }

  public static function requireRole($roles) {
    self::requireLogin();
    $user = Auth::user();
    if (!in_array($user['role_key'], (array)$roles)) {
      Response::json(['error' => 'forbidden'], 403);
    }
  }
}
