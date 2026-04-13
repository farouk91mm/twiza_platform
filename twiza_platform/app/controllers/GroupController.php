<?php

class GroupController extends Controller
{
    private Group   $groupModel;
    private Project $projectModel;

    public function __construct()
    {
        $this->groupModel   = new Group();
        $this->projectModel = new Project();
    }

    // ════════════════════════════════
    //  GET /groups/create?project_id=x
    // ════════════════════════════════
    public function createForm(): void
    {
        $this->requireRole('individual');

        $projectId = (int)($_GET['project_id'] ?? 0);
        $project   = $this->projectModel->getDetails($projectId);

        if (!$project || $project['status'] !== 'active') {
            $this->redirect('/projects');
        }

        if (!$project['allow_groups']) {
            Session::set('error',
                'هذا المشروع لا يقبل المجموعات'
            );
            $this->redirect("/projects/show?id=$projectId");
        }

        $this->view('groups/create', [
            'title'   => 'إنشاء مجموعة خيرية',
            'project' => $project,
            'errors'  => [],
            'old'     => [],
        ]);
    }

    // ════════════════════════════════
    //  POST /groups/create
    // ════════════════════════════════
    public function create(): void
    {
        $this->requireRole('individual');

        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            $this->redirect('/projects');
        }

        $projectId = (int)($_POST['project_id'] ?? 0);
        $project   = $this->projectModel->getDetails($projectId);

        if (!$project) {
            $this->redirect('/projects');
        }

        $v = new Validator($_POST);
        $v->required('name',          'اسم المجموعة')
          ->minLength('name', 3,       'اسم المجموعة')
          ->required('target_amount',  'المبلغ المستهدف')
          ->numeric('target_amount',   'المبلغ المستهدف');

        if (!$v->ok()) {
            $this->view('groups/create', [
                'title'   => 'إنشاء مجموعة خيرية',
                'project' => $project,
                'errors'  => $v->errors(),
                'old'     => $_POST,
            ]);
            return;
        }

        $userId     = Session::get('user_id');
        $inviteCode = $this->groupModel->generateInviteCode();

        // إنشاء المجموعة
        $groupId = $this->groupModel->create([
            'project_id'    => $projectId,
            'creator_id'    => $userId,
            'name'          => trim($_POST['name']),
            'description'   => trim($_POST['description'] ?? ''),
            'target_amount' => (float)$_POST['target_amount'],
            'invite_code'   => $inviteCode,
        ]);

        // إضافة المنشئ كـ admin
        $this->groupModel->addMember(
            $groupId,
            $userId,
            'admin',
            (float)($_POST['my_pledge'] ?? 0) ?: null
        );

