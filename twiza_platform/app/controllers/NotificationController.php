<?php

class NotificationController extends Controller
{
    private Notification $notifModel;

    public function __construct()
    {
        $this->notifModel = new Notification();
    }

    // ════════════════════════════════
    //  GET /notifications
    // ════════════════════════════════
    public function index(): void
    {
        $this->requireAuth();

        $userId        = Session::get('user_id');
        $notifications = $this->notifModel->getByUser($userId);

        // تعيين كل الإشعارات كمقروءة عند الفتح
        $this->notifModel->markAllAsRead($userId);

        $this->view('notifications/index', [
            'title'         => 'الإشعارات',
            'notifications' => $notifications,
        ]);
    }

    // ════════════════════════════════
    //  POST /notifications/read
    // ════════════════════════════════
    public function markRead(): void
    {
        $this->requireAuth();

        $notifId = (int)($_POST['notif_id'] ?? 0);
        $userId  = Session::get('user_id');

        $this->notifModel->markAsRead($notifId, $userId);
        $this->json(['success' => true]);
    }

    // ════════════════════════════════
    //  GET /notifications/count (AJAX)
    // ════════════════════════════════
    public function count(): void
    {
        $this->requireAuth();

        $count = $this->notifModel->countUnread(
            Session::get('user_id')
        );

        $this->json(['count' => $count]);
    }

    // ════════════════════════════════
    //  POST /notifications/delete
    // ════════════════════════════════
    public function delete(): void
    {
        $this->requireAuth();

        $notifId = (int)($_POST['notif_id'] ?? 0);
        $userId  = Session::get('user_id');

        $this->notifModel->deleteNotif($notifId, $userId);
        $this->redirect('/notifications');
    }
}