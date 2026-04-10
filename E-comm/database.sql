
CREATE DATABASE IF NOT EXISTS `ecommerce_db`;
USE `ecommerce_db`;

CREATE TABLE `users` (
  `user_id` INT PRIMARY KEY AUTO_INCREMENT,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `phone` VARCHAR(20),
  `address` TEXT,
  `city` VARCHAR(50),
  `state` VARCHAR(50),
  `postal_code` VARCHAR(10),
  `country` VARCHAR(50),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `admin` (
  `admin_id` INT PRIMARY KEY AUTO_INCREMENT,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `role` ENUM('superadmin', 'admin') DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categories` (
  `category_id` INT PRIMARY KEY AUTO_INCREMENT,
  `category_name` VARCHAR(100) UNIQUE NOT NULL,
  `description` TEXT,
  `image` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_name` (`category_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
  `product_id` INT PRIMARY KEY AUTO_INCREMENT,
  `category_id` INT NOT NULL,
  `product_name` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10, 2) NOT NULL,
  `stock` INT NOT NULL DEFAULT 0,
  `image` VARCHAR(255),
  `sku` VARCHAR(50) UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE,
  INDEX `idx_name` (`product_name`),
  INDEX `idx_category` (`category_id`),
  INDEX `idx_sku` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cart` (
  `cart_id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_user_product` (`user_id`, `product_id`),
  INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
  `order_id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `order_number` VARCHAR(50) UNIQUE NOT NULL,
  `total_amount` DECIMAL(10, 2) NOT NULL,
  `status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
  `shipping_address` TEXT,
  `shipping_city` VARCHAR(50),
  `shipping_state` VARCHAR(50),
  `shipping_postal_code` VARCHAR(10),
  `shipping_country` VARCHAR(50),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  INDEX `idx_user` (`user_id`),
  INDEX `idx_order_number` (`order_number`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_items` (
  `order_item_id` INT PRIMARY KEY AUTO_INCREMENT,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `unit_price` DECIMAL(10, 2) NOT NULL,
  `total_price` DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE RESTRICT,
  INDEX `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `categories` (`category_name`, `description`) VALUES
('Electronics', 'Electronic devices and gadgets'),
('Clothing', 'Men and women clothing items'),
('Books', 'Educational and entertainment books'),
('Home & Garden', 'Home decor and garden supplies'),
('Sports', 'Sports equipment and accessories');

INSERT INTO `products` (`category_id`, `product_name`, `description`, `price`, `stock`, `sku`) VALUES
(1, 'Wireless Headphones', 'High-quality wireless headphones with noise cancellation', 79.99, 50, 'ELEC001'),
(1, 'USB-C Cable', 'Durable and fast charging USB-C cable', 9.99, 200, 'ELEC002'),
(2, 'Cotton T-Shirt', 'Comfortable 100% cotton t-shirt', 19.99, 100, 'CLOTH001'),
(2, 'Denim Jeans', 'Classic blue denim jeans', 49.99, 80, 'CLOTH002'),
(3, 'Programming Guide', 'Complete guide to web development', 39.99, 30, 'BOOK001'),
(3, 'Database Design', 'Mastering database design principles', 34.99, 25, 'BOOK002'),
(4, 'Indoor Plant Pot', 'Decorative ceramic pot for indoor plants', 24.99, 60, 'HOME001'),
(4, 'Wall Clock', 'Modern minimalist wall clock', 29.99, 40, 'HOME002'),
(5, 'Yoga Mat', 'Premium quality yoga mat with carrying strap', 29.99, 75, 'SPORT001'),
(5, 'Water Bottle', 'Stainless steel water bottle 1L', 19.99, 100, 'SPORT002');

INSERT INTO `admin` (`username`, `email`, `password`, `first_name`, `last_name`, `role`) VALUES
('rayhan', 'admin@ecommerce.com', '$2y$12$bhBvVFNaV1ZKT722MmfUk.ycV259lXJUMl475JhJTOlkNjSg4DoH.', 'Admin', 'User', 'superadmin');

CREATE TABLE IF NOT EXISTS `contact_messages` (
  `message_id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `subject` VARCHAR(200),
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`username`, `email`, `password`, `first_name`, `last_name`, `phone`, `address`, `city`, `state`, `postal_code`, `country`) VALUES
('user1', 'user1@example.com', '$2y$12$MJvUbagS.MrG9HvBSkxYq.fqr/pS/XavrrkKqMo8De6DRGTzob4si', 'John', 'Doe', '555-1234', '123 Main St', 'New York', 'NY', '10001', 'USA');

CREATE INDEX `idx_products_stock` ON `products` (`stock`);
CREATE INDEX `idx_orders_user_date` ON `orders` (`user_id`, `created_at`);
CREATE INDEX `idx_cart_user_date` ON `cart` (`user_id`, `added_at`);

