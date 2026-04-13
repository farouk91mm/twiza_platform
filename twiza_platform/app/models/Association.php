<?php

class Association extends Model
{
    protected string $table = 'associations';

    // جلب الجمعية عبر user_id
    public function findByUserId(int $userId): array|false
    {
        return $this->db->fetchOne(
            "SELECT * FROM associations WHERE user_id = ? LIMIT 1",
            [$userId]
        );
    }

    // إنشاء جمعية جديدة
    public function create(array $data): int
    {
        return $this->db->insert(
            "INSERT INTO associations 
                (user_id, official_name, registration_number,
                 description, wilaya, phone, bank_account, ccp_account)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['user_id'],
                $data['official_name'],
                $data['registration_number'],
                $data['description']    ?? null,
                $data['wilaya']         ?? null,
                $data['phone']          ?? null,
                $data['bank_account']   ?? null,
                $data['ccp_account']    ?? null,
            ]
        );
    }

    // تحديث بيانات الجمعية
    public function update(int $id, array $data): void
    {
        $this->db->execute(
            "UPDATE associations SET
                official_name       = ?,
                description         = ?,
                wilaya              = ?,
                phone               = ?,
                bank_account        = ?,
                ccp_account         = ?
             WHERE id = ?",
            [
                $data['official_name'],
                $data['description']  ?? null,
                $data['wilaya']       ?? null,
                $data['phone']        ?? null,
                $data['bank_account'] ?? null,
                $data['ccp_account']  ?? null,
                $id
            ]
        );
    }

    // إحصائيات الجمعية
    public function getStats(int $associationId): array
    {
        // عدد المشاريع
        $projects = $this->db->fetchOne(
            "SELECT COUNT(*) as total,
                    SUM(CASE WHEN status='active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as completed
             FROM projects WHERE association_id = ?",
            [$associationId]
        );

        // إجمالي التبرعات المؤكدة
        $donations = $this->db->fetchOne(
            "SELECT 
                COUNT(*) as total_donations,
                SUM(d.amount) as total_amount
             FROM donations d
             JOIN projects p ON p.id = d.project_id
             WHERE p.association_id = ? AND d.status = 'confirmed'",
            [$associationId]
        );

        return [
            'total_projects'     => $projects['total']      ?? 0,
            'active_projects'    => $projects['active']     ?? 0,
            'completed_projects' => $projects['completed']  ?? 0,
            'total_donations'    => $donations['total_donations'] ?? 0,
            'total_amount'       => $donations['total_amount']    ?? 0,
        ];
    }
}