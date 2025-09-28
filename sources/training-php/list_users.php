<?php


require_once 'models/UserModel.php';
$userModel = new UserModel();

$params = [];
if (!empty($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']);
    if (strlen($keyword) <= 100) { // Giới hạn độ dài keyword
        $params['keyword'] = $keyword;
    }
}

$users = $userModel->getUsers($params);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Danh sách người dùng</title>
    <?php include 'views/meta.php' ?>
</head>

<body>
    <?php include 'views/header.php' ?>
    <div class="container">
        <?php if (!empty($users)) { ?>
        <div class="alert alert-warning" role="alert">
            Danh sách người dùng!
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Tên người dùng</th>
                    <th scope="col">Họ và tên</th>
                    <th scope="col">Loại</th>
                    <th scope="col">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) { ?>
                <tr>
                    <th scope="row"><?php echo htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8'); ?></th>
                    <td><?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($user['fullname'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($user['type'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <a href="form_user.php?id=<?php echo htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <i class="fa fa-pencil-square-o" aria-hidden="true" title="Cập nhật"></i>
                        </a>
                        <a href="view_user.php?id=<?php echo htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <i class="fa fa-eye" aria-hidden="true" title="Xem"></i>
                        </a>
                        <form method="POST" action="delete_user.php" style="display:inline;"
                            onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
                            <input type="hidden" name="id"
                                value="<?php echo htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8'); ?>">
                      
                            <button type="submit" style="border:none;background:none;padding:0;cursor:pointer;">
                                <i class="fa fa-eraser text-danger" aria-hidden="true" title="Xóa"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } else { ?>
        <div class="alert alert-dark" role="alert">
            Không tìm thấy người dùng!
        </div>
        <?php } ?>
    </div>
</body>

</html> 