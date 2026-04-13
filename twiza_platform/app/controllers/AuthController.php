<?php

class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    // ════════════════════════════════
    //  GET /auth/register
    // ════════════════════════════════
    public function registerForm(): void
    {
        // إذا مسجّل دخوله ارسله للوحة التحكم
        if (Session::isLoggedIn()) {
            $this->redirectToDashboard();
        }

        $this->view('auth/register', [
            'title'  => 'إنشاء حساب جديد',
            'errors' => [],
            'old'    => []
        ]);
    }

    // ════════════════════════════════
    //  POST /auth/register
    // ════════════════════════════════
    public function register(): void
    {
        // التحقق من CSRF
        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            $this->redirect('/auth/register');
        }

        $type = $_POST['user_type'] ?? '';
        $errors = [];
        $old    = $_POST;

        // ── التحقق المشترك لجميع الأنواع ──
        $v = new Validator($_POST);
        $v->required('full_name', 'الاسم الكامل')
          ->minLength('full_name', 3, 'الاسم الكامل')
          ->required('email', 'البريد الإلكتروني')
          ->email('email', 'البريد الإلكتروني')
          ->required('password', 'كلمة المرور')
          ->minLength('password', 8, 'كلمة المرور')
          ->matches('password', 'password_confirm', 'كلمة المرور');

        // ── تحقق إضافي حسب نوع المستخدم ──
        if ($type === 'merchant') {
            $v->required('shop_name', 'اسم المحل')
              ->required('activity_type', 'نوع النشاط');
        }

        if (!in_array($type, ['individual', 'merchant'], true)) {
            $errors['user_type'] = 'يرجى اختيار نوع الحساب';
        }

        $errors = array_merge($errors, $v->errors());

        // ── التحقق من تكرار البريد ──
        if (empty($errors['email']) &&
            $this->userModel->emailExists($_POST['email'])) {
            $errors['email'] = 'البريد الإلكتروني مستخدم مسبقاً';
        }

        if (!empty($errors)) {
            $this->view('auth/register', [
                'title'  => 'إنشاء حساب جديد',
                'errors' => $errors,
                'old'    => $old
            ]);
            return;
        }

        // ── إنشاء المستخدم ──
        $userId = $this->userModel->create([
            'full_name' => trim($_POST['full_name']),
            'email'     => strtolower(trim($_POST['email'])),
            'password'  => password_hash($_POST['password'], PASSWORD_BCRYPT),
            'user_type' => $type
        ]);

        // ── إنشاء الملف الشخصي حسب النوع ──
        if ($type === 'individual') {
            $this->userModel->createIndividualProfile($userId);
        }

        if ($type === 'merchant') {
            $this->userModel->createMerchantProfile(
                $userId,
                trim($_POST['shop_name']),
                trim($_POST['activity_type'])
            );
        }

        // ── تسجيل الدخول تلقائياً بعد التسجيل ──
        $user = $this->userModel->find($userId);
        $this->startUserSession($user);

        $this->redirectToDashboard();
    }

    // ════════════════════════════════
    //  GET /auth/login
    // ════════════════════════════════
    public function loginForm(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirectToDashboard();
        }

        $this->view('auth/login', [
            'title'  => 'تسجيل الدخول',
            'errors' => [],
            'old'    => []
        ]);
    }

    // ════════════════════════════════
    //  POST /auth/login
    // ════════════════════════════════
    public function login(): void
    {
        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            $this->redirect('/auth/login');
        }

        $v = new Validator($_POST);
        $v->required('email', 'البريد الإلكتروني')
          ->email('email', 'البريد الإلكتروني')
          ->required('password', 'كلمة المرور');

        if (!$v->ok()) {
            $this->view('auth/login', [
                'title'  => 'تسجيل الدخول',
                'errors' => $v->errors(),
                'old'    => $_POST
            ]);
            return;
        }

        $user = $this->userModel->findByEmail(
            strtolower(trim($_POST['email']))
        );

        // ── التحقق من المستخدم وكلمة المرور ──
        if (!$user || !password_verify($_POST['password'], $user['password'] ?? '')) {
            $this->view('auth/login', [
                'title'  => 'تسجيل الدخول',
                'errors' => ['email' => 'البريد أو كلمة المرور غير صحيحة'],
                'old'    => ['email' => $_POST['email']]
            ]);
            return;
        }

        // ── التحقق من أن الحساب مفعّل ──
        if (!$user['is_active']) {
            $this->view('auth/login', [
                'title'  => 'تسجيل الدخول',
                'errors' => ['email' => 'حسابك موقوف، تواصل مع الإدارة'],
                'old'    => ['email' => $_POST['email']]
            ]);
            return;
        }

        $this->userModel->updateLastLogin($user['id']);
        $this->startUserSession($user);
        $this->redirectToDashboard();
    }

    // ════════════════════════════════
    //  GET /auth/logout
    // ════════════════════════════════
    public function logout(): void
    {
        Session::destroy();
        $this->redirect('/auth/login');
    }

    // ════════════════════════════════
    //  دوال مساعدة خاصة
    // ════════════════════════════════

    // بدء الجلسة بعد تسجيل الدخول
    private function startUserSession(array $user): void
    {
        session_regenerate_id(true); // حماية من Session Fixation

        Session::set('user_id',   (int)$user['id']);
        Session::set('user_name', $user['full_name']);
        Session::set('user_type', $user['user_type']);
        Session::set('user_email',$user['email']);
        Session::set('avatar',    $user['avatar'] ?? '');
    }

    // إعادة توجيه حسب نوع المستخدم
    private function redirectToDashboard(): void
    {
        $routes = [
            'individual'  => '/individual/dashboard',
            'merchant'    => '/merchant/dashboard',
            'association' => '/association/dashboard',
            'admin'       => '/admin/dashboard',
        ];

        $type = Session::get('user_type', 'individual');
        $this->redirect($routes[$type] ?? '/');
    }
}