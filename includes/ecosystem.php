<?php
declare(strict_types=1);

$beyondSessionBootstrap = __DIR__ . '/../beyond-id/includes/session.php';
if (is_file($beyondSessionBootstrap)) {
    require_once $beyondSessionBootstrap;
} elseif (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = !empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

$beyondFunctionsBootstrap = __DIR__ . '/../beyond-id/includes/functions.php';
if (is_file($beyondFunctionsBootstrap)) {
    require_once $beyondFunctionsBootstrap;
}
if (!function_exists('e')) {
    function e(?string $value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

function beyond_base_path(): string {
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $known = ['/app-store/','/beyond-id/','/beyond-math/','/beyond-french/','/dailybreath/','/beyond-health/','/beyond-tv/','/beyond-catering/','/beyond-baby-names/','/beyond-tattoo/','/beyond-space/','/beyond-ancient/','/beyond-preschool/','/beyond-careers/','/beyond-sell/','/beyond-market/','/beyond-finance/','/beyond-investing/','/dashboard/','/admin/','/api-hub/'];
    foreach ($known as $marker) {
        $position = strpos($script, $marker);
        if ($position !== false) return substr($script, 0, $position);
    }
    return rtrim(dirname($script), '/.');
}

function beyond_url(string $path = ''): string { return rtrim(beyond_base_path(), '/') . '/' . ltrim($path, '/'); }
function beyond_return_url(): string { $uri = $_SERVER['REQUEST_URI'] ?? beyond_url(); return str_starts_with($uri, '/') ? $uri : beyond_url(); }

function beyond_app_icon(string $appName): string {
    $key = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $appName), '-'));
    $aliases = [
        'dailybreath' => 'daily-breath',
        'daily-breath' => 'daily-breath',
        'health' => 'beyond-health',
        'tv' => 'beyond-tv',
        'french' => 'beyond-french',
        'ancient' => 'beyond-ancient',
        'space' => 'beyond-space',
        'baby-names' => 'beyond-baby-names',
        'tattoo' => 'beyond-tattoo',
        'beyond-imagination-technology' => 'beyond-os',
    ];
    $slug = $aliases[$key] ?? $key;
    $supported = ['beyond-os','beyond-id','daily-breath','beyond-health','beyond-tv','beyond-french','beyond-ancient','beyond-space','beyond-baby-names','beyond-tattoo'];
    if (!in_array($slug, $supported, true)) return '';
    $versioned = [
        'beyond-baby-names' => 'beyond-baby-names-v2-192.webp?v=20260717-3',
        'beyond-tattoo' => 'beyond-tattoo-v2-192.webp?v=20260717-3',
    ];
    $file = $versioned[$slug] ?? ($slug . '-192.webp');
    $diskFile = preg_replace('/\?.*$/', '', $file);
    if (!is_file(__DIR__ . '/../assets/icons/' . $diskFile)) return '';
    return beyond_url('assets/icons/' . $file);
}
function require_beyond_id(): void { if (empty($_SESSION['user_id'])) { $_SESSION['beyond_return_to'] = beyond_return_url(); header('Location: ' . beyond_url('beyond-id/auth/login.php?required=1')); exit; } }
function beyond_db(): PDO {
    $databaseBootstrap = __DIR__ . '/../beyond-id/includes/db.php';
    if (!is_file($databaseBootstrap)) {
        throw new RuntimeException('Beyond ID database bootstrap is not installed.');
    }
    require $databaseBootstrap;
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new RuntimeException('Beyond ID database bootstrap did not provide a PDO connection.');
    }
    return $pdo;
}

function beyond_wallet(): array {
    $wallet = ['balance'=>0,'currency'=>'BITS','status'=>'pending'];
    if (empty($_SESSION['user_id'])) return $wallet;
    try {
        $pdo = beyond_db();
        $insert = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite'
            ? "INSERT OR IGNORE INTO beyond_wallets(user_id,balance,currency,status) VALUES(?,0,'BITS','active')"
            : "INSERT IGNORE INTO beyond_wallets(user_id,balance,currency,status) VALUES(?,0,'BITS','active')";
        $pdo->prepare($insert)->execute([(int)$_SESSION['user_id']]);
        $stmt = $pdo->prepare('SELECT balance,currency,status FROM beyond_wallets WHERE user_id=? LIMIT 1');
        $stmt->execute([(int)$_SESSION['user_id']]);
        $wallet = $stmt->fetch(PDO::FETCH_ASSOC) ?: $wallet;
    } catch (Throwable $exception) {
        error_log('Beyond Wallet unavailable: ' . $exception->getMessage());
    }
    return $wallet;
}

