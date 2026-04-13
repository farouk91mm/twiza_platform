<?php

class AssociationController extends Controller
{
    private Association $associationModel;
    private Project     $projectModel;

    public function __construct()
    {
        $this->associationModel = new Association();
        $this->projectModel     = new Project();
    }

    // ════════════════════════════════
    //  GET /association/dashboard
    // ════════════════════════════════
public function dashboard(): void
{
    $this->requireRole('association');

    $userId      = Session::get('user_id');
    $association = $this->associationModel->findByUserId($userId);

    // ← هذا السطر هو المشكلة
    if (!$association) {
        $this->redirect('/association/setup');
    }

    $stats    = $this->associationModel->getStats($association['id']);
    $projects = $this->projectModel->getByAssociation($association['id']);

    $this->view('association/dashboard', [
        'title'       => 'لوحة تحكم الجمعية',
        'association' => $association,
        'stats'       => $stats,
        'projects'    => $projects,
    ]);
}

    // ════════════════════════════════
    //  GET /association/setup
    // ════════════════════════════════
    public function setupForm(): void
    {
        $this->requireRole('association');

        $this->view('association/setup', [
            'title'  => 'إعداد ملف الجمعية',
            'errors' => [],
            'old'    => [],
        ]);
    }

    // ════════════════════════════════
    //  POST /association/setup
    // ════════════════════════════════
    public function setup(): void
    {
        $this->requireRole('association');

        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            $this->redirect('/association/setup');
        }

        $v = new Validator($_POST);
        $v->required('official_name', 'اسم الجمعية الرسمي')
          ->required('registration_number', 'رقم الاعتماد');

        if (!$v->ok()) {
            $this->view('association/setup', [
                'title'  => 'إعداد ملف الجمعية',
                'errors' => $v->errors(),
                'old'    => $_POST,
            ]);
            return;
        }

        // رفع الشعار
        $logo = null;
        if (!empty($_FILES['logo']['name'])) {
            $logo = Upload::logo($_FILES['logo'], 'avatars');
        }

        $this->associationModel->create([
            'user_id'             => Session::get('user_id'),
            'official_name'       => trim($_POST['official_name']),
            'registration_number' => trim($_POST['registration_number']),
            'description'         => trim($_POST['description'] ?? ''),
            'wilaya'              => $_POST['wilaya']       ?? null,
            'phone'               => $_POST['phone']        ?? null,
            'bank_account'        => $_POST['bank_account'] ?? null,
            'ccp_account'         => $_POST['ccp_account']  ?? null,
            'logo'                => $logo,
        ]);

        $this->redirect('/association/dashboard');
    }

    // ════════════════════════════════
    //  GET /association/projects/add
    // ════════════════════════════════
    public function addProjectForm(): void
    {
        $this->requireRole('association');

        $userId      = Session::get('user_id');
        $association = $this->associationModel->findByUserId($userId);

        if (!$association) {
            $this->redirect('/association/setup');
        }

        $categories = $this->projectModel->getCategories();

        $this->view('association/add_project', [
            'title'       => 'إضافة مشروع خيري',
            'categories'  => $categories,
            'association' => $association,
            'errors'      => [],
            'old'         => [],
        ]);
    }

    // ════════════════════════════════
    //  POST /association/projects/add
    // ════════════════════════════════
    public function addProject(): void
    {
        $this->requireRole('association');

        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            $this->redirect('/association/projects/add');
        }

        $userId      = Session::get('user_id');
        $association = $this->associationModel->findByUserId($userId);

        $v = new Validator($_POST);
        $v->required('title', 'عنوان المشروع')
          ->required('description', 'وصف المشروع')
          ->required('target_amount', 'المبلغ المطلوب')
          ->required('category_id', 'فئة المشروع')
          ->numeric('target_amount', 'المبلغ المطلوب');

        if (!$v->ok()) {
            $this->view('association/add_project', [
                'title'       => 'إضافة مشروع خيري',
                'categories'  => $this->projectModel->getCategories(),
                'association' => $association,
                'errors'      => $v->errors(),
                'old'         => $_POST,
            ]);
            return;
        }


