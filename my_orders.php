<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Lấy danh sách đơn hàng của user này
$sql = "SELECT * FROM ORDERS WHERE userid = ? ORDER BY createdate DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý Hủy đơn hàng
if (isset($_GET['cancel_id'])) {
    $cancel_id = $_GET['cancel_id'];
    // Chỉ cho hủy nếu trạng thái là Pending
    $sql_cancel = "UPDATE ORDERS SET status = 'Cancelled' WHERE id = ? AND userid = ? AND status = 'Pending'";
    $stmt_cancel = $conn->prepare($sql_cancel);
    if($stmt_cancel->execute([$cancel_id, $user_id])) {
        header("Location: my_orders.php");
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">ANDROID SHOP</a>
            <div class="d-flex">
                <a href="profile.php" class="btn btn-outline-light me-2">Thông tin</a>
                <a href="logout.php" class="btn btn-danger">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-4">Lịch sử đặt hàng</h2>
        
        <?php if(count($orders) == 0): ?>
            <p>Bạn chưa có đơn hàng nào.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover bg-white shadow-sm">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th>Địa chỉ</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo $order['createdate']; ?></td>
                            <td><?php echo htmlspecialchars($order['address']); ?></td>
                            <td class="text-danger fw-bold"><?php echo number_format($order['total'], 0, ',', '.'); ?> đ</td>
                            <td>
                                <?php 
                                    $status = $order['status'];
                                    $color = 'secondary';
                                    if($status == 'Pending') $color = 'warning';
                                    if($status == 'Cancelled') $color = 'danger';
                                    if($status == 'Delivered') $color = 'success';
                                ?>
                                <span class="badge bg-<?php echo $color; ?>"><?php echo $status; ?></span>
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm text-white" type="button" data-bs-toggle="collapse" data-bs-target="#detail-<?php echo $order['id']; ?>">
                                    Chi tiết
                                </button>
                                
                                <?php if($status == 'Pending'): ?>
                                    <a href="my_orders.php?cancel_id=<?php echo $order['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bạn chắc chắn muốn hủy đơn này?');">Hủy</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" class="p-0 border-0">
                                <div class="collapse p-3 bg-light" id="detail-<?php echo $order['id']; ?>">
                                    <h6>Chi tiết sản phẩm:</h6>
                                    <ul>
                                        <?php 
                                        // Truy vấn lấy chi tiết từng sản phẩm trong đơn
                                        $sql_d = "SELECT od.*, p.productname 
                                                  FROM ORDERDETAILS od 
                                                  JOIN PRODUCTS p ON od.productid = p.id 
                                                  WHERE od.orderid = ?";
                                        $stmt_d = $conn->prepare($sql_d);
                                        $stmt_d->execute([$order['id']]);
                                        $details = $stmt_d->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        foreach($details as $d):
                                        ?>
                                            <li>
                                                <?php echo htmlspecialchars($d['productname']); ?> 
                                                - SL: <?php echo $d['quantity']; ?> 
                                                - Giá: <?php echo number_format($d['price'],0,',','.'); ?> đ
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>