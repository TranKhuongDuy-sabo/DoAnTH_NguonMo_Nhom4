<?php
session_start();
require_once 'db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$message = "";

// ... (Phần session_start và require_once giữ nguyên) ...

if (isset($_POST['btn_update'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['numberphone'];
    $address = $_POST['address'];
    $id = $user['id'];
    
    // Lấy giá trị redirect từ ô input ẩn
    $redirect_target = $_POST['redirect_value']; 

    $sql = "UPDATE USERS SET fullname=?, email=?, numberphone=?, address=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$fullname, $email, $phone, $address, $id])) {
        // Cập nhật lại Session
        $_SESSION['user']['fullname'] = $fullname;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['numberphone'] = $phone;
        $_SESSION['user']['address'] = $address;
        
        // --- LOGIC CHUYỂN HƯỚNG MỚI ---
        if ($redirect_target == 'checkout') {
            header("Location: checkout.php"); // Quay về thanh toán
            exit();
        }
        // ------------------------------

        $message = "Cập nhật thành công!";
        $user = $_SESSION['user']; 
    } else {
        $message = "Lỗi cập nhật.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">ANDROID SHOP</a>
            <a href="index.php" class="btn btn-secondary">Về trang chủ</a>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Cập nhật thông tin</h4>
                    </div>
                    <div class="card-body">
                        <?php if($message): ?>
                            <div class="alert alert-success"><?php echo $message; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                        <?php 
                            $redirect_to = isset($_GET['redirect']) ? $_GET['redirect'] : ''; 
                        ?>
                        <input type="hidden" name="redirect_value" value="<?php echo htmlspecialchars($redirect_to); ?>">
                            <div class="mb-3">
                                <label>Họ và tên</label>
                                <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Số điện thoại</label>
                                <input type="text" name="numberphone" class="form-control" value="<?php echo htmlspecialchars($user['numberphone'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label>Địa chỉ nhận hàng</label>
                                <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>
                            <button type="submit" name="btn_update" class="btn btn-primary">Lưu thay đổi</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>