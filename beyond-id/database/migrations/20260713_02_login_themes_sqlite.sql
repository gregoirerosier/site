-- Per-app Beyond ID login themes — SQLite 3
CREATE TABLE IF NOT EXISTS app_login_themes (app_key TEXT PRIMARY KEY, app_name TEXT NOT NULL, primary_color TEXT NOT NULL, secondary_color TEXT NOT NULL, accent_color TEXT NOT NULL, background_start TEXT NOT NULL, background_end TEXT NOT NULL, motif TEXT NULL, mark_text TEXT NULL, welcome_message TEXT NULL, return_path TEXT NOT NULL, is_active INTEGER NOT NULL DEFAULT 1 CHECK(is_active IN(0,1)), updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP);
INSERT INTO app_login_themes VALUES
('beyond-id','Beyond ID','#4058d6','#17a8a5','#ffd06a','#eef1ff','#e6f8f5','⚛','B','Sign in once to continue securely.','/beyond-id/',1,CURRENT_TIMESTAMP),
('dailybreath','DailyBreath','#173f2c','#477a57','#e0b85e','#f4ead6','#dce8d9','☀','✝','Continue your faith and wellness journey.','/dailybreath/',1,CURRENT_TIMESTAMP),
('health','Beyond Health','#087f79','#e56b6f','#ffd166','#e2f6f3','#fff0ec','♥','+','Continue to your secure health experience.','/health/',1,CURRENT_TIMESTAMP),
('education','Beyond Education','#2459b8','#5a83d7','#f2c84b','#e8f0ff','#fff8dc','✦','E','Continue learning with your Beyond ID.','/education/',1,CURRENT_TIMESTAMP),
('finance','Beyond Finance','#123f35','#2c8062','#d6ad52','#e5eee9','#f8efd8','↗','$','Sign in to your protected financial workspace.','/finance/',1,CURRENT_TIMESTAMP),
('preschool','Beyond Preschool','#ed735a','#3ca6a0','#f5c34d','#fff0e9','#e4f6f3','★','P','A bright, safe place for little learners.','/preschool/',1,CURRENT_TIMESTAMP),
('careers','Beyond Careers','#6a45a1','#e27a48','#f4c15d','#f0e8fa','#fff0e6','◆','C','Continue building your next opportunity.','/careers/',1,CURRENT_TIMESTAMP)
ON CONFLICT(app_key) DO UPDATE SET app_name=excluded.app_name,primary_color=excluded.primary_color,secondary_color=excluded.secondary_color,accent_color=excluded.accent_color,background_start=excluded.background_start,background_end=excluded.background_end,motif=excluded.motif,mark_text=excluded.mark_text,welcome_message=excluded.welcome_message,return_path=excluded.return_path,is_active=excluded.is_active,updated_at=CURRENT_TIMESTAMP;

