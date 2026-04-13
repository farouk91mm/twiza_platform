<?php

class DonationController extends Controller
{
    private Donation $donationModel;
    private Project  $projectModel;

    public function __construct()
    {
        $this->donationModel = new Donation();
        $this->projectModel  = new Project();
    }

    // ════════════════════════════════
    //  POST /donations/add
    // ════════════════════════════════
    public function store(): void
    {
        $this->requireAuth();

        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            $this->redirect('/projects');
        }

        $v = new Validator($_POST);
        $v->required('project_id',    'المشروع')
          ->required('amount',         'المبلغ')
          ->numeric('amount',          'المبلغ')
          ->required('payment_method', 'طريقة الدفع');

        $projectId = (int)($_POST['project_id'] ?? 0);

        if (!$v->ok()) {
            $this->redirect("/projects/show?id=$projectId");
            return;
        }

        // رفع صورة إثبات التحويل
        $proofImage = null;
        if (!empty($_FILES['proof_image']['name'])) {
            $proofImage = Upload::proof($_FILES['proof_image'], 'proofs');
        }

        $donationId = $this->donationModel->create([
            'user_id'        => Session::get('user_id'),
            'project_id'     => $projectId,
            'amount'         => (float)$_POST['amount'],
            'donation_type'  => $_POST['donation_type']  ?? 'one_time',
            'payment_method' => $_POST['payment_method'],
            'proof_image'    => $proofImage,
            'is_anonymous'   => isset($_POST['is_anonymous']) ? 1 : 0,
            'notes'          => trim($_POST['notes'] ?? ''),
        ]);

        // إذا تبرع شهري
        if (($_POST['donation_type'] ?? '') === 'recurring') {
            Database::getInstance()->execute(
                "INSERT INTO recurring_donations
                    (donation_id, user_id, project_id,
                     amount, next_due_date)
                 VALUES (?, ?, ?, ?, ?)",
                [
                    $donationId,
                    Session::get('user_id'),
                    $projectId,
                    (float)$_POST['amount'],
                    date('Y-m-d', strtotime('+1 month'))
                ]
            );
        }

        Session::set('success', 'تم تسجيل تبرعك بنجاح ✅');
        $this->redirect('/individual/donations');
    }

    // ════════════════════════════════
    //  GET /donations/edit?id=x
    // ════════════════════════════════
    public function editForm(): void
    {
        $this->requireRole('individual');

        $donationId = (int)($_GET['id'] ?? 0);
        $userId     = Session::get('user_id');

        $donation = $this->donationModel->findByUser(
            $donationId, $userId
        );

        // التحقق من وجود التبرع وأنه pending
        if (!$donation || $donation['status'] !== 'pending') {
            Session::set('error',
                'لا يمكن تعديل هذا التبرع'
            );
            $this->redirect('/individual/donations');
            return;
        }

        $this->view('individual/edit_donation', [
            'title'    => 'تعديل التبرع',
            'donation' => $donation,
            'errors'   => [],
        ]);
    }

    // ════════════════════════════════
    //  POST /donations/edit
    // ════════════════════════════════
    public function edit(): void
    {
        $this->requireRole('individual');

        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            $this->redirect('/individual/donations');
        }

        $donationId = (int)($_POST['donation_id'] ?? 0);
        $userId     = Session::get('user_id');

        $donation = $this->donationModel->findByUser(
            $donationId, $userId
        );

        // التحقق من وجود التبرع وأنه pending
        if (!$donation || $donation['status'] !== 'pending') {
            Session::set('error',
                'لا يمكن تعديل هذا التبرع'
            );
            $this->redirect('/individual/donations');
            return;
        }

        $v = new Validator($_POST);
        $v->required('amount',         'المبلغ')
          ->numeric('amount',          'المبلغ')
          ->required('payment_method', 'طريقة الدفع');

        if (!$v->ok()) {
            $this->view('individual/edit_donation', [
                'title'    => 'تعديل التبرع',
                'donation' => $donation,
                'errors'   => $v->errors(),
            ]);
            return;
        }

        // تحديث بيانات التبرع
        $this->donationModel->update($donationId, [
            'amount'         => (float)$_POST['amount'],
            'payment_method' => $_POST['payment_method'],
            'donation_type'  => $_POST['donation_type'] ?? 'one_time',
            'is_anonymous'   => isset($_POST['is_anonymous']) ? 1 : 0,
            'notes'          => trim($_POST['notes'] ?? ''),
        ]);

        // تحديث صورة الإثبات إن رُفعت صورة جديدة
        if (!empty($_FILES['proof_image']['name'])) {
            $newImage = Upload::image(
                $_FILES['proof_image'], 'proofs'
            );
            if ($newImage) {
                // حذف الصورة القديمة
                if ($donation['proof_image']) {
                    Upload::delete($donation['proof_image']);
                }
                $this->donationModel->updateProof(
                    $donationId, $newImage
                );
            }
        }

        Session::set('success', 'تم تعديل التبرع بنجاح ✅');
        $this->redirect('/individual/donations');
    }

    // ════════════════════════════════
    //  POST /donations/delete
    // ════════════════════════════════
    public function delete(): void
    {
        $this->requireRole('individual');

        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            $this->redirect('/individual/donations');
        }

        $donationId = (int)($_POST['donation_id'] ?? 0);
        $userId     = Session::get('user_id');

        $donation = $this->donationModel->findByUser(
            $donationId, $userId
        );

        // التحقق من وجود التبرع وأنه pending
        if (!$donation || $donation['status'] !== 'pending') {
            Session::set('error',
                'لا يمكن حذف هذا التبرع'
            );
            $this->redirect('/individual/donations');
            return;
        }

        // حذف صورة الإثبات إن وجدت
        if ($donation['proof_image']) {
            Upload::delete($donation['proof_image']);
        }

        $this->donationModel->deleteDonation($donationId, $userId);

        Session::set('success', 'تم حذف التبرع بنجاح');
        $this->redirect('/individual/donations');
    }
}