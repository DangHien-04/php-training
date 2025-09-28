<?php
require_once 'models/UserModel.php';
$userModel = new UserModel();

$user = NULL;
$id = NULL;

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $user = $userModel->findUserById($id);
}

if (!empty($_POST['submit'])) {
    if (!empty($id)) {
        $userModel->updateUser($_POST);
    } else {
        $userModel->insertUser($_POST);
    }
    header('location: list_users.php');
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Hồ sơ người dùng</title>
    <?php include 'views/meta.php' ?>
</head>

<body>
    <?php include 'views/header.php' ?>
    <div class="container">
        <?php if ($user || empty($id)) { ?>
        <div class="alert alert-warning" role="alert">
            Hồ sơ người dùng
        </div>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <div class="form-group">
                <label for="name">Tên</label>
                <span><?php echo htmlspecialchars($user[0]['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="form-group">
                <label for="fullname">Họ và tên</label>
                <span><?php echo htmlspecialchars($user[0]['fullname'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <span><?php echo htmlspecialchars($user[0]['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </form>
        <?php } else { ?>
        <div class="alert alert-success" role="alert">
            Không tìm thấy người dùng!
        </div>
        <?php } ?>
    </div>
</body>

</html>