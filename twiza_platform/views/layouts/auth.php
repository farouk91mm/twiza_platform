<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? APP_NAME) ?></title>

    <!-- Bootstrap 5 RTL -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            background: linear-gradient(135deg, #1a8a5a 0%, #0d5c3a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .auth-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .auth-logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #1a8a5a, #0d5c3a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        .btn-google { background:#fff; border:1px solid #ddd; color:#333; }
        .btn-google:hover { background:#f8f8f8; }
        .btn-facebook { background:#1877f2; color:#fff; border:none; }
        .btn-facebook:hover { background:#166fe5; }
        .divider { position:relative; text-align:center; margin:20px 0; }
        .divider::before {
            content:'';
            position:absolute; top:50%; left:0; right:0;
            height:1px; background:#dee2e6;
        }
        .divider span {
            background:#fff;
            padding:0 12px;
            color:#6c757d;
            position:relative;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5">

                <!-- Logo -->
                <div class="text-center mb-4">
                    <div class="auth-logo">
                        <i class="bi bi-heart-fill text-white fs-2"></i>
                    </div>
                    <h4 class="text-white fw-bold"><?= APP_NAME ?></h4>
                </div>

                <!-- البطاقة الرئيسية -->
                <div class="card auth-card">
                    <div class="card-body p-4 p-md-5">
                        <?= $content ?? '' ?>
                    </div>
                </div>

                <p class="text-center text-white-50 mt-3 small">
                    جميع الحقوق محفوظة © <?= date('Y') ?> <?= APP_NAME ?>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>