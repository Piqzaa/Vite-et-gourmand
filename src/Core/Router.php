<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function add(string $page, Route $route): void
    {
        $this->routes[$page] = $route;
    }

    public function resolve(string $page): ?Route
    {
        return $this->routes[$page] ?? null;
    }
}