// رفع صورة الغلاف
$coverImage = null;
if (!empty($_FILES['cover_image']['name'])) {
    $coverImage = Upload::image($_FILES['cover_image'], 'projects');
}

        $projectId = $this->projectModel->create([
    'association_id'    => $association['id'],
    'category_id'       => (int)$_POST['category_id'],
    'title'             => trim($_POST['title']),
    'description'       => trim($_POST['description']),
    'target_amount'     => (float)$_POST['target_amount'],
    'beneficiary_count' => !empty($_POST['beneficiary_count']) 
                           ? (int)$_POST['beneficiary_count'] 
                           : null,
    'cover_image'       => $coverImage,
    'deadline'          => !empty($_POST['deadline']) 
                           ? $_POST['deadline'] 
                           : null,
    'allow_recurring'   => isset($_POST['allow_recurring']) ? 1 : 0,
    'allow_groups'      => isset($_POST['allow_groups'])    ? 1 : 0,
]);

        $this->redirect('/association/dashboard');
    }

    // ════════════════════════════════
    //  GET /association/donations
    // ════════════════════════════════
   public function donations(): void
{
    $this->requireRole('association');

    $userId      = Session::get('user_id');
    $association = $this->associationModel->findByUserId($userId);

    if (!$association) {
        $this->redirect('/association/setup');
    }

    $donations = Database::getInstance()->fetchAll(
        "SELECT
            d.*,
            u.full_name  as donor_name,
            u.email      as donor_email,
            p.title      as project_title,
            p.id         as project_id
         FROM donations d
         JOIN users u    ON u.id = d.user_id
         JOIN projects p ON p.id = d.project_id
         WHERE p.association_id = ?
         ORDER BY
             CASE d.status
                 WHEN 'pending'   THEN 1
                 WHEN 'confirmed' THEN 2
                 WHEN 'rejected'  THEN 3
             END,
             d.created_at DESC",
        [$association['id']]
    );

    $this->view('association/donations', [
        'title'       => 'قائمة التبرعات',
        'donations'   => $donations,
        'association' => $association,
    ]);
}

    // ════════════════════════════════
    //  POST /association/donations/confirm
    // ════════════════════════════════
   public function confirmDonation(): void
{
    $this->requireRole('association');

    if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
        $this->redirect('/association/donations');
    }

    $donationId = (int)($_POST['donation_id'] ?? 0);
    $action     = $_POST['action'] ?? 'confirm';
    $notes      = trim($_POST['notes'] ?? '');

    if ($donationId === 0) {
        $this->redirect('/association/donations');
    }

    // جلب بيانات التبرع
    $donation = (new Donation())->find($donationId);

    if (!$donation) {
        $this->redirect('/association/donations');
    }

    // جلب بيانات المشروع
    $project = $this->projectModel->find($donation['project_id']);

    if ($action === 'confirm') {

        // رفع صورة التأكيد
        $confirmImage = null;
        if (!empty($_FILES['confirmation_image']['name'])) {
            $confirmImage = Upload::proof($_FILES['confirmation_image'], 'proofs');
        }

        // تأكيد التبرع
        Database::getInstance()->execute(
            "UPDATE donations SET
                status             = 'confirmed',
                confirmed_by       = ?,
                confirmed_at       = NOW(),
                confirmation_image = ?,
                notes              = ?
             WHERE id = ?",
            [
                Session::get('user_id'),
                $confirmImage,
                $notes,
                $donationId
            ]
        );

        // تحديث المبلغ المجموع
        $this->projectModel->updateCollected($donation['project_id']);

        // ── إشعار المتبرع ──
        Notify::donationConfirmed(
            $donation['user_id'],
            $project['title']  ?? '',
            $donation['amount'],
            $donationId
        );

        // تحقق هل اكتمل المشروع
        $updatedProject = $this->projectModel->find(
            $donation['project_id']
        );

        if ($updatedProject &&
            $updatedProject['collected_amount'] >=
            $updatedProject['target_amount']) {

            // تغيير حالة المشروع
            $this->projectModel->updateStatus(
                $donation['project_id'],
                'completed'
            );

            // إشعار كل المتبرعين في هذا المشروع
            $donors = Database::getInstance()->fetchAll(
                "SELECT DISTINCT user_id FROM donations
                 WHERE project_id = ? AND status = 'confirmed'",
                [$donation['project_id']]
            );

            foreach ($donors as $donor) {
                Notify::projectCompleted(
                    $donor['user_id'],
                    $updatedProject['title'],
                    $donation['project_id']
                );
            }
        }

    } elseif ($action === 'reject') {

        Database::getInstance()->execute(
            "UPDATE donations SET
                status = 'rejected',
                notes  = ?
             WHERE id = ?",
            [$notes, $donationId]
        );

        // ── إشعار المتبرع بالرفض ──
        Notify::donationRejected(
            $donation['user_id'],
            $project['title']  ?? '',
            $donation['amount'],
            $notes ?: 'يرجى التواصل مع الجمعية',
            $donationId
        );
    }

    $this->redirect('/association/donations');
}

    // دالة مساعدة
    private function db(): Database
    {
        return Database::getInstance();
    }

    // ════════════════════════════════
