CREATE TABLE IF NOT EXISTS academy_stripe_events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  stripe_event_id VARCHAR(255) NOT NULL UNIQUE,
  event_type VARCHAR(120) NOT NULL,
  processed TINYINT(1) NOT NULL DEFAULT 0,
  payload_json JSON NOT NULL,
  last_error TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  processed_at TIMESTAMP NULL,
  INDEX idx_academy_stripe_processed(processed,created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
