<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Bắt buộc đăng nhập mới được thanh toán
    exit();
}

if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
$total_money = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_money += $item['price'] * $item['qty'];
}

// Xử lý ĐẶT HÀNG
if (isset($_POST['btn_order'])) {
    $address = $_POST['address'];
    $note = $_POST['note'];
    $user_id = $user['id'];

    // 1. Lưu vào bảng ORDERS
    $sql_order = "INSERT INTO ORDERS (userid, total, address, note, status, createdate) VALUES (?, ?, ?, ?, 'Pending', NOW())";
    $stmt = $conn->prepare($sql_order);
    
    if ($stmt->execute([$user_id, $total_money, $address, $note])) {
        $order_id = $conn->lastInsertId(); // Lấy ID của đơn hàng vừa tạo

        // 2. Lưu chi tiết vào bảng ORDERDETAILS
        $sql_detail = "INSERT INTO ORDERDETAILS (orderid, productid, quantity, price, total) VALUES (?, ?, ?, ?, ?)";
        $stmt_detail = $conn->prepare($sql_detail);

        foreach ($_SESSION['cart'] as $pro_id => $item) {
            $subtotal = $item['price'] * $item['qty'];
            $stmt_detail->execute([$order_id, $pro_id, $item['qty'], $item['price'], $subtotal]);
        }

        // 3. Xóa giỏ hàng và thông báo
        unset($_SESSION['cart']);
        echo "<script>alert('Đặt hàng thành công! Mã đơn: #$order_id'); window.location.href='my_orders.php';</script>";
        exit();
    } else {
        echo "Lỗi hệ thống, vui lòng thử lại.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
    <a href="index.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Hủy thanh toán & Về trang chủ
    </a>
</div>
<div class="container mt-5">
    <h2 class="text-center mb-4">Xác nhận đơn hàng</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="card p-3 mb-3">
                <h4>Sản phẩm đã chọn</h4>
                <ul class="list-group">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['qty']; ?>)
                            <span><?php echo number_format($item['price'] * $item['qty'], 0, ',', '.'); ?> đ</span>
                        </li>
                    <?php endforeach; ?>
                    <li class="list-group-item bg-light fw-bold d-flex justify-content-between">
                        TỔNG TIỀN:
                        <span class="text-danger"><?php echo number_format($total_money, 0, ',', '.'); ?> đ</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-4">
                <h4>Thông tin giao hàng</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label>Người nhận:</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['fullname']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Số điện thoại:</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['numberphone'] ?? ''); ?>" readonly>
                        <small class="text-muted">Để thay đổi SĐT, vui lòng vào <a href="profile.php?redirect=checkout">Cập nhật thông tin</a></small>
                    </div>
                    <div class="mb-3">
                        <label>Địa chỉ nhận hàng (*):</label>
                        <textarea name="address" class="form-control" required rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Ghi chú đơn hàng:</label>
                        <textarea name="note" class="form-control" placeholder="Ví dụ: Giao giờ hành chính..."></textarea>
                    </div>
                    <button type="submit" name="btn_order" class="btn btn-success w-100 py-2">XÁC NHẬN ĐẶT HÀNG</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>