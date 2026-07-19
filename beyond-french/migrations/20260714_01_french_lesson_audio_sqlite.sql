CREATE TABLE IF NOT EXISTS french_lesson_audio (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    lesson_id INTEGER NOT NULL,
    provider TEXT NOT NULL,
    voice TEXT NOT NULL,
    language TEXT NOT NULL,
    format TEXT NOT NULL DEFAULT 'mp3',
    audio_path TEXT NOT NULL DEFAULT '',
    content_hash TEXT NOT NULL,
    generation_status TEXT NOT NULL DEFAULT 'processing' CHECK(generation_status IN ('processing','ready','failed')),
    error_code TEXT DEFAULT NULL,
    created_by INTEGER NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (lesson_id, content_hash)
);
CREATE INDEX IF NOT EXISTS idx_french_audio_lesson ON french_lesson_audio(lesson_id);
CREATE INDEX IF NOT EXISTS idx_french_audio_status ON french_lesson_audio(generation_status);

CREATE TABLE IF NOT EXISTS french_narration_rate_limits (
    admin_id INTEGER NOT NULL,
    action TEXT NOT NULL,
    window_started_at INTEGER NOT NULL,
    request_count INTEGER NOT NULL DEFAULT 0,
    PRIMARY KEY (admin_id, action)
);
