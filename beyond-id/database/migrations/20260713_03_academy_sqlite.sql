-- DailyBreath Bible Academy catalog for SQLite 3
-- Apply to the same SQLite database used by Beyond ID.

PRAGMA foreign_keys = ON;
BEGIN IMMEDIATE;

CREATE TABLE IF NOT EXISTS academy_settings (
  setting_key TEXT NOT NULL PRIMARY KEY,
  setting_value TEXT NOT NULL,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS academy_age_groups (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  slug TEXT NOT NULL UNIQUE,
  name TEXT NOT NULL,
  min_age INTEGER NULL,
  max_age INTEGER NULL,
  icon TEXT NULL,
  sort_order INTEGER NOT NULL DEFAULT 0,
  is_active INTEGER NOT NULL DEFAULT 1 CHECK (is_active IN (0,1)),
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS academy_courses (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  slug TEXT NOT NULL UNIQUE,
  title TEXT NOT NULL,
  summary TEXT NULL,
  cover_image_url TEXT NULL,
  is_free INTEGER NOT NULL DEFAULT 0 CHECK (is_free IN (0,1)),
  is_published INTEGER NOT NULL DEFAULT 1 CHECK (is_published IN (0,1)),
  sort_order INTEGER NOT NULL DEFAULT 0,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS academy_course_age_groups (
  course_id INTEGER NOT NULL,
  age_group_id INTEGER NOT NULL,
  PRIMARY KEY (course_id, age_group_id),
  FOREIGN KEY (course_id) REFERENCES academy_courses(id) ON DELETE CASCADE,
  FOREIGN KEY (age_group_id) REFERENCES academy_age_groups(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS academy_lessons (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  course_id INTEGER NOT NULL,
  lesson_number INTEGER NOT NULL,
  title TEXT NOT NULL,
  lesson_type TEXT NOT NULL DEFAULT 'audio' CHECK (lesson_type IN ('audio','video','reading','activity')),
  media_url TEXT NULL,
  transcript TEXT NULL,
  duration_seconds INTEGER NULL,
  is_preview INTEGER NOT NULL DEFAULT 0 CHECK (is_preview IN (0,1)),
  is_published INTEGER NOT NULL DEFAULT 1 CHECK (is_published IN (0,1)),
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (course_id, lesson_number),
  FOREIGN KEY (course_id) REFERENCES academy_courses(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS academy_subscriptions (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL UNIQUE,
  provider TEXT NOT NULL DEFAULT 'stripe',
  provider_customer_id TEXT NULL,
  provider_subscription_id TEXT NULL,
  status TEXT NOT NULL DEFAULT 'active' CHECK (status IN ('trialing','active','past_due','canceled','expired')),
  current_period_start TEXT NULL,
  current_period_end TEXT NULL,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_academy_subscription_access
ON academy_subscriptions (user_id, status, current_period_end);

CREATE TABLE IF NOT EXISTS academy_progress (
  user_id INTEGER NOT NULL,
  lesson_id INTEGER NOT NULL,
  progress_seconds INTEGER NOT NULL DEFAULT 0,
  completed_at TEXT NULL,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, lesson_id),
  FOREIGN KEY (lesson_id) REFERENCES academy_lessons(id) ON DELETE CASCADE
);

INSERT INTO academy_settings (setting_key, setting_value) VALUES
  ('subscription_price_cents', '499'),
  ('subscription_currency', 'USD'),
  ('subscription_interval', 'month'),
  ('first_course_free', '1')
ON CONFLICT(setting_key) DO UPDATE SET
  setting_value = excluded.setting_value,
  updated_at = CURRENT_TIMESTAMP;

INSERT INTO academy_age_groups (slug, name, min_age, max_age, icon, sort_order) VALUES
  ('preschool', 'Preschool', 3, 5, '🧸', 10),
  ('kids', 'Kids', 6, 9, '🌱', 20),
  ('preteen', 'Preteen', 10, 12, '📖', 30),
  ('teen', 'Teen', 13, 17, '✨', 40),
  ('adult', 'Adult', 18, NULL, '🕊️', 50)
ON CONFLICT(slug) DO UPDATE SET
  name = excluded.name,
  min_age = excluded.min_age,
  max_age = excluded.max_age,
  icon = excluded.icon,
  sort_order = excluded.sort_order,
  is_active = 1,
  updated_at = CURRENT_TIMESTAMP;

INSERT INTO academy_courses (slug, title, summary, is_free, is_published, sort_order) VALUES
  ('commandments-audio-journey', 'The Commandments: An Audio Journey',
   'A free starter course with 12 short, age-adapted audio lessons and read-along text.', 1, 1, 10),
  ('bible-foundations', 'Bible Foundations',
   'A guided introduction to Scripture, prayer, faith, and everyday discipleship.', 0, 1, 20)
ON CONFLICT(slug) DO UPDATE SET
  title = excluded.title,
  summary = excluded.summary,
  is_free = excluded.is_free,
  is_published = excluded.is_published,
  sort_order = excluded.sort_order,
  updated_at = CURRENT_TIMESTAMP;

INSERT OR IGNORE INTO academy_course_age_groups (course_id, age_group_id)
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
ON CONFLICT(course_id, lesson_number) DO UPDATE SET
  title = excluded.title,
  lesson_type = excluded.lesson_type,
  is_preview = excluded.is_preview,
  is_published = excluded.is_published,
  updated_at = CURRENT_TIMESTAMP;

COMMIT;

