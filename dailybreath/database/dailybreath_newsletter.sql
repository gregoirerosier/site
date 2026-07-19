CREATE TABLE IF NOT EXISTS dailybreath_subscribers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  source VARCHAR(80) DEFAULT 'dailybreath_landing',
  status ENUM('active','unsubscribed') DEFAULT 'active',
  ip_address VARCHAR(45) NULL,
  user_agent VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
