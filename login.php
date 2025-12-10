<?php
session_start(); // Bắt buộc phải có dòng này đầu tiên để dùng Session
require_once 'db_connect.php';

$message = "";

if (isset($_POST['btn_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM USERS-datas WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Đăng nhập thành công -> Lưu thông tin user vào Session
        $_SESSION['user'] = $user;
        
        // Chuyển hướng về Trang chủ
        header("Location: index.php");
        exit();
    } else {
        $message = "Sai tên đăng nhập hoặc mật khẩu!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">

<div class="card shadow p-4" style="width: 400px;">
    <h3 class="text-center mb-4 text-primary">Đăng Nhập</h3>
    
    <?php if($message): ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" name="btn_login" class="btn btn-primary w-100">Đăng nhập</button>
        <div class="text-center mt-3">
    <a href="index.php" class="text-decoration-none text-secondary">
        <i class="bi bi-arrow-left"></i> Về trang chủ
    </a>
</div>
    </form>
    <div class="text-center mt-3">
        <a href="register.php">Chưa có tài khoản? Đăng ký ngay</a>
    </div>
    <div class="text-center mt-2">
    </div>
</div>

</body>
</html>