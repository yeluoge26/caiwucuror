<?php
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/ApiGuard.php';

class AuthApiController {
  public function login() {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    
    if (empty($username) || empty($password)) {
      Response::error('Username and password are required', 400);
    }
    
    if (Auth::login($username, $password)) {
      session_regenerate_id();
      $user = Auth::user();
      Response::success([
        'token' => session_id(),
        'user' => [
          'id' => $user['id'],
          'username' => $user['username'],
          'display_name' => $user['display_name'],
          'role_key' => $user['role_key'],
          'role_name' => $user['role_name_zh'] ?? $user['role_name_vi'] ?? '',
        ],
      ], 'Login successful');
    } else {
      Response::error('Invalid username or password', 401);
    }
  }

  public function logout() {
    ApiGuard::requireLogin();
    session_destroy();
    Response::success(null, 'Logout successful');
  }

  public function me() {
    ApiGuard::requireLogin();
    $user = Auth::user();
    Response::success([
      'id' => $user['id'],
      'username' => $user['username'],
      'display_name' => $user['display_name'],
      'role_key' => $user['role_key'],
      'role_name' => $user['role_name_zh'] ?? $user['role_name_vi'] ?? '',
    ]);
  }
}
