CREATE TABLE IF NOT EXISTS academy_stripe_events (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  stripe_event_id TEXT NOT NULL UNIQUE,
  event_type TEXT NOT NULL,
  processed INTEGER NOT NULL DEFAULT 0,
  payload_json TEXT NOT NULL,
  last_error TEXT NULL,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  processed_at TEXT NULL
);
CREATE INDEX IF NOT EXISTS idx_academy_stripe_processed ON academy_stripe_events(processed,created_at);
