-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 19, 2026 at 03:55 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travel_clean_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `status` enum('draft','published') DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `author_id`, `title`, `slug`, `excerpt`, `content`, `featured_image`, `category`, `tags`, `views`, `status`, `published_at`, `created_at`) VALUES
(1, 1, 'Website này được xây dựng bởi SonG Toan Pro', 'website-nay-duoc-xay-dung-boi-song-toan-pro', '', '12h là con số xây dựng nên website này', '1781800830_6a341f7e78d9a.jpg', NULL, NULL, 18, 'published', NULL, '2026-01-16 21:22:39');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tour_id` int(11) NOT NULL,
  `departure_date_id` int(11) NOT NULL,
  `booking_code` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `adults` int(11) DEFAULT 1,
  `children` int(11) DEFAULT 0,
  `babies` int(11) DEFAULT 0,
  `total_amount` decimal(15,2) NOT NULL,
  `discount_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(15,2) DEFAULT 0.00,
  `final_amount` decimal(15,2) NOT NULL,
  `addons` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid','refunded') DEFAULT 'pending',
  `booking_status` enum('new','processing','confirmed','completed','cancelled') DEFAULT 'new',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `tour_id`, `departure_date_id`, `booking_code`, `full_name`, `email`, `phone`, `adults`, `children`, `babies`, `total_amount`, `discount_code`, `discount_amount`, `final_amount`, `addons`, `payment_method`, `payment_status`, `booking_status`, `notes`, `created_at`, `deleted_at`, `updated_at`) VALUES
(5, 2, 1, 3, 'BK202601161103', 'toàn', 'mailien0074@gmail.com', '2', 1, 0, 0, 4492500.00, NULL, 0.00, 4492500.00, NULL, 'bank_transfer', 'refunded', 'cancelled', '', '2026-01-16 11:01:54', NULL, '2026-06-18 08:18:30'),
(6, 3, 1, 4, 'BK202601177124', 'toàn', 'vomaithuy78@gmail.com', '0905440548', 1, 0, 0, 4492500.00, NULL, 0.00, 4492500.00, NULL, 'vnpay', 'paid', 'confirmed', '', '2026-01-16 18:52:53', NULL, '2026-01-17 11:23:43'),
(7, NULL, 1, 4, 'BK2601171628', 'le lơ', 'mailien0074@gmail.com', '0905440548', 1, 0, 0, 4492500.00, NULL, 0.00, 4492500.00, NULL, 'vnpay', 'paid', 'confirmed', '', '2026-01-16 21:30:30', NULL, '2026-01-17 11:25:35'),
(8, 1, 1, 4, 'BK2601176693', 'Super Admin', 'vosongtoan08@gmail.com', '0905440548', 1, 1, 0, 8085000.00, NULL, 0.00, 8085000.00, NULL, 'bank_transfer', 'pending', 'new', '', '2026-01-17 11:56:06', NULL, '2026-01-17 11:56:06'),
(9, 1, 1, 4, 'BK2601178270', 'Super Admin', 'vosongtoan08@gmail.com', '0905440548', 1, 1, 0, 8085000.00, NULL, 0.00, 8085000.00, NULL, 'vnpay', 'refunded', 'confirmed', '', '2026-01-17 11:56:11', NULL, '2026-06-18 08:17:56'),
(10, 2, 3, 10, 'BK2606194250', 'toàn', 'mailien0074@gmail.com', '12', 1, 1, 0, 300000.00, NULL, 0.00, 300000.00, NULL, 'bank_transfer', 'pending', 'new', '', '2026-06-18 23:54:37', NULL, '2026-06-18 23:54:37'),
(11, 2, 3, 10, 'BK2606194779', 'toàn', 'mailien0074@gmail.com', '12', 1, 0, 0, 100000.00, NULL, 0.00, 100000.00, NULL, 'vnpay', 'pending', 'new', '', '2026-06-18 23:55:06', NULL, '2026-06-18 23:55:06'),
(12, 2, 3, 10, 'BK2606199825', 'toàn', 'mailien0074@gmail.com', '12', 1, 0, 0, 100000.00, NULL, 0.00, 100000.00, NULL, 'vnpay', 'pending', 'new', '', '2026-06-19 00:38:03', NULL, '2026-06-19 00:38:03'),
(13, 2, 3, 10, 'BK2606199669', 'toàn', 'mailien0074@gmail.com', '12', 2, 0, 0, 200000.00, NULL, 0.00, 200000.00, NULL, 'vnpay', 'pending', 'new', '', '2026-06-19 00:38:34', NULL, '2026-06-19 00:38:34'),
(14, 2, 3, 10, 'BK2606191276', 'toàn', 'mailien0074@gmail.com', '12', 2, 0, 0, 200000.00, NULL, 0.00, 200000.00, NULL, 'vnpay', 'pending', 'new', '', '2026-06-19 00:40:43', NULL, '2026-06-19 00:40:43'),
(15, 2, 3, 10, 'BK2606196613', 'toàn', 'mailien0074@gmail.com', '2', 2, 0, 0, 200000.00, NULL, 0.00, 200000.00, NULL, 'vnpay', 'pending', 'new', '', '2026-06-19 00:40:56', NULL, '2026-06-19 00:40:56'),
(16, 2, 3, 10, 'BK2606193746', 'toàn', 'mailien0074@gmail.com', '2', 2, 0, 0, 200000.00, NULL, 0.00, 200000.00, NULL, 'vnpay', 'pending', 'new', '', '2026-06-19 00:42:54', NULL, '2026-06-19 00:42:54'),
(17, 2, 3, 10, 'BK2606191640', 'toàn', 'mailien0074@gmail.com', '2', 2, 0, 0, 200000.00, NULL, 0.00, 200000.00, NULL, 'vnpay', 'pending', 'new', '', '2026-06-19 00:48:24', NULL, '2026-06-19 00:48:24'),
(18, 2, 3, 11, 'BK2606195530', 'toàn', 'mailien0074@gmail.com', '1', 1, 0, 0, 100000.00, NULL, 0.00, 100000.00, NULL, 'vnpay', 'pending', 'new', '', '2026-06-19 01:08:22', NULL, '2026-06-19 01:08:22'),
(19, 2, 3, 11, 'BK2606197442', 'toàn', 'mailien0074@gmail.com', '1', 1, 0, 0, 100000.00, NULL, 0.00, 100000.00, NULL, 'vnpay', 'paid', 'confirmed', '', '2026-06-19 01:12:03', NULL, '2026-06-19 01:13:18');

