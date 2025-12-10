<?php
session_start();
require_once 'db_connect.php';

// Khởi tạo các biến để giữ lại giá trị người dùng đã chọn (cho Form không bị reset)
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$filter_ram = isset($_GET['ram']) ? $_GET['ram'] : '';
$filter_rom = isset($_GET['rom']) ? $_GET['rom'] : '';
$filter_price = isset($_GET['price']) ? $_GET['price'] : '';

// --- XÂY DỰNG CÂU TRUY VẤN SQL ĐỘNG ---
$sql = "SELECT * FROM PRODUCTS WHERE 1=1"; // 1=1 giúp dễ dàng nối chuỗi AND phía sau
$params = [];


if (!empty($keyword)) {
    $sql .= " AND productname LIKE ?";
    $params[] = "%" . $keyword . "%";
}

// 2. Lọc theo RAM (Tìm chuỗi "RAM: 8GB" trong cột detail)
if (!empty($filter_ram)) {
    $sql .= " AND detail LIKE ?";
    $params[] = "%RAM: " . $filter_ram . "%"; 
}

// 3. Lọc theo Bộ nhớ trong (Tìm chuỗi "Bộ nhớ: 128GB" trong cột detail)
if (!empty($filter_rom)) {
    $sql .= " AND detail LIKE ?";
    $params[] = "%Bộ nhớ: " . $filter_rom . "%";
}

// 4. Lọc theo Giá
if (!empty($filter_price)) {
    if ($filter_price == 'duoi-5tr') {
        $sql .= " AND price > 5000000";
    } elseif ($filter_price == '5tr-10tr') {
        $sql .= " AND price BETWEEN 5000000 AND 10000000";
    } elseif ($filter_price == 'tren-10tr') {
        $sql .= " AND price > 10000000";
    }
}

// Sắp xếp mới nhất lên đầu
$sql .= " ORDER BY id DESC";

// Thực thi truy vấn
try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tìm kiếm & Lọc sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .product-card img { height: 200px; object-fit: contain; padding: 10px; }
        .filter-box { background-color: #f8f9fa; border-radius: 10px; padding: 20px; }
    </style>
</head>
<body>

<div class="bg-dark py-3 mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <h3 class="text-white m-0">TÌM KIẾM SẢN PHẨM</h3>
        <a href="index.php" class="btn btn-outline-light">
            <i class="bi bi-house-door-fill"></i> Về Trang chủ
        </a>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="filter-box shadow-sm">
                <h5 class="mb-3"><i class="bi bi-funnel-fill"></i> Bộ lọc tìm kiếm</h5>
                <form action="search.php" method="GET">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên sản phẩm</label>
                        <input type="text" name="keyword" class="form-control" 
                               value="<?php echo htmlspecialchars($keyword); ?>" 
                               placeholder="Nhập tên (VD: Samsung...)">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Dung lượng RAM</label>
                        <select name="ram" class="form-select">
                            <option value="">-- Tất cả --</option>
                            <option value="4GB" <?php if($filter_ram == '4GB') echo 'selected'; ?>>4GB</option>
                            <option value="6GB" <?php if($filter_ram == '6GB') echo 'selected'; ?>>6GB</option>
                            <option value="8GB" <?php if($filter_ram == '8GB') echo 'selected'; ?>>8GB</option>
                            <option value="12GB" <?php if($filter_ram == '12GB') echo 'selected'; ?>>12GB</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Bộ nhớ trong</label>
                        <select name="rom" class="form-select">
                            <option value="">-- Tất cả --</option>
                            <option value="64GB" <?php if($filter_rom == '64GB') echo 'selected'; ?>>64GB</option>
                            <option value="128GB" <?php if($filter_rom == '128GB') echo 'selected'; ?>>128GB</option>
                            <option value="256GB" <?php if($filter_rom == '256GB') echo 'selected'; ?>>256GB</option>
                            <option value="512GB" <?php if($filter_rom == '512GB') echo 'selected'; ?>>512GB</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Khoảng giá</label>
                        <select name="price" class="form-select">
                            <option value="">-- Tất cả mức giá --</option>
                            <option value="duoi-5tr" <?php if($filter_price == 'duoi-5tr') echo 'selected'; ?>>Dưới 5 triệu</option>
                            <option value="5tr-10tr" <?php if($filter_price == '5tr-10tr') echo 'selected'; ?>>Từ 5 - 10 triệu</option>
                            <option value="tren-10tr" <?php if($filter_price == 'tren-10tr') echo 'selected'; ?>>Trên 10 triệu</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Áp dụng bộ lọc
                        </button>
                        <a href="search.php" class="btn btn-outline-secondary">Xóa lọc</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-9">
            <?php if (count($products) > 0): ?>
                <div class="alert alert-info py-2">
                    Tìm thấy <strong><?php echo count($products); ?></strong> sản phẩm phù hợp.
                </div>
                
                <div class="row">
                    <?php foreach ($products as $row): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm product-card">
                                <img src="uploads/<?php echo $row['id']; ?>.jpg" class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($row['productname']); ?>"
                                     onerror="this.src='https://via.placeholder.com/200?text=No+Image'">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-truncate"><?php echo htmlspecialchars($row['productname']); ?></h5>
                                    
                                    <p class="card-text text-danger fw-bold mb-1">
                                        <?php echo number_format($row['price'], 0, ',', '.'); ?> đ
                                    </p>
                                    
                                    <p class="card-text small text-muted mb-3 flex-grow-1">
                                        <?php echo substr(htmlspecialchars($row['detail']), 0, 60) . '...'; ?>
                                    </p>
                                    
                                    <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary w-100 mt-auto">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-emoji-frown display-1 text-muted"></i>
                    <h3 class="mt-3 text-muted">Không tìm thấy sản phẩm nào!</h3>
                    <p>Hãy thử thay đổi tiêu chí lọc hoặc tìm từ khóa khác.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>