<?php
class I18n {
  private static $lang = [];
  private static $current = 'vi';

  public static function init() {
    if (isset($_GET['lang'])) {
      $_SESSION['lang'] = $_GET['lang'];
    }
    self::$current = $_SESSION['lang'] ?? 'vi';
    self::$lang = require __DIR__ . "/../../lang/" . self::$current . ".php";
  }

  public static function t($key) {
    return self::$lang[$key] ?? $key;
  }
}

function __($key) {
  return I18n::t($key);
}
