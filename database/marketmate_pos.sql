-- Import this schema into an empty MySQL database selected by your hosting provider.

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address VARCHAR(150),
    customer_type VARCHAR(20) NOT NULL DEFAULT 'Regular',
    loyalty_points INT NOT NULL DEFAULT 0,
    account_status VARCHAR(10) NOT NULL DEFAULT 'Active',
    created_at DATETIME NOT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    password_hash VARCHAR(255),
    role VARCHAR(20) NOT NULL DEFAULT 'Staff',
    account_status VARCHAR(10) NOT NULL DEFAULT 'Active',
    last_login DATETIME,
    created_at DATETIME NOT NULL,
    avatar MEDIUMTEXT NULL
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    image MEDIUMTEXT,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL
);

CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    customer_id INT NULL,
    sold_by INT NOT NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX (created_at),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (sold_by) REFERENCES users(id)
);

CREATE TABLE ci_sessions (
    id VARCHAR(128) NOT NULL PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    data BLOB NOT NULL,
    KEY ci_sessions_timestamp (timestamp)
);

INSERT INTO customers
    (id, full_name, email, phone, address, customer_type, loyalty_points, account_status, created_at)
VALUES
    (1, 'Elisha Cancino', 'Celisha@gmail.com', '0917-000-0001', 'Makati City', 'Regular', 10, 'Active', '2026-09-25 07:49:29'),
    (2, 'Angelo Pineda', 'Pangelo@gmail.com', '0917-000-0002', 'Occidental Mindoro', 'Member', 45, 'Active', '2026-09-25 07:49:29'),
    (3, 'Angel Tolentino', 'Tangel@gmail.com', '0917-000-0003', 'Valenzuela City', 'Regular', 0, 'Active', '2026-09-25 07:49:29'),
    (4, 'Raquel Casis', 'Craquel@gmail.com', '0917-000-0004', 'Virginia State', 'Member', 100, 'Active', '2026-09-25 07:49:29'),
    (5, 'Aisanee Jabedo', 'Jaisanee@gmail.com', '0917-000-0005', 'Cebu City', 'Regular', 5, 'Inactive', '2026-09-25 07:49:29'),
    (6, 'Nicole Caluag', 'Ccaluag@gmail.com', '0917-000-0006', 'New York State', 'Member', 20, 'Active', '2026-09-25 09:03:56'),
    (7, 'Elon Musk', 'Melon@gmail.com', '0917-000-0007', 'California Usa', 'Regular', 0, 'Active', '2026-09-25 09:03:56'),
    (8, 'Mark Zuckenberg', 'Zmark@gmail.com', '0917-000-0008', 'Ontario City', 'Member', 60, 'Active', '2026-09-25 09:03:56'),
    (9, 'Small Laude ', 'Lsmall@gmail.com', '0917-000-0009', 'Hawaii Island', 'Regular', 8, 'Inactive', '2026-09-25 09:03:56'),
    (10, 'Rey Parks', 'Rpark@gmail.com', '0917-000-0010', 'New York State', 'Member', 30, 'Active', '2026-09-25 09:03:56');

INSERT INTO users
    (id, username, full_name, email, phone, password_hash, role, account_status, created_at)
