<?php

class Project extends Model
{
    protected string $table = 'projects';

    // إنشاء مشروع جديد
    public function create(array $data): int
    {
        $beneficiaryCount = !empty($data['beneficiary_count'])
                            ? (int)$data['beneficiary_count']
                            : null;

        $deadline = !empty($data['deadline'])
                    ? $data['deadline']
                    : null;

        $coverImage = !empty($data['cover_image'])
                      ? $data['cover_image']
                      : null;

        return $this->db->insert(
            "INSERT INTO projects
                (association_id, category_id, title, description,
                 target_amount, beneficiary_count, cover_image,
                 deadline, allow_recurring, allow_groups)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                (int)$data['association_id'],
                (int)$data['category_id'],
                $data['title'],
                $data['description'],
                (float)$data['target_amount'],
                $beneficiaryCount,
                $coverImage,
                $deadline,
                (int)($data['allow_recurring'] ?? 1),
                (int)($data['allow_groups']    ?? 1),
            ]
        );
    }

    // جلب مشاريع جمعية معينة
    public function getByAssociation(int $associationId): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, pc.name as category_name,
                    pc.icon as category_icon,
                    pc.color as category_color,
                    COUNT(d.id) as donors_count
             FROM projects p
             LEFT JOIN project_categories pc ON pc.id = p.category_id
             LEFT JOIN donations d ON d.project_id = p.id
                        AND d.status = 'confirmed'
             WHERE p.association_id = ?
             GROUP BY p.id
             ORDER BY p.created_at DESC",
            [$associationId]
        );
    }

    // جلب مشروع خاص بجمعية معينة
    public function findByAssociation(
        int $projectId,
        int $associationId
    ): array|false {
        return $this->db->fetchOne(
            "SELECT * FROM projects
             WHERE id = ? AND association_id = ?
             LIMIT 1",
            [$projectId, $associationId]
        );
    }

    // تحديث المشروع
    public function update(int $id, array $data): void
    {
        $this->db->execute(
            "UPDATE projects SET
                category_id       = ?,
                title             = ?,
                description       = ?,
                target_amount     = ?,
                beneficiary_count = ?,
                deadline          = ?,
                allow_recurring   = ?,
                allow_groups      = ?,
                status            = ?,
                updated_at        = NOW()
             WHERE id = ?",
            [
                (int)$data['category_id'],
                $data['title'],
                $data['description'],
                (float)$data['target_amount'],
                !empty($data['beneficiary_count'])
                    ? (int)$data['beneficiary_count']
                    : null,
                !empty($data['deadline'])
                    ? $data['deadline']
                    : null,
                (int)($data['allow_recurring'] ?? 0),
                (int)($data['allow_groups']    ?? 0),
                $data['status'] ?? 'active',
                $id,
            ]
        );
    }

    // تحديث صورة الغلاف فقط
    public function updateCover(int $id, string $imagePath): void
    {
        $this->db->execute(
            "UPDATE projects SET cover_image = ? WHERE id = ?",
            [$imagePath, $id]
        );
    }

    // جلب كل المشاريع النشطة
    public function getActive(
        int    $limit    = 12,
        int    $offset   = 0,
        string $category = '',
        string $search   = ''
    ): array {
        $sql = "SELECT p.*,
                       pc.name  as category_name,
                       pc.icon  as category_icon,
                       pc.color as category_color,
                       a.official_name as association_name,
                       a.logo          as association_logo,
                       COUNT(d.id)     as donors_count
                FROM projects p
                LEFT JOIN project_categories pc ON pc.id = p.category_id
                LEFT JOIN associations a ON a.id = p.association_id
                LEFT JOIN donations d ON d.project_id = p.id
                           AND d.status = 'confirmed'
                WHERE p.status = 'active'";

        $params = [];

        if ($category !== '') {
            $sql     .= " AND p.category_id = ?";
            $params[] = (int)$category;
        }

        if ($search !== '') {
            $sql     .= " AND p.title LIKE ?";
            $params[] = "%$search%";
        }

        $sql .= " GROUP BY p.id
                  ORDER BY p.created_at DESC
                  LIMIT $limit OFFSET $offset";

        return $this->db->fetchAll($sql, $params);
    }

    // جلب تفاصيل مشروع واحد
    public function getDetails(int $projectId): array|false
    {
        return $this->db->fetchOne(
            "SELECT p.*,
                    pc.name  as category_name,
                    pc.icon  as category_icon,
                    pc.color as category_color,
                    a.official_name as association_name,
                    a.logo          as association_logo,
                    a.bank_account  as association_bank,
                    a.ccp_account   as association_ccp,
                    a.phone         as association_phone,
                    COUNT(d.id)     as donors_count
             FROM projects p
             LEFT JOIN project_categories pc ON pc.id = p.category_id
             LEFT JOIN associations a ON a.id = p.association_id
             LEFT JOIN donations d ON d.project_id = p.id
                        AND d.status = 'confirmed'
             WHERE p.id = ?
             GROUP BY p.id
             LIMIT 1",
            [$projectId]
        );
    }

    // تحديث المبلغ المجموع
    public function updateCollected(int $projectId): void
    {
        $this->db->execute(
            "UPDATE projects p SET
                collected_amount = (
                    SELECT COALESCE(SUM(amount), 0)
                    FROM donations
                    WHERE project_id = ? AND status = 'confirmed'
                )
             WHERE p.id = ?",
            [$projectId, $projectId]
        );
    }

    // زيادة عدد المشاهدات
    public function incrementViews(int $projectId): void
    {
        $this->db->execute(
            "UPDATE projects SET views_count = views_count + 1
             WHERE id = ?",
            [$projectId]
        );
    }

    // جلب كل الفئات
    public function getCategories(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM project_categories ORDER BY name"
        );
    }

    // تغيير حالة المشروع
    public function updateStatus(int $projectId, string $status): void
    {
        $this->db->execute(
            "UPDATE projects SET status = ? WHERE id = ?",
            [$status, $projectId]
        );
    }

    // ← الدالة الجديدة المضافة
    public function belongsToAssociation(
        int $projectId,
        int $associationId
    ): bool {
        $row = $this->db->fetchOne(
            "SELECT id FROM projects
             WHERE id = ? AND association_id = ?
             LIMIT 1",
            [$projectId, $associationId]
        );
        return (bool)$row;
    }

    // التحقق هل للمشروع تبرعات
public function hasDonations(int $projectId): bool
{
    $row = $this->db->fetchOne(
        "SELECT COUNT(*) as total FROM donations
         WHERE project_id = ?",
        [$projectId]
    );
    return ($row['total'] ?? 0) > 0;
}

// حذف المشروع
public function deleteProject(int $projectId): void
{
    $this->db->execute(
        "DELETE FROM projects WHERE id = ?",
        [$projectId]
    );
}
}