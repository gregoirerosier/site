-- Beyond ID secure Remember Me tokens — SQLite 3
PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS auth_remember_tokens (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  selector TEXT NOT NULL UNIQUE,
  validator_hash TEXT NOT NULL,
  user_agent_hash TEXT NULL,
  expires_at TEXT NOT NULL,
  last_used_at TEXT NULL,
  revoked_at TEXT NULL,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_remember_user
ON auth_remember_tokens (user_id);

CREATE INDEX IF NOT EXISTS idx_remember_expiry
ON auth_remember_tokens (expires_at, revoked_at);

