-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th10 28, 2025 lúc 09:30 AM
-- Phiên bản máy phục vụ: 9.1.0
-- Phiên bản PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `android_shop`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

DROP TABLE IF EXISTS `carts`;
CREATE TABLE IF NOT EXISTS `carts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quantity` int NOT NULL,
  `userid` int DEFAULT NULL,
  `productid` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `productid` (`productid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `categoryname` varchar(255) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `categoryname`, `description`) VALUES
(1, 'Samsung', NULL),
(2, 'Xiaomi', NULL),
(3, 'Oppo', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content` text,
  `rate` tinyint DEFAULT NULL,
  `createdate` datetime DEFAULT CURRENT_TIMESTAMP,
  `userid` int DEFAULT NULL,
  `productid` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `productid` (`productid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `emails`
--

DROP TABLE IF EXISTS `emails`;
CREATE TABLE IF NOT EXISTS `emails` (
  `id` int NOT NULL AUTO_INCREMENT,
  `message` text,
  `createdate` datetime DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `userid` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `emails`
--

INSERT INTO `emails` (`id`, `message`, `createdate`, `name`, `email`, `userid`) VALUES
(1, '12345 test', '2025-11-28 15:50:02', 'nguyen xuan duy', 'nguyenduyen2572@gmail.com', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE IF NOT EXISTS `images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `description` text,
  `productid` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `productid` (`productid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orderdetails`
--

DROP TABLE IF EXISTS `orderdetails`;
CREATE TABLE IF NOT EXISTS `orderdetails` (
  `orderid` int NOT NULL,
  `productid` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`orderid`,`productid`),
  KEY `productid` (`productid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userid` int DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `address` text,
  `note` text,
  `status` varchar(50) DEFAULT 'Pending',
  `process` varchar(50) DEFAULT NULL,
  `createdate` datetime DEFAULT CURRENT_TIMESTAMP,
  `promotionid` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `promotionid` (`promotionid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `userid`, `total`, `address`, `note`, `status`, `process`, `createdate`, `promotionid`) VALUES
(1, 2, 15990000.00, 'C5/15E Phạm Hùng', '', 'Cancelled', NULL, '2025-11-28 15:15:02', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `productname` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `detail` text,
  `description` text,
  `discount` decimal(5,2) DEFAULT '0.00',
  `status` tinyint DEFAULT '1',
  `guarantee` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `categoryid` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoryid` (`categoryid`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `productname`, `price`, `quantity`, `detail`, `description`, `discount`, `status`, `guarantee`, `image`, `categoryid`) VALUES
(1, 'Samsung Galaxy S23', 15000000.00, 10, 'Màn hình 6.1 inch, RAM: 8GB, Bộ nhớ: 128GB, Camera 50MP', NULL, 0.00, 1, NULL, '1.jpg', 1),
(2, 'Xiaomi Redmi Note 12', 4500000.00, 25, 'Màn hình 6.67 inch, RAM: 4GB, Bộ nhớ: 64GB, Pin 5000mAh', NULL, 0.00, 1, NULL, '2.jpg', 2),
(3, 'Oppo Find X6 Pro', 20000000.00, 5, 'Màn hình AMOLED 6.8 inch, RAM: 12GB, Bộ nhớ: 256GB, Sạc nhanh 100W', NULL, 0.00, 1, NULL, '3.jpg', 3),
(4, 'Samsung A54', 7900000.00, 30, 'Màn hình 6.4 inch, RAM: 8GB, Bộ nhớ: 128GB, Chống nước IP67', NULL, 0.00, 1, NULL, '4.jpg', 1),
(5, 'Samsung Galaxy Z Flip5', 23990000.00, 15, 'Màn hình gập 6.7 inch, RAM: 8GB, Bộ nhớ: 256GB, Chip Snapdragon 8 Gen 2', NULL, 0.00, 1, NULL, '5.jpg', 1),
(6, 'Xiaomi 13T Pro', 15990000.00, 20, 'Màn hình 144Hz, RAM: 12GB, Bộ nhớ: 512GB, Camera Leica chuyên nghiệp', NULL, 0.00, 1, NULL, '6.jpg', 2),
(7, 'Oppo Reno10 5G', 9990000.00, 30, 'Màn hình cong 3D, RAM: 8GB, Bộ nhớ: 256GB, Camera chân dung 32MP', NULL, 0.00, 1, NULL, '7.jpg', 3),
(8, 'Samsung Galaxy S23 Ultra', 26990000.00, 10, 'Màn hình 6.8 inch, RAM: 12GB, Bộ nhớ: 512GB, Camera 200MP zoom 100x', NULL, 0.00, 1, NULL, '8.jpg', 1),
(9, 'Xiaomi Redmi Note 13', 4890000.00, 50, 'Màn hình AMOLED, RAM: 6GB, Bộ nhớ: 128GB, Pin 5000mAh', NULL, 0.00, 1, NULL, '9.jpg', 2),
(10, 'Oppo Find N3 Flip', 22990000.00, 8, 'Thiết kế gập vỏ sò, Camera Hasselblad, RAM: 12GB, Bộ nhớ: 256GB', NULL, 0.00, 1, NULL, '10.jpg', 3),
(11, 'Samsung Galaxy A05s', 3590000.00, 40, 'Giá rẻ, Màn hình 6.7 inch, RAM: 4GB, Bộ nhớ: 128GB, Pin trâu cả ngày', NULL, 0.00, 1, NULL, '11.jpg', 1),
(12, 'Xiaomi Poco F5 Pro', 12990000.00, 15, 'Cấu hình khủng, Chip Snapdragon 8+ Gen 1, RAM: 12GB, Bộ nhớ: 256GB', NULL, 0.00, 1, NULL, '12.jpg', 2),
(13, 'Oppo A78', 6490000.00, 25, 'Thiết kế kim cương, RAM: 8GB, Bộ nhớ: 256GB, Sạc siêu nhanh 67W', NULL, 0.00, 1, NULL, '13.jpg', 3),
(14, 'Samsung Galaxy M34 5G', 7690000.00, 20, 'Pin khủng 6000mAh, RAM: 8GB, Bộ nhớ: 128GB, Màn hình Super AMOLED', NULL, 0.00, 1, NULL, '14.jpg', 1),
(15, 'Samsung Galaxy S24', 22990000.00, 20, 'Màn hình 6.2 inch, RAM: 8GB, Bộ nhớ: 256GB, AI tích hợp', NULL, 0.00, 1, NULL, '15.jpg', 1),
(16, 'Xiaomi Redmi Note 13 Pro', 7290000.00, 30, 'Camera 200MP, RAM: 8GB, Bộ nhớ: 128GB, Sạc nhanh 67W', NULL, 0.00, 1, NULL, '16.jpg', 2),
(17, 'Oppo Reno11 F 5G', 8990000.00, 15, 'Thiết kế vân đá, RAM: 8GB, Bộ nhớ: 256GB, Chống nước IP65', NULL, 0.00, 1, NULL, '17.jpg', 3),
(18, 'Samsung Galaxy S24 Ultra', 33990000.00, 10, 'Khung Titan, RAM: 12GB, Bộ nhớ: 512GB, Snapdragon 8 Gen 3', NULL, 0.00, 1, NULL, '18.jpg', 1),
(19, 'Xiaomi 14', 22990000.00, 12, 'Camera Leica, RAM: 12GB, Bộ nhớ: 256GB, Nhỏ gọn mạnh mẽ', NULL, 0.00, 1, NULL, '19.jpg', 2),
(20, 'Oppo Find N3', 41990000.00, 5, 'Gập quyển sách, RAM: 16GB, Bộ nhớ: 512GB, Camera nhiếp ảnh', NULL, 0.00, 1, NULL, '20.jpg', 3),
(21, 'Samsung Galaxy A34 5G', 7490000.00, 25, 'Thiết kế tối giản, RAM: 8GB, Bộ nhớ: 128GB, Kháng nước IP67', NULL, 0.00, 1, NULL, '21.jpg', 1),
(22, 'Xiaomi Poco X6 Pro', 8990000.00, 20, 'Hiệu năng gaming, RAM: 8GB, Bộ nhớ: 256GB, Dimensity 8300', NULL, 0.00, 1, NULL, '22.jpg', 2),
(23, 'Oppo A58', 4690000.00, 40, 'Màn hình lớn, RAM: 6GB, Bộ nhớ: 128GB, Loa kép âm thanh nổi', NULL, 0.00, 1, NULL, '23.jpg', 3),
(24, 'Samsung Galaxy A25 5G', 6590000.00, 30, 'Màn hình Super AMOLED, RAM: 6GB, Bộ nhớ: 128GB, Camera chống rung', NULL, 0.00, 1, NULL, '24.jpg', 1),
(25, 'Xiaomi Redmi 13C', 3090000.00, 50, 'Giá rẻ quốc dân, RAM: 4GB, Bộ nhớ: 128GB, Màn hình 90Hz', NULL, 0.00, 1, NULL, '25.jpg', 2),
(26, 'Oppo A18', 3290000.00, 45, 'Thiết kế Glow, RAM: 4GB, Bộ nhớ: 128GB, Pin bền bỉ', NULL, 0.00, 1, NULL, '26.jpg', 3),
(27, 'Samsung Galaxy M54 5G', 10490000.00, 15, 'Pin khủng 6000mAh, RAM: 8GB, Bộ nhớ: 256GB, Màn hình to', NULL, 0.00, 1, NULL, '27.jpg', 1),
(28, 'Xiaomi 13 Lite', 8990000.00, 18, 'Thiết kế siêu mỏng, RAM: 8GB, Bộ nhớ: 256GB, Camera selfie kép', NULL, 0.00, 1, NULL, '28.jpg', 2),
(29, 'Oppo Reno8 T 5G', 8490000.00, 20, 'Màn hình cong 120Hz, RAM: 8GB, Bộ nhớ: 256GB, Camera 108MP', NULL, 0.00, 1, NULL, '29.jpg', 3),
(30, 'Samsung Galaxy Z Fold5', 35990000.00, 8, 'Đa nhiệm đỉnh cao, RAM: 12GB, Bộ nhớ: 512GB, S Pen hỗ trợ', NULL, 0.00, 1, NULL, '30.jpg', 1),
(31, 'Xiaomi Redmi A3', 2490000.00, 60, 'Thiết kế mặt lưng kính, RAM: 4GB, Bộ nhớ: 128GB, Android Go', NULL, 0.00, 1, NULL, '31.jpg', 2),
(32, 'Oppo Find X5 Pro', 15990000.00, 5, 'Gốm sang trọng, RAM: 12GB, Bộ nhớ: 256GB, Chip MariSilicon X', NULL, 0.00, 1, NULL, '32.jpg', 3),
(33, 'Samsung Galaxy S21 FE', 9990000.00, 20, 'Fan Edition, RAM: 8GB, Bộ nhớ: 128GB, Exynos 2100', NULL, 0.00, 1, NULL, '33.jpg', 1),
(34, 'Samsung Galaxy A15', 4990000.00, 35, 'Thiết kế Key Island, RAM: 8GB, Bộ nhớ: 128GB, Màn hình AMOLED', NULL, 0.00, 1, NULL, '34.jpg', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotions`
--

DROP TABLE IF EXISTS `promotions`;
CREATE TABLE IF NOT EXISTS `promotions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `promotionname` varchar(255) DEFAULT NULL,
  `description` text,
  `percent` decimal(5,2) DEFAULT NULL,
  `startdate` datetime DEFAULT NULL,
  `enddate` datetime DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `numberphone` varchar(20) DEFAULT NULL,
  `address` text,
  `avatarimage` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'user',
  `status` tinyint DEFAULT '1',
  `token` varchar(255) DEFAULT NULL,
  `rejectnum` int DEFAULT '0',
  `ordernum` int DEFAULT '0',
  `createdate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `fullname`, `numberphone`, `address`, `avatarimage`, `role`, `status`, `token`, `rejectnum`, `ordernum`, `createdate`) VALUES
(1, 'khachhang', '123456', 'khach@gmail.com', 'Nguyễn Văn A', NULL, 'Hà Nội', NULL, 'user', 1, NULL, 0, 0, '2025-11-28 15:08:39'),
(2, 'duynguyen', '123', 'nguyenduyen2572@gmail.com', 'Nguyễn Xuân Duy', '0945783769', 'C5/15E Phạm Hùng', NULL, 'user', 1, NULL, 0, 0, '2025-11-28 15:15:41');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