//  GET /association/projects/edit?id=x
// ════════════════════════════════
public function editProjectForm(): void
{
    $this->requireRole('association');

    $userId      = Session::get('user_id');
    $association = $this->associationModel->findByUserId($userId);

    if (!$association) {
        $this->redirect('/association/setup');
    }

    $projectId = (int)($_GET['id'] ?? 0);

    // التحقق أن المشروع تابع لهذه الجمعية
    if (!$this->projectModel->belongsToAssociation(
        $projectId,
        $association['id']
    )) {
        $this->redirect('/association/dashboard');
    }

    $project    = $this->projectModel->find($projectId);
    $categories = $this->projectModel->getCategories();

    $this->view('association/edit_project', [
        'title'       => 'تعديل المشروع',
        'project'     => $project,
        'categories'  => $categories,
        'association' => $association,
        'errors'      => [],
    ]);
}

// ════════════════════════════════
//  POST /association/projects/edit
// ════════════════════════════════
public function editProject(): void
{
    $this->requireRole('association');

    if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
        $this->redirect('/association/dashboard');
    }

    $userId      = Session::get('user_id');
    $association = $this->associationModel->findByUserId($userId);
    $projectId   = (int)($_POST['project_id'] ?? 0);

    // التحقق أن المشروع تابع لهذه الجمعية
    if (!$this->projectModel->belongsToAssociation(
        $projectId,
        $association['id']
    )) {
        $this->redirect('/association/dashboard');
    }

    // التحقق من المدخلات
    $v = new Validator($_POST);
    $v->required('title',         'عنوان المشروع')
      ->required('description',   'وصف المشروع')
      ->required('target_amount', 'المبلغ المطلوب')
      ->required('category_id',   'فئة المشروع')
      ->numeric('target_amount',  'المبلغ المطلوب');

    if (!$v->ok()) {
        $project    = $this->projectModel->find($projectId);
        $categories = $this->projectModel->getCategories();

        $this->view('association/edit_project', [
            'title'       => 'تعديل المشروع',
            'project'     => $project,
            'categories'  => $categories,
            'association' => $association,
            'errors'      => $v->errors(),
        ]);
        return;
    }

    // تحديث بيانات المشروع
    $this->projectModel->update($projectId, [
        'category_id'       => (int)$_POST['category_id'],
        'title'             => trim($_POST['title']),
        'description'       => trim($_POST['description']),
        'target_amount'     => (float)$_POST['target_amount'],
        'beneficiary_count' => $_POST['beneficiary_count'] ?? null,
        'deadline'          => $_POST['deadline']          ?? null,
        'allow_recurring'   => isset($_POST['allow_recurring']) ? 1 : 0,
        'allow_groups'      => isset($_POST['allow_groups'])    ? 1 : 0,
        'status'            => $_POST['status'] ?? 'active',
    ]);

    // تحديث الصورة إن رُفعت صورة جديدة
    if (!empty($_FILES['cover_image']['name'])) {
        $newImage = Upload::image($_FILES['cover_image'], 'projects');
        if ($newImage) {
            // حذف الصورة القديمة
            $oldProject = $this->projectModel->find($projectId);
            if ($oldProject['cover_image']) {
                Upload::delete($oldProject['cover_image']);
            }
            $this->projectModel->updateCover($projectId, $newImage);
        }
    }

    $this->redirect('/association/dashboard');
}

