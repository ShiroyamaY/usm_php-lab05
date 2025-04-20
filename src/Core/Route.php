<?php

namespace App\Core;

use Closure;
use Exception;

class Route
{
    public static array $patterns = [
        ':id[0-9]*' => '([0-9]+)',
        ':url[0-9]*' => '([0-9a-zA-Z-_+]+)',
        ':slug[0-9]*' => '([0-9a-zA-Z-_]+)'
    ];

    public static bool $hasRoute = false;

    public static array $routes = [];

    public static string $prefix = '';

    public static string $subdomain = '';

    public static function get(string $path, $callback, $subdomain = null): Route
    {
        $fullPath = self::$prefix . $path;
        self::$routes['get'][$fullPath] = ['callback' => $callback, 'subdomain' => $subdomain];
        return new self();
    }

    public static function post(string $path, $callback, $subdomain = null): Route
    {
        $fullPath = self::$prefix . $path;
        self::$routes['post'][$fullPath] = ['callback' => $callback, 'subdomain' => $subdomain];
        return new self();
    }

    public static function dispatch(): void
    {
        $url = self::getURL();
        $method = self::getMethod();
        $currentSubdomain = self::getSubdomain();

        if (!isset(self::$routes[$method])) {
            self::hasRoute();
            return;
        }

        foreach (self::$routes[$method] as $path => $props) {
            if ($props['subdomain'] !== null && $props['subdomain'] !== $currentSubdomain) {
                continue;
            }

            foreach (self::$patterns as $key => $pattern) {
                $path = preg_replace('#' . $key . '#', $pattern, $path);
            }

            $pattern = '#^' . rtrim($path, '/') . '/?$#';

            if (preg_match($pattern, $url, $params)) {
                self::$hasRoute = true;
                array_shift($params);

                if (isset($props['redirect'])) {
                    Redirect::to($props['redirect'], $props['status']);
                }

                $callback = $props['callback'];

                if (is_callable($callback)) {
                    echo call_user_func_array($callback, $params);
                } elseif (is_string($callback)) {
                    [$controllerName, $methodName] = explode('@', $callback);
                    $controllerName = '\App\Http\Controllers\\' . $controllerName;
                    $controller = new $controllerName();
                    echo call_user_func_array([$controller, $methodName], $params);
                }

                return;
            }
        }

        self::hasRoute();
    }

    public static function hasRoute(): void
    {
        if (!self::$hasRoute) {
            Redirect::to('/');
        }
    }

    public static function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public static function getURL(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = rtrim(str_replace(BASE_PATH, '', $uri), '/');
        return $path ?: '/';
    }

    public function name(string $name): void
    {
        $key = array_key_last(self::$routes['get']);
        self::$routes['get'][$key]['name'] = $name;
    }

    /**
     * @throws Exception
     */
    public static function url(string $name, array $params = []): string
    {
        foreach (self::$routes['get'] as $path => $route) {
            if (isset($route['name']) && $route['name'] === $name) {
                foreach ($params as $key => $value) {
                    $path = preg_replace('/:' . $key . '[0-9]*/', $value, $path);
                }
                return $path;
            }
        }

        throw new Exception("Route named '{$name}' not found.");
    }

    public static function prefix(string $prefix): Route
    {
        self::$prefix = $prefix;
        return new self();
    }

    public static function group(Closure $closure): void
    {
        $closure();
        self::$prefix = '';
    }

    public static function where(string $key, string $pattern): void
    {
        self::$patterns[':' . $key] = '(' . $pattern . ')';
    }

    public static function redirect(string $from, string $to, int $status = 301): void
    {
        $froms = array_map('trim', explode(',', $from));
        foreach ($froms as $from_) {
            $path = self::$prefix . $from_;
            self::$routes['get'][$path] = [
                'redirect' => $to,
                'status' => $status,
                'subdomain' => self::$subdomain
            ];
        }
    }

    public static function subdomain(string $subdomain, Closure $closure): void
    {
        self::$subdomain = $subdomain;
        $closure();
        self::$subdomain = '';
    }

    public static function getSubdomain(): string
    {
        $hostParts = explode('.', $_SERVER['HTTP_HOST']);
        return (count($hostParts) >= 3) ? implode('.', array_slice($hostParts, 0, -2)) : '';
    }

    public static function put(string $path, $callback, $subdomain = null): Route
    {
        $fullPath = self::$prefix . $path;
        self::$routes['put'][$fullPath] = ['callback' => $callback, 'subdomain' => $subdomain];
        return new self();
    }

    public static function delete(string $path, $callback, $subdomain = null): Route
    {
        $fullPath = self::$prefix . $path;
        self::$routes['delete'][$fullPath] = ['callback' => $callback, 'subdomain' => $subdomain];
        return new self();
    }
}
