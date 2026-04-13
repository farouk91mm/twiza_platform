<?php

class Router
{
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, string $controller, string $method): void
    {
        $this->routes['GET'][$path] = [$controller, $method];
    }

    public function post(string $path, string $controller, string $method): void
    {
        $this->routes['POST'][$path] = [$controller, $method];
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        // إزالة BASE_PATH من بداية الرابط
        if (str_starts_with($uri, BASE_PATH)) {
            $uri = substr($uri, strlen(BASE_PATH));
        }
        $uri = $uri === '' ? '/' : $uri;

        if (!isset($this->routes[$method][$uri])) {
            http_response_code(404);
            echo "404 | الصفحة غير موجودة";
            return;
        }

        [$controllerName, $action] = $this->routes[$method][$uri];

        if (!class_exists($controllerName)) {
            http_response_code(500);
            echo "Controller غير موجود: $controllerName";
            return;
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $action)) {
            http_response_code(500);
            echo "Method غير موجود: $controllerName::$action";
            return;
        }

        $controller->$action();
    }
}