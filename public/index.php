<?php
session_start();

require_once __DIR__ . '/../app/config/app.php';
require_once __DIR__ . '/../app/config/db.php';

require_once __DIR__ . '/../app/core/DB.php';
require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/core/I18n.php';
require_once __DIR__ . '/../app/core/Csrf.php';

I18n::init();     // 初始化语言
Router::dispatch();
