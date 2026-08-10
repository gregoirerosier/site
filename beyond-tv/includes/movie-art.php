<?php
declare(strict_types=1);

function beyond_tv_poster_url(array $item): ?string
{
    $slug = preg_replace('/[^a-z0-9-]/', '', strtolower((string)($item['slug'] ?? '')));
    if ($slug === '') {
        return null;
    }

    foreach (['poster_url', 'thumbnail'] as $field) {
        $explicitPoster = trim((string)($item[$field] ?? ''));
        if ($explicitPoster !== '') {
            return $explicitPoster;
        }
    }

    return null;
}
