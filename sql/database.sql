CREATE DATABASE IF NOT EXISTS zak_market CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

CREATE USER IF NOT EXISTS 'zakuser'@'localhost' IDENTIFIED BY 'zakpass123';
GRANT ALL PRIVILEGES ON zak_market.* TO 'zakuser'@'localhost';
FLUSH PRIVILEGES;

USE zak_market;

DROP TABLE IF EXISTS order_item_addons;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS product_addons;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS shops;
DROP TABLE IF EXISTS subcategories;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS user_addresses;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'vendor', 'customer', 'delivery') NOT NULL,
    phone VARCHAR(30),
    profile_image VARCHAR(255),
    is_approved TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    label VARCHAR(50) NOT NULL,
    full_address TEXT NOT NULL,
    city VARCHAR(100),
    area VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE subcategories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE shops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(30),
    description TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    shop_id INT NOT NULL,
    category_id INT NOT NULL,
    subcategory_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    quantity INT DEFAULT 0,
    image VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (subcategory_id) REFERENCES subcategories(id) ON DELETE CASCADE
);

CREATE TABLE product_addons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    addon_name VARCHAR(100) NOT NULL,
    addon_price DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    delivery_id INT NULL,
    address_id INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    delivery_fee DECIMAL(10,2) DEFAULT 40.00,
    cash_to_collect DECIMAL(10,2) NOT NULL,
    pickup_location TEXT,
    dropoff_address TEXT,
    customer_phone VARCHAR(30),
    status ENUM('pending', 'preparing', 'ready_for_pickup', 'accepted_by_delivery', 'picked_up', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (delivery_id) REFERENCES users(id),
    FOREIGN KEY (address_id) REFERENCES user_addresses(id)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    item_status ENUM('pending', 'preparing', 'ready') DEFAULT 'pending',
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE order_item_addons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_item_id INT NOT NULL,
    addon_name VARCHAR(100) NOT NULL,
    addon_price DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (order_item_id) REFERENCES order_items(id) ON DELETE CASCADE
);

INSERT INTO users (id, name, email, password, role, phone, is_approved) VALUES
(1, 'Admin', 'admin@zak.com', '$2y$12$Lirt3xMJVas7Sc.bxZd9jeTCg8rXpad066LdvRQzQ3yeJ.3YfOUQm', 'admin', '01000000001', 1),
(2, 'Mac Restaurant', 'mac@zak.com', '$2y$12$Lirt3xMJVas7Sc.bxZd9jeTCg8rXpad066LdvRQzQ3yeJ.3YfOUQm', 'vendor', '01000000002', 1),
(3, 'Buffalo Restaurant', 'buffalo@zak.com', '$2y$12$Lirt3xMJVas7Sc.bxZd9jeTCg8rXpad066LdvRQzQ3yeJ.3YfOUQm', 'vendor', '01000000003', 1),
(4, 'Amo Hassan Restaurant', 'amohassan@zak.com', '$2y$12$Lirt3xMJVas7Sc.bxZd9jeTCg8rXpad066LdvRQzQ3yeJ.3YfOUQm', 'vendor', '01000000004', 1),
(5, 'Pizza Bemoz Restaurant', 'bemoz@zak.com', '$2y$12$Lirt3xMJVas7Sc.bxZd9jeTCg8rXpad066LdvRQzQ3yeJ.3YfOUQm', 'vendor', '01000000005', 1),
(6, 'Saleem Kebab Restaurant', 'saleem@zak.com', '$2y$12$Lirt3xMJVas7Sc.bxZd9jeTCg8rXpad066LdvRQzQ3yeJ.3YfOUQm', 'vendor', '01000000006', 1),
(7, 'Farida Kebab Restaurant', 'farida@zak.com', '$2y$12$Lirt3xMJVas7Sc.bxZd9jeTCg8rXpad066LdvRQzQ3yeJ.3YfOUQm', 'vendor', '01000000007', 1),
(8, 'Qasr El Kababgy Restaurant', 'kababgy@zak.com', '$2y$12$Lirt3xMJVas7Sc.bxZd9jeTCg8rXpad066LdvRQzQ3yeJ.3YfOUQm', 'vendor', '01000000008', 1),
(9, 'Customer One', 'customer@zak.com', '$2y$12$Lirt3xMJVas7Sc.bxZd9jeTCg8rXpad066LdvRQzQ3yeJ.3YfOUQm', 'customer', '01000000009', 1),
(10, 'Delivery Worker', 'delivery@zak.com', '$2y$12$Lirt3xMJVas7Sc.bxZd9jeTCg8rXpad066LdvRQzQ3yeJ.3YfOUQm', 'delivery', '01000000010', 1);

INSERT INTO user_addresses (user_id, label, full_address, city, area, notes) VALUES
(9, 'Home', 'Building 12, Street 9', 'Cairo', 'Nasr City', 'Call before arriving'),
(9, 'Work', 'AAST Campus Gate 3', 'Cairo', 'Sheraton', 'Meet at main gate');

