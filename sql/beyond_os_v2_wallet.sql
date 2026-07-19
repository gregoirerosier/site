-- Beyond OS 2.0 shared wallet migration
CREATE TABLE IF NOT EXISTS `beyond_wallets` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `balance` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `currency` VARCHAR(12) NOT NULL DEFAULT 'BITS',
  `status` ENUM('active','locked','closed') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_beyond_wallet_user` (`user_id`),
  CONSTRAINT `fk_beyond_wallet_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `beyond_wallet_transactions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `wallet_id` BIGINT UNSIGNED NOT NULL,
  `amount` DECIMAL(18,2) NOT NULL,
  `type` ENUM('credit','debit') NOT NULL,
  `app_slug` VARCHAR(80) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `idempotency_key` VARCHAR(120) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_wallet_idempotency` (`idempotency_key`),
  KEY `idx_wallet_created` (`wallet_id`,`created_at`),
  CONSTRAINT `fk_wallet_transaction` FOREIGN KEY (`wallet_id`) REFERENCES `beyond_wallets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
