<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// ── الإعدادات ──
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/oauth.php';

// ── إنشاء مجلدات الرفع تلقائياً ──
$uploadDirs = [
    UPLOAD_PATH . 'projects/',
    UPLOAD_PATH . 'proofs/',
    UPLOAD_PATH . 'avatars/',
    UPLOAD_PATH . 'documents/',
];

foreach ($uploadDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// ── Autoloader ──
spl_autoload_register(function (string $class): void {
    $dirs = [
        __DIR__ . '/../core/',
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../app/models/',
        __DIR__ . '/../app/helpers/',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ── تشغيل الجلسة ──
Session::start();

// ── الراوتر ──
$router = new Router();
require_once __DIR__ . '/../routes/web.php';
$router->run();