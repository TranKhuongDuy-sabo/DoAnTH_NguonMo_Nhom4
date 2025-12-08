<?php
session_start();
require_once 'db_connect.php';

$message_status = "";

if (isset($_POST['btn_contact'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    
    // Nếu đang đăng nhập thì lấy ID, không thì để NULL
    $userid = isset($_SESSION['user']) ? $_SESSION['user']['id'] : NULL;

    $sql = "INSERT INTO EMAILS (name, email, message, userid) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$name, $email, $message, $userid])) {
        $message_status = "Cảm ơn bạn! Chúng tôi đã nhận được tin nhắn và sẽ phản hồi sớm nhất.";
    } else {
        $message_status = "Có lỗi xảy ra, vui lòng thử lại.";
    }
}

// Nếu user đã đăng nhập, tự điền sẵn tên và email vào form cho tiện
$current_name = isset($_SESSION['user']) ? $_SESSION['user']['fullname'] : '';
$current_email = isset($_SESSION['user']) ? $_SESSION['user']['email'] : '';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Liên hệ với chúng tôi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">ANDROID SHOP</a>
        <a href="index.php" class="btn btn-outline-light btn-sm"><i class="bi bi-house-door-fill"></i> Trang chủ</a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-envelope-paper-fill"></i> Gửi thắc mắc / Liên hệ</h4>
                </div>
                <div class="card-body p-4">
                    
                    <?php if($message_status): ?>
                        <div class="alert alert-success text-center">
                            <i class="bi bi-check-circle-fill"></i> <?php echo $message_status; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Họ tên của bạn</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($current_name); ?>" required placeholder="Nhập họ tên...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email liên hệ</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($current_email); ?>" required placeholder="Nhập email...">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nội dung tin nhắn</label>
                            <textarea name="message" class="form-control" rows="5" required placeholder="Bạn cần hỗ trợ gì về sản phẩm, bảo hành, giao hàng...?"></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="btn_contact" class="btn btn-success btn-lg">
                                <i class="bi bi-send-fill"></i> Gửi tin nhắn
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">
                    
                    <div class="row text-center">
                        <div class="col-md-4">
                            <i class="bi bi-geo-alt-fill text-danger fs-3"></i>
                            <p class="fw-bold mt-2">Địa chỉ</p>
                            <small>123 Đường ABC, Quận 8, TP.HCM</small>
                        </div>
                        <div class="col-md-4">
                            <i class="bi bi-telephone-fill text-success fs-3"></i>
                            <p class="fw-bold mt-2">Hotline</p>
                            <small>0945.783.769</small>
                        </div>
                        <div class="col-md-4">
                            <i class="bi bi-envelope-fill text-primary fs-3"></i>
                            <p class="fw-bold mt-2">Email</p>
                            <small>hotro@androidshop.com</small>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>