<?php
declare(strict_types=1);
require __DIR__ . '/../../includes/config.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=300');
$query = mb_substr(trim((string)($_GET['q'] ?? '')), 0, 120);
$studios = array_map(static function(array $studio): array {
    return [
        'slug' => $studio['slug'],
        'name' => $studio['name'],
        'city' => $studio['city'],
        'province' => $studio['province'],
        'address' => trim($studio['address_line1'] . ', ' . $studio['city'] . ', ' . $studio['province'] . ' ' . $studio['postal_code']),
        'phone' => $studio['phone'],
        'walk_ins' => (bool)$studio['walk_ins'],
        'artist_count' => (int)$studio['artist_count'],
        'profile_url' => beyond_url('beyond-tattoo/studio-profile.php?slug=' . rawurlencode($studio['slug'])),
        'instagram_url' => $studio['instagram_url'],
    ];
}, bt_list_studios($query));
echo json_encode(['provider'=>'beyond-tattoo-directory','query'=>$query,'studios'=>$studios], JSON_UNESCAPED_SLASHES);
