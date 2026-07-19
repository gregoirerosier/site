CREATE TABLE IF NOT EXISTS academy_module_exam_attempts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NOT NULL,course_id INT UNSIGNED NOT NULL,
  score INT NOT NULL DEFAULT 0,question_count INT NOT NULL DEFAULT 10,passed TINYINT(1) NOT NULL DEFAULT 0,
  answers_json JSON NOT NULL,attempted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_academy_exam_access(user_id,course_id,passed,attempted_at),
  CONSTRAINT fk_academy_exam_course FOREIGN KEY(course_id) REFERENCES academy_courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
UPDATE academy_courses SET is_published=0 WHERE slug IN('commandments-audio-journey','bible-foundations');
-- MySQL catalog rows are installed by the Academy V1 catalog bootstrap in academy.php.
