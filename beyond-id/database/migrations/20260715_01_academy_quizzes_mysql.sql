CREATE TABLE IF NOT EXISTS academy_quiz_attempts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  lesson_id INT UNSIGNED NOT NULL,
  score INT NOT NULL DEFAULT 0,
  question_count INT NOT NULL DEFAULT 10,
  passed TINYINT(1) NOT NULL DEFAULT 0,
  answers_json JSON NOT NULL,
  attempted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_academy_quiz_access(user_id,lesson_id,passed,attempted_at),
  CONSTRAINT fk_academy_quiz_lesson FOREIGN KEY(lesson_id) REFERENCES academy_lessons(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
