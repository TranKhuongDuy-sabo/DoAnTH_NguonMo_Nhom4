<?php
session_start();
require_once 'db_connect.php';

// Lấy 8 sản phẩm mới nhất
$stmt = $conn->prepare("SELECT * FROM PRODUCTS ORDER BY id DESC ");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Android Shop - Trang chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .hero-banner {
            background: linear-gradient(to right, #007bff, #6610f2);
            color: white;
            padding: 60px 0;
            text-align: center;
        }
        .card-img-top { height: 200px; object-fit: contain; padding: 15px;}
        
        /* Hiệu ứng fade-in cho sản phẩm khi bấm xem thêm */
        .fade-in {
            animation: fadeIn 0.5s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">ANDROID SHOP</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link active" href="index.php">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="search.php">Tìm kiếm</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Liên hệ</a></li>
            </ul>
            
            <ul class="navbar-nav">
                <?php if(isset($_SESSION['user'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-warning" href="#" role="button" data-bs-toggle="dropdown">
                            Xin chào, <?php echo htmlspecialchars($_SESSION['user']['fullname']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            
                            <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 'admin'): ?>
                                <li>
                                    <a class="dropdown-item text-primary fw-bold" href="admin.php">
                                        <i class="bi bi-speedometer2"></i> Trang Quản Trị
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="profile.php">Quản lý tài khoản</a></li>
                            <li><a class="dropdown-item" href="my_orders.php">Lịch sử đơn hàng</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Đăng xuất</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Đăng nhập</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-primary text-white ms-2" href="register.php">Đăng ký</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="hero-banner mb-5">
    <div class="container">
        <h1>Chào mừng đến với Android Shop</h1>
        <p class="lead">Nơi cung cấp điện thoại chính hãng giá tốt nhất</p>
        <a href="search.php" class="btn btn-light btn-lg mt-3">Tìm điện thoại ngay</a>
    </div>
</div>

<div class="container mb-5">
    <h3 class="border-bottom pb-2 mb-4 text-primary">Sản phẩm mới nhất</h3>
   <div class="row" id="product-list">
    <?php 
    $count = 0; 
    // Kiểm tra biến $products có tồn tại không để tránh lỗi nếu database trống
    if (!empty($products)) {
        foreach ($products as $row): 
            $count++;
            $hiddenClass = ($count > 8) ? 'product-hidden d-none' : '';
        ?>
            <div class="col-md-3 mb-4 <?php echo $hiddenClass; ?>">
                <div class="card h-100 shadow-sm">
                    <img src="uploads/<?php echo $row['id']; ?>.jpg" class="card-img-top" onerror="this.src='https://via.placeholder.com/200'">
                    <div class="card-body">
                        <h5 class="card-title text-truncate"><?php echo htmlspecialchars($row['productname']); ?></h5>
                        <p class="text-danger fw-bold"><?php echo number_format($row['price'], 0, ',', '.'); ?> đ</p>
                        <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary w-100">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        <?php endforeach; 
    } else {
        echo "<p class='text-center'>Chưa có sản phẩm nào.</p>";
    }
    ?>
    </div>
</div>

<?php if(isset($products) && count($products) > 8): ?>
    <div class="text-center mt-3 mb-5">
        <button id="btn-load-more" class="btn btn-outline-secondary px-5 py-2 rounded-pill">
            Xem thêm <?php echo count($products) - 8; ?> sản phẩm <i class="bi bi-chevron-down"></i>
        </button>
    </div>
<?php endif; ?>

<footer class="bg-dark text-white text-center py-3 mt-auto w-100">
    &copy; 2025 Android Shop Project.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const loadMoreBtn = document.getElementById('btn-load-more');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            const hiddenProducts = document.querySelectorAll('.product-hidden');
            hiddenProducts.forEach(product => {
                product.classList.remove('d-none');
                product.classList.add('fade-in'); 
            });
            this.style.display = 'none';
        });
    }
</script>
</body>
</html>