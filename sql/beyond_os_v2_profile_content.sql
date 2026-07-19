-- Beyond OS 2.0 profile onboarding and content studio
ALTER TABLE `profiles`
  ADD COLUMN IF NOT EXISTS `display_name` VARCHAR(100) DEFAULT NULL AFTER `user_id`,
  ADD COLUMN IF NOT EXISTS `interests` TEXT DEFAULT NULL AFTER `bio`,
  ADD COLUMN IF NOT EXISTS `goals` TEXT DEFAULT NULL AFTER `interests`,
  ADD COLUMN IF NOT EXISTS `profile_completed_at` DATETIME DEFAULT NULL AFTER `goals`;

CREATE TABLE IF NOT EXISTS `beyond_content` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `product` ENUM('french','math','dailybreath') NOT NULL,
  `content_type` VARCHAR(60) NOT NULL,
  `title` VARCHAR(190) NOT NULL,
  `body` TEXT NOT NULL,
  `metadata_json` LONGTEXT DEFAULT NULL,
  `scheduled_for` DATE DEFAULT NULL,
  `status` ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
  `created_by` INT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_content_delivery` (`product`,`status`,`scheduled_for`),
  CONSTRAINT `fk_content_admin` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
