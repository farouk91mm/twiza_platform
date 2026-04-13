<?php

class Notify
{
    private static function model(): Notification
    {
        return new Notification();
    }

    // ── تبرع مؤكد — للمتبرع ──
    public static function donationConfirmed(
        int    $userId,
        string $projectTitle,
        float  $amount,
        int    $donationId
    ): void {
        self::model()->create(
            $userId,
            '✅ تم تأكيد تبرعك',
            "تم تأكيد استلام تبرعك بمبلغ " .
            number_format($amount, 0, ',', '.') .
            " دج لمشروع: $projectTitle",
            'donation_confirmed',
            $donationId,
            'donation'
        );
    }

    // ── تبرع مرفوض — للمتبرع ──
    public static function donationRejected(
        int    $userId,
        string $projectTitle,
        float  $amount,
        string $reason,
        int    $donationId
    ): void {
        self::model()->create(
            $userId,
            '❌ تبرعك بحاجة لمراجعة',
            "بخصوص تبرعك بمبلغ " .
            number_format($amount, 0, ',', '.') .
            " دج لمشروع: $projectTitle. السبب: $reason",
            'donation_confirmed',
            $donationId,
            'donation'
        );
    }

    // ── مشروع اكتمل — لكل المتبرعين ──
    public static function projectCompleted(
        int    $userId,
        string $projectTitle,
        int    $projectId
    ): void {
        self::model()->create(
            $userId,
            '🎉 اكتمل المشروع',
            "المشروع الذي ساهمت فيه اكتمل: $projectTitle",
            'project_completed',
            $projectId,
            'project'
        );
    }

    // ── تحديث مشروع — لكل المتبرعين ──
    public static function projectUpdate(
        int    $userId,
        string $projectTitle,
        string $updateTitle,
        int    $projectId
    ): void {
        self::model()->create(
            $userId,
            '📢 تحديث جديد',
            "تحديث على مشروع $projectTitle: $updateTitle",
            'project_update',
            $projectId,
            'project'
        );
    }

    // ── موافقة على مجموعة ──
    public static function groupApproved(
        int    $userId,
        string $groupName,
        int    $groupId
    ): void {
        self::model()->create(
            $userId,
            '✅ تمت الموافقة على مجموعتك',
            "تمت الموافقة على المجموعة: $groupName",
            'group_approved',
            $groupId,
            'group'
        );
    }

    // ── دعوة لمجموعة ──
    public static function groupInvite(
        int    $userId,
        string $groupName,
        string $inviteCode,
        int    $groupId
    ): void {
        self::model()->create(
            $userId,
            '👥 دعوة لمجموعة خيرية',
            "تمت دعوتك للانضمام لمجموعة: $groupName" .
            " (كود الدعوة: $inviteCode)",
            'group_invite',
            $groupId,
            'group'
        );
    }
}