-- --------------------------------------------------------

--
-- Table structure for table `booking_passengers`
--

CREATE TABLE `booking_passengers` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `type` enum('adult','child','baby') NOT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `type` enum('domestic','international','hotel','flight','combo') NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `type`, `status`, `created_at`) VALUES
(1, 'Tour Miền Bắc', 'tour-mien-bac', 'Khám phá vẻ đẹp miền Bắc Việt Nam', NULL, 'domestic', 'active', '2026-01-06 15:33:22'),
(2, 'Tour Miền Trung', 'tour-mien-trung', 'Trải nghiệm miền Trung di sản', NULL, 'domestic', 'active', '2026-01-06 15:33:22'),
(3, 'Tour Miền Nam', 'tour-mien-nam', 'Khám phá miền Nam sông nước', NULL, 'domestic', 'active', '2026-01-06 15:33:22'),
(4, 'Tour Châu Âu', 'tour-chau-au', 'Du lịch Châu Âu sang trọng', NULL, 'international', 'active', '2026-01-06 15:33:22'),
(5, 'Tour Châu Á', 'tour-chau-a', 'Khám phá các nước Châu Á', NULL, 'international', 'active', '2026-01-06 15:33:22');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--
-- Làm sạch bảng trước khi thêm dữ liệu mẫu (đảm bảo không bị trùng lặp ID)
TRUNCATE TABLE `contact_messages`;

-- Thêm dữ liệu mẫu (Dummy Data) an toàn dùng cho GitHub
INSERT INTO `contact_messages` (`id`, `user_id`, `full_name`, `email`, `message`, `status`, `created_at`) VALUES
(1, 2, 'Khách hàng Test', 'khachhang@example.com', 'Tôi cần tư vấn thêm về tour du lịch.', 'new', '2026-01-17 08:37:01'),
(2, 1, 'Super Admin', 'admin@travelvn.com', 'Test hệ thống tin nhắn lần 1.', 'new', '2026-01-17 08:51:04'),
(3, 1, 'Super Admin', 'admin@travelvn.com', 'Test hệ thống tin nhắn lần 2 - Xin chào!', 'new', '2026-01-17 09:08:34');
-- Dữ liệu mẫu cho bảng người dùng (Mật khẩu mặc định là: 123456)
TRUNCATE TABLE `users`;
INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`) VALUES
(1, 'Super Admin', 'admin@travelvn.com', '$2y$10$8/XEKuN1bOq.W91d4eL4ReJ04jL/g6gV0s2C3oQ1hQ6yT9J9N4F6W', 'admin'),
(2, 'Khách hàng Test', 'khachhang@example.com', '$2y$10$8/XEKuN1bOq.W91d4eL4ReJ04jL/g6gV0s2C3oQ1hQ6yT9J9N4F6W', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` enum('percent','fixed') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `min_order` decimal(10,2) DEFAULT 0.00,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `type`, `value`, `min_order`, `max_discount`, `usage_limit`, `used_count`, `valid_from`, `valid_to`, `status`, `created_at`) VALUES
(1, '123', 'percent', 50.00, 5000000.00, NULL, 2, 0, NULL, '2026-01-18', 'active', '2026-01-17 11:57:12');

