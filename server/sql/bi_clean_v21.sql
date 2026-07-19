DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS api_usage;
DROP TABLE IF EXISTS dailybreath_posts;
DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL DEFAULT 'admin',
  status VARCHAR(30) NOT NULL DEFAULT 'active',
  last_login DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE clients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  type VARCHAR(80) DEFAULT 'business',
  status VARCHAR(30) DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE dailybreath_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(190) NOT NULL,
  body TEXT NULL,
  status VARCHAR(30) DEFAULT 'draft',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE api_usage (
  id INT AUTO_INCREMENT PRIMARY KEY,
  endpoint VARCHAR(190) NOT NULL,
  method VARCHAR(20) NOT NULL,
  status_code INT DEFAULT 200,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE activity_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(100) NOT NULL,
  detail VARCHAR(255) NULL,
  ip_address VARCHAR(80) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token VARCHAR(120) NOT NULL UNIQUE,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO users (name,email,password_hash,role,status) VALUES ('Admin','admin@beyondimagination.co.technology','$2y$10$Fq8vEZdjE62sjjZhLSsBlONjixJ9C6lmpD6Mz9r.vy7CuicgwwF2O','super_admin','active');
INSERT INTO clients (name,type,status) VALUES ('Beyond Catering','platform','active');
INSERT INTO dailybreath_posts (title,body,status) VALUES ('Welcome to DailyBreath','Daily content module is ready.','draft');
