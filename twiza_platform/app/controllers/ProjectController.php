<?php

class ProjectController extends Controller
{
    private Project $projectModel;

    public function __construct()
    {
        $this->projectModel = new Project();
    }

    // ════════════════════════════════
    //  GET /projects
    // ════════════════════════════════
    public function index(): void
{
    // إذا كان مسجل دخوله وجّهه للوحة التحكم
    if (Session::isLoggedIn()) {
        $type   = Session::get('user_type');
        $routes = [
            'individual'  => '/individual/dashboard',
            'merchant'    => '/merchant/dashboard',
            'association' => '/association/dashboard',
            'admin'       => '/admin/dashboard',
        ];
        $this->redirect($routes[$type] ?? '/projects/list');
        return;
    }

    // إحصائيات المنصة
    $db   = Database::getInstance();
    $stats = [
        'projects'  => $db->fetchOne(
            "SELECT COUNT(*) as total FROM projects
             WHERE status = 'active'"
        )['total'] ?? 0,

        'donations' => $db->fetchOne(
            "SELECT COALESCE(SUM(amount), 0) as total
             FROM donations WHERE status = 'confirmed'"
        )['total'] ?? 0,

        'donors'    => $db->fetchOne(
            "SELECT COUNT(DISTINCT user_id) as total
             FROM donations WHERE status = 'confirmed'"
        )['total'] ?? 0,

        'associations' => $db->fetchOne(
            "SELECT COUNT(*) as total FROM associations
             WHERE is_verified = 1"
        )['total'] ?? 0,
    ];

    // أحدث المشاريع النشطة
    $projects   = $this->projectModel->getActive(6);
    $categories = $this->projectModel->getCategories();

    $this->view('home/index', [
        'title'      => 'منصة توزيعة — العمل الخيري الرقمي',
        'stats'      => $stats,
        'projects'   => $projects,
        'categories' => $categories,
    ]);
}

// صفحة تصفح المشاريع للزوار وللمسجلين
public function projectsList(): void
{
    $category = $_GET['category'] ?? '';
    $search   = $_GET['search']   ?? '';
    $page     = max(1, (int)($_GET['page'] ?? 1));
    $perPage  = 9;
    $offset   = ($page - 1) * $perPage;

    $projects   = $this->projectModel->getActive(
        $perPage, $offset, $category, $search
    );
    $categories = $this->projectModel->getCategories();

    $this->view('projects/index', [
        'title'      => 'المشاريع الخيرية',
        'projects'   => $projects,
        'categories' => $categories,
        'category'   => $category,
        'search'     => $search,
        'page'       => $page,
    ]);
}

    // ════════════════════════════════
    //  GET /projects/show?id=x
    // ════════════════════════════════
    public function show(): void
    {
        $id      = (int)($_GET['id'] ?? 0);
        $project = $this->projectModel->getDetails($id);

        if (!$project) {
            http_response_code(404);
            echo "المشروع غير موجود";
            return;
        }

        // زيادة المشاهدات
        $this->projectModel->incrementViews($id);

        // آخر تحديثات المشروع
        $updates = Database::getInstance()->fetchAll(
            "SELECT * FROM project_updates
             WHERE project_id = ?
             ORDER BY created_at DESC
             LIMIT 5",
            [$id]
        );

        // صور المشروع
        $images = Database::getInstance()->fetchAll(
            "SELECT * FROM project_images
             WHERE project_id = ?
             ORDER BY uploaded_at DESC",
            [$id]
        );

        $this->view('projects/show', [
            'title'   => $project['title'],
            'project' => $project,
            'updates' => $updates,
            'images'  => $images,
        ]);
    }
}