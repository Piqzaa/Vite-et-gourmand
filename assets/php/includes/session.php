<?php

function sessionStart() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isConnected(): bool {
    return isset($_SESSION['user_id']);
}

function getUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

function getUserRole(): ?string {
    return $_SESSION['user_role'] ?? null;
}
