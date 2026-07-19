-- Beyond OS v4.2 Incremental Database Update
-- Safe upgrade file for EXISTING databases.
-- Do NOT use this for brand-new installs; use database_v4_2_complete.sql instead.
-- Tested target: MariaDB 10.11+ / MySQL-compatible shared hosting.
-- Run inside phpMyAdmin after selecting your BeyondImagination database.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

START TRANSACTION;

-- --------------------------------------------------------
-- 1) Schema version tracking
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `schema_version` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `version` VARCHAR(30) NOT NULL,
  `notes` VARCHAR(255) DEFAULT NULL,
  `applied_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `version` (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 2) Users: Beyond ID + email verification compatibility
-- --------------------------------------------------------
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `first_name` VARCHAR(80) DEFAULT NULL AFTER `id`,
  ADD COLUMN IF NOT EXISTS `last_name` VARCHAR(80) DEFAULT NULL AFTER `first_name`,
  ADD COLUMN IF NOT EXISTS `name` VARCHAR(160) DEFAULT NULL AFTER `last_name`,
  ADD COLUMN IF NOT EXISTS `phone` VARCHAR(40) DEFAULT NULL AFTER `email`,
  ADD COLUMN IF NOT EXISTS `password` VARCHAR(255) DEFAULT NULL AFTER `phone`,
  ADD COLUMN IF NOT EXISTS `email_verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `password`,
  ADD COLUMN IF NOT EXISTS `verification_token` VARCHAR(255) DEFAULT NULL AFTER `email_verified`,
  ADD COLUMN IF NOT EXISTS `verification_sent_at` DATETIME DEFAULT NULL AFTER `verification_token`,
  ADD COLUMN IF NOT EXISTS `email_verified_at` DATETIME DEFAULT NULL AFTER `verification_sent_at`,
  ADD COLUMN IF NOT EXISTS `password_hash` VARCHAR(255) DEFAULT NULL AFTER `email_verified_at`,
  ADD COLUMN IF NOT EXISTS `role` VARCHAR(50) NOT NULL DEFAULT 'user' AFTER `password_hash`,
  ADD COLUMN IF NOT EXISTS `status` VARCHAR(30) NOT NULL DEFAULT 'active' AFTER `role`,
  ADD COLUMN IF NOT EXISTS `last_login` DATETIME DEFAULT NULL AFTER `status`,
  ADD COLUMN IF NOT EXISTS `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `last_login`,
  ADD COLUMN IF NOT EXISTS `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

CREATE INDEX IF NOT EXISTS `idx_users_email` ON `users` (`email`);
CREATE INDEX IF NOT EXISTS `idx_users_role` ON `users` (`role`);
CREATE INDEX IF NOT EXISTS `idx_users_status` ON `users` (`status`);
CREATE INDEX IF NOT EXISTS `idx_users_email_verified` ON `users` (`email_verified`);

