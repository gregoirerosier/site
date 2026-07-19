-- Beyond Bible narration preferences and progress — MySQL/MariaDB
CREATE TABLE IF NOT EXISTS bible_narration_preferences (
  user_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
  language_code VARCHAR(16) NOT NULL DEFAULT 'en-US',
  voice_name VARCHAR(160) NULL,
  playback_rate DECIMAL(3,2) NOT NULL DEFAULT 1.00,
  pitch DECIMAL(3,2) NOT NULL DEFAULT 1.00,
  auto_advance TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bible_narration_progress (
  user_id BIGINT UNSIGNED NOT NULL,
  translation_code VARCHAR(24) NOT NULL,
  book_code VARCHAR(24) NOT NULL,
  chapter_number SMALLINT UNSIGNED NOT NULL,
  verse_number SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  character_offset INT UNSIGNED NOT NULL DEFAULT 0,
  completed TINYINT(1) NOT NULL DEFAULT 0,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, translation_code, book_code, chapter_number),
  KEY idx_narration_resume (user_id, updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

