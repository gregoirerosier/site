-- DailyBreath Bible Academy catalog for MySQL/MariaDB (phpMyAdmin)
-- Import into the same database used by Beyond ID.

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS academy_settings (
  setting_key VARCHAR(80) NOT NULL PRIMARY KEY,
  setting_value VARCHAR(255) NOT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS academy_age_groups (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(40) NOT NULL UNIQUE,
  name VARCHAR(80) NOT NULL,
  min_age TINYINT UNSIGNED NULL,
  max_age TINYINT UNSIGNED NULL,
  icon VARCHAR(20) NULL,
  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS academy_courses (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(120) NOT NULL UNIQUE,
  title VARCHAR(180) NOT NULL,
  summary TEXT NULL,
  cover_image_url VARCHAR(500) NULL,
  is_free TINYINT(1) NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS academy_course_age_groups (
  course_id INT UNSIGNED NOT NULL,
  age_group_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (course_id, age_group_id),
  CONSTRAINT fk_acag_course FOREIGN KEY (course_id) REFERENCES academy_courses(id) ON DELETE CASCADE,
  CONSTRAINT fk_acag_age FOREIGN KEY (age_group_id) REFERENCES academy_age_groups(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS academy_lessons (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  course_id INT UNSIGNED NOT NULL,
  lesson_number SMALLINT UNSIGNED NOT NULL,
  title VARCHAR(180) NOT NULL,
  lesson_type ENUM('audio','video','reading','activity') NOT NULL DEFAULT 'audio',
  media_url VARCHAR(500) NULL,
  transcript TEXT NULL,
  duration_seconds INT UNSIGNED NULL,
  is_preview TINYINT(1) NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_course_lesson_number (course_id, lesson_number),
  CONSTRAINT fk_lesson_course FOREIGN KEY (course_id) REFERENCES academy_courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS academy_subscriptions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  provider VARCHAR(40) NOT NULL DEFAULT 'stripe',
  provider_customer_id VARCHAR(255) NULL,
  provider_subscription_id VARCHAR(255) NULL,
  status ENUM('trialing','active','past_due','canceled','expired') NOT NULL DEFAULT 'active',
  current_period_start DATETIME NULL,
  current_period_end DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_academy_user (user_id),
  KEY idx_academy_subscription_access (user_id, status, current_period_end)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS academy_progress (
  user_id BIGINT UNSIGNED NOT NULL,
  lesson_id INT UNSIGNED NOT NULL,
  progress_seconds INT UNSIGNED NOT NULL DEFAULT 0,
  completed_at DATETIME NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, lesson_id),
  CONSTRAINT fk_progress_lesson FOREIGN KEY (lesson_id) REFERENCES academy_lessons(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO academy_settings (setting_key, setting_value) VALUES
  ('subscription_price_cents', '499'),
  ('subscription_currency', 'USD'),
  ('subscription_interval', 'month'),
  ('first_course_free', '1')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

INSERT INTO academy_age_groups (slug, name, min_age, max_age, icon, sort_order) VALUES
  ('preschool', 'Preschool', 3, 5, '🧸', 10),
  ('kids', 'Kids', 6, 9, '🌱', 20),
  ('preteen', 'Preteen', 10, 12, '📖', 30),
  ('teen', 'Teen', 13, 17, '✨', 40),
  ('adult', 'Adult', 18, NULL, '🕊️', 50)
ON DUPLICATE KEY UPDATE
  name = VALUES(name), min_age = VALUES(min_age), max_age = VALUES(max_age),
  icon = VALUES(icon), sort_order = VALUES(sort_order), is_active = 1;

INSERT INTO academy_courses (slug, title, summary, is_free, is_published, sort_order) VALUES
  ('commandments-audio-journey', 'The Commandments: An Audio Journey',
   'A free starter course with 12 short, age-adapted audio lessons and read-along text.', 1, 1, 10),
  ('bible-foundations', 'Bible Foundations',
   'A guided introduction to Scripture, prayer, faith, and everyday discipleship.', 0, 1, 20)
ON DUPLICATE KEY UPDATE
  title = VALUES(title), summary = VALUES(summary), is_free = VALUES(is_free),
  is_published = VALUES(is_published), sort_order = VALUES(sort_order);

INSERT IGNORE INTO academy_course_age_groups (course_id, age_group_id)
SELECT c.id, a.id
FROM academy_courses c
CROSS JOIN academy_age_groups a
WHERE c.slug IN ('commandments-audio-journey', 'bible-foundations');

INSERT INTO academy_lessons
  (course_id, lesson_number, title, lesson_type, is_preview, is_published)
SELECT c.id, x.lesson_number, x.title, 'audio', 1, 1
FROM academy_courses c
JOIN (
  SELECT 1 lesson_number, 'God Gives Good Guidance' title UNION ALL
  SELECT 2, 'Put God First' UNION ALL
  SELECT 3, 'Honor God’s Name' UNION ALL
  SELECT 4, 'Make Time for Worship and Rest' UNION ALL
  SELECT 5, 'Honor Your Father and Mother' UNION ALL
  SELECT 6, 'Choose Life and Kindness' UNION ALL
  SELECT 7, 'Keep Promises Faithfully' UNION ALL
  SELECT 8, 'Respect What Belongs to Others' UNION ALL
  SELECT 9, 'Tell the Truth' UNION ALL
  SELECT 10, 'Practice Contentment' UNION ALL
  SELECT 11, 'Love God with Your Whole Life' UNION ALL
  SELECT 12, 'Love Your Neighbor as Yourself'
) x
WHERE c.slug = 'commandments-audio-journey'
ON DUPLICATE KEY UPDATE
  title = VALUES(title), lesson_type = VALUES(lesson_type),
  is_preview = VALUES(is_preview), is_published = VALUES(is_published);

