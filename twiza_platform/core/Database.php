<?php

class Database
{
    private static ?Database $instance = null;
    private mysqli $conn;

    private function __construct()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $this->conn = new mysqli(
            DB_HOST,
            DB_USER,
            DB_PASS,
            DB_NAME,
            DB_PORT   // ← مهم في MAMP
        );

        $this->conn->set_charset(DB_CHARSET);
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): mysqli
    {
        return $this->conn;
    }

    // ── استنتاج أنواع الباراميترات تلقائياً ──
    private function buildTypes(array $params): string
    {
        $types = '';
        foreach ($params as $p) {
            if (is_int($p))   $types .= 'i';
            elseif (is_float($p)) $types .= 'd';
            else              $types .= 's';
        }
        return $types;
    }

    // ── ربط الباراميترات بالـ statement ──
    private function bindAndExecute(mysqli_stmt $stmt, array $params): void
    {
        if (empty($params)) {
            $stmt->execute();
            return;
        }

        $types = $this->buildTypes($params);
        $refs  = [];

        foreach ($params as $k => $v) {
            $refs[$k] = &$params[$k];
        }

        array_unshift($refs, $types);
        call_user_func_array([$stmt, 'bind_param'], $refs);
        $stmt->execute();
    }

    // ── تنفيذ استعلام ──
    public function execute(string $sql, array $params = []): mysqli_stmt
    {
        $stmt = $this->conn->prepare($sql);
        $this->bindAndExecute($stmt, $params);
        return $stmt;
    }

    // ── جلب صف واحد ──
    public function fetchOne(string $sql, array $params = []): array|false
    {
        $result = $this->execute($sql, $params)->get_result();
        return $result->fetch_assoc() ?? false;
    }

    // ── جلب كل الصفوف ──
    public function fetchAll(string $sql, array $params = []): array
    {
        $result = $this->execute($sql, $params)->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ── إدراج وإرجاع الـ ID ──
    public function insert(string $sql, array $params = []): int
    {
        $this->execute($sql, $params);
        return (int)$this->conn->insert_id;
    }

    // ── عدد الصفوف المتأثرة ──
    public function rowCount(string $sql, array $params = []): int
    {
        return $this->execute($sql, $params)->affected_rows;
    }
}