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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        /* ═══════════════════════════
           SIDEBAR
        ═══════════════════════════ */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: linear-gradient(180deg, #1a8a5a 0%, #0d5c3a 100%);
            position: fixed;
            top: 0;
            right: 0;
            overflow-y: auto;
            z-index: 999;
        }

        .sidebar-brand {
            padding: 20px 15px;
            border-bottom: 1px solid rgba(255,255,255,0.15);
        }

        .sidebar-brand h5 {
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            margin: 0;
        }

        .sidebar-brand small {
            color: rgba(255,255,255,0.6);
            font-size: 0.75rem;
        }

        .sidebar nav {
            padding: 10px 0;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 11px 18px;
            margin: 2px 8px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.18);
            color: #fff;
        }

        .sidebar .nav-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 15px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        /* ═══════════════════════════
           MAIN CONTENT
        ═══════════════════════════ */
        .main-wrapper {
            margin-right: 250px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ═══════════════════════════
           TOPBAR
        ═══════════════════════════ */
        .topbar {
            background: #fff;
            padding: 12px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }

        .topbar-title {
            font-weight: 700;
            font-size: 1rem;
            color: #1f2937;
            margin: 0;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #1a8a5a;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        /* ═══════════════════════════
           PAGE CONTENT
        ═══════════════════════════ */
        .page-content {
            padding: 24px;
            flex: 1;
        }

        /* ═══════════════════════════
           STAT CARDS
        ═══════════════════════════ */
        .stat-card {
            border: none;
            border-radius: 12px;
            padding: 20px;
            color: #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .stat-card .number {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1;
        }

        .stat-card .label {
            font-size: 0.8rem;
            opacity: 0.85;
            margin-top: 4px;
        }

        .stat-card .icon {
            font-size: 2rem;
            opacity: 0.4;
        }

        /* ═══════════════════════════
           CARDS
        ═══════════════════════════ */
        .content-card {
            background: #fff;
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .content-card .card-header {
            background: #fff;
            border-bottom: 1px solid #f3f4f6;
            padding: 16px 20px;
            font-weight: 700;
            font-size: 0.95rem;
        }

        /* ═══════════════════════════
           PROGRESS
        ═══════════════════════════ */
        .progress {
            height: 6px;
            border-radius: 10px;
            background: #e5e7eb;
        }

        .progress-bar {
            border-radius: 10px;
        }

        /* ═══════════════════════════
           MOBILE
        ═══════════════════════════ */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 998;
        }

        @media (max-width: 768px) {
            .sidebar {
                right: -250px;
                transition: right 0.3s;
            }
            .sidebar.open {
                right: 0;
            }
            .sidebar-overlay.show {
                display: block;
            }
            .main-wrapper {
                margin-right: 0;
            }
        }
    </style>
</head>
<body>

<!-- Overlay للموبايل فقط -->
<div class="sidebar-overlay" id="overlay"
     onclick="closeSidebar()"></div>

<!-- ═══════════════ SIDEBAR ═══════════════ -->
<div class="sidebar" id="sidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-heart-fill text-white"></i>
            <h5><?= APP_NAME ?></h5>
        </div>
        <small>
            <?php
            $labels = [
                'association' => 'جمعية خيرية',
                'individual'  => 'متبرع',
                'merchant'    => 'تاجر / خدمات',
                'admin'       => 'مشرف المنصة',
            ];
            echo $labels[Session::get('user_type')] ?? '';
            ?>
        </small>
    </div>

    <!-- Navigation -->
    <nav>
        <?php
        $type  = Session::get('user_type');
        $links = [];

        if ($type === 'association') {
    $links = [
        [
            'url'   => '/association/dashboard',
            'icon'  => 'bi-speedometer2',
            'label' => 'لوحة التحكم'
        ],
        [
            'url'   => '/association/projects/add',
            'icon'  => 'bi-plus-circle',
            'label' => 'إضافة مشروع'
        ],
        [
            'url'   => '/association/donations',
            'icon'  => 'bi-cash-stack',
            'label' => 'التبرعات'
        ],
        // ← هنا أضفنا المجموعات
        [
            'url'   => '/association/groups',
            'icon'  => 'bi-people',
            'label' => 'المجموعات'
        ],
    ];
        } elseif ($type === 'individual') {
    $links = [
        [
            'url'   => '/individual/dashboard',
            'icon'  => 'bi-speedometer2',
            'label' => 'لوحة التحكم'
        ],
        [
            'url'   => '/projects',
            'icon'  => 'bi-grid',
            'label' => 'المشاريع'
        ],
        [
            'url'   => '/individual/donations',
            'icon'  => 'bi-heart',
            'label' => 'تبرعاتي'
        ],
        // ← هنا أضفنا المجموعات
        [
            'url'   => '/groups',
            'icon'  => 'bi-people',
            'label' => 'مجموعاتي'
        ],
    ];
        } elseif ($type === 'merchant') {
            $links = [
                [
                    'url'   => '/merchant/dashboard',
                    'icon'  => 'bi-speedometer2',
                    'label' => 'لوحة التحكم'
                ],
                [
                    'url'   => '/merchant/products',
                    'icon'  => 'bi-bag',
                    'label' => 'منتجاتي'
                ],
            ];
        } elseif ($type === 'admin') {
            $links = [
                [
                    'url'   => '/admin/dashboard',
                    'icon'  => 'bi-speedometer2',
                    'label' => 'لوحة التحكم'
                ],
                [
                    'url'   => '/admin/associations',
                    'icon'  => 'bi-building',
                    'label' => 'الجمعيات'
                ],
            ];
        }

        $current = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($links as $link):
            $isActive = str_contains($current, $link['url'])
                        ? 'active' : '';
        ?>
            <a href="<?= APP_URL . $link['url'] ?>"
               class="nav-link <?= $isActive ?>">
                <i class="bi <?= $link['icon'] ?>"></i>
                <?= $link['label'] ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Logout -->
    <div class="sidebar-footer">
        <a href="<?= APP_URL ?>/auth/logout"
           class="nav-link"
           style="color:rgba(255,255,255,0.6)">
            <i class="bi bi-box-arrow-left"></i>
            تسجيل الخروج
        </a>
    </div>
</div>

<!-- ═══════════════ MAIN ═══════════════ -->
<div class="main-wrapper">

    <!-- Topbar -->
<div class="topbar">
    <button class="btn btn-sm btn-light d-md-none border-0"
            onclick="openSidebar()">
        <i class="bi bi-list fs-5"></i>
    </button>

    <h6 class="topbar-title">
        <?= htmlspecialchars($title ?? '') ?>
    </h6>

    <div class="d-flex align-items-center gap-3">

        <!-- ← زر الإشعارات الجديد -->
        <a href="<?= APP_URL ?>/notifications"
           class="position-relative text-muted"
           style="text-decoration:none"
           id="notifBtn">
            <i class="bi bi-bell fs-5"></i>
            <span class="position-absolute top-0 start-100
                         translate-middle badge rounded-pill bg-danger"
                  id="notifCount"
                  style="font-size:9px;display:none">
                0
            </span>
        </a>

        <!-- معلومات المستخدم -->
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted d-none d-md-inline"
                  style="font-size:0.85rem">
                <?= htmlspecialchars(Session::get('user_name') ?? '') ?>
            </span>
            <div class="user-avatar">
                <?= mb_substr(Session::get('user_name') ?? 'U', 0, 1) ?>
            </div>
        </div>

    </div>
</div>

    <!-- Content -->
    <div class="page-content">
        <?= $content ?? '' ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('overlay').classList.add('show');
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('overlay').classList.remove('show');
    }
</script>
<script>
// جلب عدد الإشعارات كل 30 ثانية
function fetchNotifCount() {
    fetch('<?= APP_URL ?>/notifications/count')
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('notifCount');
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(() => {});
}

// استدعاء فوري ثم كل 30 ثانية
fetchNotifCount();
setInterval(fetchNotifCount, 30000);
</script>
</body>
</html>