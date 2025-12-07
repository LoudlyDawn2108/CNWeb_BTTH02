<?php

class Router {
    private $routes = [];

    public function get($path, $handler): void {
        $this->add('GET', $path, $handler);
    }

    private function add($method, $path, $handler): void {
        // Convert {id} to (\d+) for numeric IDs
        $regex = preg_replace('/\{id}/', '(\d+)', $path);
        // Convert other {param} to ([^/]+)
        $regex = preg_replace('/\{[a-zA-Z_]+}/', '([^/]+)', $regex);

        $this->routes[] = [
            'method' => $method,
            'pattern' => "#^" . $regex . "$#",
            'handler' => $handler
        ];
    }

    public function post($path, $handler): void {
        $this->add('POST', $path, $handler);
    }

    public function dispatch($method, $uri): void {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches); // remove full match

                [$controllerClass, $action] = $route['handler'];

                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $action)) {
                        // Simple parameter mapping for route params
                        // If the action expects arguments, we pass the regex matches in order
                        $controller->$action(...$matches);
                        return;
                    }
                }
            }
        }

        $this->handleNotFound();
    }

    private function handleNotFound(): void {
        http_response_code(404);
        if (file_exists(BASE_PATH . '/views/errors/404.php')) {
            require BASE_PATH . '/views/errors/404.php';
        } else {
            echo '<!DOCTYPE html><html lang="vi"><head><title>404 - Page Not Found</title>
                  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                  </head><body class="bg-light">
                  <div class="container text-center py-5">
                  <h1 class="display-1">404</h1>
                  <p class="lead">Trang không tồn tại</p>
                  <a href="/" class="btn btn-primary">Về trang chủ</a>
                  </div></body></html>';
        }
    }
}
