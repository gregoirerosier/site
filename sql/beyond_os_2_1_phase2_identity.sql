-- Beyond OS 2.1 Beta — Phase 2 Identity migration
-- Additive migration. Back up the database before import.

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS preferred_locale VARCHAR(12) NOT NULL DEFAULT 'en' AFTER role,
  ADD COLUMN IF NOT EXISTS timezone VARCHAR(64) NOT NULL DEFAULT 'America/Vancouver' AFTER preferred_locale,
  ADD COLUMN IF NOT EXISTS last_login_at DATETIME DEFAULT NULL AFTER timezone,
  ADD COLUMN IF NOT EXISTS last_login_ip VARCHAR(45) DEFAULT NULL AFTER last_login_at;

ALTER TABLE profiles
  ADD COLUMN IF NOT EXISTS display_name VARCHAR(100) DEFAULT NULL AFTER user_id,
  ADD COLUMN IF NOT EXISTS interests TEXT DEFAULT NULL AFTER bio,
  ADD COLUMN IF NOT EXISTS goals TEXT DEFAULT NULL AFTER interests,
  ADD COLUMN IF NOT EXISTS profile_completed_at DATETIME DEFAULT NULL AFTER goals;

CREATE TABLE IF NOT EXISTS user_sessions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  session_token_hash CHAR(64) NOT NULL,
  ip_address VARCHAR(45) DEFAULT NULL,
  user_agent VARCHAR(500) DEFAULT NULL,
  last_seen_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME NOT NULL,
  revoked_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_user_session_token (session_token_hash),
  KEY idx_user_sessions_user (user_id, revoked_at, expires_at),
  CONSTRAINT fk_user_sessions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_notifications (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  type VARCHAR(40) NOT NULL DEFAULT 'system',
  title VARCHAR(180) NOT NULL,
  body TEXT NOT NULL,
  action_url VARCHAR(500) DEFAULT NULL,
  read_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_notifications_user (user_id, read_at, created_at),
  CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS connected_apps (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  app_slug VARCHAR(80) NOT NULL,
  permissions_json LONGTEXT DEFAULT NULL,
  connected_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_used_at DATETIME DEFAULT NULL,
  revoked_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_connected_app (user_id, app_slug),
  KEY idx_connected_apps_user (user_id, revoked_at),
  CONSTRAINT fk_connected_apps_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_preferences (
  user_id INT NOT NULL,
  theme ENUM('system','light','dark') NOT NULL DEFAULT 'system',
  email_notifications TINYINT(1) NOT NULL DEFAULT 1,
  in_app_notifications TINYINT(1) NOT NULL DEFAULT 1,
  marketing_emails TINYINT(1) NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id),
  CONSTRAINT fk_user_preferences_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ensure existing users have shared records.
INSERT IGNORE INTO beyond_wallets (user_id,balance,currency,status)
SELECT id,0,'BITS','active' FROM users;

INSERT IGNORE INTO user_preferences (user_id)
SELECT id FROM users;
