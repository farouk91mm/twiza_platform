<?php

class Donation extends Model
{
    protected string $table = 'donations';

    // إنشاء تبرع جديد
    public function create(array $data): int
    {
        return $this->db->insert(
            "INSERT INTO donations
                (user_id, project_id, amount, donation_type,
                 payment_method, proof_image, is_anonymous, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['user_id'],
                $data['project_id'],
                $data['amount'],
                $data['donation_type']  ?? 'one_time',
                $data['payment_method'],
                $data['proof_image']    ?? null,
                $data['is_anonymous']   ?? 0,
                $data['notes']          ?? null,
            ]
        );
    }

    // جلب تبرعات مستخدم
    public function getByUser(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT d.*,
                    p.title         as project_title,
                    p.id            as project_id,
                    a.official_name as association_name
             FROM donations d
             JOIN projects p     ON p.id = d.project_id
             JOIN associations a ON a.id = p.association_id
             WHERE d.user_id = ?
             ORDER BY d.created_at DESC",
            [$userId]
        );
    }

    // جلب تبرع خاص بمستخدم معين
    public function findByUser(
        int $donationId,
        int $userId
    ): array|false {
        return $this->db->fetchOne(
            "SELECT d.*,
                    p.title         as project_title,
                    p.id            as project_id,
                    a.official_name as association_name
             FROM donations d
             JOIN projects p     ON p.id = d.project_id
             JOIN associations a ON a.id = p.association_id
             WHERE d.id = ? AND d.user_id = ?
             LIMIT 1",
            [$donationId, $userId]
        );
    }

    // تحديث تبرع
    public function update(int $id, array $data): void
    {
        $this->db->execute(
            "UPDATE donations SET
                amount         = ?,
                payment_method = ?,
                donation_type  = ?,
                is_anonymous   = ?,
                notes          = ?
             WHERE id = ? AND status = 'pending'",
            [
                (float)$data['amount'],
                $data['payment_method'],
                $data['donation_type']  ?? 'one_time',
                $data['is_anonymous']   ?? 0,
                $data['notes']          ?? null,
                $id,
            ]
        );
    }

    // تحديث صورة الإثبات
    public function updateProof(int $id, string $imagePath): void
    {
        $this->db->execute(
            "UPDATE donations SET proof_image = ?
             WHERE id = ? AND status = 'pending'",
            [$imagePath, $id]
        );
    }

    // حذف تبرع
    public function deleteDonation(int $id, int $userId): void
    {
        $this->db->execute(
            "DELETE FROM donations
             WHERE id = ? AND user_id = ? AND status = 'pending'",
            [$id, $userId]
        );
    }

    // التحقق أن التبرع بانتظار التأكيد
    public function isPending(int $id): bool
    {
        $row = $this->db->fetchOne(
            "SELECT status FROM donations WHERE id = ? LIMIT 1",
            [$id]
        );
        return ($row['status'] ?? '') === 'pending';
    }
}