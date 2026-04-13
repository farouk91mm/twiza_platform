<?php

class Group extends Model
{
    protected string $table = 'groups';

    // ── إنشاء مجموعة جديدة ──
    public function create(array $data): int
    {
        return $this->db->insert(
            "INSERT INTO groups
                (project_id, creator_id, name, description,
                 target_amount, invite_code)
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                (int)$data['project_id'],
                (int)$data['creator_id'],
                $data['name'],
                $data['description'] ?? null,
                (float)$data['target_amount'],
                $data['invite_code'],
            ]
        );
    }

    // ── جلب مجموعة بالكود ──
    public function findByCode(string $code): array|false
{
    return $this->db->fetchOne(
        "SELECT
            g.id,
            g.project_id,
            g.creator_id,
            g.name,
            g.description,
            g.target_amount,
            g.collected_amount,
            g.invite_code,
            g.status,
            g.approved_by_association,
            g.approved_at,
            g.created_at,

            p.title         AS project_title,
            p.target_amount AS project_target,
            p.cover_image   AS project_image,
            p.status        AS project_status,

            a.official_name AS association_name,
            u.full_name     AS creator_name

         FROM groups g
         JOIN projects     p ON p.id = g.project_id
         JOIN associations a ON a.id = p.association_id
         JOIN users        u ON u.id = g.creator_id
         WHERE g.invite_code = ?
         LIMIT 1",
        [$code]
    );
}

    // ── جلب مجموعة بالـ ID مع تفاصيل ──
    public function getDetails(int $groupId): array|false
{
    return $this->db->fetchOne(
        "SELECT
            g.id,
            g.project_id,
            g.creator_id,
            g.name,
            g.description,
            g.target_amount,
            g.collected_amount,
            g.invite_code,
            g.status,
            g.approved_by_association,
            g.approved_at,
            g.created_at,

            p.title         AS project_title,
            p.cover_image   AS project_image,
            p.status        AS project_status,
            p.target_amount AS project_target,

            a.official_name AS association_name,
            a.bank_account  AS association_bank,
            a.ccp_account   AS association_ccp,
            a.phone         AS association_phone,

            u.full_name     AS creator_name,

            (
                SELECT COUNT(*)
                FROM group_members gm
                WHERE gm.group_id = g.id
            ) AS members_count

         FROM groups g
         JOIN projects     p ON p.id = g.project_id
         JOIN associations a ON a.id = p.association_id
         JOIN users        u ON u.id = g.creator_id
         WHERE g.id = ?
         LIMIT 1",
        [$groupId]
    );
}

    // ── جلب مجموعات المستخدم ──
    public function getByUser(int $userId): array
{
    return $this->db->fetchAll(
        "SELECT
            g.id,
            g.project_id,
            g.creator_id,
            g.name,
            g.description,
            g.target_amount,
            g.collected_amount,
            g.invite_code,
            g.status,
            g.approved_by_association,
            g.approved_at,
            g.created_at,

            p.title         AS project_title,
            p.cover_image   AS project_image,
            a.official_name AS association_name,

            gm.role         AS my_role,
            gm.pledged_amount,
            gm.paid_amount,

            (
                SELECT COUNT(*)
                FROM group_members gm2
                WHERE gm2.group_id = g.id
            ) AS members_count

         FROM groups g
         JOIN group_members gm
              ON gm.group_id = g.id
             AND gm.user_id  = ?
         JOIN projects p
              ON p.id = g.project_id
         JOIN associations a
              ON a.id = p.association_id
         ORDER BY g.created_at DESC",
        [$userId]
    );
}

    // ── جلب مجموعات جمعية معينة ──
    public function getByAssociation(int $associationId): array
{
    return $this->db->fetchAll(
        "SELECT
            g.id,
            g.project_id,
            g.creator_id,
            g.name,
            g.description,
            g.target_amount,
            g.collected_amount,
            g.invite_code,
            g.status,
            g.approved_by_association,
            g.approved_at,
            g.created_at,

            p.title       AS project_title,
            u.full_name   AS creator_name,
            u.email       AS creator_email,

            (
                SELECT COUNT(*)
                FROM group_members gm
                WHERE gm.group_id = g.id
            ) AS members_count

         FROM groups g
         JOIN projects p ON p.id = g.project_id
         JOIN users    u ON u.id = g.creator_id
         WHERE p.association_id = ?
         ORDER BY
            CASE g.status
                WHEN 'pending' THEN 1
                WHEN 'active'  THEN 2
                ELSE 3
            END,
            g.created_at DESC",
        [$associationId]
    );
}

    // ── توليد كود دعوة فريد ──
    public function generateInviteCode(): string
    {
        do {
            $code = strtoupper(substr(
                str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'),
                0, 8
            ));
            $exists = $this->db->fetchOne(
                "SELECT id FROM groups WHERE invite_code = ?",
                [$code]
            );
        } while ($exists);

        return $code;
    }

    // ── موافقة الجمعية على المجموعة ──
    public function approve(int $groupId): void
    {
        $this->db->execute(
            "UPDATE groups SET
                status                  = 'active',
                approved_by_association = 1,
                approved_at             = NOW()
             WHERE id = ?",
            [$groupId]
        );
    }

    // ── رفض المجموعة ──
    public function reject(int $groupId): void
    {
        $this->db->execute(
            "UPDATE groups SET status = 'cancelled'
             WHERE id = ?",
            [$groupId]
        );
    }

    // ── تحديث المبلغ المجموع ──
    public function updateCollected(int $groupId): void
    {
        $this->db->execute(
            "UPDATE groups SET
                collected_amount = (
                    SELECT COALESCE(SUM(total_amount), 0)
                    FROM group_donations
                    WHERE group_id = ? AND status = 'confirmed'
                )
             WHERE id = ?",
            [$groupId, $groupId]
        );
    }

    // ── هل المستخدم عضو؟ ──
    public function isMember(int $groupId, int $userId): bool
    {
        $row = $this->db->fetchOne(
            "SELECT id FROM group_members
             WHERE group_id = ? AND user_id = ?
             LIMIT 1",
            [$groupId, $userId]
        );
        return (bool)$row;
    }

    // ── جلب أعضاء المجموعة ──
    public function getMembers(int $groupId): array
    {
        return $this->db->fetchAll(
            "SELECT gm.*, u.full_name, u.email, u.avatar
             FROM group_members gm
             JOIN users u ON u.id = gm.user_id
             WHERE gm.group_id = ?
             ORDER BY
                CASE gm.role WHEN 'admin' THEN 1 ELSE 2 END,
                gm.joined_at ASC",
            [$groupId]
        );
    }

    // ── إضافة عضو ──
    public function addMember(
        int    $groupId,
        int    $userId,
        string $role          = 'member',
        float  $pledgedAmount = null
    ): int {
        return $this->db->insert(
            "INSERT INTO group_members
                (group_id, user_id, role, pledged_amount)
             VALUES (?, ?, ?, ?)",
            [$groupId, $userId, $role, $pledgedAmount]
        );
    }

    // ── تحديث المبلغ المدفوع للعضو ──
    public function updateMemberPaid(
        int   $groupId,
        int   $userId,
        float $amount
    ): void {
        $this->db->execute(
            "UPDATE group_members SET
                paid_amount = paid_amount + ?
             WHERE group_id = ? AND user_id = ?",
            [$amount, $groupId, $userId]
        );
    }
}