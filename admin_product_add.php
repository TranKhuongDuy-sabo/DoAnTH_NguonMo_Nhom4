<?php
session_start();
require_once 'db_connect.php';

// 1. CHẶN KHÔNG PHẢI ADMIN
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// 2. XỬ LÝ THÊM SẢN PHẨM
if (isset($_POST['btn_add'])) {
    $name = $_POST['productname'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $category_id = $_POST['categoryid'];
    $detail = $_POST['detail']; // Cấu hình (RAM, ROM...)
    $desc = $_POST['description']; // Mô tả chi tiết

    // --- XỬ LÝ UPLOAD ẢNH ---
    $image_file = $_FILES['image'];
    $filename = ""; // Mặc định rỗng

    if ($image_file['name'] != "") {
        // Tạo tên file mới để tránh trùng (Ví dụ: 1716453_anh.jpg)
        $filename = time() . "_" . $image_file['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . $filename;

        // Di chuyển file từ bộ nhớ tạm vào thư mục uploads
        move_uploaded_file($image_file['tmp_name'], $target_file);
    }
    // -------------------------

    // Câu lệnh INSERT
    $sql = "INSERT INTO PRODUCTS (productname, price, quantity, categoryid, detail, description, image) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$name, $price, $quantity, $category_id, $detail, $desc, $filename])) {
        echo "<script>alert('Thêm sản phẩm thành công!'); window.location.href='admin_products.php';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại!');</script>";
    }
}

// 3. LẤY DANH SÁCH DANH MỤC (Để hiện trong ô chọn)
$stmt_cat = $conn->prepare("SELECT * FROM CATEGORIES");
$stmt_cat->execute();
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sản phẩm mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Thêm Sản Phẩm Mới</h4>
                </div>
                <div class="card-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên sản phẩm</label>
                            <input type="text" name="productname" class="form-control" required placeholder="VD: Samsung Galaxy S24...">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giá bán (VNĐ)</label>
                                <input type="number" name="price" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Số lượng trong kho</label>
                                <input type="number" name="quantity" class="form-control" value="10" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hãng sản xuất (Danh mục)</label>
                            <select name="categoryid" class="form-select" required>
                                <option value="">-- Chọn hãng --</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>">
                                        <?php echo htmlspecialchars($cat['categoryname']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hình ảnh đại diện</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Cấu hình tóm tắt</label>
                            <input type="text" name="detail" class="form-control" placeholder="VD: RAM: 8GB, Bộ nhớ: 256GB">
                            <div class="form-text">Nhập đúng format "RAM: ... Bộ nhớ: ..." để bộ lọc tìm kiếm hoạt động tốt.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả chi tiết</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Bài viết đánh giá sản phẩm..."></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="admin_products.php" class="btn btn-secondary">Quay lại</a>
                            <button type="submit" name="btn_add" class="btn btn-success px-5">Lưu sản phẩm</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>