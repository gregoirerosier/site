CREATE TABLE IF NOT EXISTS visitor_traffic (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    event_type TEXT NOT NULL DEFAULT 'page_view',
    path TEXT NOT NULL,
    page_title TEXT,
    app_slug TEXT NOT NULL DEFAULT 'beyond-os',
    visitor_hash TEXT NOT NULL,
    session_hash TEXT NOT NULL,
    user_id INTEGER,
    referrer_host TEXT,
    referrer_path TEXT,
    device_type TEXT NOT NULL DEFAULT 'desktop',
    browser TEXT NOT NULL DEFAULT 'Other',
    operating_system TEXT NOT NULL DEFAULT 'Other',
    country_code TEXT,
    occurred_at TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_visitor_traffic_occurred ON visitor_traffic(occurred_at);
CREATE INDEX IF NOT EXISTS idx_visitor_traffic_visitor ON visitor_traffic(visitor_hash, occurred_at);
CREATE INDEX IF NOT EXISTS idx_visitor_traffic_session ON visitor_traffic(session_hash, occurred_at);
CREATE INDEX IF NOT EXISTS idx_visitor_traffic_app ON visitor_traffic(app_slug, occurred_at);
CREATE INDEX IF NOT EXISTS idx_visitor_traffic_path ON visitor_traffic(path, occurred_at);
