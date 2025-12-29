<?php
session_start();
require_once 'db_connect.php';

// 1. CHẶN KHÔNG PHẢI ADMIN
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// 2. XỬ LÝ XÓA SẢN PHẨM
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    // Xóa sản phẩm (Lưu ý: Nếu sản phẩm đã có trong đơn hàng thì có thể lỗi do khóa ngoại, cần xử lý kỹ hơn ở thực tế)
    $stmt = $conn->prepare("DELETE FROM PRODUCTS WHERE id = ?");
    if($stmt->execute([$id])){
        echo "<script>alert('Đã xóa thành công!'); window.location.href='admin_products.php';</script>";
    } else {
        echo "<script>alert('Lỗi: Không thể xóa sản phẩm này (có thể do đã có đơn hàng)!');</script>";
    }
}

// 3. LẤY DANH SÁCH SẢN PHẨM
$stmt = $conn->prepare("SELECT p.*, c.categoryname FROM PRODUCTS p LEFT JOIN CATEGORIES c ON p.categoryid = c.id ORDER BY p.id DESC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .sidebar { min-height: 100vh; background-color: #343a40; }
        .nav-link { color: rgba(255,255,255,.8); margin-bottom: 5px; }
        .nav-link:hover { color: #fff; background-color: #0d6efd; border-radius: 5px; }
        .img-thumb { width: 50px; height: 50px; object-fit: contain; background: #fff; border: 1px solid #ddd; }
    </style>
</head>
<body class="bg-light">

<div class="d-flex">
    <div class="sidebar p-3 d-flex flex-column text-white" style="width: 260px;">
        <a href="index.php" class="text-white text-decoration-none mb-4">
            <h4 class="fw-bold"><i class="bi bi-phone"></i> ANDROID SHOP</h4>
        </a>
        <hr>
        <ul class="nav flex-column mb-auto">
            <li><a href="admin.php" class="nav-link"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
            <li><a href="admin_products.php" class="nav-link active bg-primary"><i class="bi bi-box-seam me-2"></i> Quản lý Sản phẩm</a></li>
            <li><a href="admin_orders.php" class="nav-link"><i class="bi bi-receipt me-2"></i> Quản lý Đơn hàng</a></li>
            <li><a href="admin_customers.php" class="nav-link"><i class="bi bi-people me-2"></i> Khách hàng</a></li>
        </ul>
    </div>

    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Danh sách sản phẩm</h2>
            <a href="admin_product_add.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Thêm mới</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Kho</th>
                            <th>Hãng</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $row): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td>
                                <img src="uploads/<?php echo $row['image'] ?? 'no-img.jpg'; ?>" class="img-thumb">
                            </td>
                            <td class="fw-bold"><?php echo htmlspecialchars($row['productname']); ?></td>
                            <td class="text-danger"><?php echo number_format($row['price'], 0, ',', '.'); ?> đ</td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars( $row['categoryname']); ?></span></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                                <a href="admin_products.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa không?');">
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>