// ════════════════════════════════
//  POST /association/projects/delete
// ════════════════════════════════
public function deleteProject(): void
{
    $this->requireRole('association');

    if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
        $this->redirect('/association/dashboard');
    }

    $userId      = Session::get('user_id');
    $association = $this->associationModel->findByUserId($userId);
    $projectId   = (int)($_POST['project_id'] ?? 0);

    if (!$association || $projectId === 0) {
        $this->redirect('/association/dashboard');
    }

    // التحقق أن المشروع تابع لهذه الجمعية
    if (!$this->projectModel->belongsToAssociation(
        $projectId,
        $association['id']
    )) {
        $this->redirect('/association/dashboard');
    }

    // التحقق هل للمشروع تبرعات
    if ($this->projectModel->hasDonations($projectId)) {
        // لا يمكن الحذف — له تبرعات
        Session::set('error',
            'لا يمكن حذف المشروع لأنه يحتوي على تبرعات مسجلة'
        );
        $this->redirect('/association/dashboard');
        return;
    }

    // حذف صورة الغلاف إن وجدت
    $project = $this->projectModel->find($projectId);
    if ($project && $project['cover_image']) {
        Upload::delete($project['cover_image']);
    }

    // حذف المشروع
    $this->projectModel->deleteProject($projectId);

    Session::set('success', 'تم حذف المشروع بنجاح');
    $this->redirect('/association/dashboard');
}

// ════════════════════════════════
//  GET /association/groups
// ════════════════════════════════
public function groups(): void
{
    $this->requireRole('association');

    $userId      = Session::get('user_id');
    $association = $this->associationModel->findByUserId($userId);

    if (!$association) {
        $this->redirect('/association/setup');
    }

    $groupModel = new Group();
    $groups     = $groupModel->getByAssociation($association['id']);

    $this->view('association/groups', [
        'title'       => 'المجموعات الخيرية',
        'groups'      => $groups,
        'association' => $association,
    ]);
}

// ════════════════════════════════
//  POST /association/groups/approve
// ════════════════════════════════
public function approveGroup(): void
{
    $this->requireRole('association');

    if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
        $this->redirect('/association/groups');
    }

    $groupId    = (int)($_POST['group_id'] ?? 0);
    $action     = $_POST['action'] ?? 'approve';
    $groupModel = new Group();

    if ($action === 'approve') {
        $groupModel->approve($groupId);

        // إشعار منشئ المجموعة
        $group = $groupModel->find($groupId);
        if ($group) {
            Notify::groupApproved(
                $group['creator_id'],
                $group['name'],
                $groupId
            );
        }

        Session::set('success', 'تمت الموافقة على المجموعة ✅');

    } else {
        $groupModel->reject($groupId);
        Session::set('success', 'تم رفض المجموعة');
    }

    $this->redirect('/association/groups');
}

// ════════════════════════════════
//  POST /association/groups/confirm-donation
// ════════════════════════════════
public function confirmGroupDonation(): void
{
    $this->requireRole('association');

    if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
        $this->redirect('/association/groups');
    }

    $groupDonationId = (int)($_POST['group_donation_id'] ?? 0);
    $action          = $_POST['action'] ?? 'confirm';
    $notes           = trim($_POST['notes'] ?? '');

    $confirmImage = null;
    if (!empty($_FILES['confirmation_image']['name'])) {
        $confirmImage = Upload::proof(
            $_FILES['confirmation_image'], 'proofs'
        );
    }

    if ($action === 'confirm') {
        Database::getInstance()->execute(
            "UPDATE group_donations SET
                status             = 'confirmed',
                confirmed_by       = ?,
                confirmed_at       = NOW(),
                confirmation_image = ?,
                notes              = ?
             WHERE id = ?",
            [
                Session::get('user_id'),
                $confirmImage,
                $notes,
                $groupDonationId
            ]
        );

        // تحديث المبلغ المجموع للمجموعة
        $gd = Database::getInstance()->fetchOne(
            "SELECT * FROM group_donations WHERE id = ?",
            [$groupDonationId]
        );

        if ($gd) {
            $groupModel = new Group();
            $groupModel->updateCollected($gd['group_id']);

            // إشعار منشئ المجموعة
            $group = $groupModel->find($gd['group_id']);
            if ($group) {
                Notify::donationConfirmed(
                    $group['creator_id'],
                    $group['name'],
                    $gd['total_amount'],
                    $groupDonationId
                );
            }
        }

        Session::set('success', 'تم تأكيد تبرع المجموعة ✅');

    } else {
        Database::getInstance()->execute(
            "UPDATE group_donations SET
                status = 'rejected',
                notes  = ?
             WHERE id = ?",
            [$notes, $groupDonationId]
        );

        Session::set('success', 'تم رفض تبرع المجموعة');
    }

    $this->redirect('/association/groups');
}
}