        Session::set('success',
            "تم إنشاء المجموعة بنجاح ✅ — كود الدعوة: $inviteCode"
        );
        $this->redirect("/groups/show?id=$groupId");
    }

    // ════════════════════════════════
    //  GET /groups/show?id=x
    // ════════════════════════════════
    public function show(): void
    {
        $this->requireAuth();

        $groupId = (int)($_GET['id'] ?? 0);
        $group   = $this->groupModel->getDetails($groupId);

        if (!$group) {
            $this->redirect('/individual/dashboard');
        }

        $userId  = Session::get('user_id');
        $members = $this->groupModel->getMembers($groupId);

        // هل المستخدم عضو؟
        $isMember = $this->groupModel->isMember($groupId, $userId);

        // هل المستخدم مشرف؟
        $isAdmin = false;
        foreach ($members as $m) {
            if ($m['user_id'] == $userId && $m['role'] === 'admin') {
                $isAdmin = true;
                break;
            }
        }

        // تبرعات المجموعة
        $donations = Database::getInstance()->fetchAll(
            "SELECT gd.*,
                    u.full_name as submitted_by_name
             FROM group_donations gd
             LEFT JOIN users u ON u.id = gd.confirmed_by
             WHERE gd.group_id = ?
             ORDER BY gd.created_at DESC",
            [$groupId]
        );

        $this->view('groups/show', [
            'title'     => $group['name'],
            'group'     => $group,
            'members'   => $members,
            'isMember'  => $isMember,
            'isAdmin'   => $isAdmin,
            'userId'    => $userId,
            'donations' => $donations,
        ]);
    }

    // ════════════════════════════════
    //  GET /groups/join?code=xxxxx
    // ════════════════════════════════
    public function joinForm(): void
    {
        $this->requireRole('individual');

        $code  = trim($_GET['code'] ?? '');
        $group = $code ? $this->groupModel->findByCode($code) : null;

        $this->view('groups/join', [
            'title'  => 'الانضمام لمجموعة',
            'group'  => $group,
            'code'   => $code,
            'errors' => [],
        ]);
    }

    // ════════════════════════════════
    //  POST /groups/join
    // ════════════════════════════════
    public function join(): void
    {
        $this->requireRole('individual');

        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            $this->redirect('/groups/join');
        }

        $code   = trim($_POST['invite_code'] ?? '');
        $group  = $this->groupModel->findByCode($code);
        $userId = Session::get('user_id');

        if (!$group) {
            $this->view('groups/join', [
                'title'  => 'الانضمام لمجموعة',
                'group'  => null,
                'code'   => $code,
                'errors' => ['invite_code' => 'كود الدعوة غير صحيح'],
            ]);
            return;
        }

        // التحقق أن المجموعة نشطة
        if ($group['status'] !== 'active') {
            Session::set('error',
                'هذه المجموعة غير متاحة للانضمام حالياً'
            );
            $this->redirect('/groups/join');
            return;
        }

        // التحقق أنه ليس عضواً مسبقاً
        if ($this->groupModel->isMember($group['id'], $userId)) {
            Session::set('error', 'أنت عضو في هذه المجموعة مسبقاً');
            $this->redirect("/groups/show?id={$group['id']}");
            return;
        }

        // إضافة العضو
        $pledgedAmount = !empty($_POST['pledged_amount'])
                         ? (float)$_POST['pledged_amount']
                         : null;

        $this->groupModel->addMember(
            $group['id'],
            $userId,
            'member',
            $pledgedAmount
        );

        Session::set('success', 'تم انضمامك للمجموعة بنجاح 🎉');
        $this->redirect("/groups/show?id={$group['id']}");
    }

    // ════════════════════════════════
    //  POST /groups/donate
    // ════════════════════════════════
    public function donate(): void
    {
        $this->requireRole('individual');

        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            $this->redirect('/individual/dashboard');
        }

        $groupId = (int)($_POST['group_id'] ?? 0);
        $group   = $this->groupModel->getDetails($groupId);
        $userId  = Session::get('user_id');

        if (!$group || !$this->groupModel->isMember($groupId, $userId)) {
            $this->redirect('/individual/dashboard');
        }

        $v = new Validator($_POST);
        $v->required('total_amount',   'المبلغ الإجمالي')
          ->numeric('total_amount',    'المبلغ الإجمالي')
          ->required('payment_method', 'طريقة الدفع');

        if (!$v->ok()) {
            Session::set('error', 'يرجى ملء جميع الحقول');
            $this->redirect("/groups/show?id=$groupId");
            return;
        }

        // رفع صورة الإثبات
        $proofImage = null;
        if (!empty($_FILES['proof_image']['name'])) {
            $proofImage = Upload::image(
                $_FILES['proof_image'], 'proofs'
            );
        }

        // إنشاء تبرع المجموعة
        Database::getInstance()->insert(
            "INSERT INTO group_donations
                (group_id, project_id, total_amount,
                 payment_method, proof_image, notes)
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $groupId,
                $group['project_id'],
                (float)$_POST['total_amount'],
                $_POST['payment_method'],
                $proofImage,
                trim($_POST['notes'] ?? ''),
            ]
        );

        Session::set('success',
            'تم تسجيل تبرع المجموعة ✅ بانتظار تأكيد الجمعية'
        );
        $this->redirect("/groups/show?id=$groupId");
    }

    // ════════════════════════════════
//  GET /groups
// ════════════════════════════════
public function myGroups(): void
{
    $this->requireRole('individual');

    $userId = Session::get('user_id');
    $groups = $this->groupModel->getByUser($userId);

    $this->view('groups/my_groups', [
        'title'  => 'مجموعاتي',
        'groups' => $groups,
    ]);
}
}