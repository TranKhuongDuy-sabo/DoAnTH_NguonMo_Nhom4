<?php
session_start();
require_once 'db_connect.php';

// --- XỬ LÝ GỬI BÌNH LUẬN ---
if (isset($_POST['btn_submit'])) {
    // Kiểm tra đăng nhập (Nếu chưa đăng nhập thì chặn lại)
    if (!isset($_SESSION['user'])) {
        echo "<script>alert('Vui lòng đăng nhập để bình luận!'); window.location.href='login.php';</script>";
        exit();
    }

    $content = $_POST['content'];
    $rate = $_POST['rate'];
    $product_id_post = $_GET['id'];
    $user_id = $_SESSION['user']['id']; // Lấy ID từ session người dùng thật

    if (!empty($content)) {
        $sql_insert = "INSERT INTO COMMENTS (content, rate, userid, productid) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        if ($stmt_insert->execute([$content, $rate, $user_id, $product_id_post])) {
            header("Location: detail.php?id=" . $product_id_post);
            exit();
        }
    }
}

// Kiểm tra ID sản phẩm
if (!isset($_GET['id'])) {
    die("Không tìm thấy sản phẩm!");
}

$product_id = $_GET['id'];

// 1. Truy vấn thông tin sản phẩm
$sql_product = "SELECT p.*, c.categoryname 
                FROM PRODUCTS p 
                LEFT JOIN CATEGORIES c ON p.categoryid = c.id 
                WHERE p.id = ?";
$stmt = $conn->prepare($sql_product);
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Sản phẩm không tồn tại.");
}

// 2. Truy vấn bình luận
$sql_comments = "SELECT cm.*, u.fullname 
                 FROM COMMENTS cm 
                 LEFT JOIN USERS u ON cm.userid = u.id 
                 WHERE cm.productid = ? 
                 ORDER BY cm.createdate DESC"; // <--- ĐÃ SỬA TÊN CỘT Ở ĐÂY
$stmt_cm = $conn->prepare($sql_comments);
$stmt_cm->execute([$product_id]);
$comments = $stmt_cm->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết - <?php echo htmlspecialchars($product['productname']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <a href="search.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Quay lại tìm kiếm</a>
    <a href="index.php" class="btn btn-secondary mb-3"><i class="bi bi-house-door-fill"></i> Trở về trang chủ</a>
    <div class="card shadow-sm">
        <div class="row g-0">
            <div class="col-md-5 bg-white d-flex align-items-center justify-content-center border-end">
                <img src="uploads/<?php echo $product['id']; ?>.jpg" 
                     class="img-fluid p-4" 
                     alt="<?php echo htmlspecialchars($product['productname']); ?>"
                     style="max-height: 400px;"
                     onerror="this.src='https://via.placeholder.com/400x400?text=No+Image'">
            </div>
            
            <div class="col-md-7">
                <div class="card-body p-4">
                    <span class="badge bg-info text-dark mb-2"><?php echo htmlspecialchars($product['categoryname']); ?></span>
                    <h2 class="card-title fw-bold"><?php echo htmlspecialchars($product['productname']); ?></h2>
                    
                    <h3 class="text-danger my-3"><?php echo number_format($product['price'], 0, ',', '.'); ?> đ</h3>
                    
                    <div class="mb-4">
                        <h5>Cấu hình nổi bật:</h5>
                        <p class="text-muted bg-light p-2 rounded border">
                            <?php echo htmlspecialchars($product['detail']); ?>
                        </p>
                    </div>

                    <div class="mb-4">
                        <h5>Mô tả sản phẩm:</h5>
                        <p><?php echo nl2br(htmlspecialchars($product['description'] ?? 'Đang cập nhật...')); ?></p>
                    </div>

                    <form action="cart.php" method="POST" class="mt-4 mb-4">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['productname']); ?>">
                        <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                        
                        <div class="d-flex gap-3">
                            <button type="submit" name="action" value="add" class="btn btn-outline-primary btn-lg flex-grow-1">
                                <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                            </button>

                            <button type="submit" name="action" value="buynow" class="btn btn-danger btn-lg flex-grow-1">
                                <i class="bi bi-lightning-fill"></i> Đặt hàng ngay
                            </button>
                        </div>
                    </form>
                    <div class="mt-3 text-success">
                        <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($product['guarantee'] ?? 'Bảo hành chính hãng 12 tháng'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 mb-5">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Đánh giá & Bình luận (<?php echo count($comments); ?>)</h5>
                </div>
                
                <div class="card-body border-bottom">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Gửi đánh giá của bạn</label>
                            <div class="d-flex align-items-center mb-2">
                                <span class="me-2">Chọn số sao:</span>
                                <select name="rate" class="form-select w-auto">
                                    <option value="5">5 Sao (Tuyệt vời)</option>
                                    <option value="4">4 Sao (Tốt)</option>
                                    <option value="3">3 Sao (Bình thường)</option>
                                    <option value="2">2 Sao (Kém)</option>
                                    <option value="1">1 Sao (Tệ)</option>
                                </select>
                            </div>
                            <textarea name="content" class="form-control" rows="3" placeholder="Nhập nội dung đánh giá..." required></textarea>
                        </div>
                        <button type="submit" name="btn_submit" class="btn btn-primary">Gửi đánh giá</button>
                    </form>
                </div>
                <div class="card-body">
                    <?php if(count($comments) > 0): ?>
                        <?php foreach($comments as $cmt): ?>
                            <div class="d-flex mb-3 border-bottom pb-3">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <?php echo strtoupper(substr($cmt['fullname'] ?? 'A', 0, 1)); ?>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($cmt['fullname'] ?? 'Người dùng ẩn danh'); ?></h6>
                                    <small class="text-muted"><?php echo $cmt['createdate']; ?></small>
                                    <div class="text-warning mb-1">
                                        <?php 
                                        for($i=1; $i<=5; $i++) {
                                            echo ($i <= $cmt['rate']) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                                        }
                                        ?>
                                    </div>
                                    <p class="mb-0"><?php echo htmlspecialchars($cmt['content']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-muted">Chưa có bình luận nào cho sản phẩm này.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>