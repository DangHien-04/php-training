<?php
/**
 * CSRF Protection Helper Class
 * Cung cấp các phương thức để bảo vệ khỏi CSRF attacks
 */
class CSRFProtection
{
    /**
     * Tạo CSRF token nếu chưa có
     */
    public static function generateToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Lấy CSRF token hiện tại
     */
    public static function getToken()
    {
        return self::generateToken();
    }

    /**
     * Tạo hidden input field cho CSRF token
     */
    public static function getTokenField()
    {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Kiểm tra CSRF token từ POST request
     * @param string $errorMessage Thông báo lỗi nếu token không hợp lệ
     * @throws Exception Nếu token không hợp lệ
     */
    public static function requirePostToken($errorMessage = 'CSRF token không hợp lệ!')
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Chỉ cho phép POST request');
        }

        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            throw new Exception($errorMessage);
        }

        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            throw new Exception($errorMessage);
        }
    }

    /**
     * Kiểm tra CSRF token từ GET request
     * @param string $errorMessage Thông báo lỗi nếu token không hợp lệ
     * @throws Exception Nếu token không hợp lệ
     */
    public static function requireGetToken($errorMessage = 'CSRF token không hợp lệ!')
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            throw new Exception('Chỉ cho phép GET request');
        }

        if (!isset($_GET['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            throw new Exception($errorMessage);
        }

        if (!hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
            throw new Exception($errorMessage);
        }
    }

    /**
     * Kiểm tra CSRF token từ request (POST hoặc GET)
     * @param string $errorMessage Thông báo lỗi nếu token không hợp lệ
     * @throws Exception Nếu token không hợp lệ
     */
    public static function requireToken($errorMessage = 'CSRF token không hợp lệ!')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method === 'POST') {
            self::requirePostToken($errorMessage);
        } elseif ($method === 'GET') {
            self::requireGetToken($errorMessage);
        } else {
            throw new Exception('Phương thức request không được hỗ trợ');
        }
    }

    /**
     * Tạo URL với CSRF token
     * @param string $url URL gốc
     * @return string URL với CSRF token
     */
    public static function getUrlWithToken($url)
    {
        $token = self::getToken();
        $separator = (strpos($url, '?') !== false) ? '&' : '?';
        return $url . $separator . 'csrf_token=' . urlencode($token);
    }
}
?>
