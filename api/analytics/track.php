<?php
declare(strict_types=1);

header('Cache-Control: no-store, max-age=0');
header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo '{"ok":false}';
    exit;
}
if (($_SERVER['HTTP_DNT'] ?? '') === '1') {
    http_response_code(204);
    exit;
}
$fetchSite = strtolower((string)($_SERVER['HTTP_SEC_FETCH_SITE'] ?? ''));
if ($fetchSite === 'cross-site') {
    http_response_code(403);
    echo '{"ok":false}';
    exit;
}
$origin = (string)($_SERVER['HTTP_ORIGIN'] ?? '');
if ($origin !== '') {
    $originHost = strtolower((string)(parse_url($origin, PHP_URL_HOST) ?? ''));
    $requestHost = strtolower((string)preg_replace('/:\d+$/', '', (string)($_SERVER['HTTP_HOST'] ?? '')));
    if ($originHost === '' || $requestHost === '' || $originHost !== $requestHost) {
        http_response_code(403);
        echo '{"ok":false}';
        exit;
    }
}
if ((int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 16384) {
    http_response_code(413);
    echo '{"ok":false}';
    exit;
}

require_once dirname(__DIR__, 2) . '/includes/ecosystem.php';
require_once dirname(__DIR__, 2) . '/includes/visitor-analytics.php';
require dirname(__DIR__, 2) . '/beyond-id/includes/db.php';

$raw = file_get_contents('php://input') ?: '';
$data = json_decode($raw, true);
if (!is_array($data)) $data = $_POST;
$path = beyond_analytics_clean_path((string)($data['path'] ?? '/'));
if (!beyond_analytics_should_track_path($path)) {
    http_response_code(204);
    exit;
}

$userAgent = beyond_analytics_limit((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 800);
$profile = beyond_analytics_client_profile($userAgent, (int)($data['screen_width'] ?? 0));
if ($profile['is_bot']) {
    http_response_code(204);
    exit;
}

$visitorId = beyond_analytics_cookie('beyond_visitor_id', 400 * 86400);
$sessionId = beyond_analytics_cookie('beyond_visit_session', 30 * 60);
$visitorHash = beyond_analytics_hash('visitor|' . $visitorId);
$sessionHash = beyond_analytics_hash('session|' . $sessionId);
[$referrerHost, $referrerPath] = beyond_analytics_referrer((string)($data['referrer'] ?? ''));
$country = strtoupper((string)($_SERVER['HTTP_CF_IPCOUNTRY'] ?? $_SERVER['HTTP_X_VERCEL_IP_COUNTRY'] ?? ''));
if (!preg_match('/^[A-Z]{2}$/', $country)) $country = null;
$title = trim(strip_tags((string)($data['title'] ?? '')));
$title = $title === '' ? null : beyond_analytics_limit($title, 255);
$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
if ($userId !== null && $userId < 1) $userId = null;

try {
    $dedupe = $pdo->prepare('SELECT 1 FROM visitor_traffic WHERE session_hash=? AND path=? AND occurred_at>=? LIMIT 1');
    $dedupe->execute([$sessionHash, $path, gmdate('Y-m-d H:i:s', time() - 2)]);
    if (!$dedupe->fetchColumn()) {
        $stmt = $pdo->prepare('INSERT INTO visitor_traffic
            (event_type,path,page_title,app_slug,visitor_hash,session_hash,user_id,referrer_host,referrer_path,device_type,browser,operating_system,country_code,occurred_at)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([
            'page_view', $path, $title, beyond_analytics_app_slug($path), $visitorHash, $sessionHash,
            $userId, $referrerHost, $referrerPath, $profile['device'], $profile['browser'], $profile['os'], $country,
            gmdate('Y-m-d H:i:s'),
        ]);
    }
    if (random_int(1, 100) === 1) {
        $prune = $pdo->prepare('DELETE FROM visitor_traffic WHERE occurred_at<?');
        $prune->execute([gmdate('Y-m-d H:i:s', time() - 400 * 86400)]);
    }
} catch (Throwable $exception) {
    error_log('Visitor analytics capture failed: ' . $exception->getMessage());
}

http_response_code(204);
