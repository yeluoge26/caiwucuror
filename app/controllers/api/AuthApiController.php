<?php
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/ApiGuard.php';

class AuthApiController {
  public function login() {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    if (Auth::login($username, $password)) {
      session_regenerate_id();
      Response::json([
        'token' => session_id(),
        'user' => [
          'id' => Auth::user()['id'],
          'display_name' => Auth::user()['display_name'],
          'role_key' => Auth::user()['role_key'],
        ],
      ]);
    } else {
      Response::json(['error' => 'invalid_credentials'], 401);
    }
  }

  public function me() {
    ApiGuard::requireLogin();
    $u = Auth::user();
    Response::json(['user' => $u]);
  }
}
