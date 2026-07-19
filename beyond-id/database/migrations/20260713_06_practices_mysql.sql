-- DailyBreath spiritual practices — MySQL/MariaDB
CREATE TABLE IF NOT EXISTS breathing_exercises (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(120) NOT NULL,
  locale VARCHAR(16) NOT NULL DEFAULT 'en',
  title VARCHAR(180) NOT NULL,
  scripture_reference VARCHAR(180) NULL,
  inhale_seconds TINYINT UNSIGNED NOT NULL DEFAULT 4,
  hold_seconds TINYINT UNSIGNED NOT NULL DEFAULT 0,
  exhale_seconds TINYINT UNSIGNED NOT NULL DEFAULT 6,
  cycles TINYINT UNSIGNED NOT NULL DEFAULT 8,
  prompt_text TEXT NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_breathing_slug_locale (slug, locale)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS breathing_sessions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  exercise_id INT UNSIGNED NOT NULL,
  completed_cycles SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  duration_seconds INT UNSIGNED NOT NULL DEFAULT 0,
  completed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_breathing_user_date (user_id, completed_at),
  CONSTRAINT fk_breathing_session_exercise FOREIGN KEY (exercise_id) REFERENCES breathing_exercises(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS guided_prayers (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(160) NOT NULL,
  locale VARCHAR(16) NOT NULL DEFAULT 'en',
  category VARCHAR(80) NOT NULL,
  title VARCHAR(200) NOT NULL,
  prayer_text LONGTEXT NOT NULL,
  scripture_reference VARCHAR(180) NULL,
  audio_url VARCHAR(500) NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_prayer_slug_locale (slug, locale),
  KEY idx_prayer_category (locale, category, is_published)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reflection_prompts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(160) NOT NULL,
  locale VARCHAR(16) NOT NULL DEFAULT 'en',
  prompt_text TEXT NOT NULL,
  scripture_reference VARCHAR(180) NULL,
  prompt_date DATE NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_reflection_prompt_locale (slug, locale)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reflection_journal_entries (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  prompt_id BIGINT UNSIGNED NULL,
  content_ciphertext LONGTEXT NOT NULL,
  encryption_version SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  mood VARCHAR(40) NULL,
  entry_date DATE NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_reflection_user_date (user_id, entry_date),
  CONSTRAINT fk_journal_prompt FOREIGN KEY (prompt_id) REFERENCES reflection_prompts(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS weekly_challenges (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(160) NOT NULL,
  locale VARCHAR(16) NOT NULL DEFAULT 'en',
  title VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  scripture_reference VARCHAR(180) NULL,
  starts_on DATE NOT NULL,
  ends_on DATE NOT NULL,
  target_count SMALLINT UNSIGNED NOT NULL DEFAULT 7,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_challenge_slug_locale (slug, locale),
  KEY idx_challenge_dates (locale, is_published, starts_on, ends_on)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS weekly_challenge_progress (
  user_id BIGINT UNSIGNED NOT NULL,
  challenge_id BIGINT UNSIGNED NOT NULL,
  completed_count SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  completed_at DATETIME NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, challenge_id),
  CONSTRAINT fk_weekly_progress_challenge FOREIGN KEY (challenge_id) REFERENCES weekly_challenges(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO breathing_exercises (slug, locale, title, scripture_reference, inhale_seconds, hold_seconds, exhale_seconds, cycles, prompt_text, sort_order)
VALUES ('peace-breath', 'en', 'Peace Breath', 'Psalm 46:10', 4, 0, 6, 8, 'Breathe in: Be still. Breathe out: Know that God is near.', 10)
ON DUPLICATE KEY UPDATE title=VALUES(title), scripture_reference=VALUES(scripture_reference), inhale_seconds=VALUES(inhale_seconds), hold_seconds=VALUES(hold_seconds), exhale_seconds=VALUES(exhale_seconds), cycles=VALUES(cycles), prompt_text=VALUES(prompt_text), is_published=1;

INSERT INTO guided_prayers (slug, locale, category, title, prayer_text, scripture_reference, sort_order) VALUES
('prayer-for-guidance','en','guidance','A Prayer for Guidance','God, quiet the noise around me and help me recognize the next faithful step. Give me wisdom, patience, and courage to follow where You lead.','Proverbs 3:5–6',10),
('prayer-for-peace','en','anxiety','A Prayer for Peace','God, meet me in this anxious moment. Steady my breathing, settle my thoughts, and remind me that I am not alone.','Philippians 4:6–7',20),
('prayer-for-family','en','family','A Prayer for Family','God, protect and strengthen my family. Help us speak with kindness, forgive freely, and care for one another with patience.','Colossians 3:13–14',30),
('prayer-of-gratitude','en','gratitude','A Prayer of Gratitude','God, open my eyes to the gifts already surrounding me. Let gratitude shape my words, choices, and relationships today.','1 Thessalonians 5:18',40)
ON DUPLICATE KEY UPDATE category=VALUES(category), title=VALUES(title), prayer_text=VALUES(prayer_text), scripture_reference=VALUES(scripture_reference), sort_order=VALUES(sort_order), is_published=1;

INSERT INTO reflection_prompts (slug, locale, prompt_text, scripture_reference, prompt_date)
VALUES ('where-do-you-need-stillness','en','Where do you need to choose stillness and trust today?','Psalm 46:10',CURRENT_DATE)
ON DUPLICATE KEY UPDATE prompt_text=VALUES(prompt_text), scripture_reference=VALUES(scripture_reference), prompt_date=VALUES(prompt_date), is_published=1;

INSERT INTO weekly_challenges (slug, locale, title, description, scripture_reference, starts_on, ends_on, target_count)
VALUES ('seven-days-of-gratitude','en','Seven Days of Gratitude','Write down one specific gift each day and thank someone who made a difference in your life.','1 Thessalonians 5:18',CURRENT_DATE,DATE_ADD(CURRENT_DATE, INTERVAL 6 DAY),7)
ON DUPLICATE KEY UPDATE title=VALUES(title), description=VALUES(description), scripture_reference=VALUES(scripture_reference), starts_on=VALUES(starts_on), ends_on=VALUES(ends_on), target_count=VALUES(target_count), is_published=1;

