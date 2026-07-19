-- Admin Verse of the Day generator — SQLite 3
CREATE TABLE IF NOT EXISTS verse_day_posts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  publish_date TEXT NOT NULL,
  locale TEXT NOT NULL DEFAULT 'en',
  translation_code TEXT NOT NULL DEFAULT 'KJV',
  heading TEXT NOT NULL DEFAULT 'VERSE OF THE DAY',
  verse_text TEXT NOT NULL,
  scripture_reference TEXT NOT NULL,
  weekday_label TEXT NULL,
  date_label TEXT NULL,
  footer_message TEXT NULL,
  background_asset_url TEXT NOT NULL,
  rendered_asset_url TEXT NULL,
  show_footer INTEGER NOT NULL DEFAULT 1 CHECK(show_footer IN(0,1)),
  show_frame INTEGER NOT NULL DEFAULT 1 CHECK(show_frame IN(0,1)),
  status TEXT NOT NULL DEFAULT 'draft' CHECK(status IN('draft','scheduled','published','archived')),
  created_by INTEGER NULL,
  published_at TEXT NULL,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(publish_date, locale)
);
CREATE INDEX IF NOT EXISTS idx_verse_day_status ON verse_day_posts(status,publish_date);

