-- Beyond ID secure Remember Me tokens — MySQL/MariaDB
CREATE TABLE IF NOT EXISTS auth_remember_tokens (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  selector CHAR(24) NOT NULL,
  validator_hash CHAR(64) NOT NULL,
  user_agent_hash CHAR(64) NULL,
  expires_at DATETIME NOT NULL,
  last_used_at DATETIME NULL,
  revoked_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_remember_selector (selector),
  KEY idx_remember_user (user_id),
  KEY idx_remember_expiry (expires_at, revoked_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

