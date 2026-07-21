PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS tattoo_profiles (
  user_id INTEGER PRIMARY KEY,
  account_type TEXT NOT NULL DEFAULT 'client' CHECK (account_type IN ('client','artist','owner')),
  city TEXT NULL,
  bio TEXT NULL,
  styles TEXT NULL,
  experience TEXT NULL,
  studio_name TEXT NULL,
  budget TEXT NULL,
  availability TEXT NULL,
  onboarding_complete INTEGER NOT NULL DEFAULT 0 CHECK (onboarding_complete IN (0,1)),
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tattoo_studios (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  slug TEXT NOT NULL UNIQUE,
  name TEXT NOT NULL,
  description TEXT NULL,
  address_line1 TEXT NULL,
  city TEXT NOT NULL,
  province TEXT NULL,
  postal_code TEXT NULL,
  country TEXT NOT NULL DEFAULT 'Canada',
  phone TEXT NULL,
  owner_display_name TEXT NULL,
  owner_instagram_url TEXT NULL,
  instagram_url TEXT NULL,
  services TEXT NULL,
  walk_ins INTEGER NOT NULL DEFAULT 0 CHECK (walk_ins IN (0,1)),
  status TEXT NOT NULL DEFAULT 'active',
  source_note TEXT NULL,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tattoo_artists (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  slug TEXT NOT NULL UNIQUE,
  user_id INTEGER NULL,
  studio_id INTEGER NULL,
  display_name TEXT NOT NULL,
  instagram_handle TEXT NOT NULL,
  instagram_url TEXT NOT NULL,
  city TEXT NULL,
  languages TEXT NULL,
  styles TEXT NULL,
  bio TEXT NULL,
  availability TEXT NULL,
  status TEXT NOT NULL DEFAULT 'active',
  source_note TEXT NULL,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (studio_id) REFERENCES tattoo_studios(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS tattoo_tattoos (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  name TEXT NOT NULL,
  artist_name TEXT NULL,
  studio_name TEXT NULL,
  placement TEXT NULL,
  style TEXT NULL,
  start_date TEXT NOT NULL,
  healing_days INTEGER NOT NULL DEFAULT 28,
  status TEXT NOT NULL DEFAULT 'active',
  notes TEXT NULL,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_tattoo_tattoos_user ON tattoo_tattoos(user_id,status,start_date);

CREATE TABLE IF NOT EXISTS tattoo_healing_entries (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  tattoo_id INTEGER NULL,
  file_path TEXT NOT NULL,
  mime TEXT NOT NULL,
  bytes INTEGER NOT NULL,
  width INTEGER NOT NULL,
  height INTEGER NOT NULL,
  notes TEXT NULL,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (tattoo_id) REFERENCES tattoo_tattoos(id) ON DELETE SET NULL
);
CREATE INDEX IF NOT EXISTS idx_tattoo_healing_user ON tattoo_healing_entries(user_id,created_at);

CREATE TABLE IF NOT EXISTS tattoo_beta_signups (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE COLLATE NOCASE,
  interest TEXT NOT NULL DEFAULT 'all',
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tattoo_jobs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  owner_user_id INTEGER NOT NULL,
  studio_name TEXT NOT NULL,
  title TEXT NOT NULL,
  opportunity_type TEXT NOT NULL,
  details TEXT NULL,
  status TEXT NOT NULL DEFAULT 'open',
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tattoo_invites (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  owner_user_id INTEGER NOT NULL,
  artist_id INTEGER NULL,
  target_label TEXT NOT NULL,
  message TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'sent',
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (artist_id) REFERENCES tattoo_artists(id) ON DELETE SET NULL
);

INSERT INTO tattoo_studios
  (slug,name,description,address_line1,city,province,postal_code,country,phone,owner_display_name,owner_instagram_url,instagram_url,services,walk_ins,status,source_note)
VALUES
  ('stonerinkk-ottawa','StonerInkk','Custom tattoo and piercing studio offering walk-ins, full-day sessions, and large custom work.','234 Dalhousie Street','Ottawa','Ontario','K1N 7E2','Canada','438-925-1407','@inkby_stoner','https://www.instagram.com/inkby_stoner/','https://www.instagram.com/stonerinkkottawa/','Tattooing, piercing, custom work, full-day sessions',1,'active','Profile details supplied by the studio owner on 2026-07-21')
ON CONFLICT(slug) DO UPDATE SET
  name=excluded.name,description=excluded.description,address_line1=excluded.address_line1,city=excluded.city,
  province=excluded.province,postal_code=excluded.postal_code,country=excluded.country,phone=excluded.phone,
  owner_display_name=excluded.owner_display_name,owner_instagram_url=excluded.owner_instagram_url,
  instagram_url=excluded.instagram_url,services=excluded.services,walk_ins=excluded.walk_ins,status='active',
  source_note=excluded.source_note,updated_at=CURRENT_TIMESTAMP;

INSERT INTO tattoo_artists
  (slug,studio_id,display_name,instagram_handle,instagram_url,city,languages,styles,bio,availability,status,source_note)
SELECT 'ceddy-joseph',id,'Ceddy Joseph','@dop3inkk.tattoos','https://www.instagram.com/dop3inkk.tattoos/','Ottawa, Ontario','English, Spanish, French, Haitian Creole','Portraits, black-and-grey realism, custom','Ottawa tattoo artist specializing in portraits, black-and-grey realism, and custom work.','Bookings open','active','Profile details supplied by the studio owner on 2026-07-21'
FROM tattoo_studios WHERE slug='stonerinkk-ottawa'
ON CONFLICT(slug) DO UPDATE SET
  studio_id=excluded.studio_id,display_name=excluded.display_name,instagram_handle=excluded.instagram_handle,
  instagram_url=excluded.instagram_url,city=excluded.city,languages=excluded.languages,styles=excluded.styles,
  bio=excluded.bio,availability=excluded.availability,status='active',source_note=excluded.source_note,updated_at=CURRENT_TIMESTAMP;

INSERT INTO tattoo_artists
  (slug,studio_id,display_name,instagram_handle,instagram_url,city,languages,styles,bio,availability,status,source_note)
SELECT 'yc-the-artist',id,'YC The Artist','@yctats','https://www.instagram.com/yctats/','Ottawa, Ontario',NULL,'Fine line, black-and-grey, ornamental','Ottawa tattoo artist focused on fine-line, black-and-grey, and ornamental work.',NULL,'active','Profile details supplied by the studio owner on 2026-07-21'
FROM tattoo_studios WHERE slug='stonerinkk-ottawa'
ON CONFLICT(slug) DO UPDATE SET
  studio_id=excluded.studio_id,display_name=excluded.display_name,instagram_handle=excluded.instagram_handle,
  instagram_url=excluded.instagram_url,city=excluded.city,languages=excluded.languages,styles=excluded.styles,
  bio=excluded.bio,availability=excluded.availability,status='active',source_note=excluded.source_note,updated_at=CURRENT_TIMESTAMP;

