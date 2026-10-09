-- ====================================================================
-- VR Fashion - Ready-Made Clothing Marketplace Database Dump
-- Sri Lanka Online Apparel & Garment Marketplace
-- Database Name: vr_fashion
-- Compatible with MySQL 5.7+ / MySQL 8.0+ / MariaDB 10.4+ (XAMPP / WAMP)
-- ====================================================================

CREATE DATABASE IF NOT EXISTS `vr_fashion` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `vr_fashion`;

-- --------------------------------------------------------------------
-- Drop existing tables if present (respecting foreign key order)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `cart`;
DROP TABLE IF EXISTS `wishlist`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `users`;

-- --------------------------------------------------------------------
-- Table structure for table: users
-- Supports both 'buyer' and 'seller' roles
-- --------------------------------------------------------------------
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('buyer', 'seller') NOT NULL DEFAULT 'buyer',
  `shop_name` VARCHAR(100) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- Table structure for table: products
-- Ready-made garments listed by sellers
-- --------------------------------------------------------------------
CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `seller_id` INT NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `category` ENUM('Men', 'Women', 'Kids', 'Footwear', 'Accessories') NOT NULL,
  `description` TEXT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `sizes` VARCHAR(100) NOT NULL DEFAULT 'S, M, L, XL',
  `colour` VARCHAR(50) NOT NULL DEFAULT 'Standard',
  `stock` INT NOT NULL DEFAULT 10,
  `image` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_products_seller` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- Table structure for table: wishlist
-- --------------------------------------------------------------------
CREATE TABLE `wishlist` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_user_wishlist_product` (`user_id`, `product_id`),
  CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- Table structure for table: cart
-- --------------------------------------------------------------------
CREATE TABLE `cart` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `size` VARCHAR(10) NOT NULL DEFAULT 'M',
  `quantity` INT NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- Table structure for table: orders
