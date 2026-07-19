-- Beyond Bible narration preferences and progress — SQLite 3
CREATE TABLE IF NOT EXISTS bible_narration_preferences (
  user_id INTEGER NOT NULL PRIMARY KEY,
  language_code TEXT NOT NULL DEFAULT 'en-US',
  voice_name TEXT NULL,
  playback_rate REAL NOT NULL DEFAULT 1.0,
  pitch REAL NOT NULL DEFAULT 1.0,
  auto_advance INTEGER NOT NULL DEFAULT 1 CHECK (auto_advance IN (0,1)),
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bible_narration_progress (
  user_id INTEGER NOT NULL,
  translation_code TEXT NOT NULL,
  book_code TEXT NOT NULL,
  chapter_number INTEGER NOT NULL,
  verse_number INTEGER NOT NULL DEFAULT 1,
  character_offset INTEGER NOT NULL DEFAULT 0,
  completed INTEGER NOT NULL DEFAULT 0 CHECK (completed IN (0,1)),
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, translation_code, book_code, chapter_number)
);

CREATE INDEX IF NOT EXISTS idx_narration_resume
ON bible_narration_progress (user_id, updated_at);

