CREATE TABLE IF NOT EXISTS dailybreath_content_revisions (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  content_type TEXT NOT NULL,
  content_key TEXT NOT NULL,
  action TEXT NOT NULL,
  payload_json TEXT NOT NULL DEFAULT '{}',
  created_by INTEGER NOT NULL DEFAULT 0,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_dailybreath_revisions ON dailybreath_content_revisions(content_type,content_key,created_at);
