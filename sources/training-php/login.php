<?php
// Start the session
session_start();

require_once 'models/UserModel.php';
$userModel = new UserModel();


if (!empty($_POST['submit'])) {
    $users = [
        'username' => $_POST['username'],
        'password' => $_POST['password']
    ];
    $user = NULL;
    if ($user = $userModel->auth($users['username'], $users['password'])) {
        //Login successful
        $_SESSION['id'] = $user[0]['id'];

        // Save login info to Redis if Remember Me is checked
        if (!empty($_POST['remember'])) {
            try {
                require_once 'configs/redis.php';
                $redis = createRedisClient();
                
                $loginData = [
                    'user_id' => $user[0]['id'],
                    'username' => $users['username'],
                    'login_time' => date('Y-m-d H:i:s'),
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ];
                
                // Save to Redis with expiration (7 days)
                $redis->setex('user_login_' . $user[0]['id'], 7*24*60*60, json_encode($loginData));
                $redis->setex('user_session_' . $users['username'], 7*24*60*60, $user[0]['id']);
                
                $_SESSION['message'] = 'Login successful - Data saved to Redis!';
            } catch (Exception $e) {
                $_SESSION['message'] = 'Login successful - Redis save failed: ' . $e->getMessage();
            }
        } else {
            $_SESSION['message'] = 'Login successful';
        }
        
        header('location: list_users.php');
    }else {
        //Login failed
        $_SESSION['message'] = 'Login failed';
    }

}

?>
<!DOCTYPE html>
<html>
<head>
    <title>User form</title>
    <?php include 'views/meta.php' ?>
</head>
<body>
<?php include 'views/header.php'?>

    <div class="container">
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-info" >
                <div class="panel-heading">
                    <div class="panel-title">Login</div>
                    <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="#">Forgot password?</a></div>
                </div>

                <div style="padding-top:30px" class="panel-body" >
                    <form method="post" class="form-horizontal" role="form">

                        <div class="margin-bottom-25 input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input id="login-username" type="text" class="form-control" name="username" value="" placeholder="username or email">
                        </div>

                        <div class="margin-bottom-25 input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input id="login-password" type="password" class="form-control" name="password" placeholder="password">
                        </div>

                        <div class="margin-bottom-25">
                            <input type="checkbox" tabindex="3" class="" name="remember" id="remember">
                            <label for="remember"> Remember Me (Lưu vào localStorage)</label>
                        </div>

                        <div class="margin-bottom-25 input-group">
                            <!-- Button -->
                            <div class="col-sm-12 controls">
                                <button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
                                <a id="btn-fblogin" href="#" class="btn btn-primary">Login with Facebook</a>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12 control">
                                    Don't have an account!
                                    <a href="form_user.php">
                                        Sign Up Here
                                    </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Debug function
    function debugLog(message) {
        console.log('[DEBUG] ' + message);
        // Also show in page for debugging
        if (typeof window.debugMessages === 'undefined') {
            window.debugMessages = [];
        }
        window.debugMessages.push(message);
    }

    // Auto-fill form from localStorage
    document.addEventListener('DOMContentLoaded', function() {
        debugLog('DOM loaded, checking localStorage...');
        
        const savedUsername = localStorage.getItem('saved_username');
        const savedPassword = localStorage.getItem('saved_password');
        const rememberMe = localStorage.getItem('remember_me');
        
        debugLog('savedUsername: ' + savedUsername);
        debugLog('savedPassword: ' + (savedPassword ? '***' : 'null'));
        debugLog('rememberMe: ' + rememberMe);
        
        if (savedUsername) {
            document.getElementById('login-username').value = savedUsername;
            debugLog('Auto-filled username');
        }
        if (savedPassword && rememberMe === 'true') {
            document.getElementById('login-password').value = savedPassword;
            document.getElementById('remember').checked = true;
            debugLog('Auto-filled password and checked remember me');
        }
        
        // Show current localStorage content
        debugLog('Current localStorage keys: ' + Object.keys(localStorage));
    });

    // Save to localStorage when form is submitted with Remember Me checked
    document.querySelector('form').addEventListener('submit', function(e) {
        debugLog('Form submitted, checking remember me...');
        
        const username = document.getElementById('login-username').value;
        const password = document.getElementById('login-password').value;
        const rememberMe = document.getElementById('remember').checked;
        
        debugLog('username: ' + username);
        debugLog('password: ' + (password ? '***' : 'null'));
        debugLog('rememberMe: ' + rememberMe);
        
        if (rememberMe && username && password) {
            localStorage.setItem('saved_username', username);
            localStorage.setItem('saved_password', password);
            localStorage.setItem('remember_me', 'true');
            debugLog('Đã lưu thông tin đăng nhập vào localStorage');
            
            // Verify it was saved
            setTimeout(function() {
                debugLog('Verification - localStorage after save:');
                debugLog('saved_username: ' + localStorage.getItem('saved_username'));
                debugLog('saved_password: ' + (localStorage.getItem('saved_password') ? '***' : 'null'));
                debugLog('remember_me: ' + localStorage.getItem('remember_me'));
            }, 100);
        } else {
            localStorage.removeItem('saved_username');
            localStorage.removeItem('saved_password');
            localStorage.removeItem('remember_me');
            debugLog('Đã xóa thông tin đăng nhập khỏi localStorage');
        }
    });

    // Alternative: Save on checkbox change
    document.getElementById('remember').addEventListener('change', function() {
        const username = document.getElementById('login-username').value;
        const password = document.getElementById('login-password').value;
        const rememberMe = this.checked;
        
        debugLog('Remember me checkbox changed to: ' + rememberMe);
        
        if (rememberMe && username && password) {
            localStorage.setItem('saved_username', username);
            localStorage.setItem('saved_password', password);
            localStorage.setItem('remember_me', 'true');
            debugLog('Đã lưu thông tin đăng nhập vào localStorage (checkbox change)');
        }
    });

    // Alternative: Save on input change
    document.getElementById('login-username').addEventListener('input', function() {
        const rememberMe = document.getElementById('remember').checked;
        if (rememberMe) {
            localStorage.setItem('saved_username', this.value);
            debugLog('Updated username in localStorage: ' + this.value);
        }
    });

    document.getElementById('login-password').addEventListener('input', function() {
        const rememberMe = document.getElementById('remember').checked;
        if (rememberMe) {
            localStorage.setItem('saved_password', this.value);
            debugLog('Updated password in localStorage');
        }
    });

    // Test localStorage functionality
    function testLocalStorage() {
        debugLog('Testing localStorage functionality...');
        try {
            localStorage.setItem('test_key', 'test_value');
            const testValue = localStorage.getItem('test_key');
            if (testValue === 'test_value') {
                debugLog('localStorage test PASSED');
                localStorage.removeItem('test_key');
            } else {
                debugLog('localStorage test FAILED');
            }
        } catch (e) {
            debugLog('localStorage test ERROR: ' + e.message);
        }
    }

    // Run test on load
    document.addEventListener('DOMContentLoaded', function() {
        testLocalStorage();
    });
    </script>

</body>
</html>