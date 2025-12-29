<?php
session_start();

// --- PHẦN 1: XỬ LÝ LOGIC (THÊM/SỬA/XÓA) ---

// Xử lý khi người dùng bấm nút từ trang Chi tiết
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $id = $_POST['product_id'];
    $name = $_POST['product_name'];
    $price = $_POST['product_price'];
    $action = $_POST['action']; // Lấy xem khách bấm nút nào (add hay buynow)

    // Khởi tạo giỏ hàng nếu chưa có
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Logic thêm sản phẩm: Nếu có rồi thì tăng số lượng, chưa có thì thêm mới
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['qty'] = 1;
    } else {
        $_SESSION['cart'][$id] = [
            'name' => $name,
            'price' => $price,
            'qty' => 1
        ];
    }

    // QUAN TRỌNG: Điều hướng dựa trên nút bấm
    if ($action == 'buynow') {
        // Nếu bấm Đặt hàng -> Chuyển thẳng sang thanh toán
        header("Location: checkout.php");
        exit();
    } else {
        // Nếu bấm Thêm vào giỏ -> Ở lại trang Quản lý giỏ hàng
        header("Location: cart.php");
        exit();
    }
}

// Xử lý nút Xóa sản phẩm khỏi giỏ
if (isset($_GET['remove'])) {
    $id_remove = $_GET['remove'];
    unset($_SESSION['cart'][$id_remove]);
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Giỏ hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-light bg-white shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-arrow-left"></i> Tiếp tục mua sắm</a>
        <span class="navbar-text fw-bold text-primary">GIỎ HÀNG CỦA BẠN</span>
    </div>
</nav>

<div class="container">
    <?php if (empty($_SESSION['cart'])): ?>
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h4 class="mt-3">Giỏ hàng đang trống</h4>
            <a href="index.php" class="btn btn-primary mt-3">Quay lại trang chủ</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold">Sản phẩm đã chọn</div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Giá</th>
                                    <th>SL</th>
                                    <th>Tổng</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_bill = 0;
                                foreach ($_SESSION['cart'] as $id => $item): 
                                    $subtotal = $item['price'] * $item['qty'];
                                    $total_bill += $subtotal;
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></div>
                                    </td>
                                    <td><?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo $item['qty']; ?></span>
                                    </td>
                                    <td class="fw-bold text-primary">
                                        <?php echo number_format($subtotal, 0, ',', '.'); ?> đ
                                    </td>
                                    <td>
                                        <a href="cart.php?remove=<?php echo $id; ?>" class="text-danger" onclick="return confirm('Xóa sản phẩm này?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title d-flex justify-content-between">
                            <span>Tạm tính:</span>
                            <span><?php echo number_format($total_bill, 0, ',', '.'); ?> đ</span>
                        </h5>
                        <hr>
                        <h4 class="d-flex justify-content-between text-danger fw-bold mb-4">
                            <span>Tổng cộng:</span>
                            <span><?php echo number_format($total_bill, 0, ',', '.'); ?> đ</span>
                        </h4>
                        
                        <a href="checkout.php" class="btn btn-success w-100 py-2 fw-bold text-uppercase">
                            Tiến hành Thanh toán <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>