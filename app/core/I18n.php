<?php
class I18n {
  private static $lang = [];
  private static $current = 'vi';

  public static function init() {
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['zh', 'vi'])) {
      $_SESSION['lang'] = $_GET['lang'];
    }
    self::$current = $_SESSION['lang'] ?? 'vi';
    $langFile = __DIR__ . "/../../lang/" . self::$current . ".php";
    if (file_exists($langFile)) {
      self::$lang = require $langFile;
    } else {
      self::$lang = [];
    }
  }

  public static function t($key) {
    return self::$lang[$key] ?? $key;
  }

  public static function current() {
    return self::$current;
  }
}

function __($key) {
  return I18n::t($key);
}

