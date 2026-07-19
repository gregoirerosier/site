CREATE TABLE IF NOT EXISTS bible_verses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    translation_code VARCHAR(20) NOT NULL DEFAULT 'WEBP',
    book_name VARCHAR(80) NOT NULL,
    chapter_number SMALLINT UNSIGNED NOT NULL,
    verse_number SMALLINT UNSIGNED NOT NULL,
    verse_text TEXT NOT NULL,
    UNIQUE KEY uq_bible_reference (translation_code,book_name,chapter_number,verse_number),
    KEY idx_bible_chapter (book_name,chapter_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bible_academy_progress (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    course_slug VARCHAR(120) NOT NULL,
    module_number SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    completed_at DATETIME DEFAULT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_academy_progress (user_id,course_slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
