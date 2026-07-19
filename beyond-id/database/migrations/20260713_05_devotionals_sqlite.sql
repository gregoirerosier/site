-- DailyBreath devotionals — SQLite 3
PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS devotionals (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  slug TEXT NOT NULL,
  locale TEXT NOT NULL DEFAULT 'en',
  title TEXT NOT NULL,
  excerpt TEXT NULL,
  body TEXT NOT NULL,
  scripture_reference TEXT NULL,
  duration_minutes INTEGER NOT NULL DEFAULT 5,
  audio_url TEXT NULL,
  image_url TEXT NULL,
  publish_date TEXT NOT NULL,
  is_published INTEGER NOT NULL DEFAULT 1 CHECK (is_published IN (0,1)),
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (slug, locale)
);

CREATE INDEX IF NOT EXISTS idx_devotional_publish
ON devotionals (is_published, locale, publish_date);

CREATE TABLE IF NOT EXISTS devotional_progress (
  user_id INTEGER NOT NULL,
  devotional_id INTEGER NOT NULL,
  progress_percent INTEGER NOT NULL DEFAULT 0 CHECK (progress_percent BETWEEN 0 AND 100),
  is_bookmarked INTEGER NOT NULL DEFAULT 0 CHECK (is_bookmarked IN (0,1)),
  completed_at TEXT NULL,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, devotional_id),
  FOREIGN KEY (devotional_id) REFERENCES devotionals(id) ON DELETE CASCADE
);

INSERT INTO devotionals
  (slug, locale, title, excerpt, body, scripture_reference, duration_minutes, publish_date, is_published)
VALUES
  ('walk-in-quiet-confidence', 'en', 'Walk in Quiet Confidence',
   'Make room for stillness and remember that God is present before you take the next step.',
   'Pause before the noise of the day takes over. Stillness is not wasted time; it is a place where trust can grow. You do not need every answer before taking the next faithful step. Breathe deeply, release what you cannot control, and remember that God is already present in the moment ahead.',
   'Psalm 46:10', 5, CURRENT_DATE, 1)
ON CONFLICT(slug, locale) DO UPDATE SET
  title = excluded.title,
  excerpt = excluded.excerpt,
  body = excluded.body,
  scripture_reference = excluded.scripture_reference,
  duration_minutes = excluded.duration_minutes,
  publish_date = excluded.publish_date,
  is_published = excluded.is_published,
  updated_at = CURRENT_TIMESTAMP;

