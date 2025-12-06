<?php

use Lib\FormRequest;

class Router {
    private $routes = [];

    public function get($path, $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post($path, $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add($method, $path, $handler): void
    {
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

    public function dispatch($method, $uri): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches); // remove full match
                
                [$controllerClass, $action] = $route['handler'];
                
                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $action)) {
                        
                        // Reflection to map parameters
                        $reflection = new ReflectionMethod($controller, $action);
                        $args = [];
                        
                        foreach ($reflection->getParameters() as $param) {
                            $type = $param->getType();
                            if ($type && !$type->isBuiltin() && is_subclass_of($type->getName(), FormRequest::class)) {
                                // Instantiate FormRequest (triggers hydration and validation)
                                $requestClass = $type->getName();
                                $args[] = new $requestClass();
                            } else {
                                // Route parameter (scalar)
                                if (!empty($matches)) {
                                    $args[] = array_shift($matches);
                                } else {
                                    // Optional param or error?
                                    // If default value exists, use it
                                    if ($param->isDefaultValueAvailable()) {
                                        $args[] = $param->getDefaultValue();
                                    } else {
                                        // Should not happen if route regex matches params
                                        $args[] = null; 
                                    }
                                }
                            }
                        }
                        $controller->$action(...$args);
                        return;
                    }
                }
            }
        }

        $this->handleNotFound();
    }

    private function handleNotFound(): void
    {
        http_response_code(404);
        echo '<!DOCTYPE html><html><head><title>404 - Page Not Found</title>
              <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
              </head><body class="bg-light">
              <div class="container text-center py-5">
              <h1 class="display-1">404</h1>
              <p class="lead">Trang không tồn tại</p>
              <a href="/" class="btn btn-primary">Về trang chủ</a>
              </div></body></html>';
    }
}
