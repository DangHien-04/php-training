<?php
session_start();
require_once 'models/UserModel.php';
$userModel = new UserModel();

// Chỉ cho phép POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Kiểm tra CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token mismatch!");
    }

    // Lấy id từ query string (không hidden)
    if (!empty($_GET['id'])) {
        $id = (int) $_GET['id'];
        $userModel->deleteUserById($id); // Delete existing user
    }

    header('Location: list_users.php');
    exit;
} else {
    die("Invalid request method!");
}