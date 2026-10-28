<?php

declare(strict_types=1);

namespace Core;

/**
 * Dischedule Fast RESTful Router
 */
class Router
{
    private array $routes = [];
    private array $groupStack = [];

    public function get(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->addRoute('POST', $path, $handler, $middlewares);
    }

    public function put(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->addRoute('PUT', $path, $handler, $middlewares);
    }

    public function delete(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    public function any(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->addRoute(['GET', 'POST', 'PUT', 'DELETE'], $path, $handler, $middlewares);
    }

    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    private function addRoute(string|array $methods, string $path, array|callable $handler, array $middlewares = []): self
    {
        $prefix = '';
        $groupMiddlewares = [];

        foreach ($this->groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }
            if (isset($group['middleware'])) {
                $m = is_array($group['middleware']) ? $group['middleware'] : [$group['middleware']];
                $groupMiddlewares = array_merge($groupMiddlewares, $m);
            }
        }

        $combined = trim($prefix . '/' . trim($path, '/'), '/');
        $fullPath = $combined === '' ? '/' : '/' . $combined;

        $mergedMiddlewares = array_merge($groupMiddlewares, $middlewares);
        $methods = is_array($methods) ? $methods : [$methods];

        foreach ($methods as $method) {
            $this->routes[] = [
                'method'      => strtoupper($method),
                'path'        => $fullPath,
                'handler'     => $handler,
                'middlewares' => $mergedMiddlewares
            ];
        }

        return $this;
    }

    public function dispatch(Request $request, Response $response): void
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method && $route['method'] !== 'ANY') {
                continue;
            }

            $pattern = $this->convertPathToRegex($route['path']);
            if (preg_match($pattern, $path, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = urldecode($value);
                    }
                }
                $request->setRouteParams($params);

                // Run middlewares
                foreach ($route['middlewares'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    $middleware->handle($request, $response);
                }

                $handler = $route['handler'];

                if (is_callable($handler)) {
                    call_user_func($handler, $request, $response);
                    return;
                }

                if (is_array($handler) && count($handler) === 2) {
                    [$controllerClass, $action] = $handler;
                    if (!class_exists($controllerClass)) {
                        throw new \RuntimeException("Controller class {$controllerClass} not found.");
                    }
                    $controller = new $controllerClass($request, $response);
                    if (!method_exists($controller, $action)) {
                        throw new \RuntimeException("Action {$action} not found in {$controllerClass}.");
                    }
                    $controller->$action($request, $response);
                    return;
                }
            }
        }

        // No route matched: 404
        $this->handleNotFound($request, $response);
    }

    private function convertPathToRegex(string $path): string
    {
        // Replace {param} with named regex capture group (?P<param>[^/]+)
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function handleNotFound(Request $request, Response $response): void
    {
        if ($request->isAjax() || str_starts_with($request->getPath(), '/api/')) {
            $response->json([
                'success' => false,
                'message' => 'Endpoint or resource not found.'
            ], 404);
            return;
        }

        $response->setStatusCode(404);
        $html = View::render('errors.404', [
            'title' => 'Page Not Found'
        ], 'main');
        $response->html($html);
    }
}
