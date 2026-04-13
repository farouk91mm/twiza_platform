<?php
define('APP_NAME', 'منصة تويزة');
define('BASE_PATH', '/twiza_platform/public');
define('APP_URL',  'http://localhost:8888' . BASE_PATH);

define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
define('UPLOAD_URL',  APP_URL . '/uploads/');

define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);