<?php
require_once 'db_connect.php';

$message = "";

if (isset($_POST['btn_register'])) {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Kiểm tra xem username đã tồn tại chưa
    $check = $conn->prepare("SELECT id FROM USERS WHERE username = ?");
    $check->execute([$username]);
    
    if ($check->rowCount() > 0) {
        $message = "Tên đăng nhập đã tồn tại!";
    } else {
        // Mã hóa mật khẩu (Bảo mật)
        // Lưu ý: Nếu cột password trong CSDL ngắn quá (dưới 60 ký tự) thì cần sửa lại độ dài VARCHAR(255)
        // Tuy nhiên bảng USERS mình tạo lúc trước là VARCHAR(255) rồi nên yên tâm.
        // Ở đây mình lưu pass thường để bạn dễ test, thực tế nên dùng password_hash()
        
        $sql = "INSERT INTO USERS (fullname, username, email_Ss, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([$fullname, $username, $email, $password])) {
            header("Location: login.php"); // Đăng ký xong chuyển qua trang đăng nhập
            exit();
        } else {
            $message = "Có lỗi xảy ra, vui lòng thử lại.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">

<div class="card shadow p-4" style="width: 400px;">
    <h3 class="text-center mb-4">Đăng Ký</h3>
    
    <?php if($message): ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label">Họ và tên</label>
            <input type="text" name="fullname" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" name="btn_register" class="btn btn-success w-100">Đăng ký</button>
        <div class="text-center mt-3">
    <a href="index.php" class="text-decoration-none text-secondary">
        <i class="bi bi-arrow-left"></i> Về trang chủ
    </a>
</div>
    </form>
    <div class="text-center mt-3">
        <a href="login.php">Đã có tài khoản? Đăng nhập</a>
    </div>
</div>

</body>
</html>