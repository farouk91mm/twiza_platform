<?php

class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $file = __DIR__ . '/../views/' . $view . '.php';

        if (!file_exists($file)) {
            http_response_code(500);
            echo "View غير موجود: $view";
            return;
        }

        require $file;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . APP_URL . $path);
        exit;
    }

    protected function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function requireAuth(): void
    {
        if (!Session::isLoggedIn()) $this->redirect('/auth/login');
    }

    protected function requireRole(string $role): void
    {
        $this->requireAuth();
        if (Session::get('user_type') !== $role) $this->redirect('/');
    }
}