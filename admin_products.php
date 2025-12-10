<?php
session_start();
require_once 'db_connect.php';
// Check Admin...
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') { header("Location: index.php"); exit(); }

// Code xóa (giữ nguyên lỗi xóa GET để test luôn)
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $conn->prepare("DELETE FROM PRODUCTS WHERE id=?")->execute([$id]);
    echo "<script>window.location.href='admin_products.php';</script>";
}

// Lấy danh sách
$stmt = $conn->prepare("SELECT p.*, c.categoryname FROM PRODUCTS p LEFT JOIN CATEGORIES c ON p.categoryid = c.id ORDER BY p.id DESC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách (BẢN LỖI HIỂN THỊ)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>.img-thumb { width: 50px; height: 50px; object-fit: contain; }</style>
</head>
<body class="bg-light p-4">

<h2 class="text-danger">DEMO LỖI HIỂN THỊ (XSS & BROKEN HTML)</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm (Lỗi XSS)</th>
                    <th>Giá</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <img src="uploads/<?php echo $row['image']; ?>" class="img-thumb">
                    </td>
                    
                    <td class="fw-bold">
                        <?php echo $row['productname']; ?> 
                    </td>
                    
                    <td class="text-danger"><?php echo number_format($row['price']); ?> đ</td>
                    <td>
                        <a href="admin_products.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>