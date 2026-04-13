<?php

class IndividualController extends Controller
{
    private Project  $projectModel;
    private Donation $donationModel;

    public function __construct()
    {
        $this->projectModel  = new Project();
        $this->donationModel = new Donation();
    }

    // ════════════════════════════════
    //  GET /individual/dashboard
    // ════════════════════════════════
    public function dashboard(): void
    {
        $this->requireRole('individual');

        $userId = Session::get('user_id');

        // آخر تبرعات المستخدم
        $donations = $this->donationModel->getByUser($userId);

        // إحصائيات سريعة
        $stats = Database::getInstance()->fetchOne(
            "SELECT
                COUNT(*)                                    as total_donations,
                COALESCE(SUM(amount), 0)                   as total_amount,
                SUM(CASE WHEN status='confirmed' THEN 1
                         ELSE 0 END)                       as confirmed_donations,
                SUM(CASE WHEN status='pending'   THEN 1
                         ELSE 0 END)                       as pending_donations
             FROM donations
             WHERE user_id = ?",
            [$userId]
        );

        // أحدث المشاريع النشطة
        $latestProjects = $this->projectModel->getActive(6);

        $this->view('individual/dashboard', [
            'title'          => 'لوحة التحكم',
            'donations'      => $donations,
            'stats'          => $stats,
            'latestProjects' => $latestProjects,
        ]);
    }

    // ════════════════════════════════
    //  GET /individual/donations
    // ════════════════════════════════
    public function myDonations(): void
    {
        $this->requireRole('individual');

        $userId    = Session::get('user_id');
        $donations = $this->donationModel->getByUser($userId);

        $this->view('individual/donations', [
            'title'     => 'تبرعاتي',
            'donations' => $donations,
        ]);
    }
}