CREATE TABLE `users` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_person` int,
  `username` varchar(255),
  `password` varchar(255),
  `is_admin` boolean,
  `status` boolean,
  `created_at` datetime,
  `updated_at` datetime,
  `deleted_at` datetime
);

CREATE TABLE `user_logs` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_user` int,
  `log` varchar(128),
  `ip` varchar(45),
  `useragent` varchar(128),
  `session_id` varchar(64),
  `url` varchar(128),
  `created_at` datetime
);

CREATE TABLE `user_password_recoveries` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_user` int,
  `ip` varchar(45),
  `recovery_at` datetime,
  `created_at` datetime
);

CREATE TABLE `persons` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(64),
  `email` varchar(128),
  `phone` int,
  `created_at` datetime,
  `updated_at` datetime
);

CREATE TABLE `addresses` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_person` int,
  `address` varchar(128),
  `complement` varchar(32),
  `city` varchar(32),
  `state` varchar(32),
  `country` varchar(32),
  `zipcode` int,
  `created_at` datetime,
  `updated_at` datetime
);

CREATE TABLE `orders` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_cart` int,
  `id_user` int,
  `id_status` int,
  `total` decimal(10,2),
  `created_at` datetime
);

CREATE TABLE `order_status` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `status` varchar(32),
  `created_at` datetime
);

CREATE TABLE `carts` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `session_id` varchar(64),
  `id_user` int,
  `id_address` int,
  `freight` decimal(10,2),
  `created_at` datetime
);

CREATE TABLE `cart_products` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_cart` int,
  `id_product` int,
  `removed_at` datetime,
  `created_at` datetime
);

CREATE TABLE `products` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `product` varchar(64),
  `price` decimal(10,2),
  `width` decimal(10,2),
  `height` decimal(10,2),
  `length` decimal(10,2),
  `url` varchar(128),
  `created_at` datetime,
  `updated_at` datetime
);

CREATE TABLE `product_categories` (
  `id_category` int,
  `id_person` int
);

CREATE TABLE `categories` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `category` varchar(32),
  `created_at` datetime
);

ALTER TABLE `users` ADD FOREIGN KEY (`id_person`) REFERENCES `persons` (`id`);

ALTER TABLE `user_logs` ADD FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

ALTER TABLE `user_password_recoveries` ADD FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

ALTER TABLE `addresses` ADD FOREIGN KEY (`id_person`) REFERENCES `persons` (`id`);

ALTER TABLE `orders` ADD FOREIGN KEY (`id_cart`) REFERENCES `carts` (`id`);

ALTER TABLE `orders` ADD FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

ALTER TABLE `orders` ADD FOREIGN KEY (`id_status`) REFERENCES `order_status` (`id`);

ALTER TABLE `carts` ADD FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

ALTER TABLE `carts` ADD FOREIGN KEY (`id_address`) REFERENCES `addresses` (`id`);

ALTER TABLE `cart_products` ADD FOREIGN KEY (`id_cart`) REFERENCES `carts` (`id`);

ALTER TABLE `cart_products` ADD FOREIGN KEY (`id_product`) REFERENCES `products` (`id`);

ALTER TABLE `product_categories` ADD FOREIGN KEY (`id_category`) REFERENCES `categories` (`id`);

ALTER TABLE `product_categories` ADD FOREIGN KEY (`id_person`) REFERENCES `products` (`id`);
