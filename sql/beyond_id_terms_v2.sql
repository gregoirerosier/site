-- Beyond ID 2.0 consent audit fields
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `terms_accepted_at` DATETIME DEFAULT NULL AFTER `email_verified_at`,
  ADD COLUMN IF NOT EXISTS `terms_version` VARCHAR(20) DEFAULT NULL AFTER `terms_accepted_at`;
