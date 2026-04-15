-- Elite Print database schema (MySQL 8+ / MariaDB 10.4+)
-- Import this file in phpMyAdmin or via mysql client.

SET sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
SET time_zone = '+00:00';

CREATE DATABASE IF NOT EXISTS elite_print
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE elite_print;

-- =========================
-- USERS / AUTH
-- =========================
CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(254) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  name VARCHAR(120) NULL,
  role ENUM('admin','staff','customer') NOT NULL DEFAULT 'customer',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS auth_sessions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  token_hash CHAR(64) NOT NULL,
  ip VARCHAR(45) NULL,
  user_agent VARCHAR(255) NULL,
  expires_at DATETIME NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  revoked_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_sessions_token_hash (token_hash),
  KEY idx_sessions_user_id (user_id),
  CONSTRAINT fk_sessions_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================
-- CONTENT TABLES
-- =========================
CREATE TABLE IF NOT EXISTS clients (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  website VARCHAR(255) NULL,
  logo_url VARCHAR(512) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_clients_name (name),
  KEY idx_clients_active_sort (is_active, sort_order, name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS faqs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  question VARCHAR(255) NOT NULL,
  answer TEXT NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_faqs_active_sort (is_active, sort_order)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS product_categories (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  description TEXT NULL,
  image_url VARCHAR(512) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_product_categories_name (name),
  KEY idx_categories_active_sort (is_active, sort_order, name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS product_items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(160) NOT NULL,
  description TEXT NULL,
  image_url VARCHAR(512) NULL,
  sku VARCHAR(80) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_product_items_sku (sku),
  KEY idx_product_items_category (category_id, is_active),
  CONSTRAINT fk_product_items_category
    FOREIGN KEY (category_id) REFERENCES product_categories(id)
    ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS enquiries (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  source ENUM('contact_form','whatsapp','other') NOT NULL DEFAULT 'contact_form',
  name VARCHAR(120) NOT NULL,
  email VARCHAR(254) NOT NULL,
  phone VARCHAR(40) NULL,
  message TEXT NULL,
  page VARCHAR(120) NULL,
  ip VARCHAR(45) NULL,
  user_agent VARCHAR(255) NULL,
  status ENUM('new','in_progress','closed','spam') NOT NULL DEFAULT 'new',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_enquiries_status_created (status, created_at),
  KEY idx_enquiries_email (email)
) ENGINE=InnoDB;

-- =========================
-- ORDERS / PRODUCTION / PAYMENTS
-- =========================
CREATE TABLE IF NOT EXISTS orders (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  customer_id BIGINT UNSIGNED NOT NULL,
  order_no VARCHAR(30) NOT NULL,
  status ENUM('new','processing','printing','ready','dispatched','delivered','cancelled') NOT NULL DEFAULT 'new',
  notes TEXT NULL,
  subtotal_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  total_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  currency CHAR(3) NOT NULL DEFAULT 'INR',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_orders_order_no (order_no),
  KEY idx_orders_customer (customer_id, created_at),
  KEY idx_orders_status (status, updated_at),
  CONSTRAINT fk_orders_customer
    FOREIGN KEY (customer_id) REFERENCES users(id)
    ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  product_name VARCHAR(200) NOT NULL,
  sku VARCHAR(80) NULL,
  quantity INT NOT NULL DEFAULT 1,
  unit_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  line_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_order_items_order (order_id),
  CONSTRAINT fk_order_items_order
    FOREIGN KEY (order_id) REFERENCES orders(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_status_history (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  old_status VARCHAR(30) NULL,
  new_status VARCHAR(30) NOT NULL,
  changed_by BIGINT UNSIGNED NULL,
  note VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_status_history_order (order_id, created_at),
  CONSTRAINT fk_status_history_order
    FOREIGN KEY (order_id) REFERENCES orders(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_status_history_user
    FOREIGN KEY (changed_by) REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payments (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NULL,
  customer_id BIGINT UNSIGNED NULL,
  amount DECIMAL(12,2) NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'INR',
  method ENUM('cash','upi','bank_transfer','cheque','other') NOT NULL DEFAULT 'cash',
  reference VARCHAR(120) NULL,
  note VARCHAR(255) NULL,
  received_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_payments_received (received_at),
  KEY idx_payments_order (order_id),
  KEY idx_payments_customer (customer_id),
  CONSTRAINT fk_payments_order
    FOREIGN KEY (order_id) REFERENCES orders(id)
    ON DELETE SET NULL,
  CONSTRAINT fk_payments_customer
    FOREIGN KEY (customer_id) REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================
-- SEED DATA (matches your current HTML)
-- =========================
INSERT IGNORE INTO clients (name, sort_order, is_active) VALUES
('Agro One', 10, 1),
('Farmline', 20, 1),
('SpiceCo', 30, 1),
('NutriPack', 40, 1),
('GreenHarvest', 50, 1),
('SnackWorks', 60, 1),
('PureGrain', 70, 1),
('FreshMart', 80, 1);

INSERT IGNORE INTO faqs (question, answer, sort_order, is_active) VALUES
('What is the minimum order quantity?', 'Thanks to cylinder-free digital printing, we can support smaller MOQs than conventional gravure setups. Exact minimums depend on size, material, and finishing — share your brief and we will confirm feasibility.', 10, 1),
('Which file formats do you accept?', 'We prefer vector artwork (AI, PDF, EPS) with outlined fonts and embedded images at print resolution. Our pre-press team can guide you to a print-ready file if your design needs minor adjustments.', 20, 1),
('How long does delivery take?', 'Typical production and dispatch fall in the range of roughly twenty to twenty-five working days after artwork approval, depending on quantity and complexity. Rush options may be available — ask when you place your enquiry.', 30, 1),
('Why choose Elite Print?', 'You avoid heavy cylinder costs, gain flexibility on quantities, and receive crisp digital output with vibrant colour. We combine technical know-how with responsive service — from agriculture inputs to FMCG launches.', 40, 1);

INSERT IGNORE INTO product_categories (name, description, image_url, sort_order, is_active) VALUES
('Pouch Printing', 'Pillow, stand-up, zipper, window, and gusset formats — ideal for food, snacks, spices, and dry goods with shelf-ready appeal.', 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=800&q=80', 10, 1),
('Mono Carton Boxes', 'Structural cartons with sharp folds and accurate registration — built for retail shelves, e-commerce, and bulk distribution.', 'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?auto=format&fit=crop&w=800&q=80', 20, 1),
('Stickers & Labels', 'Roll labels and sheet stickers with durable adhesives — perfect for bottles, jars, outer cartons, and promotional packs.', 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?auto=format&fit=crop&w=800&q=80', 30, 1),
('HDPE Non-Woven Bags', 'Lightweight, reusable carry bags with bold branding — suited for retail, exhibitions, and everyday promotions.', 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=800&q=80', 40, 1),
('BOPP Woven Bags', 'High-strength woven sacks with laminated BOPP graphics — trusted for grains, fertilisers, seeds, and industrial goods.', 'https://images.unsplash.com/photo-1625246333195-78d9c38ad449?auto=format&fit=crop&w=800&q=80', 50, 1);

-- Admin user is created by `admin/setup.php` so the password hash is correct.
-- After importing this SQL, visit: http://localhost/project/admin/setup.php

