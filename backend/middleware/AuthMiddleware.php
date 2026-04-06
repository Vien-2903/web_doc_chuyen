<?php

class AuthMiddleware {
    private static function ensureSessionStarted() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function checkLogin() {
        self::ensureSessionStarted();

        if (!isset($_SESSION['user'])) {
            header('Location: /web_doc_truyen/frontend/public/index.php?page=login');
            exit();
        }
    }

    public static function checkAdmin() {
        self::ensureSessionStarted();

        if (!isset($_SESSION['user'])) {
            header('Location: /web_doc_truyen/frontend/public/index.php?page=login');
            exit();
        }

        $role = strtolower(trim((string)($_SESSION['user']['vai_tro'] ?? '')));
        if ($role !== 'admin') {
            header('Location: /web_doc_truyen/frontend/public/index.php');
            exit();
        }
    }
}
