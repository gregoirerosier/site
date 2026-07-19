CREATE TABLE IF NOT EXISTS dailybreath_content_revisions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  content_type VARCHAR(80) NOT NULL,
  content_key VARCHAR(190) NOT NULL,
  action VARCHAR(80) NOT NULL,
  payload_json JSON NOT NULL,
  created_by BIGINT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_dailybreath_revisions(content_type,content_key,created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
