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

  public function profile() {
    Auth::requireLogin();
    $user = Auth::user();
    
    // 处理语言切换
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['zh', 'vi'])) {
      $_SESSION['lang'] = $_GET['lang'];
      // 重定向到当前页面，移除 lang 参数
      $currentUrl = strtok($_SERVER['REQUEST_URI'], '?');
      $params = $_GET;
      unset($params['lang']);
      $queryString = !empty($params) ? '?' . http_build_query($params) : '';
      header('Location: ' . $currentUrl . $queryString);
      exit;
    }
    
    include __DIR__ . '/../views/auth/profile.php';
  }
}