function beyond_notification_count(): int {
    if (empty($_SESSION['user_id'])) return 0;
    try { return unread_notification_count(beyond_db(), (int)$_SESSION['user_id']); } catch (Throwable $exception) { return 0; }
}

function beyond_track_app(string $appName): void {
    if (empty($_SESSION['user_id'])) return;
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $appName), '-'));
    try {
        $pdo = beyond_db();
        $now = date('Y-m-d H:i:s');
        if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
            $sql = 'INSERT INTO connected_apps(user_id,app_slug,permissions_json,last_used_at) VALUES(?,?,?,?) ON CONFLICT(user_id,app_slug) DO UPDATE SET last_used_at=excluded.last_used_at,revoked_at=NULL';
        } else {
            $sql = 'INSERT INTO connected_apps(user_id,app_slug,permissions_json,last_used_at) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE last_used_at=VALUES(last_used_at),revoked_at=NULL';
        }
        $pdo->prepare($sql)->execute([(int)$_SESSION['user_id'], $slug, json_encode(['profile:read','wallet:read']), $now]);
    } catch (Throwable $exception) {
    }
}

function beyond_nav_bootstrap(string $appName, ?array $wallet = null): array {
    $wallet ??= beyond_wallet();
    if (PHP_SAPI !== 'cli' && empty($GLOBALS['beyond_nav_started'])) {
        $GLOBALS['beyond_nav_started'] = true;
        ob_start(static function (string $html) use ($appName, $wallet): string {
            if (stripos($html, '<body') === false || str_contains($html, 'id="beyond-os-shell"')) return $html;
            $icon = beyond_app_icon($appName) ?: beyond_app_icon('Beyond OS');
            if ($icon && stripos($html, 'rel="icon"') === false) {
                $tag = '<link rel="icon" type="image/webp" href="' . e($icon) . '">';
                $html = preg_replace('/<\/head>/i', $tag . '</head>', $html, 1) ?? $html;
            }
            if (!str_contains($html, 'beyond-theme-default.js')) {
                $themeAssets = '<script src="' . e(beyond_url('assets/js/beyond-theme-default.js?v=20260719-2')) . '"></script>'
                    . '<script src="' . e(beyond_url('assets/js/beyond-locales.js?v=20260719-2')) . '" defer></script>'
                    . '<link rel="stylesheet" href="' . e(beyond_url('assets/css/beyond-dark-default.css')) . '">';
                $html = preg_replace('/<\/head>/i', $themeAssets . '</head>', $html, 1) ?? $html;
            }
            return preg_replace('/(<body[^>]*>)/i', '$1' . beyond_shell_markup($appName, $wallet), $html, 1) ?? $html;
        });
    }
    return $wallet;
}

function beyond_app_bootstrap(string $appName): array {
    require_beyond_id();
    beyond_track_app($appName);
    $wallet = beyond_wallet();
    header('X-Beyond-OS-Version: 2.2.1');
    header('X-Beyond-App: ' . preg_replace('/[^A-Za-z0-9 -]/', '', $appName));
    return beyond_nav_bootstrap($appName, $wallet);
}

