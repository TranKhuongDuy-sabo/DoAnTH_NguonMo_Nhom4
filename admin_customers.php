<?php
session_start();
require_once 'db_connect.php';

// 1. CHẶN KHÔNG PHẢI ADMIN
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// 2. XỬ LÝ KHÓA / MỞ KHÓA TÀI KHOẢN
if (isset($_GET['toggle_id'])) {
    $id = $_GET['toggle_id'];
    $current_status = $_GET['status'];
    
    // Nếu đang là 1 (Active) thì đổi thành 0 (Blocked) và ngược lại
    $new_status = ($current_status == 1) ? 0 : 1;
    
    $stmt = $conn->prepare("UPDATE USERS SET status = ? WHERE id = ?");
    if($stmt->execute([$new_status, $id])) {
        header("Location: admin_customers.php"); // Load lại trang
        exit();
    }
}

// 3. LẤY DANH SÁCH KHÁCH HÀNG (Chỉ lấy role='user', không lấy admin)
$stmt = $conn->prepare("SELECT * FROM USERS WHERE role = 'user' ORDER BY id DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Khách hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .sidebar { min-height: 100vh; background-color: #343a40; }
        .nav-link { color: rgba(255,255,255,.8); margin-bottom: 5px; }
        .nav-link:hover { color: #fff; background-color: #0d6efd; border-radius: 5px; }
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
            <li><a href="admin_products.php" class="nav-link"><i class="bi bi-box-seam me-2"></i> Quản lý Sản phẩm</a></li>
            <li><a href="admin_orders.php" class="nav-link"><i class="bi bi-receipt me-2"></i> Quản lý Đơn hàng</a></li>
            <li><a href="admin_customers.php" class="nav-link active bg-primary"><i class="bi bi-people me-2"></i> Khách hàng</a></li>
        </ul>
    </div>

    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Danh sách Khách hàng</h2>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Họ tên</th>
                            <th>Thông tin liên hệ</th>
                            <th>Ngày tham gia</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $row): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($row['fullname']); ?></div>
                                <small class="text-muted">Username: <?php echo htmlspecialchars($row['username']); ?></small>
                            </td>
                            <td>
                                <div><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($row['email']); ?></div>
                                <div><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($row['numberphone'] ?? 'Chưa có SĐT'); ?></div>
                                <div class="small text-muted"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($row['address'] ?? 'Chưa có địa chỉ'); ?></div>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($row['createdate'])); ?></td>
                            <td>
                                <?php if($row['status'] == 1): ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Đã khóa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['status'] == 1): ?>
                                    <a href="admin_customers.php?toggle_id=<?php echo $row['id']; ?>&status=1" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Bạn muốn khóa tài khoản này? Khách sẽ không thể đăng nhập được nữa.');">
                                        <i class="bi bi-lock-fill"></i> Khóa
                                    </a>
                                <?php else: ?>
                                    <a href="admin_customers.php?toggle_id=<?php echo $row['id']; ?>&status=0" 
                                       class="btn btn-sm btn-outline-success"
                                       onclick="return confirm('Mở khóa cho tài khoản này?');">
                                        <i class="bi bi-unlock-fill"></i> Mở khóa
                                    </a>
                                <?php endif; ?>
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