<?php



require_once 'models/UserModel.php';

$userModel = new UserModel();


    if (!empty($_POST['submit'])) {
        // Kiểm tra đầu vào
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';

        // Kiểm tra định dạng username
        if (!preg_match('/^[a-zA-Z0-9._@]+$/', $username)) {
            $_SESSION['message'] = 'Tên người dùng không hợp lệ!';
        } elseif (empty($password)) {
            $_SESSION['message'] = 'Mật khẩu không được để trống!';
        } else {
            $users = [
                'username' => $username,
                'password' => $password
            ];
            $user = NULL;

            if ($user = $userModel->auth($users['username'], $users['password'])) {
                $_SESSION['id'] = $user[0]['id'];
                $_SESSION['message'] = 'Đăng nhập thành công';

              

                header('location: list_users.php');
                exit;
            } else {
                $_SESSION['message'] = 'Đăng nhập thất bại';
            }
        }
    }

?>
<!DOCTYPE html>
<html>

<head>
    <title>Biểu mẫu đăng nhập</title>
    <?php include 'views/meta.php' ?>
    <!-- Thêm Content Security Policy -->
    <meta http-equiv="Content-Security-Policy"
        content="default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline';">
</head>

<body>
    <?php include 'views/header.php' ?>
    <div class="container">
        <?php if (!empty($_SESSION['message'])) { ?>
        <div class="alert alert-info" role="alert">
            <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>
            <?php unset($_SESSION['message']); ?>
        </div>
        <?php } ?>
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title">Đăng nhập</div>
                    <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="#">Quên mật
                            khẩu?</a></div>
                </div>
                <div style="padding-top:30px" class="panel-body">
                    <form id="login-form" method="post" class="form-horizontal" role="form">
                        <div class="margin-bottom-25 input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input id="login-username" type="text" class="form-control" name="username" value=""
                                placeholder="Tên người dùng hoặc email">
                        </div>
                        <div class="margin-bottom-25 input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input id="login-password" type="password" class="form-control" name="password"
                                placeholder="Mật khẩu">
                        </div>
                        <div class="margin-bottom-25">
                            <input type="checkbox" tabindex="3" name="remember" id="remember">
                            <label for="remember"> Ghi nhớ tôi</label>
                        </div>
                  
                        <div class="margin-bottom-25 input-group">
                            <div class="col-sm-12 controls">
                                <button type="submit" name="submit" value="submit" class="btn btn-primary">Gửi</button>
                                <a id="btn-fblogin" href="#" class="btn btn-primary">Đăng nhập bằng Facebook</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12 control">
                                Chưa có tài khoản?
                                <a href="form_user.php">Đăng ký tại đây</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    // Lưu username vào localStorage trước khi submit
    document.getElementById("login-form").addEventListener("submit", function(e) {
        let username = document.getElementById("login-username").value.trim();
        // Kiểm tra định dạng username trước khi lưu
        if (username && /^[a-zA-Z0-9._@]+$/.test(username)) {
            if (document.getElementById("remember").checked) {
                localStorage.setItem("username", username);
            } else {
                localStorage.removeItem("username");
            }
        }
    });
    </script>
</body>

</html>