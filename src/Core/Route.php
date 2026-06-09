<?php

namespace App\Core;

class Route
{
    public readonly string $defaultAction;
    public readonly array $actionMap;
    public $handler;

    public function __construct(
        public readonly ?string $controllerClass = null,
        string $defaultAction = 'index',
        array $actionMap = [],
        $handler = null,
    ) {
        $this->defaultAction = $defaultAction;
        $this->actionMap = $actionMap;
        $this->handler = $handler;
    }

    public function getAction(string $action): string
    {
        return $this->actionMap[$action] ?? $this->defaultAction;
    }
}
