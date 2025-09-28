<?php
/**
 * CSRF Protection Test Script
 * Chạy script này để test tự động bảo mật CSRF
 */

echo "<h1>CSRF Protection Test</h1>\n";

// Test 1: Kiểm tra class CSRFProtection có tồn tại không
echo "<h2>Test 1: Kiểm tra CSRFProtection class</h2>\n";
if (file_exists('helpers/CSRFProtection.php')) {
    require_once 'helpers/CSRFProtection.php';
    echo "✅ File CSRFProtection.php tồn tại<br>\n";
    
    if (class_exists('CSRFProtection')) {
        echo "✅ Class CSRFProtection tồn tại<br>\n";
    } else {
        echo "❌ Class CSRFProtection không tồn tại<br>\n";
    }
} else {
    echo "❌ File CSRFProtection.php không tồn tại<br>\n";
}

// Test 2: Kiểm tra token generation
echo "<h2>Test 2: Token Generation</h2>\n";
session_start();
$token1 = CSRFProtection::getToken();
$token2 = CSRFProtection::getToken();

echo "Token 1: " . substr($token1, 0, 10) . "...<br>\n";
echo "Token 2: " . substr($token2, 0, 10) . "...<br>\n";

if ($token1 === $token2) {
    echo "✅ Token generation hoạt động đúng (cùng session)<br>\n";
} else {
    echo "❌ Token generation có vấn đề<br>\n";
}

// Test 3: Kiểm tra token field generation
echo "<h2>Test 3: Token Field Generation</h2>\n";
$tokenField = CSRFProtection::getTokenField();
echo "Token field: " . htmlspecialchars($tokenField) . "<br>\n";

if (strpos($tokenField, 'csrf_token') !== false && strpos($tokenField, $token1) !== false) {
    echo "✅ Token field generation hoạt động đúng<br>\n";
} else {
    echo "❌ Token field generation có vấn đề<br>\n";
}

// Test 4: Kiểm tra validation (simulate)
echo "<h2>Test 4: Token Validation</h2>\n";

// Test với token đúng
$_POST['csrf_token'] = $token1;
$_SERVER['REQUEST_METHOD'] = 'POST';

try {
    CSRFProtection::requirePostToken('Test error message');
    echo "✅ Token validation với token đúng: PASS<br>\n";
} catch (Exception $e) {
    echo "❌ Token validation với token đúng: FAIL - " . $e->getMessage() . "<br>\n";
}

// Test với token sai
$_POST['csrf_token'] = 'fake_token_123';

try {
    CSRFProtection::requirePostToken('Test error message');
    echo "❌ Token validation với token sai: FAIL (không bị chặn)<br>\n";
} catch (Exception $e) {
    echo "✅ Token validation với token sai: PASS - " . $e->getMessage() . "<br>\n";
}

// Test 5: Kiểm tra các file có CSRF protection
echo "<h2>Test 5: File Protection Check</h2>\n";

$files_to_check = [
    'login.php' => 'CSRFProtection::requirePostToken',
    'form_user.php' => 'CSRFProtection::requirePostToken', 
    'delete_user.php' => 'csrf_token'
];

foreach ($files_to_check as $file => $pattern) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strpos($content, $pattern) !== false) {
            echo "✅ $file có CSRF protection<br>\n";
        } else {
            echo "❌ $file thiếu CSRF protection<br>\n";
        }
    } else {
        echo "❌ $file không tồn tại<br>\n";
    }
}

echo "<h2>Test hoàn thành!</h2>\n";
echo "<p>Nếu tất cả test đều PASS, dự án đã có bảo mật CSRF đầy đủ.</p>\n";
?>
