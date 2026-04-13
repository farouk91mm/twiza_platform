<?php

class Upload
{
    // ── رفع وضغط صورة ──
    public static function image(
        array  $file,
        string $folder,
        int    $maxWidth   = 1200,
        int    $quality    = 82,
        int    $maxSizeKB  = 1024  // 1MB افتراضي
    ): string|false {

        // التحقق من وجود خطأ
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return false;
        }

        // التحقق من الحجم الأقصى (5MB مبدئياً قبل الضغط)
        if (($file['size'] ?? 0) > MAX_FILE_SIZE) {
            return false;
        }

        $tmp = $file['tmp_name'] ?? '';

        if (!is_uploaded_file($tmp)) {
            return false;
        }

        // التحقق من الامتداد
        $ext = strtolower(
            pathinfo($file['name'] ?? '', PATHINFO_EXTENSION)
        );

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowedExtensions, true)) {
            return false;
        }

        // التحقق أنه صورة حقيقية
        $imageInfo = getimagesize($tmp);
        if ($imageInfo === false) {
            return false;
        }

        $allowedMimes = [
            IMAGETYPE_JPEG,
            IMAGETYPE_PNG,
            IMAGETYPE_WEBP,
        ];

        if (!in_array($imageInfo[2], $allowedMimes, true)) {
            return false;
        }

        // إنشاء اسم فريد دائماً بـ .jpg (بعد الضغط)
        $name = uniqid('img_', true) . '.jpg';

        // إنشاء المجلد إن لم يكن موجوداً
        $dir = rtrim(UPLOAD_PATH, '/') . '/' . trim($folder, '/') . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $destination = $dir . $name;

        // ── ضغط وحفظ الصورة ──
        $compressed = self::compress(
            $tmp,
            $destination,
            $imageInfo[2],
            $maxWidth,
            $quality
        );

        if (!$compressed) {
            return false;
        }

        return trim($folder, '/') . '/' . $name;
    }

    // ── ضغط الصورة ──
    private static function compress(
        string $source,
        string $destination,
        int    $imageType,
        int    $maxWidth,
        int    $quality
    ): bool {

        // تحميل الصورة حسب نوعها
        $image = match($imageType) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($source),
            IMAGETYPE_PNG  => imagecreatefrompng($source),
            IMAGETYPE_WEBP => imagecreatefromwebp($source),
            default        => false
        };

        if (!$image) {
            return false;
        }

        // الأبعاد الأصلية
        $originalWidth  = imagesx($image);
        $originalHeight = imagesy($image);

        // حساب الأبعاد الجديدة
        if ($originalWidth > $maxWidth) {
            $ratio     = $maxWidth / $originalWidth;
            $newWidth  = $maxWidth;
            $newHeight = (int)($originalHeight * $ratio);
        } else {
            $newWidth  = $originalWidth;
            $newHeight = $originalHeight;
        }

        // إنشاء صورة جديدة بالأبعاد الجديدة
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // دعم الشفافية للـ PNG
        if ($imageType === IMAGETYPE_PNG) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha(
                $newImage, 255, 255, 255, 127
            );
            imagefilledrectangle(
                $newImage, 0, 0, $newWidth, $newHeight, $transparent
            );
        }

        // تغيير الحجم
        imagecopyresampled(
            $newImage, $image,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $originalWidth, $originalHeight
        );

        // حفظ كـ JPEG دائماً (أصغر حجماً)
        $result = imagejpeg($newImage, $destination, $quality);

        // تحرير الذاكرة
        imagedestroy($image);
        imagedestroy($newImage);

        return $result;
    }

    // ── رفع صورة الشعار (أصغر) ──
    public static function logo(array $file, string $folder): string|false
    {
        return self::image(
            $file,
            $folder,
            400,   // عرض أقصى 400px
            85,    // جودة 85%
            512    // 500KB أقصى
        );
    }

    // ── رفع صورة إثبات (جودة أعلى) ──
    public static function proof(array $file, string $folder): string|false
    {
        return self::image(
            $file,
            $folder,
            1600,  // عرض أقصى 1600px
            90,    // جودة 90% للوضوح
            2048   // 2MB أقصى
        );
    }

    // ── حذف ملف ──
    public static function delete(string $filePath): bool
    {
        $fullPath = UPLOAD_PATH . $filePath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    // ── معلومات الصورة ──
    public static function info(string $filePath): array
    {
        $fullPath = UPLOAD_PATH . $filePath;
        if (!file_exists($fullPath)) {
            return [];
        }

        $size = filesize($fullPath);
        return [
            'size_bytes' => $size,
            'size_kb'    => round($size / 1024, 2),
            'size_mb'    => round($size / 1024 / 1024, 2),
        ];
    }
}