function beyond_shell_markup(string $appName, array $wallet): string {
    $signedIn = !empty($_SESSION['user_id']);
    $app = e($appName);
    $home = e(beyond_url());
    // The full Beyond OS asset is a wordmark, not a compact navbar icon.
    // Use the atom mark here so its text cannot be compressed beside the label.
    $homeIcon = '<span class="bos-logo-mark" aria-hidden="true"><i></i><i></i><i></i><b></b></span>';
    $currentIconPath = beyond_app_icon($appName);
    $currentIcon = $currentIconPath ? '<img class="bos-current-icon" src="' . e($currentIconPath) . '" alt="">' : '';
    $appIdentity = strcasecmp(trim($appName), 'Beyond OS') === 0
        ? ''
        : '<span class="bos-app-label">/</span>' . $currentIcon . '<strong class="bos-app">' . $app . '</strong>';

    if ($signedIn) {
        $email = e($_SESSION['email'] ?? 'Member');
        $avatar = e(strtoupper(substr($_SESSION['name'] ?? $_SESSION['email'] ?? 'B', 0, 1)));
        $balance = number_format((float)($wallet['balance'] ?? 0), 0);
        $unread = beyond_notification_count();
        $accountActions = '<a class="bos-action" href="' . e(beyond_url('beyond-id/dashboard/notifications.php')) . '" aria-label="Notifications">🔔' . ($unread ? '<span class="bos-badge">' . $unread . '</span>' : '') . '</a>'
            . '<a class="bos-action bos-bits" href="' . e(beyond_url('beyond-id/dashboard/wallet.php')) . '">' . $balance . ' bit$</a>'
            . '<a class="bos-avatar" href="' . e(beyond_url('beyond-id/dashboard/')) . '" aria-label="Beyond ID for ' . $email . '">' . $avatar . '</a>'
            . '<a class="bos-action bos-email" href="' . e(beyond_url('beyond-id/auth/logout.php')) . '">Sign out</a>';
    } else {
        $accountActions = '<a class="bos-action bos-create" href="' . e(beyond_url('beyond-id/auth/login.php')) . '">Beyond ID</a>';
    }

    $navTools = '<label class="bos-locale" title="Choose language"><span aria-hidden="true">🌐</span><span class="bos-sr-only">Language</span><select id="localePicker" aria-label="Choose language"><option value="en">English</option><option value="fr">Français</option><option value="ht">Kreyòl</option><option value="es">Español</option></select></label>'
        . '<button class="theme-toggle bos-theme-toggle" type="button" aria-label="Switch theme" title="Switch theme">☀</button>';
    $appStoreAction = '<a class="bos-action bos-app-store" href="' . e(beyond_url('app-store/')) . '"><span aria-hidden="true">🛍</span><span class="bos-app-store-label bos-app-store-label-full">App Store</span><span class="bos-app-store-label bos-app-store-label-mobile">Apps</span></a>';

    return '<style>
#beyond-os-shell{position:relative;top:auto;z-index:100;min-height:58px;padding:max(8px,env(safe-area-inset-top)) 16px 8px;background:rgba(10,10,18,.94);color:#fff;border-bottom:1px solid rgba(255,255,255,.14);backdrop-filter:blur(18px);font:600 13px/1.3 system-ui,sans-serif}
#beyond-os-shell *{box-sizing:border-box}#beyond-os-shell a{color:inherit;text-decoration:none}
#beyond-os-shell .bos-row{width:100%;max-width:1320px;min-width:0;margin:auto;display:flex;align-items:center;gap:12px}
#beyond-os-shell .bos-home{color:#a5b4fc;font-weight:900;letter-spacing:.04em;display:flex;align-items:center;gap:8px;flex:0 0 auto;white-space:nowrap}
#beyond-os-shell .bos-home-label{display:none}
#beyond-os-shell .bos-home img,#beyond-os-shell .bos-current-icon,#beyond-os-shell .bos-logo-mark{width:30px;height:30px;border-radius:9px;object-fit:cover;border:1px solid rgba(255,255,255,.18)}
#beyond-os-shell .bos-logo-mark{position:relative;display:block;background:#0b0b1d;box-shadow:0 6px 18px rgba(88,108,255,.3)}
#beyond-os-shell .bos-logo-mark i{position:absolute;left:5px;top:11px;width:18px;height:6px;border:1.5px solid #8d70ff;border-radius:50%}
#beyond-os-shell .bos-logo-mark i:nth-child(2){transform:rotate(60deg)}#beyond-os-shell .bos-logo-mark i:nth-child(3){transform:rotate(120deg)}
#beyond-os-shell .bos-logo-mark b{position:absolute;left:13px;top:13px;width:4px;height:4px;border-radius:50%;background:#f05ab8;box-shadow:0 0 7px #f05ab8}
#beyond-os-shell .bos-app{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
#beyond-os-shell .bos-actions{min-width:0;margin-left:auto;display:flex;align-items:center;gap:8px}
#beyond-os-shell .bos-action{min-height:38px;display:flex;align-items:center;gap:6px;padding:8px 11px;border:1px solid rgba(255,255,255,.13);border-radius:999px;background:rgba(255,255,255,.06);color:#fff;font:inherit}
#beyond-os-shell .bos-app-store{background:linear-gradient(100deg,#586cff,#8b5cf6);border-color:transparent;font-weight:900;box-shadow:0 8px 24px rgba(88,108,255,.24)}#beyond-os-shell .bos-app-store>span[aria-hidden="true"],#beyond-os-shell .bos-app-store-label-mobile{display:none}
#beyond-os-shell .bos-create{background:linear-gradient(100deg,#586cff,#ef4897);border:0}#beyond-os-shell .bos-bits{color:#ffe17a}
html[data-theme="sunset"] #beyond-os-shell{background:rgba(57,20,47,.95);border-color:rgba(255,204,176,.2)}html[data-theme="sunset"] #beyond-os-shell .bos-action,html[data-theme="sunset"] #beyond-os-shell .bos-locale,html[data-theme="sunset"] #beyond-os-shell .bos-theme-toggle{background:rgba(112,43,76,.46);border-color:rgba(255,208,180,.25)}html[data-theme="sunset"] #beyond-os-shell .bos-app-store{background:linear-gradient(110deg,#ff8a62,#a83e81);box-shadow:0 8px 24px rgba(255,108,92,.25)}
#beyond-os-shell .bos-avatar{width:34px;height:34px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(135deg,#5b8cff,#e9449f);font-weight:900;flex:0 0 34px}.bos-badge{display:inline-grid;place-items:center;min-width:18px;height:18px;padding:0 5px;margin-left:4px;border-radius:999px;background:#ef476f;font-size:10px}
#beyond-os-shell .bos-locale,#beyond-os-shell .bos-theme-toggle{position:relative;z-index:2;width:38px;height:38px;flex:0 0 38px;display:grid;place-items:center;padding:0;border:1px solid rgba(255,255,255,.13);border-radius:50%;background:rgba(255,255,255,.06);color:#fff;cursor:pointer;touch-action:manipulation;pointer-events:auto}
#beyond-os-shell .bos-locale select{position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer}#beyond-os-shell .bos-sr-only{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0)}
@media(max-width:760px){#beyond-os-shell{width:100%;max-width:100vw;padding-left:7px;padding-right:7px}#beyond-os-shell .bos-row{gap:5px}#beyond-os-shell .bos-home-label,#beyond-os-shell .bos-email,#beyond-os-shell .bos-app-label,#beyond-os-shell .bos-app,#beyond-os-shell .bos-app-store-label{display:none}#beyond-os-shell .bos-current-icon{display:block;flex:0 0 30px}#beyond-os-shell .bos-action{min-height:40px;padding:6px 8px}#beyond-os-shell .bos-actions{gap:5px}#beyond-os-shell .bos-app-store{width:40px;justify-content:center;padding:0}#beyond-os-shell .bos-app-store>span[aria-hidden="true"]{display:inline}}
@media(max-width:430px){#beyond-os-shell .bos-row{gap:4px}#beyond-os-shell .bos-current-icon{display:none}#beyond-os-shell .bos-home img{width:28px;height:28px}#beyond-os-shell .bos-locale,#beyond-os-shell .bos-theme-toggle{display:grid;width:36px;height:36px;flex-basis:36px}#beyond-os-shell .bos-app-store{width:auto;min-width:58px;min-height:36px;padding:6px 8px;justify-content:center}#beyond-os-shell .bos-app-store-label-full{display:none}#beyond-os-shell .bos-app-store-label-mobile{display:inline;font-size:11px}#beyond-os-shell .bos-bits{display:none}#beyond-os-shell .bos-avatar{width:32px;height:32px;flex-basis:32px}}
</style><nav id="beyond-os-shell" aria-label="Beyond OS navigation"><div class="bos-row"><a class="bos-home" href="' . $home . '" aria-label="Beyond OS 2.2.1" title="Beyond OS 2.2.1">' . $homeIcon . '<span class="bos-home-label">BEYOND OS 2.2.1</span></a>' . $appIdentity . '<div class="bos-actions">' . $appStoreAction . $navTools . $accountActions . '</div></div></nav>';
}

function render_beyond_bar(string $appName, array $wallet = []): void { echo beyond_shell_markup($appName, $wallet ?: beyond_wallet()); }
