CREATE TABLE IF NOT EXISTS social_identities (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  provider VARCHAR(32) NOT NULL,
  provider_user_id VARCHAR(191) NOT NULL,
  email VARCHAR(190) DEFAULT NULL,
  display_name VARCHAR(190) DEFAULT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_social_provider_subject (provider, provider_user_id),
  UNIQUE KEY uq_social_user_provider (user_id, provider),
  CONSTRAINT fk_social_identity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