-- Cash on Delivery and checkout orders
-- --------------------------------------------------------------------
CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('Pending', 'Shipped', 'Delivered', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `address` TEXT NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- Table structure for table: order_items
-- --------------------------------------------------------------------
CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `size` VARCHAR(10) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- Table structure for table: messages
-- --------------------------------------------------------------------
CREATE TABLE `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- Sample Data: users
-- All sample users share password: Password123!
-- --------------------------------------------------------------------
INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `shop_name`, `created_at`) VALUES
(1, 'araliya_store', 'seller1@vrfashion.lk', '$2y$10$fRBpPefeBmZ55.BpUE0pIujCV9szvYQPIZU.QaZeMMcUQGW95IyAO', 'seller', 'Araliya Cotton & Handlooms', NOW()),
(2, 'lanka_threads', 'seller2@vrfashion.lk', '$2y$10$fRBpPefeBmZ55.BpUE0pIujCV9szvYQPIZU.QaZeMMcUQGW95IyAO', 'seller', 'Lanka Threads Colombo', NOW()),
(3, 'kasun_perera', 'buyer1@vrfashion.lk', '$2y$10$fRBpPefeBmZ55.BpUE0pIujCV9szvYQPIZU.QaZeMMcUQGW95IyAO', 'buyer', NULL, NOW()),
(4, 'anusha_fernando', 'buyer2@vrfashion.lk', '$2y$10$fRBpPefeBmZ55.BpUE0pIujCV9szvYQPIZU.QaZeMMcUQGW95IyAO', 'buyer', NULL, NOW());

-- --------------------------------------------------------------------
-- Sample Data: products (18 realistic ready-made items in LKR)
-- --------------------------------------------------------------------
INSERT INTO `products` (`id`, `seller_id`, `name`, `category`, `description`, `price`, `sizes`, `colour`, `stock`, `image`, `created_at`) VALUES
(1, 1, 'Classic Crewneck Cotton T-Shirt', 'Men', '100% breathable Sri Lankan combed cotton t-shirt. Ideal for casual everyday wear in warm tropical climates. Pre-shrunk and durable.', 1850.00, 'S, M, L, XL, XXL', 'Navy Blue', 45, 'images/product-1.svg', NOW()),
(2, 1, 'Casual Batik Linen Long Sleeve Shirt', 'Men', 'Premium lightweight linen shirt featuring subtle artisanal Sri Lankan batik accents along the collar and cuffs. Tailored comfortable fit.', 4200.00, 'M, L, XL', 'Off White', 25, 'images/product-2.svg', NOW()),
(3, 1, 'Floral Wrap Midi Summer Frock', 'Women', 'Elegant floral print rayon midi dress with flattering waist tie belt. Soft, flowy and perfect for weekend outings and festive brunches.', 3850.00, 'S, M, L, XL', 'Coral Rose', 30, 'images/product-3.svg', NOW()),
(4, 1, 'Traditional Handloom Cotton Sarong', 'Men', 'Authentic woven handloom cotton sarong with stitched tube border. Extremely soft, lightweight, and vibrant checked pattern.', 2650.00, 'Free Size', 'Indigo Check', 50, 'images/product-4.svg', NOW()),
(5, 2, 'Slim Fit Stretch Denim Jeans', 'Men', 'Modern 5-pocket blue denim jeans with 2% elastane for comfortable stretch. Reinforced stitching and durable brass zip fly.', 4950.00, '30, 32, 34, 36', 'Dark Indigo', 40, 'images/product-5.svg', NOW()),
(6, 2, 'Women\'s Pure Cotton Kurti Top', 'Women', 'Handcrafted embroidered cotton tunic kurti top with three-quarter sleeves and mandarin neckline. Pairs beautifully with leggings or jeans.', 3200.00, 'S, M, L, XL', 'Maroon Red', 35, 'images/product-6.svg', NOW()),
(7, 2, 'Kids Cotton Dungaree & Tee Set', 'Kids', 'Adorable toddler dungaree outfit made with soft organic cotton. Features adjustable shoulder straps and side pockets.', 2950.00, '2-3Y, 3-4Y, 4-5Y', 'Denim Blue', 20, 'images/product-7.svg', NOW()),
(8, 2, 'Casual Canvas Low-Top Sneakers', 'Footwear', 'Everyday cushioned sneakers with vulcanized rubber sole and breathable canvas upper. Non-slip grip suitable for daily commuting.', 3750.00, '40, 41, 42, 43, 44', 'Classic White', 28, 'images/product-8.svg', NOW()),
(9, 1, 'Women\'s Wide Leg Linen Palazzo Pants', 'Women', 'High-waisted elasticated linen-cotton trousers with deep functional pockets. Breezy, relaxed silhouette designed for comfort.', 3400.00, 'S, M, L, XL', 'Olive Green', 30, 'images/product-9.svg', NOW()),
(10, 1, 'Kids Unisex School Sports Polo', 'Kids', 'Durable honeycomb pique polo shirt designed for school sports and active play. Quick-dry moisture wicking fabric.', 1650.00, '5-6Y, 7-8Y, 9-10Y, 11-12Y', 'Pure White', 60, 'images/product-10.svg', NOW()),
(11, 2, 'Handcrafted Woven Jute Tote Bag', 'Accessories', 'Eco-friendly natural jute tote bag with reinforced faux-leather handles and zippered main compartment for beach or market shopping.', 2400.00, 'Standard', 'Natural Beige', 35, 'images/product-11.svg', NOW()),
(12, 2, 'Genuine Leather Formal Men\'s Belt', 'Accessories', 'Full-grain smooth leather dress belt with classic brushed nickel buckle. Reversible brown and black utility.', 2250.00, '32, 34, 36, 38', 'Dark Brown', 45, 'images/product-12.svg', NOW()),
(13, 1, 'Women\'s Comfort Strap Wedge Sandals', 'Footwear', 'Ergonomic dual-strap platform slide sandals with memory foam cushioning. Easy slip-on style for all-day comfort.', 3600.00, '36, 37, 38, 39, 40', 'Tan Brown', 22, 'images/product-13.svg', NOW()),
(14, 1, 'Boys Printed Cotton Shorts & Tee Set', 'Kids', 'Two-piece playful summer outfit made of 100% natural cotton. Machine washable, skin-friendly dyes with fun safari print.', 2350.00, '3-4Y, 5-6Y, 7-8Y', 'Sea Green', 25, 'images/product-14.svg', NOW()),
(15, 2, 'Classic Pique Polo Shirt with Collar', 'Men', 'Premium regular-fit pique knit polo shirt with mother-of-pearl buttons and ribbed collar. Smart casual essential.', 2750.00, 'S, M, L, XL, XXL', 'Burgundy', 38, 'images/product-15.svg', NOW()),
(16, 2, 'Embroidered Silk Chiffon Saree Blouse', 'Women', 'Ready-to-wear tailored saree blouse with subtle gold zari thread embroidery and back tie cords. Padded bust for graceful silhouette.', 3900.00, '34, 36, 38, 40', 'Royal Gold', 18, 'images/product-16.svg', NOW()),
(17, 1, 'Casual Washed Cotton Baseball Cap', 'Accessories', 'Adjustable 6-panel unstructured dad hat with curved brim and antique brass buckle closure. Breathable eyelets.', 1450.00, 'One Size', 'Charcoal Grey', 50, 'images/product-17.svg', NOW()),
(18, 2, 'Kids Traditional Festive Kurta Set', 'Kids', 'Festive cotton silk kurta pyjama set for boys. Lightweight, non-itchy inner lining, and elegant collar piping for special occasions.', 3650.00, '4-5Y, 6-7Y, 8-9Y', 'Golden Yellow', 15, 'images/product-18.svg', NOW());

-- --------------------------------------------------------------------
-- Sample Data: wishlist (for buyer 3: kasun_perera)
-- --------------------------------------------------------------------
INSERT INTO `wishlist` (`user_id`, `product_id`, `created_at`) VALUES
(3, 1, NOW()),
(3, 3, NOW()),
(3, 8, NOW());

-- --------------------------------------------------------------------
-- Sample Data: cart (for buyer 3: kasun_perera)
-- --------------------------------------------------------------------
INSERT INTO `cart` (`user_id`, `product_id`, `size`, `quantity`, `created_at`) VALUES
(3, 2, 'L', 1, NOW()),
(3, 5, '34', 1, NOW());

-- --------------------------------------------------------------------
-- Sample Data: orders & order_items
-- --------------------------------------------------------------------
INSERT INTO `orders` (`id`, `user_id`, `total`, `status`, `address`, `phone`, `created_at`) VALUES
(1, 3, 5700.00, 'Delivered', 'No. 42/B, Galle Road, Colombo 03', '0771234567', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(2, 3, 3850.00, 'Pending', 'No. 15, Station Road, Maharagama', '0719876543', NOW());

INSERT INTO `order_items` (`order_id`, `product_id`, `size`, `quantity`, `price`) VALUES
(1, 1, 'L', 1, 1850.00),
(1, 3, 'M', 1, 3850.00),
(2, 3, 'L', 1, 3850.00);

-- --------------------------------------------------------------------
-- Sample Data: messages
-- --------------------------------------------------------------------
INSERT INTO `messages` (`name`, `email`, `message`, `created_at`) VALUES
('Nisal Dissanayake', 'nisal@gmail.com', 'Hello, do you support wholesale orders for ready-made linen shirts from Kandy?', NOW()),
('Chathuri Perera', 'chathuri.p@yahoo.com', 'Inquiring about bulk delivery timelines for festive dresses before Sinhala and Tamil New Year.', NOW());
