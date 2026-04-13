<?php

class Model
{
    protected Database $db;
    protected string $table = '';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find(int $id): array|false
    {
        return $this->db->fetchOne("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function all(int $limit = 50): array
    {
        // لا نستعمل LIMIT كـ parameter في بعض الإعدادات، لذا نحقنه بعد تحويله لـ int
        $limit = max(1, (int)$limit);
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY id DESC LIMIT $limit");
    }
}