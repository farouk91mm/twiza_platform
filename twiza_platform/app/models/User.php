<?php

class User extends Model
{
    protected string $table = 'users';

    // ── إنشاء مستخدم جديد ──
    public function create(array $data): int
    {
        return $this->db->insert(
            "INSERT INTO users 
                (full_name, email, password, user_type)
             VALUES (?, ?, ?, ?)",
            [
                $data['full_name'],
                $data['email'],
                $data['password'], // مشفر مسبقاً
                $data['user_type']
            ]
        );
    }

    // ── البحث بالبريد ──
    public function findByEmail(string $email): array|false
    {
        return $this->db->fetchOne(
            "SELECT * FROM users WHERE email = ? LIMIT 1",
            [$email]
        );
    }

    // ── البحث بـ provider_id (Google/Facebook) ──
    public function findBySocialId(
        string $provider,
        string $providerId
    ): array|false {
        return $this->db->fetchOne(
            "SELECT u.* FROM users u
             JOIN social_accounts sa ON sa.user_id = u.id
             WHERE sa.provider = ? AND sa.provider_id = ?
             LIMIT 1",
            [$provider, $providerId]
        );
    }

    // ── ربط حساب اجتماعي بمستخدم موجود ──
    public function attachSocialAccount(
        int    $userId,
        string $provider,
        string $providerId,
        string $accessToken = ''
    ): int {
        return $this->db->insert(
            "INSERT INTO social_accounts 
                (user_id, provider, provider_id, access_token)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE access_token = VALUES(access_token)",
            [$userId, $provider, $providerId, $accessToken]
        );
    }

    // ── إنشاء ملف الفرد بعد التسجيل ──
    public function createIndividualProfile(int $userId): void
    {
        $this->db->execute(
            "INSERT INTO individual_profiles (user_id) VALUES (?)",
            [$userId]
        );
    }

    // ── إنشاء ملف التاجر بعد التسجيل ──
    public function createMerchantProfile(
        int    $userId,
        string $shopName,
        string $activityType
    ): void {
        $this->db->execute(
            "INSERT INTO merchant_profiles 
                (user_id, shop_name, activity_type)
             VALUES (?, ?, ?)",
            [$userId, $shopName, $activityType]
        );
    }

    // ── التحقق من وجود البريد ──
    public function emailExists(string $email): bool
    {
        $row = $this->db->fetchOne(
            "SELECT id FROM users WHERE email = ? LIMIT 1",
            [$email]
        );
        return (bool)$row;
    }

    // ── تحديث آخر دخول ──
    public function updateLastLogin(int $userId): void
    {
        $this->db->execute(
            "UPDATE users SET updated_at = NOW() WHERE id = ?",
            [$userId]
        );
    }
}