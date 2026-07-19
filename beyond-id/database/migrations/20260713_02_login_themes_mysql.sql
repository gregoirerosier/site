-- Per-app Beyond ID login themes — MySQL/MariaDB
CREATE TABLE IF NOT EXISTS app_login_themes (
  app_key VARCHAR(64) NOT NULL PRIMARY KEY,
  app_name VARCHAR(120) NOT NULL,
  primary_color CHAR(7) NOT NULL,
  secondary_color CHAR(7) NOT NULL,
  accent_color CHAR(7) NOT NULL,
  background_start CHAR(7) NOT NULL,
  background_end CHAR(7) NOT NULL,
  motif VARCHAR(32) NULL,
  mark_text VARCHAR(12) NULL,
  welcome_message VARCHAR(240) NULL,
  return_path VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO app_login_themes VALUES
('beyond-id','Beyond ID','#4058d6','#17a8a5','#ffd06a','#eef1ff','#e6f8f5','⚛','B','Sign in once to continue securely.','/beyond-id/',1,CURRENT_TIMESTAMP),
('dailybreath','DailyBreath','#173f2c','#477a57','#e0b85e','#f4ead6','#dce8d9','☀','✝','Continue your faith and wellness journey.','/dailybreath/',1,CURRENT_TIMESTAMP),
('health','Beyond Health','#087f79','#e56b6f','#ffd166','#e2f6f3','#fff0ec','♥','+','Continue to your secure health experience.','/health/',1,CURRENT_TIMESTAMP),
('education','Beyond Education','#2459b8','#5a83d7','#f2c84b','#e8f0ff','#fff8dc','✦','E','Continue learning with your Beyond ID.','/education/',1,CURRENT_TIMESTAMP),
('finance','Beyond Finance','#123f35','#2c8062','#d6ad52','#e5eee9','#f8efd8','↗','$','Sign in to your protected financial workspace.','/finance/',1,CURRENT_TIMESTAMP),
('preschool','Beyond Preschool','#ed735a','#3ca6a0','#f5c34d','#fff0e9','#e4f6f3','★','P','A bright, safe place for little learners.','/preschool/',1,CURRENT_TIMESTAMP),
('careers','Beyond Careers','#6a45a1','#e27a48','#f4c15d','#f0e8fa','#fff0e6','◆','C','Continue building your next opportunity.','/careers/',1,CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE app_name=VALUES(app_name),primary_color=VALUES(primary_color),secondary_color=VALUES(secondary_color),accent_color=VALUES(accent_color),background_start=VALUES(background_start),background_end=VALUES(background_end),motif=VALUES(motif),mark_text=VALUES(mark_text),welcome_message=VALUES(welcome_message),return_path=VALUES(return_path),is_active=VALUES(is_active);

