<?php
declare(strict_types=1);

/**
 * Shared DailyBreath Verse of the Day loader.
 * Both the signed-in home and the Beyond ID login page use this source,
 * so publishing a verse in Daily Studio updates both screens.
 *
 * @return array{text:string,reference:string,book:string,chapter:int,verse:int,source:string}
 */
function dailybreath_verse_of_day(PDO $pdo, string $locale = 'en'): array
{
    $fallbacks = [
        ['Be still, and know that I am God.', 'Psalm 46:10'],
        ['Yahweh is my shepherd: I shall lack nothing.', 'Psalm 23:1'],
        ['Trust in Yahweh with all your heart, and don’t lean on your own understanding.', 'Proverbs 3:5'],
        ['I can do all things through Christ, who strengthens me.', 'Philippians 4:13'],
        ['For we walk by faith, not by sight.', '2 Corinthians 5:7'],
        ['Cast all your worries on him, because he cares for you.', '1 Peter 5:7'],
        ['The joy of Yahweh is your strength.', 'Nehemiah 8:10'],
        ['Let all that you do be done in love.', '1 Corinthians 16:14'],
        ['Yahweh is my light and my salvation. Whom shall I fear?', 'Psalm 27:1'],
        ['My grace is sufficient for you, for my power is made perfect in weakness.', '2 Corinthians 12:9'],
        ['In peace I will both lay myself down and sleep, for you alone, Yahweh, make me live in safety.', 'Psalm 4:8'],
        ['Those who wait for Yahweh will renew their strength.', 'Isaiah 40:31'],
        ['Don’t be anxious for anything, but in everything, by prayer and petition with thanksgiving, let your requests be made known to God.', 'Philippians 4:6'],
        ['We know that all things work together for good for those who love God.', 'Romans 8:28'],
    ];

    $result = null;
    try {
        $query = $pdo->prepare("SELECT verse_text, scripture_reference FROM verse_day_posts WHERE status='published' AND publish_date<=? AND locale=? ORDER BY publish_date DESC, id DESC LIMIT 1");
        $query->execute([date('Y-m-d'), $locale]);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if ($row && trim((string)($row['verse_text'] ?? '')) !== '') {
            $result = [
                'text' => trim((string)$row['verse_text']),
                'reference' => trim((string)($row['scripture_reference'] ?? '')),
                'source' => 'verse_day_posts',
            ];
        }
    } catch (Throwable $exception) {
        // Older installs may not have this table yet.
    }

    if ($result === null) {
        try {
            $query = $pdo->prepare("SELECT title, body FROM beyond_content WHERE product='dailybreath' AND status='published' AND scheduled_for<=? ORDER BY scheduled_for DESC, id DESC LIMIT 1");
            $query->execute([date('Y-m-d')]);
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if ($row && trim((string)($row['title'] ?? '')) !== '') {
                $result = [
                    'text' => trim((string)$row['title']),
                    'reference' => trim((string)($row['body'] ?? '')),
                    'source' => 'beyond_content',
                ];
            }
        } catch (Throwable $exception) {
            // Fall through to the bundled daily rotation.
        }
    }

    if ($result === null) {
        $index = (int)(abs(crc32(date('Y-m-d'))) % count($fallbacks));
        $result = [
            'text' => $fallbacks[$index][0],
            'reference' => $fallbacks[$index][1],
            'source' => 'bundled_rotation',
        ];
    }

    $location = dailybreath_reference_location($result['reference']);
    return $result + $location;
}

/** @return array{book:string,chapter:int,verse:int} */
function dailybreath_reference_location(string $reference): array
{
    $book = 'Psalms';
    $chapter = 46;
    $verse = 1;

    if (preg_match('/^\s*((?:[1-3]\s*)?[A-Za-z]+(?:\s+[A-Za-z]+)*)\s+(\d+)(?::(\d+))?/u', $reference, $match)) {
        $book = trim($match[1]);
        $chapter = max(1, (int)$match[2]);
        $verse = isset($match[3]) ? max(1, (int)$match[3]) : 1;
    }

    $aliases = [
        'Psalm' => 'Psalms',
        'Psalms' => 'Psalms',
        'Song of Songs' => 'Song of Solomon',
        'Song of Solomon' => 'Song of Solomon',
        'Revelations' => 'Revelation',
    ];
    $book = $aliases[$book] ?? $book;

    return ['book' => $book, 'chapter' => $chapter, 'verse' => $verse];
}

function dailybreath_bible_url(array $verse, string $prefix = ''): string
{
    $base = rtrim($prefix, '/') . '/dailybreath/bible.php';
    if ($prefix === '') $base = 'bible.php';
    return $base
        . '?book=' . rawurlencode((string)($verse['book'] ?? 'Psalms'))
        . '&chapter=' . max(1, (int)($verse['chapter'] ?? 46))
        . '#verse-' . max(1, (int)($verse['verse'] ?? 1));
}
