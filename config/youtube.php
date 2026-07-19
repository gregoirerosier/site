<?php
declare(strict_types=1);

/**
 * Beyond TV YouTube API configuration.
 *
 * Production recommendation:
 * Set YOUTUBE_API_KEYS as a comma-separated environment variable.
 * The bundled fallback keeps this deployment functional on shared hosting.
 */
return [
    'api_keys' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string)(getenv('YOUTUBE_API_KEYS') ?: 'AIzaSyAaq0e_qMeeXMRp9R56u7qK-oiy6XecHLE'))
    ))),
    'timeout_seconds' => 8,
];