-- --------------------------------------------------------

--
-- Table structure for table `departure_dates`
--

CREATE TABLE `departure_dates` (
  `id` int(11) NOT NULL,
  `tour_id` int(11) NOT NULL,
  `departure_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `available_slots` int(11) DEFAULT 50,
  `status` enum('available','full','cancelled') DEFAULT 'available',
  `price_adult` decimal(15,2) NOT NULL,
  `price_child` decimal(10,2) DEFAULT NULL,
  `price_baby` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departure_dates`
--

INSERT INTO `departure_dates` (`id`, `tour_id`, `departure_date`, `return_date`, `available_slots`, `status`, `price_adult`, `price_child`, `price_baby`) VALUES
(1, 1, '2026-01-08', '2026-01-11', 20, 'available', 5990000.00, 4790000.00, 0.00),
(2, 1, '2026-01-11', '2026-01-14', 15, 'available', 5990000.00, 4790000.00, 0.00),
(3, 1, '2026-01-16', '2026-01-19', 22, 'available', 5990000.00, 4790000.00, 0.00),
(4, 1, '2026-01-21', '2026-01-24', 4, 'available', 5990000.00, 4790000.00, 0.00),
(5, 2, '2026-01-09', '2026-01-11', 15, 'available', 4500000.00, 3600000.00, 0.00),
(6, 2, '2026-01-13', '2026-01-15', 22, 'available', 4500000.00, 3600000.00, 0.00),
(7, 2, '2026-01-20', '2026-01-22', 29, 'available', 4500000.00, 3600000.00, 0.00),
(9, 3, '2026-01-18', NULL, 20, 'available', 30000.00, NULL, NULL),
(10, 3, '2026-12-12', NULL, 1, 'available', 0.00, 0.00, NULL),
(11, 3, '2026-09-02', NULL, 1999998, 'available', 0.00, 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tour_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `title` varchar(200) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `images` longtext DEFAULT NULL CHECK (json_valid(`images`)),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` varchar(50) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `updated_at`) VALUES
(1, 'site_name', 'Travel Website', 'text', '2026-01-16 10:44:28'),
(2, 'site_email', 'vosongtoan08@gmail.com', 'email', '2026-01-17 09:05:16'),
(3, 'site_phone', '0987654321', 'text', '2026-01-16 10:44:28'),
(4, 'currency', 'VND', 'text', '2026-01-16 10:44:28'),
(5, 'language', 'vi', 'text', '2026-01-16 10:44:28');

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `itinerary` longtext DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `departure_location` varchar(100) DEFAULT NULL,
  `destination` varchar(100) DEFAULT NULL,
  `price_adult` decimal(15,2) NOT NULL,
  `price_child` decimal(10,2) DEFAULT NULL,
  `price_baby` decimal(10,2) DEFAULT NULL,
  `discount_percent` int(11) DEFAULT 0,
  `includes` text DEFAULT NULL,
  `excludes` text DEFAULT NULL,
  `cancellation_policy` text DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `flash_sale` tinyint(1) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_reviews` int(11) DEFAULT 0,
  `available_slots` int(11) DEFAULT 50,
  `transport` varchar(50) DEFAULT NULL,
  `tour_type` varchar(50) DEFAULT NULL,
  `images` longtext DEFAULT NULL CHECK (json_valid(`images`)),
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `category_id`, `title`, `slug`, `description`, `itinerary`, `duration`, `departure_location`, `destination`, `price_adult`, `price_child`, `price_baby`, `discount_percent`, `includes`, `excludes`, `cancellation_policy`, `featured`, `flash_sale`, `rating`, `total_reviews`, `available_slots`, `transport`, `tour_type`, `images`, `status`, `created_at`, `deleted_at`, `updated_at`) VALUES
(1, 1, 'Hà Nội - Hạ Long - Sapa 4N3Đ', 'ha-noi-ha-long-sapa', 'Khám phá vẻ đẹp hùng vĩ của miền Bắc Việt Nam với du thuyền Hạ Long và núi rừng Sapa.', '', '4 ngày 3 đêm', 'Hà Nội', 'Hạ Long', 5990000.00, 0.00, NULL, 25, '', '', NULL, 1, 1, 4.80, 12, 50, NULL, NULL, '[\"1781777218_6a33c3429f3f1.jpeg\",\"1781777255_6a33c367ae530.jpeg\"]', 'active', '2026-01-06 15:53:25', NULL, '2026-06-19 01:31:09'),
(2, 2, 'Đà Nẵng - Hội An - Bà Nà Hills', 'da-nang-hoi-an', 'Trải nghiệm con đường di sản miền Trung, phố cổ Hội An và Cầu Vàng.', '', '3 ngày 2 đêm', 'TP.HCM', 'Đà Nẵng', 4500000.00, 0.00, NULL, 10, '', '', NULL, 1, 1, 4.50, 8, 50, NULL, NULL, '[\"1781777174_6a33c31615608.jpeg\"]', 'active', '2026-01-06 15:53:25', NULL, '2026-06-19 01:31:07'),
(3, 3, 'Chuyến đi đến Đồng Nai', 'dong-nai', 'Rất thú vị và đặc sắc bởi khung cảnh tuyệt đẹp ở Vĩnh Hy - Ninh Thuận', 'Ngày 1: 17/1 \r\nNgày 2: 21/1', '2N3Đ', NULL, 'Vĩnh hy - Ninh Thuận', 100000.00, 200000.00, NULL, 0, '', '', NULL, 1, 1, 0.00, 0, 50, NULL, NULL, '[\"1781776746_6a33c16a18851.jpeg\"]', 'active', '2026-01-17 11:30:55', NULL, '2026-06-19 01:31:07');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL COMMENT 'vnpay, momo, paypal, bank_transfer',
  `transaction_ref` varchar(100) DEFAULT NULL COMMENT 'Mã giao dịch trả về từ cổng thanh toán (VD: VNP123456)',
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'VND',
  `status` enum('pending','success','failed','refunded') DEFAULT 'pending',
  `response_code` varchar(50) DEFAULT NULL COMMENT 'Mã lỗi từ cổng thanh toán (VD: 00 là thành công)',
  `raw_response` text DEFAULT NULL COMMENT 'Lưu toàn bộ chuỗi JSON/String từ cổng thanh toán để debug sau này',
  `description` text DEFAULT NULL COMMENT 'Nội dung lỗi hiển thị hoặc ghi chú giao dịch',
  `payment_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `booking_id`, `payment_method`, `transaction_ref`, `amount`, `currency`, `status`, `response_code`, `raw_response`, `description`, `payment_time`, `created_at`, `updated_at`) VALUES
(1, 5, 'bank_transfer', 'TXN_BK202601161103_1768561314', 4492500.00, 'VND', 'pending', NULL, NULL, NULL, '2026-01-16 11:01:54', '2026-01-16 11:01:54', '2026-01-16 11:01:54'),
(2, 6, 'vnpay', 'TXN_BK202601177124_1768589573', 4492500.00, 'VND', 'success', '00', NULL, 'Thanh toán thành công qua VNPAY', '2026-01-16 18:52:53', '2026-01-16 18:52:53', '2026-01-17 11:23:43'),
(3, 7, 'vnpay', 'TXN_BK2601171628_1768599030', 4492500.00, 'VND', 'failed', '24', NULL, NULL, '2026-01-16 21:30:30', '2026-01-16 21:30:30', '2026-01-16 21:30:36'),
(4, 7, 'manual', 'MANUAL_1_1768639084', 0.00, 'VND', 'success', NULL, NULL, 'Admin xác nhận thanh toán thủ công', '2026-01-17 08:38:04', '2026-01-17 08:38:04', '2026-01-17 08:38:04'),
(5, 7, 'manual', 'MANUAL_1_1768649135', 0.00, 'VND', 'success', NULL, NULL, 'Admin xác nhận thanh toán thủ công', '2026-01-17 11:25:35', '2026-01-17 11:25:35', '2026-01-17 11:25:35'),
(6, 5, 'manual', 'MANUAL_1_1768649161', 0.00, 'VND', 'success', NULL, NULL, 'Admin xác nhận thanh toán thủ công', '2026-01-17 11:26:01', '2026-01-17 11:26:01', '2026-01-17 11:26:01'),
(7, 8, 'bank_transfer', 'TXN_BK2601176693_1768650966', 8085000.00, 'VND', 'pending', NULL, NULL, NULL, '2026-01-17 11:56:06', '2026-01-17 11:56:06', '2026-01-17 11:56:06'),
(8, 9, 'vnpay', 'TXN_BK2601178270_1768650971', 8085000.00, 'VND', 'failed', '15', NULL, NULL, '2026-01-17 11:56:11', '2026-01-17 11:56:11', '2026-01-17 12:11:56'),
(9, 14, 'vnpay', 'TXN_BK2606191276_1781829643', 200000.00, 'VND', 'pending', NULL, NULL, NULL, '2026-06-19 00:40:43', '2026-06-19 00:40:43', '2026-06-19 00:40:43'),
(10, 15, 'vnpay', 'TXN_BK2606196613_1781829656', 200000.00, 'VND', 'pending', NULL, NULL, NULL, '2026-06-19 00:40:56', '2026-06-19 00:40:56', '2026-06-19 00:40:56'),
(11, 16, 'vnpay', 'TXN_BK2606193746_1781829774', 200000.00, 'VND', 'pending', NULL, NULL, NULL, '2026-06-19 00:42:54', '2026-06-19 00:42:54', '2026-06-19 00:42:54'),
(12, 17, 'vnpay', 'TXN_BK2606191640_1781830104', 200000.00, 'VND', 'pending', NULL, NULL, NULL, '2026-06-19 00:48:24', '2026-06-19 00:48:24', '2026-06-19 00:48:24'),
(13, 18, 'vnpay', 'TXN_BK2606195530_1781831302', 100000.00, 'VND', 'pending', NULL, NULL, NULL, '2026-06-19 01:08:22', '2026-06-19 01:08:22', '2026-06-19 01:08:22'),
(14, 19, 'vnpay', '15589380', 100000.00, 'VND', 'success', NULL, NULL, NULL, '2026-06-19 01:12:03', '2026-06-19 01:12:03', '2026-06-19 01:13:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `points` int(11) DEFAULT 0,
  `member_level` enum('bronze','silver','gold','platinum') DEFAULT 'bronze',
  `is_verified` tinyint(1) DEFAULT 0,
  `verify_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `reset_token`, `reset_expiry`, `phone`, `role`, `status`, `points`, `member_level`, `is_verified`, `verify_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'vosongtoan08@gmail.com', '$2y$12$APL52LGwkbylNI0Imwib7e/nJNJUH1myj8Ejh/okvfgxKoMnZINdq', NULL, NULL, NULL, 'admin', 'active', 0, 'platinum', 1, NULL, '2026-01-16 10:44:28', '2026-06-18 07:57:12'),
(2, 'toàn', 'mailien0074@gmail.com', '$2y$12$483NR8HJd0lq.2W.6RYRsenFHEo595P2hJuyZbD7Ke4RU0VT8TBQ6', NULL, NULL, '0905440548', 'customer', 'active', 0, 'bronze', 0, '1e1f13baa6d8527369650260bda052cf9ac2cd7832d025c9189b006335c0befc', '2026-01-16 11:01:15', '2026-06-18 04:36:59'),
(3, 'toàn', 'vomaithuy78@gmail.com', '$2y$10$awcXoMAGsj8Ly00.gQ1b7uCXv7zVIL7DTAD05aCMm/6KSTlWxYL6S', NULL, NULL, '0905440548', 'customer', 'active', 0, 'bronze', 0, '10661f12969ee954bdda0e74247750b216dec1506b2b73adcf5df92fae9dd945', '2026-01-16 18:52:07', '2026-01-16 18:52:07');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tour_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_code` (`booking_code`),
  ADD KEY `departure_date_id` (`departure_date_id`),
  ADD KEY `idx_booking_code` (`booking_code`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `fk_booking_tour` (`tour_id`),
  ADD KEY `idx_booking_created` (`created_at`),
  ADD KEY `idx_bookings_email` (`email`);

--
-- Indexes for table `booking_passengers`
--
ALTER TABLE `booking_passengers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`);

--
-- Indexes for table `departure_dates`
--
ALTER TABLE `departure_dates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tour_date` (`tour_id`,`departure_date`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `idx_tour` (`tour_id`),
  ADD KEY `idx_reviews_status` (`status`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_featured` (`featured`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_tour_search` (`price_adult`,`destination`,`status`),
  ADD KEY `idx_tours_destination` (`destination`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_txn_ref` (`transaction_ref`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `transactions_ibfk_1` (`booking_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_id`,`tour_id`),
  ADD KEY `tour_id` (`tour_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `booking_passengers`
--
ALTER TABLE `booking_passengers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departure_dates`
--
ALTER TABLE `departure_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
