-- Beyond OS 2.1 privileged account role patch
-- Safe to run repeatedly. It also corrects accounts created before this code patch.
UPDATE users
SET role = 'super_admin'
WHERE LOWER(TRIM(email)) = 'rosiergreg@gmail.com';

UPDATE users
SET role = 'admin'
WHERE LOWER(TRIM(email)) = 'admin@beyondimagination.co.technology';
