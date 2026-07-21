CREATE TABLE IF NOT EXISTS tattoo_profiles (
  user_id INT PRIMARY KEY,
  account_type ENUM('client','artist','owner') NOT NULL DEFAULT 'client',
  city VARCHAR(160) NULL,
  bio TEXT NULL,
  styles VARCHAR(500) NULL,
  experience VARCHAR(160) NULL,
  studio_name VARCHAR(200) NULL,
  budget VARCHAR(160) NULL,
  availability VARCHAR(255) NULL,
  onboarding_complete TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_tattoo_profile_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tattoo_studios (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(160) NOT NULL UNIQUE,
  name VARCHAR(200) NOT NULL,
  description TEXT NULL,
  address_line1 VARCHAR(255) NULL,
  city VARCHAR(160) NOT NULL,
  province VARCHAR(160) NULL,
  postal_code VARCHAR(32) NULL,
  country VARCHAR(120) NOT NULL DEFAULT 'Canada',
  phone VARCHAR(64) NULL,
  owner_display_name VARCHAR(160) NULL,
  owner_instagram_url VARCHAR(500) NULL,
  instagram_url VARCHAR(500) NULL,
  services TEXT NULL,
  walk_ins TINYINT(1) NOT NULL DEFAULT 0,
  status VARCHAR(32) NOT NULL DEFAULT 'active',
  source_note VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tattoo_artists (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(160) NOT NULL UNIQUE,
  user_id INT NULL,
  studio_id BIGINT UNSIGNED NULL,
  display_name VARCHAR(200) NOT NULL,
  instagram_handle VARCHAR(160) NOT NULL,
  instagram_url VARCHAR(500) NOT NULL,
  city VARCHAR(160) NULL,
  languages VARCHAR(500) NULL,
  styles VARCHAR(500) NULL,
  bio TEXT NULL,
  availability VARCHAR(255) NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'active',
  source_note VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_tattoo_artist_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_tattoo_artist_studio FOREIGN KEY (studio_id) REFERENCES tattoo_studios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tattoo_tattoos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(200) NOT NULL,
  artist_name VARCHAR(200) NULL,
  studio_name VARCHAR(200) NULL,
  placement VARCHAR(160) NULL,
  style VARCHAR(160) NULL,
  start_date DATE NOT NULL,
  healing_days SMALLINT UNSIGNED NOT NULL DEFAULT 28,
  status VARCHAR(32) NOT NULL DEFAULT 'active',
  notes TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_tattoo_tattoos_user(user_id,status,start_date),
  CONSTRAINT fk_tattoo_tattoo_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tattoo_healing_entries (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  tattoo_id BIGINT UNSIGNED NULL,
  file_path VARCHAR(500) NOT NULL,
  mime VARCHAR(80) NOT NULL,
  bytes BIGINT UNSIGNED NOT NULL,
  width INT UNSIGNED NOT NULL,
  height INT UNSIGNED NOT NULL,
  notes TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_tattoo_healing_user(user_id,created_at),
  CONSTRAINT fk_tattoo_healing_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_tattoo_healing_tattoo FOREIGN KEY (tattoo_id) REFERENCES tattoo_tattoos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tattoo_beta_signups (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  interest VARCHAR(100) NOT NULL DEFAULT 'all',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tattoo_jobs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  owner_user_id INT NOT NULL,
  studio_name VARCHAR(200) NOT NULL,
  title VARCHAR(220) NOT NULL,
  opportunity_type VARCHAR(120) NOT NULL,
  details TEXT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'open',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tattoo_job_owner FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tattoo_invites (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  owner_user_id INT NOT NULL,
  artist_id BIGINT UNSIGNED NULL,
  target_label VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'sent',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tattoo_invite_owner FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_tattoo_invite_artist FOREIGN KEY (artist_id) REFERENCES tattoo_artists(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tattoo_studios
  (slug,name,description,address_line1,city,province,postal_code,country,phone,owner_display_name,owner_instagram_url,instagram_url,services,walk_ins,status,source_note)
VALUES
  ('stonerinkk-ottawa','StonerInkk','Custom tattoo and piercing studio offering walk-ins, full-day sessions, and large custom work.','234 Dalhousie Street','Ottawa','Ontario','K1N 7E2','Canada','438-925-1407','@inkby_stoner','https://www.instagram.com/inkby_stoner/','https://www.instagram.com/stonerinkkottawa/','Tattooing, piercing, custom work, full-day sessions',1,'active','Profile details supplied by the studio owner on 2026-07-21')
ON DUPLICATE KEY UPDATE
  name=VALUES(name),description=VALUES(description),address_line1=VALUES(address_line1),city=VALUES(city),
  province=VALUES(province),postal_code=VALUES(postal_code),country=VALUES(country),phone=VALUES(phone),
  owner_display_name=VALUES(owner_display_name),owner_instagram_url=VALUES(owner_instagram_url),
  instagram_url=VALUES(instagram_url),services=VALUES(services),walk_ins=VALUES(walk_ins),status='active',
  source_note=VALUES(source_note),updated_at=CURRENT_TIMESTAMP;

INSERT INTO tattoo_artists
  (slug,studio_id,display_name,instagram_handle,instagram_url,city,languages,styles,bio,availability,status,source_note)
SELECT 'ceddy-joseph',id,'Ceddy Joseph','@dop3inkk.tattoos','https://www.instagram.com/dop3inkk.tattoos/','Ottawa, Ontario','English, Spanish, French, Haitian Creole','Portraits, black-and-grey realism, custom','Ottawa tattoo artist specializing in portraits, black-and-grey realism, and custom work.','Bookings open','active','Profile details supplied by the studio owner on 2026-07-21'
FROM tattoo_studios WHERE slug='stonerinkk-ottawa'
ON DUPLICATE KEY UPDATE
  studio_id=VALUES(studio_id),display_name=VALUES(display_name),instagram_handle=VALUES(instagram_handle),
  instagram_url=VALUES(instagram_url),city=VALUES(city),languages=VALUES(languages),styles=VALUES(styles),
  bio=VALUES(bio),availability=VALUES(availability),status='active',source_note=VALUES(source_note),updated_at=CURRENT_TIMESTAMP;

INSERT INTO tattoo_artists
  (slug,studio_id,display_name,instagram_handle,instagram_url,city,languages,styles,bio,availability,status,source_note)
SELECT 'yc-the-artist',id,'YC The Artist','@yctats','https://www.instagram.com/yctats/','Ottawa, Ontario',NULL,'Fine line, black-and-grey, ornamental','Ottawa tattoo artist focused on fine-line, black-and-grey, and ornamental work.',NULL,'active','Profile details supplied by the studio owner on 2026-07-21'
FROM tattoo_studios WHERE slug='stonerinkk-ottawa'
ON DUPLICATE KEY UPDATE
  studio_id=VALUES(studio_id),display_name=VALUES(display_name),instagram_handle=VALUES(instagram_handle),
  instagram_url=VALUES(instagram_url),city=VALUES(city),languages=VALUES(languages),styles=VALUES(styles),
  bio=VALUES(bio),availability=VALUES(availability),status='active',source_note=VALUES(source_note),updated_at=CURRENT_TIMESTAMP;
