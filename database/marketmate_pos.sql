CREATE DATABASE IF NOT EXISTS marketmate_pos
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE marketmate_pos;

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
    avatar VARCHAR(255) NULL
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
