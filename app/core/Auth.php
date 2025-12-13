<?php
require_once __DIR__ . '/../models/User.php';

class Auth {
  public static function check() {
    return isset($_SESSION['user_id']);
  }

  public static function user() {
    if (!self::check()) {
      return null;
    }
    return User::find($_SESSION['user_id']);
  }

  public static function requireLogin() {
    if (!self::check()) {
      header('Location: /index.php?r=auth/login');
      exit;
    }
  }

  public static function requireRole($roles) {
    self::requireLogin();
    $user = self::user();
    if (!in_array($user['role_key'], (array)$roles)) {
      http_response_code(403);
      die('Access denied');
    }
  }

  public static function login($username, $password) {
    $user = User::findByUsername($username);
    if (!$user || !$user['is_active']) {
      return false;
    }
    if (!password_verify($password, $user['password_hash'])) {
      return false;
    }
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role_key'];
    return true;
  }

  public static function logout() {
    session_destroy();
    header('Location: /index.php?r=auth/login');
    exit;
  }
}

