<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';

class AuthController {
  public function login() {
    if (Auth::check()) {
      header('Location: /index.php?r=reports/dashboard');
      exit;
    }

    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = $_POST['username'] ?? '';
      $password = $_POST['password'] ?? '';

      if (Auth::login($username, $password)) {
        header('Location: /index.php?r=reports/dashboard');
        exit;
      } else {
        $error = __('auth.login_failed');
      }
    }

    include __DIR__ . '/../views/auth/login.php';
  }

  public function logout() {
    Auth::logout();
  }
}

