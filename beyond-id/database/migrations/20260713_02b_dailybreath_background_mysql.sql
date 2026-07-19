-- Run after the app_login_themes migration.
ALTER TABLE app_login_themes
  ADD COLUMN background_image_url VARCHAR(500) NULL AFTER background_end;

UPDATE app_login_themes
SET background_image_url = '/assets/dailybreath-login-background.webp',
    updated_at = CURRENT_TIMESTAMP
WHERE app_key = 'dailybreath';

