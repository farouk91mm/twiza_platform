<?php

class Notification extends Model
{
    protected string $table = 'notifications';

    // ── إنشاء إشعار جديد ──
    public function create(
        int    $userId,
        string $title,
        string $message,
        string $type       = 'general',
        int    $relatedId  = null,
        string $relatedType = null
    ): int {
        return $this->db->insert(
            "INSERT INTO notifications
                (user_id, title, message, type,
                 related_id, related_type)
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $userId,
                $title,
                $message,
                $type,
                $relatedId,
                $relatedType,
            ]
        );
    }

    // ── جلب إشعارات مستخدم ──
    public function getByUser(
        int $userId,
        int $limit = 20
    ): array {
        return $this->db->fetchAll(
            "SELECT * FROM notifications
             WHERE user_id = ?
             ORDER BY created_at DESC
             LIMIT $limit",
            [$userId]
        );
    }

    // ── عدد الإشعارات غير المقروءة ──
    public function countUnread(int $userId): int
    {
        $row = $this->db->fetchOne(
            "SELECT COUNT(*) as total
             FROM notifications
             WHERE user_id = ? AND is_read = 0",
            [$userId]
        );
        return (int)($row['total'] ?? 0);
    }

    // ── تعيين إشعار كمقروء ──
    public function markAsRead(int $notifId, int $userId): void
    {
        $this->db->execute(
            "UPDATE notifications
             SET is_read = 1
             WHERE id = ? AND user_id = ?",
            [$notifId, $userId]
        );
    }

    // ── تعيين كل الإشعارات كمقروءة ──
    public function markAllAsRead(int $userId): void
    {
        $this->db->execute(
            "UPDATE notifications
             SET is_read = 1
             WHERE user_id = ?",
            [$userId]
        );
    }

    // ── حذف إشعار ──
    public function deleteNotif(int $notifId, int $userId): void
    {
        $this->db->execute(
            "DELETE FROM notifications
             WHERE id = ? AND user_id = ?",
            [$notifId, $userId]
        );
    }
}