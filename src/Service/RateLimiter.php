<?php

namespace App\Service;

class RateLimiter {
    private const MAX_ATTEMPTS = 5;
    private const WINDOW_SECONDS = 900;

    public function isAllowed(string $action): bool {
        $this->cleanExpired();
        $key = 'rate_limit_' . $action;
        $entry = $_SESSION[$key] ?? ['count' => 0, 'reset_at' => time() + self::WINDOW_SECONDS];
        return $entry['count'] < self::MAX_ATTEMPTS;
    }

    public function increment(string $action): void {
        $key = 'rate_limit_' . $action;
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'reset_at' => time() + self::WINDOW_SECONDS];
        }
        $_SESSION[$key]['count']++;
    }

    public function reset(string $action): void {
        unset($_SESSION['rate_limit_' . $action]);
    }

    public function getRemainingAttempts(string $action): int {
        $key = 'rate_limit_' . $action;
        $entry = $_SESSION[$key] ?? ['count' => 0, 'reset_at' => time() + self::WINDOW_SECONDS];
        return max(0, self::MAX_ATTEMPTS - $entry['count']);
    }

    private function cleanExpired(): void {
        foreach ($_SESSION as $key => $value) {
            if (str_starts_with($key, 'rate_limit_') && isset($value['reset_at']) && time() > $value['reset_at']) {
                unset($_SESSION[$key]);
            }
        }
    }
}
