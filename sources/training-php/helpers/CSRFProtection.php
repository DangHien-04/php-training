<?php

class CSRFProtection
{
    /**
     * Generate CSRF token
     */
    public static function generateToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Get CSRF token field for forms
     */
    public static function getTokenField()
    {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Verify CSRF token
     */
    public static function verifyToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Require valid CSRF token for POST requests
     */
    public static function requirePostToken($errorMessage = 'CSRF token mismatch!')
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !self::verifyToken($_POST['csrf_token'])) {
                die($errorMessage);
            }
        }
    }
}
