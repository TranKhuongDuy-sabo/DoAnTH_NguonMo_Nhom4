<?php
session_start();
require_once 'db_connect.php';

// 1. KIỂM TRA QUYỀN ADMIN
// Nếu chưa đăng nhập hoặc role không phải admin thì chặn lại
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    die('<div class="container mt-5"><div class="alert alert-danger text-center">Bạn không có quyền truy cập trang này! <a href="index.php">Về trang chủ</a></div></div>');
}

// 2. TRUY VẤN DỮ LIỆU THỐNG KÊ (REAL TIME)

// A. Thống kê Tổng doanh thu & Số đơn hàng
$sql_summary = "SELECT 
                    COUNT(id) as total_orders, 
                    SUM(total) as total_revenue 
                FROM ORDERS"; 
                // Nếu muốn chỉ tính đơn thành công thì thêm: WHERE status = 'Delivered'
$stmt_sum = $conn->prepare($sql_summary);
$stmt_sum->execute();
$summary = $stmt_sum->fetch(PDO::FETCH_ASSOC);

// B. Thống kê Khách hàng mới (Đếm tổng user)
$stmt_user = $conn->prepare("SELECT COUNT(id) as total_users FROM USERS WHERE role = 'user'");
$stmt_user->execute();
$total_users = $stmt_user->fetch(PDO::FETCH_ASSOC)['total_users'];

// C. Lấy danh sách sản phẩm Sắp hết hàng (Dưới 10 cái)
$sql_low_stock = "SELECT productname, quantity, image FROM PRODUCTS WHERE quantity < 10 ORDER BY quantity ASC LIMIT 5";
$stmt_low = $conn->prepare($sql_low_stock);
$stmt_low->execute();
$low_stock_products = $stmt_low->fetchAll(PDO::FETCH_ASSOC);

// D. Dữ liệu cho Biểu đồ (Doanh thu 7 ngày gần nhất)
// Lưu ý: Query này nhóm theo ngày
$sql_chart = "SELECT DATE(createdate) as date, SUM(total) as revenue 
              FROM ORDERS 
              GROUP BY DATE(createdate) 
              ORDER BY date DESC 
              LIMIT 7";
$stmt_chart = $conn->prepare($sql_chart);
$stmt_chart->execute();
$chart_data = $stmt_chart->fetchAll(PDO::FETCH_ASSOC);

// Chuẩn bị dữ liệu JSON cho Javascript vẽ biểu đồ
$dates = [];
$revenues = [];
// Đảo ngược mảng để ngày cũ bên trái, ngày mới bên phải
$chart_data = array_reverse($chart_data);
foreach ($chart_data as $row) {
    $dates[] = date('d/m', strtotime($row['date']));
    $revenues[] = (int)$row['revenue'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Trị - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar { min-height: 100vh; background-color: #343a40; }
        .nav-link { color: rgba(255,255,255,.8); margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { color: #fff; background-color: #0d6efd; border-radius: 5px; }
        .card-counter { transition: .3s; }
        .card-counter:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
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
            <li class="nav-item">
                <a href="admin.php" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            </li>
            <li>
                <a href="admin_products.php" class="nav-link"><i class="bi bi-box-seam me-2"></i> Quản lý Sản phẩm</a>
            </li>
            <li>
                <a href="admin_orders.php" class="nav-link"><i class="bi bi-receipt me-2"></i> Quản lý Đơn hàng</a>
            </li>
            <li>
                <a href="admin_customers.php" class="nav-link"><i class="bi bi-people me-2"></i> Khách hàng</a>
            </li>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                <strong>Admin: <?php echo htmlspecialchars($_SESSION['user']['fullname']); ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                <li><a class="dropdown-item" href="index.php">Xem trang chủ</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
            </ul>
        </div>
    </div>

    <div class="flex-grow-1 p-4">
        <h2 class="fw-bold mb-4">Tổng quan kinh doanh</h2>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card card-counter bg-primary text-white mb-3 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-uppercase bg-primary-dark">Tổng Doanh Thu</h6>
                                <h3 class="fw-bold"><?php echo number_format($summary['total_revenue'] ?? 0, 0, ',', '.'); ?> đ</h3>
                            </div>
                            <i class="bi bi-currency-dollar fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-counter bg-success text-white mb-3 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-uppercase">Tổng Đơn Hàng</h6>
                                <h3 class="fw-bold"><?php echo $summary['total_orders'] ?? 0; ?></h3>
                            </div>
                            <i class="bi bi-cart-check fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-counter bg-warning text-dark mb-3 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-uppercase">Khách Hàng</h6>
                                <h3 class="fw-bold"><?php echo $total_users; ?></h3>
                            </div>
                            <i class="bi bi-people-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white fw-bold">
                        <i class="bi bi-graph-up"></i> Doanh thu 7 ngày gần nhất
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart"></canvas>
                        <?php if(empty($dates)): ?>
                            <p class="text-center text-muted mt-5">Chưa có dữ liệu đơn hàng nào.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-5 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white fw-bold text-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i> Sắp hết hàng (Dưới 10)
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Kho</th>
                                    <th>Ảnh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($low_stock_products) > 0): ?>
                                    <?php foreach($low_stock_products as $prod): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($prod['productname']); ?></td>
                                        <td class="fw-bold text-danger"><?php echo $prod['quantity']; ?></td>
                                        <td>
                                            <img src="uploads/<?php echo $prod['image'] ?? 'no-img.jpg'; ?>" width="30">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center text-success py-3">Kho hàng ổn định!</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const ctx = document.getElementById('revenueChart');
    
    // Lấy dữ liệu từ PHP đổ vào Javascript
    const labels = <?php echo json_encode($dates); ?>;
    const data = <?php echo json_encode($revenues); ?>;

    new Chart(ctx, {
        type: 'bar', // Loại biểu đồ: bar (cột), line (đường)
        data: {
            labels: labels,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + ' đ';
                        }
                    }
                }
            }
        }
    });
</script>

</body>
</html>