-- --------------------------------------------------------
-- 3) Shared email verification table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `email_verifications` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(128) NOT NULL,
  `app` VARCHAR(50) NOT NULL DEFAULT 'beyond_id',
  `expires_at` DATETIME NOT NULL,
  `used_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`),
  KEY `email` (`email`),
  KEY `app` (`app`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 4) Protected SQL Console / Database Explorer v2
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `query_history` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `admin_user_id` INT DEFAULT NULL,
  `user_id` INT DEFAULT NULL,
  `query_text` LONGTEXT NOT NULL,
  `query_type` VARCHAR(30) DEFAULT NULL,
  `affected_rows` INT DEFAULT NULL,
  `execution_time_ms` INT DEFAULT NULL,
  `status` ENUM('success','error','blocked') DEFAULT 'success',
  `error_message` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `admin_user_id` (`admin_user_id`),
  KEY `user_id` (`user_id`),
  KEY `query_type` (`query_type`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `audit_log` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT DEFAULT NULL,
  `action` VARCHAR(120) NOT NULL,
  `ip_address` VARCHAR(64) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `details` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT DEFAULT NULL,
  `action` VARCHAR(255) DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 5) Beyond app registry / permissions foundation
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `apps` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(80) NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  `status` VARCHAR(40) NOT NULL DEFAULT 'coming_soon',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `apps` (`slug`, `name`, `status`, `created_at`) VALUES
('beyond-id', 'Beyond ID', 'active', NOW()),
('beyond-catering', 'Beyond Catering', 'active', NOW()),
('daily-breath', 'Daily Breath', 'coming_soon', NOW()),
('beyond-health', 'Beyond Health', 'coming_soon', NOW()),
('beyond-tv', 'Beyond TV', 'coming_soon', NOW()),
('beyond-math', 'Beyond Math', 'coming_soon', NOW()),
('beyond-finance', 'Beyond Finance', 'coming_soon', NOW()),
('beyond-sell', 'Beyond Sell', 'coming_soon', NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `status` = VALUES(`status`);

CREATE TABLE IF NOT EXISTS `user_apps` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `app_id` INT NOT NULL,
  `status` VARCHAR(40) NOT NULL DEFAULT 'active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_app_unique` (`user_id`, `app_id`),
  KEY `app_id` (`app_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80) NOT NULL,
  `slug` VARCHAR(80) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `roles` (`name`, `slug`, `created_at`) VALUES
('Super Admin', 'super_admin', NOW()),
('Admin', 'admin', NOW()),
('Vendor', 'vendor', NOW()),
('Customer', 'customer', NOW()),
('Developer', 'developer', NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(120) NOT NULL,
  `label` VARCHAR(160) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `permissions` (`slug`, `label`, `created_at`) VALUES
('admin.access', 'Access Admin Portal', NOW()),
('sql.read', 'Read SQL Console', NOW()),
('sql.write', 'Run Safe SQL Queries', NOW()),
('sql.dangerous', 'Run Dangerous SQL Queries', NOW()),
('vendors.manage', 'Manage Vendors', NOW()),
('billing.manage', 'Manage Billing', NOW())
ON DUPLICATE KEY UPDATE `label` = VALUES(`label`);

-- --------------------------------------------------------
-- 6) Beyond Catering vendor onboarding / website system
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `vendors` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `business_name` VARCHAR(160) NOT NULL,
  `owner_name` VARCHAR(160) NOT NULL DEFAULT '',
  `email` VARCHAR(190) NOT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `password_hash` VARCHAR(255) DEFAULT NULL,
  `plan` VARCHAR(60) DEFAULT 'premium_trial',
  `trial_ends_at` DATETIME DEFAULT NULL,
  `slug` VARCHAR(190) NOT NULL,
  `onboarding_step` INT DEFAULT 1,
  `website_status` VARCHAR(40) DEFAULT 'draft',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `status` VARCHAR(40) DEFAULT 'trial',
  `trial_started_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `slug` (`slug`),
  KEY `status` (`status`),
  KEY `plan` (`plan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `vendors`
  ADD COLUMN IF NOT EXISTS `owner_name` VARCHAR(160) NOT NULL DEFAULT '' AFTER `business_name`,
  ADD COLUMN IF NOT EXISTS `phone` VARCHAR(50) DEFAULT NULL AFTER `email`,
  ADD COLUMN IF NOT EXISTS `password_hash` VARCHAR(255) DEFAULT NULL AFTER `phone`,
  ADD COLUMN IF NOT EXISTS `plan` VARCHAR(60) DEFAULT 'premium_trial' AFTER `password_hash`,
  ADD COLUMN IF NOT EXISTS `trial_ends_at` DATETIME DEFAULT NULL AFTER `plan`,
  ADD COLUMN IF NOT EXISTS `slug` VARCHAR(190) DEFAULT NULL AFTER `trial_ends_at`,
  ADD COLUMN IF NOT EXISTS `onboarding_step` INT DEFAULT 1 AFTER `slug`,
  ADD COLUMN IF NOT EXISTS `website_status` VARCHAR(40) DEFAULT 'draft' AFTER `onboarding_step`,
  ADD COLUMN IF NOT EXISTS `status` VARCHAR(40) DEFAULT 'trial',
  ADD COLUMN IF NOT EXISTS `trial_started_at` DATETIME DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `vendor_media` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `vendor_id` INT NOT NULL,
  `file_type` ENUM('logo','hero','menu','gallery','document') DEFAULT 'gallery',
  `original_name` VARCHAR(255) NOT NULL,
  `stored_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(120) DEFAULT NULL,
  `file_size` INT DEFAULT 0,
  `usage_role` VARCHAR(60) DEFAULT 'gallery',
  `alt_text` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `file_type` (`file_type`),
  KEY `usage_role` (`usage_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `vendor_domains` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `vendor_id` INT NOT NULL,
  `domain_type` ENUM('subdomain','existing','included_lifetime') DEFAULT 'subdomain',
  `domain` VARCHAR(255) DEFAULT NULL,
  `subdomain` VARCHAR(255) DEFAULT NULL,
  `dns_status` ENUM('pending','connected','failed') DEFAULT 'pending',
  `ssl_status` ENUM('pending','active','failed') DEFAULT 'pending',
  `publish_status` ENUM('draft','published') DEFAULT 'draft',
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `domain` (`domain`),
  KEY `subdomain` (`subdomain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `vendor_sites` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `vendor_id` INT NOT NULL,
  `slug` VARCHAR(100) DEFAULT NULL,
  `theme` VARCHAR(100) DEFAULT 'modern-food',
  `site_title` VARCHAR(255) DEFAULT NULL,
  `tagline` VARCHAR(255) DEFAULT NULL,
  `hero_image` VARCHAR(255) DEFAULT NULL,
  `logo` VARCHAR(255) DEFAULT NULL,
  `about_text` TEXT DEFAULT NULL,
  `contact_email` VARCHAR(255) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `status` ENUM('draft','published') DEFAULT 'draft',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `slug` (`slug`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `website_versions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `vendor_id` INT NOT NULL,
  `version_number` INT NOT NULL,
  `title` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('draft','published','archived') DEFAULT 'draft',
  `snapshot_json` LONGTEXT DEFAULT NULL,
  `published_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `menu_categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `vendor_id` INT NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  `sort_order` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `vendor_id` INT NOT NULL,
  `category_id` INT DEFAULT NULL,
  `name` VARCHAR(160) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `image_path` VARCHAR(255) DEFAULT NULL,
  `is_available` TINYINT(1) DEFAULT 1,
  `sort_order` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `category_id` (`category_id`),
  KEY `is_available` (`is_available`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 7) Billing / Stripe / orders foundation
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `vendor_id` INT DEFAULT NULL,
  `user_id` INT DEFAULT NULL,
  `plan` VARCHAR(80) NOT NULL DEFAULT 'premium_trial',
  `status` VARCHAR(50) NOT NULL DEFAULT 'trialing',
  `stripe_customer_id` VARCHAR(255) DEFAULT NULL,
  `stripe_subscription_id` VARCHAR(255) DEFAULT NULL,
  `trial_starts_at` DATETIME DEFAULT NULL,
  `trial_ends_at` DATETIME DEFAULT NULL,
  `current_period_start` DATETIME DEFAULT NULL,
  `current_period_end` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `stripe_customer_id` (`stripe_customer_id`),
  KEY `stripe_subscription_id` (`stripe_subscription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `billing` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `vendor_id` INT DEFAULT NULL,
  `user_id` INT DEFAULT NULL,
  `plan` VARCHAR(80) DEFAULT NULL,
  `amount` DECIMAL(10,2) DEFAULT 0.00,
  `currency` VARCHAR(10) DEFAULT 'CAD',
  `status` VARCHAR(50) DEFAULT 'pending',
  `stripe_payment_intent_id` VARCHAR(255) DEFAULT NULL,
  `stripe_checkout_session_id` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `stripe_checkout_session_id` (`stripe_checkout_session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `vendor_id` INT DEFAULT NULL,
  `order_id` INT DEFAULT NULL,
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `currency` VARCHAR(10) DEFAULT 'CAD',
  `status` VARCHAR(50) DEFAULT 'pending',
  `provider` VARCHAR(50) DEFAULT 'stripe',
  `provider_payment_id` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `order_id` (`order_id`),
  KEY `status` (`status`),
  KEY `provider_payment_id` (`provider_payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `stripe_webhooks` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `event_id` VARCHAR(255) NOT NULL,
  `event_type` VARCHAR(255) DEFAULT NULL,
  `payload` LONGTEXT DEFAULT NULL,
  `processed` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`),
  KEY `event_type` (`event_type`),
  KEY `processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `vendor_id` INT NOT NULL,
  `customer_name` VARCHAR(160) DEFAULT NULL,
  `customer_email` VARCHAR(190) DEFAULT NULL,
  `customer_phone` VARCHAR(50) DEFAULT NULL,
  `order_status` VARCHAR(50) DEFAULT 'pending',
  `payment_status` VARCHAR(50) DEFAULT 'pending',
  `subtotal` DECIMAL(10,2) DEFAULT 0.00,
  `tax` DECIMAL(10,2) DEFAULT 0.00,
  `total` DECIMAL(10,2) DEFAULT 0.00,
  `notes` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `order_status` (`order_status`),
  KEY `payment_status` (`payment_status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 8) Analytics / subscribers / settings
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `analytics_events` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `vendor_id` INT DEFAULT NULL,
  `event_type` VARCHAR(80) DEFAULT NULL,
  `page` VARCHAR(120) DEFAULT NULL,
  `ip_hash` VARCHAR(128) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `event_type` (`event_type`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(190) NOT NULL,
  `source` VARCHAR(80) DEFAULT NULL,
  `status` VARCHAR(40) DEFAULT 'active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `early_access_subscribers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(190) NOT NULL,
  `app` VARCHAR(80) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_app` (`email`, `app`),
  KEY `app` (`app`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(120) NOT NULL,
  `setting_value` LONGTEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `settings` (`setting_key`, `setting_value`, `created_at`) VALUES
('beyond_os_version', '4.2', NOW()),
('email_verification_enabled', '1', NOW()),
('protected_sql_console_enabled', '1', NOW())
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`), `updated_at` = NOW();

-- --------------------------------------------------------
-- 9) Version stamp
-- --------------------------------------------------------
INSERT INTO `schema_version` (`version`, `notes`) VALUES
('4.2.0', 'Beyond OS v4.2 incremental update: email verification, Protected SQL Console v2, Beyond Catering onboarding, billing foundation')
ON DUPLICATE KEY UPDATE `notes` = VALUES(`notes`), `applied_at` = CURRENT_TIMESTAMP;

COMMIT;
