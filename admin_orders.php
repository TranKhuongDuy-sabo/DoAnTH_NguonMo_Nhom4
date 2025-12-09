<?php
session_start();
require_once 'db_connect.php';

// 1. CHẶN KHÔNG PHẢI ADMIN
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// 2. XỬ LÝ CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG
if (isset($_POST['btn_update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    $sql_update = "UPDATE ORDERS SET status = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    if ($stmt_update->execute([$new_status, $order_id])) {
        echo "<script>alert('Cập nhật trạng thái đơn #$order_id thành công!'); window.location.href='admin_orders.php';</script>";
    } else {
        echo "<script>alert('Lỗi cập nhật!');</script>";
    }
}

// 3. LẤY DANH SÁCH ĐƠN HÀNG (KÈM TÊN KHÁCH HÀNG)
$sql = "SELECT o.*, u.fullname 
        FROM ORDERS o 
        LEFT JOIN USERS u ON o.userid = u.id 
        ORDER BY o.createdate DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đơn hàng</title>
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
            <li><a href="admin_orders.php" class="nav-link active bg-primary"><i class="bi bi-receipt me-2"></i> Quản lý Đơn hàng</a></li>
            <li><a href="admin_customers.php" class="nav-link"><i class="bi bi-people me-2"></i> Khách hàng</a></li>
        </ul>
    </div>

    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Quản lý Đơn hàng</h2>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $row): ?>
                        <tr>
                            <td><strong>#<?php echo $row['id']; ?></strong></td>
                            <td>
                                <?php echo htmlspecialchars($row['fullname'] ?? 'Khách vãng lai'); ?><br>
                                <small class="text-muted"><?php echo htmlspecialchars($row['address']); ?></small>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['createdate'])); ?></td>
                            <td class="text-danger fw-bold"><?php echo number_format($row['total'], 0, ',', '.'); ?> đ</td>
                            <td>
                                <form method="POST" class="d-flex align-items-center">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <select name="status" class="form-select form-select-sm me-2" style="width: 130px;" onchange="this.form.submit()"> <option value="Pending" <?php if($row['status']=='Pending') echo 'selected'; ?> class="text-warning fw-bold">Chờ xử lý</option>
                                        <option value="Delivered" <?php if($row['status']=='Delivered') echo 'selected'; ?> class="text-success fw-bold">Đã giao</option>
                                        <option value="Cancelled" <?php if($row['status']=='Cancelled') echo 'selected'; ?> class="text-danger fw-bold">Đã hủy</option>
                                    </select>
                                    <input type="hidden" name="btn_update_status" value="1">
                                </form>
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm text-white" type="button" data-bs-toggle="collapse" data-bs-target="#detail-<?php echo $row['id']; ?>">
                                    <i class="bi bi-eye"></i> Xem
                                </button>
                            </td>
                        </tr>
                        
                        <tr>
                            <td colspan="6" class="p-0 border-0">
                                <div class="collapse bg-white border-bottom" id="detail-<?php echo $row['id']; ?>">
                                    <div class="p-3">
                                        <h6 class="fw-bold text-primary">Chi tiết sản phẩm:</h6>
                                        <ul class="list-group list-group-flush">
                                            <?php 
                                            // Truy vấn lấy chi tiết từng sản phẩm trong đơn này
                                            $sql_details = "SELECT od.*, p.productname, p.image 
                                                            FROM ORDERDETAILS od 
                                                            JOIN PRODUCTS p ON od.productid = p.id 
                                                            WHERE od.orderid = ?";
                                            $stmt_d = $conn->prepare($sql_details);
                                            $stmt_d->execute([$row['id']]);
                                            $details = $stmt_d->fetchAll(PDO::FETCH_ASSOC);
                                            
                                            foreach($details as $d):
                                            ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <img src="uploads/<?php echo $d['image'] ?? 'no-img.jpg'; ?>" width="40" height="40" class="me-2 rounded border">
                                                        <span><?php echo htmlspecialchars($d['productname']); ?></span>
                                                    </div>
                                                    <span>
                                                        x<?php echo $d['quantity']; ?> 
                                                        <strong class="ms-3"><?php echo number_format($d['price'],0,',','.'); ?> đ</strong>
                                                    </span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <div class="mt-2 text-muted fst-italic">
                                            Ghi chú của khách: "<?php echo htmlspecialchars($row['note'] ?? 'Không có'); ?>"
                                        </div>
                                    </div>
                                </div>
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