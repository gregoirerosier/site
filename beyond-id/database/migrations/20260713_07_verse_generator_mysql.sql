-- Admin Verse of the Day generator — MySQL/MariaDB
CREATE TABLE IF NOT EXISTS verse_day_posts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  publish_date DATE NOT NULL,
  locale VARCHAR(16) NOT NULL DEFAULT 'en',
  translation_code VARCHAR(24) NOT NULL DEFAULT 'KJV',
  heading VARCHAR(100) NOT NULL DEFAULT 'VERSE OF THE DAY',
  verse_text TEXT NOT NULL,
  scripture_reference VARCHAR(180) NOT NULL,
  weekday_label VARCHAR(30) NULL,
  date_label VARCHAR(50) NULL,
  footer_message VARCHAR(180) NULL,
  background_asset_url VARCHAR(500) NOT NULL,
  rendered_asset_url VARCHAR(500) NULL,
  show_footer TINYINT(1) NOT NULL DEFAULT 1,
  show_frame TINYINT(1) NOT NULL DEFAULT 1,
  status ENUM('draft','scheduled','published','archived') NOT NULL DEFAULT 'draft',
  created_by BIGINT UNSIGNED NULL,
  published_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_verse_day_locale (publish_date, locale),
  KEY idx_verse_day_status (status, publish_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