INSERT INTO categories (id, name) VALUES
(1, 'Food'),
(2, 'Drinks'),
(3, 'Grocery'),
(4, 'Desserts');

INSERT INTO subcategories (id, category_id, name) VALUES
(1, 1, 'Burger'),
(2, 1, 'Pizza'),
(3, 1, 'Egyptian Food'),
(4, 1, 'Kebab'),
(5, 1, 'Sushi'),
(6, 2, 'Soft Drinks'),
(7, 2, 'Water'),
(8, 3, 'Supermarket');

INSERT INTO shops (id, vendor_id, category_id, name, address, phone, description, status) VALUES
(1, 2, 1, 'Mac', 'City Center Food Court', '01111111111', 'Fast food meals and burgers.', 'approved'),
(2, 3, 1, 'Buffalo', 'Nasr City, Abbas El Akkad', '01222222222', 'Burger, wings, and sauces.', 'approved'),
(3, 4, 1, 'Amo Hassan', 'Heliopolis Food Street', '01333333333', 'Egyptian meals like molokhia and kofta.', 'approved'),
(4, 5, 1, 'Pizza Bemoz', 'Sheraton, Main Road', '01444444444', 'Pizza restaurant with extra toppings.', 'approved'),
(5, 6, 1, 'Saleem Kebab', 'Maadi, Road 9', '01555555555', 'Grilled kebab and kofta meals.', 'approved'),
(6, 7, 1, 'Farida Kebab', 'Mokattam, Street 20', '01666666666', 'Kebab restaurant and oriental meals.', 'approved'),
(7, 8, 1, 'Qasr El Kababgy', 'New Cairo, Fifth Settlement', '01777777777', 'Premium grill restaurant.', 'approved');

INSERT INTO products (id, vendor_id, shop_id, category_id, subcategory_id, name, description, price, quantity, status) VALUES
(1, 2, 1, 1, 1, 'Big Mac Meal', 'Burger sandwich with fries.', 180.00, 50, 'approved'),
(2, 2, 1, 1, 1, 'Chicken Mac Meal', 'Chicken burger meal with fries.', 160.00, 50, 'approved'),
(3, 3, 2, 1, 1, 'Buffalo Burger', 'Beef burger with buffalo sauce.', 190.00, 40, 'approved'),
(4, 3, 2, 1, 1, 'Buffalo Wings', 'Chicken wings with spicy sauce.', 150.00, 35, 'approved'),
(5, 4, 3, 1, 3, 'Molokhia Meal', 'Molokhia with rice and chicken.', 140.00, 30, 'approved'),
(6, 4, 3, 1, 3, 'Kofta Meal', 'Kofta with rice, salad, and bread.', 170.00, 30, 'approved'),
(7, 5, 4, 1, 2, 'Margherita Pizza', 'Classic cheese pizza.', 155.00, 25, 'approved'),
(8, 5, 4, 1, 2, 'Chicken Ranch Pizza', 'Pizza with chicken and ranch sauce.', 220.00, 20, 'approved'),
(9, 6, 5, 1, 4, 'Saleem Kebab Meal', 'Kebab with rice, tahini, salad, and bread.', 260.00, 20, 'approved'),
(10, 7, 6, 1, 4, 'Farida Kofta Meal', 'Grilled kofta with oriental sides.', 230.00, 20, 'approved'),
(11, 8, 7, 1, 4, 'Kababgy Mix Grill', 'Mixed grill meal with kebab, kofta, and chicken.', 350.00, 15, 'approved');

INSERT INTO product_addons (product_id, addon_name, addon_price) VALUES
(1, 'Pepsi', 25), (1, 'Water', 15), (1, 'Extra Cheese', 20), (1, 'Extra Fries', 35),
(2, 'Pepsi', 25), (2, 'Water', 15), (2, 'Extra Cheese', 20),
(3, 'Pepsi', 25), (3, 'Buffalo Sauce', 20), (3, 'Extra Cheese', 25),
(4, 'Ranch Sauce', 20), (4, 'Spicy Sauce', 15), (4, 'Water', 15),
(5, 'Extra Garlic', 10), (5, 'Salad', 20), (5, 'Bread', 10), (5, 'Water', 15),
(6, 'Salad', 20), (6, 'Tahini', 15), (6, 'Bread', 10),
(7, 'Extra Cheese', 30), (7, 'Pepsi', 25), (7, 'Garlic Dip', 15),
(8, 'Extra Chicken', 45), (8, 'Extra Cheese', 30), (8, 'Pepsi', 25),
(9, 'Extra Kebab', 80), (9, 'Tahini', 15), (9, 'Salad', 20), (9, 'Bread', 10),
(10, 'Extra Kofta', 70), (10, 'Salad', 20), (10, 'Tahini', 15),
(11, 'Extra Meat', 100), (11, 'Salad', 20), (11, 'Tahini', 15), (11, 'Water', 15);
