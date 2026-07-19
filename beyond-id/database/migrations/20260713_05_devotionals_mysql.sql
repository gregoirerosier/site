-- DailyBreath devotionals — MySQL/MariaDB
CREATE TABLE IF NOT EXISTS devotionals (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(180) NOT NULL,
  locale VARCHAR(16) NOT NULL DEFAULT 'en',
  title VARCHAR(220) NOT NULL,
  excerpt TEXT NULL,
  body LONGTEXT NOT NULL,
  scripture_reference VARCHAR(180) NULL,
  duration_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 5,
  audio_url VARCHAR(500) NULL,
  image_url VARCHAR(500) NULL,
  publish_date DATE NOT NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_devotional_slug_locale (slug, locale),
  KEY idx_devotional_publish (is_published, locale, publish_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS devotional_progress (
  user_id BIGINT UNSIGNED NOT NULL,
  devotional_id BIGINT UNSIGNED NOT NULL,
  progress_percent TINYINT UNSIGNED NOT NULL DEFAULT 0,
  is_bookmarked TINYINT(1) NOT NULL DEFAULT 0,
  completed_at DATETIME NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, devotional_id),
  CONSTRAINT fk_devotional_progress FOREIGN KEY (devotional_id) REFERENCES devotionals(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO devotionals
  (slug, locale, title, excerpt, body, scripture_reference, duration_minutes, publish_date, is_published)
VALUES
  ('walk-in-quiet-confidence', 'en', 'Walk in Quiet Confidence',
   'Make room for stillness and remember that God is present before you take the next step.',
   'Pause before the noise of the day takes over. Stillness is not wasted time; it is a place where trust can grow. You do not need every answer before taking the next faithful step. Breathe deeply, release what you cannot control, and remember that God is already present in the moment ahead.',
   'Psalm 46:10', 5, CURRENT_DATE, 1)
ON DUPLICATE KEY UPDATE
  title = VALUES(title), excerpt = VALUES(excerpt), body = VALUES(body),
  scripture_reference = VALUES(scripture_reference), duration_minutes = VALUES(duration_minutes),
  publish_date = VALUES(publish_date), is_published = VALUES(is_published);

