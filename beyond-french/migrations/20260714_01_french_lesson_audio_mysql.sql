CREATE TABLE IF NOT EXISTS french_lesson_audio (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lesson_id BIGINT UNSIGNED NOT NULL,
    provider VARCHAR(32) NOT NULL,
    voice VARCHAR(80) NOT NULL,
    language VARCHAR(20) NOT NULL,
    format VARCHAR(10) NOT NULL DEFAULT 'mp3',
    audio_path VARCHAR(255) NOT NULL,
    content_hash CHAR(64) NOT NULL,
    generation_status ENUM('processing','ready','failed') NOT NULL DEFAULT 'processing',
    error_code VARCHAR(80) DEFAULT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_lesson_audio_hash (lesson_id, content_hash),
    KEY idx_lesson_audio_lesson (lesson_id),
    KEY idx_lesson_audio_status (generation_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS french_narration_rate_limits (
    admin_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(32) NOT NULL,
    window_started_at BIGINT UNSIGNED NOT NULL,
    request_count INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (admin_id, action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