VALUES
    (1, 'admin01', 'Joby Miranda', 'staff.joby@gmail.com', '0928-000-0001', '$2y$10$k/MX23E610/L8141XsWpUOUKVIiJrXU8111JatYOa/5Aihgi2DxNq', 'Admin', 'Active', '2026-09-25 07:49:29'),
    (2, 'manager01', 'Lay Natawat', 'staff.lay@gmail.com', '0928-000-0002', '$2y$10$kkG.XCToiQI4oL1RbB/W9uQpm/kBj3cNs.zYp7U4kQ4LnvNlwK1Pe', 'Manager', 'Active', '2026-09-25 07:49:29'),
    (3, 'cashier01', 'Henrich Jones', 'staff.henrich@gmail.com', '0928-000-0003', '$2y$10$fH2kCqqkNm.dpbVKvpH1zeLBks2E7fccvv375HDC3h0WWhe7eK7SC', 'Cashier', 'Active', '2026-09-25 07:49:29'),
    (4, 'cashier02', 'Emily Lalalta', 'staff.lalata@gmail.com', '0928-000-0004', '$2y$10$5aYynjdlr02eZ48zlT0MzewPv3PoDzOGgsI9Qt1GW1XaDUFO1OP76', 'Cashier', 'Inactive', '2026-09-25 07:49:29'),
    (5, 'inventory01', 'Fourth Pirapat', 'staff.fourth@gmail.com', '0928-000-0005', '$2y$10$LYQmRht3TcyksEsLkXvHgu8xiBZXzyCm4bqpSdT.4pXjriPXlTtc6', 'Inventory', 'Active', '2026-09-25 07:49:29'),
    (6, 'cashier03', 'Larry Page', 'staff.larry@gmail.com', '0928-000-0006', '$2y$10$SJuiNhQ7smKBn68AgAdoweNq1F6./826mEfCXzQ0SXorgAN8FxwOC', 'Cashier', 'Active', '2026-09-25 09:03:56'),
    (7, 'cashier04', 'Jeff Bezos', 'staff.bezos@gmail.com', '0928-000-0007', '$2y$10$WHDYm6hLlFNBRa7cOm8HSu9Zo1sPieDPrAEVPtDI7ZaNBwvfkaDPe', 'Cashier', 'Active', '2026-09-25 09:03:56'),
    (8, 'inventory02', 'Sergey Bin', 'staff.sergey@gmail.com', '0928-000-0008', '$2y$10$5WciuOTNtL4wLgbNSeh5O.NL5mtx7hGWFtOiQvObmWRXCSwyxnHoa', 'Inventory', 'Active', '2026-09-25 09:03:56'),
    (9, 'supervisor01', 'Michael Dell', 'staff.michael@gmail.com', '0928-000-0009', '$2y$10$sgMMC3mHOB5GZEPPlN8ML.HF4eXmiu6nRPH4e4llI6d/uGoroCcN.', 'Supervisor', 'Active', '2026-09-25 09:03:56'),
    (10, 'staff01', 'Larry Elison', 'staff.larry@gmail.com', '0928-000-0010', '$2y$10$G7esNlrclBfCQlNH0Y8eRey/7Z31jOV3QKIWs.qnquMkr18Ekc1Ze', 'Staff', 'Inactive', '2026-09-25 09:03:56');

INSERT INTO products (name, price, stock_quantity, image, is_active, created_at) VALUES
    ('Wireless Mouse', 649.00, 24, 'wireless-mouse.jpg', 1, '2026-10-06 09:00:00'),
    ('USB-C Charging Cable', 299.00, 36, 'usb-c-charging-cable.jpg', 1, '2026-10-06 09:00:00'),
    ('65W GaN Wall Charger', 1399.00, 18, '65w-gan-wall-charger.jpg', 1, '2026-10-06 09:00:00'),
    ('10,000mAh Power Bank', 1599.00, 13, '10000mah-power-bank.jpg', 1, '2026-10-06 09:00:00'),
    ('Wireless Earbuds', 2199.00, 15, 'wireless-earbuds.jpg', 1, '2026-10-06 09:00:00'),
    ('Compact Mechanical Keyboard', 2699.00, 9, 'compact-mechanical-keyboard-photo.jpg', 1, '2026-10-06 09:00:00'),
    ('Full HD USB Webcam', 1799.00, 11, 'full-hd-usb-webcam.jpg', 1, '2026-10-06 09:00:00'),
    ('Foldable Phone Stand', 225.00, 12, 'foldable-phone-stand.jpg', 1, '2026-10-06 09:00:00'),
    ('Mini Bluetooth Speaker', 1299.00, 8, 'mini-bluetooth-speaker.jpg', 1, '2026-10-06 09:00:00'),
    ('Portable SSD 512GB', 3299.00, 7, 'portable-ssd-512gb.jpg', 1, '2026-10-06 09:00:00'),
    ('7-Port USB Hub', 899.00, 16, '7-port-usb-hub.jpg', 1, '2026-10-06 09:00:00'),
    ('Over-Ear Wireless Headphones', 3499.00, 10, 'over-ear-wireless-headphones.jpg', 1, '2026-10-06 09:00:00'),
    ('Laptop Sleeve 14-inch', 749.00, 20, 'laptop-sleeve-14-inch.jpg', 1, '2026-10-06 09:00:00'),
    ('Portable Monitor 15.6-inch', 7999.00, 5, 'portable-monitor-15-6-inch.jpg', 1, '2026-10-06 09:00:00'),
    ('USB-C Docking Station', 3999.00, 8, 'usb-c-docking-station.jpg', 1, '2026-10-06 09:00:00'),
    ('Smart Desk Lamp', 1899.00, 12, 'smart-desk-lamp.jpg', 1, '2026-10-06 09:00:00'),
    ('Wireless Presenter', 999.00, 14, 'wireless-presenter.jpg', 1, '2026-10-06 09:00:00'),
    ('4K HDMI Cable', 399.00, 30, '4k-hdmi-cable.jpg', 1, '2026-10-06 09:00:00'),
    ('2TB External Hard Drive', 4599.00, 6, '2tb-external-hard-drive.jpg', 1, '2026-10-06 09:00:00'),
    ('Rechargeable Desk Fan', 1199.00, 15, 'rechargeable-desk-fan.jpg', 1, '2026-10-06 09:00:00');
