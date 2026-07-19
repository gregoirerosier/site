CREATE TABLE IF NOT EXISTS academy_quiz_attempts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  lesson_id INTEGER NOT NULL,
  score INTEGER NOT NULL DEFAULT 0,
  question_count INTEGER NOT NULL DEFAULT 10,
  passed INTEGER NOT NULL DEFAULT 0 CHECK(passed IN(0,1)),
  answers_json TEXT NOT NULL DEFAULT '{}',
  attempted_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(lesson_id) REFERENCES academy_lessons(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_academy_quiz_access ON academy_quiz_attempts(user_id,lesson_id,passed,attempted_at);
