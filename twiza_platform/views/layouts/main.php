<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? APP_NAME) ?></title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        * { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            color: #1f2937;
        }

        /* ═══════════ NAVBAR ═══════════ */
        .navbar {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e5e7eb;
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 800;
            color: #1a8a5a !important;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-brand .brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #1a8a5a, #0d5c3a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-link {
            color: #4b5563 !important;
            font-weight: 500;
            padding: 6px 14px !important;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .nav-link:hover {
            background: #f0fdf4;
            color: #1a8a5a !important;
        }

        /* ═══════════ HERO ═══════════ */
        .hero {
            background: linear-gradient(135deg, #1a8a5a 0%, #0d5c3a 60%, #064028 100%);
            min-height: 88vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .hero-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
        }

        .hero-title span {
            color: #86efac;
        }

        .hero-subtitle {
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
            line-height: 1.8;
        }

        .hero-badge {
            background: rgba(255,255,255,0.15);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 20px;
        }

        .hero-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 16px;
            padding: 24px;
            color: #fff;
        }

        .hero-card .number {
            font-size: 2rem;
            font-weight: 800;
            color: #86efac;
        }

        /* ═══════════ SECTIONS ═══════════ */
        .section {
            padding: 80px 0;
        }

        .section-alt {
            background: #f8fafc;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .section-subtitle {
            color: #6b7280;
            font-size: 1rem;
            margin-bottom: 48px;
        }

        /* ═══════════ STATS ═══════════ */
        .stat-item {
            text-align: center;
            padding: 30px 20px;
        }

        .stat-item .number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1a8a5a;
            display: block;
        }

        .stat-item .label {
            color: #6b7280;
            font-size: 0.9rem;
            margin-top: 4px;
        }

        /* ═══════════ PROJECT CARDS ═══════════ */
        .project-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            transition: transform 0.2s, box-shadow 0.2s;
            overflow: hidden;
            height: 100%;
        }

        .project-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }

        .project-card .card-img-top {
            height: 180px;
            object-fit: cover;
        }

        .project-card .card-img-placeholder {
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }

        .progress {
            height: 6px;
            border-radius: 10px;
            background: #e5e7eb;
        }

        .progress-bar {
            border-radius: 10px;
            background: linear-gradient(90deg, #1a8a5a, #2ecc71);
        }

        /* ═══════════ CATEGORIES ═══════════ */
        .category-card {
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            padding: 24px 16px;
            text-align: center;
            text-decoration: none;
            color: #1f2937;
            transition: all 0.2s;
            display: block;
        }

        .category-card:hover {
            border-color: #1a8a5a;
            background: #f0fdf4;
            color: #1a8a5a;
            transform: translateY(-2px);
        }

        .category-card .cat-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 1.4rem;
            color: #fff;
        }

        .category-card .cat-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* ═══════════ HOW IT WORKS ═══════════ */
        .step-card {
            text-align: center;
            padding: 32px 24px;
        }

        .step-number {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #1a8a5a, #0d5c3a);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            font-weight: 800;
            margin: 0 auto 16px;
        }

        .step-title {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }

        .step-desc {
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        /* ═══════════ CTA ═══════════ */
        .cta-section {
            background: linear-gradient(135deg, #1a8a5a 0%, #0d5c3a 100%);
            padding: 80px 0;
            text-align: center;
        }

        /* ═══════════ FOOTER ═══════════ */
        .footer {
            background: #0d5c3a;
            color: rgba(255,255,255,0.7);
            padding: 40px 0 20px;
        }

        .footer h6 {
            color: #fff;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .footer a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            display: block;
            margin-bottom: 8px;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .footer a:hover { color: #fff; }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: 30px;
            padding-top: 20px;
            text-align: center;
            font-size: 0.85rem;
        }

        /* ═══════════ BUTTONS ═══════════ */
        .btn-main {
            background: linear-gradient(135deg, #1a8a5a, #0d5c3a);
            color: #fff;
            border: none;
            padding: 12px 32px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-main:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(26,138,90,0.4);
            color: #fff;
        }

        .btn-outline-main {
            background: transparent;
            color: #fff;
            border: 2px solid rgba(255,255,255,0.5);
            padding: 11px 28px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-outline-main:hover {
            background: rgba(255,255,255,0.1);
            border-color: #fff;
            color: #fff;
        }
    </style>
</head>
<body>

<!-- ═══════════ NAVBAR ═══════════ -->
<nav class="navbar navbar-expand-lg">
    <div class="container">

        <!-- Brand -->
        <a class="navbar-brand" href="<?= APP_URL ?>/">
            <div class="brand-icon">
                <i class="bi bi-heart-fill text-white"></i>
            </div>
            <?= APP_NAME ?>
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler border-0"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navMenu">
            <i class="bi bi-list fs-4"></i>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto gap-1 mt-2 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/">
                        الرئيسية
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/projects">
                        المشاريع
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#how-it-works">
                        كيف تعمل؟
                    </a>
                </li>
            </ul>

            <div class="d-flex gap-2 mt-2 mt-lg-0">
                <a href="<?= APP_URL ?>/auth/login"
                   class="btn btn-outline-success btn-sm px-3">
                    تسجيل الدخول
                </a>
                <a href="<?= APP_URL ?>/auth/register"
                   class="btn btn-success btn-sm px-3">
                    انضم الآن
                </a>
            </div>
        </div>

    </div>
</nav>

<!-- المحتوى الرئيسي -->
<?= $content ?? '' ?>

<!-- ═══════════ FOOTER ═══════════ -->
<footer class="footer">
    <div class="container">
        <div class="row g-4">

            <div class="col-12 col-md-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-heart-fill text-white"></i>
                    <h6 class="mb-0"><?= APP_NAME ?></h6>
                </div>
                <p class="small" style="line-height:1.8">
                    منصة رقمية جزائرية تربط المتبرعين
                    بالجمعيات الخيرية بشفافية وثقة
                </p>
            </div>

            <div class="col-6 col-md-2">
                <h6>روابط سريعة</h6>
                <a href="<?= APP_URL ?>/">الرئيسية</a>
                <a href="<?= APP_URL ?>/projects">المشاريع</a>
                <a href="<?= APP_URL ?>/auth/register">التسجيل</a>
            </div>

            <div class="col-6 col-md-2">
                <h6>الفئات</h6>
                <?php
                // جلب أول 4 فئات
                $footerCats = Database::getInstance()->fetchAll(
                    "SELECT * FROM project_categories LIMIT 4"
                );
                foreach ($footerCats as $fc):
                ?>
                    <a href="<?= APP_URL ?>/projects?category=<?= $fc['id'] ?>">
                        <?= htmlspecialchars($fc['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="col-12 col-md-4">
                <h6>مبدأنا</h6>
                <div class="d-flex gap-2 align-items-start mb-2">
                    <i class="bi bi-shield-check text-success mt-1"></i>
                    <small>الأموال تصل مباشرة للجمعية بدون وسيط</small>
                </div>
                <div class="d-flex gap-2 align-items-start mb-2">
                    <i class="bi bi-eye text-success mt-1"></i>
                    <small>شفافية كاملة في كل عملية تبرع</small>
                </div>
                <div class="d-flex gap-2 align-items-start">
                    <i class="bi bi-people text-success mt-1"></i>
                    <small>تبرع فردي وجماعي للجميع</small>
                </div>
            </div>

        </div>

        <div class="footer-bottom">
            <small>
                © <?= date('Y') ?> <?= APP_NAME ?> —
                جميع الحقوق محفوظة
            </small>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>