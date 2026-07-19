<?php
declare(strict_types=1);

require_once __DIR__ . '/../beyond-id/includes/session.php';
require_once __DIR__ . '/../beyond-id/includes/functions.php';

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
    return beyond_url('assets/icons/' . ($versioned[$slug] ?? ($slug . '-192.webp')));
}
function require_beyond_id(): void { if (empty($_SESSION['user_id'])) { $_SESSION['beyond_return_to'] = beyond_return_url(); header('Location: ' . beyond_url('beyond-id/auth/login.php?required=1')); exit; } }
function beyond_db(): PDO { require __DIR__ . '/../beyond-id/includes/db.php'; return $pdo; }

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
                $themeAssets = '<script src="' . e(beyond_url('assets/js/beyond-theme-default.js')) . '"></script>'
                    . '<script src="' . e(beyond_url('assets/js/beyond-locales.js')) . '" defer></script>'
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
    header('X-Beyond-OS-Version: 2.1.1-beta');
    header('X-Beyond-App: ' . preg_replace('/[^A-Za-z0-9 -]/', '', $appName));
    return beyond_nav_bootstrap($appName, $wallet);
}

function beyond_shell_markup(string $appName, array $wallet): string {
    $signedIn = !empty($_SESSION['user_id']);
    $app = e($appName);
    $home = e(beyond_url());
    $homeIcon = e(beyond_app_icon('Beyond OS'));
    $currentIconPath = beyond_app_icon($appName);
    $currentIcon = $currentIconPath ? '<img class="bos-current-icon" src="' . e($currentIconPath) . '" alt="">' : '';
    $apps = [['Math','beyond-math/','Beyond Math','&#8721;'],['French','beyond-french/','Beyond French','FR'],['Daily Breath','dailybreath/','Daily Breath','&#9728;'],['TV','beyond-tv/','Beyond TV','&#9654;'],['Baby Names','beyond-baby-names/','Beyond Baby Names','&#128118;'],['Tattoo','beyond-tattoo/','Beyond Tattoo','&#10022;']];
    $options = '';
    foreach ($apps as [$label,$path,$iconName,$fallback]) {
        $iconPath = beyond_app_icon($iconName);
        $iconMarkup = $iconPath ? '<img src="' . e($iconPath) . '" alt="" loading="lazy">' : '<span class="bos-menu-fallback">✦</span>';
        if (!$iconPath) $iconMarkup = '<span class="bos-menu-fallback" aria-hidden="true">' . $fallback . '</span>';
        $options .= '<a href="' . e(beyond_url($path)) . '">' . $iconMarkup . '<span>' . e($label) . '</span></a>';
    }
    if ($signedIn) {
        $email = e($_SESSION['email'] ?? 'Member');
        $avatar = e(strtoupper(substr($_SESSION['name'] ?? $_SESSION['email'] ?? 'B', 0, 1)));
        $balance = number_format((float)($wallet['balance'] ?? 0), 0);
        $unread = beyond_notification_count();
        $accountActions = '<a class="bos-action" href="' . e(beyond_url('beyond-id/dashboard/notifications.php')) . '" aria-label="Notifications">🔔' . ($unread ? '<span class="bos-badge">' . $unread . '</span>' : '') . '</a><a class="bos-action bos-bits" href="' . e(beyond_url('beyond-id/dashboard/wallet.php')) . '">' . $balance . ' bit$</a><a class="bos-avatar" href="' . e(beyond_url('beyond-id/dashboard/')) . '" aria-label="Beyond ID for ' . $email . '">' . $avatar . '</a><a class="bos-action bos-email" href="' . e(beyond_url('beyond-id/auth/logout.php')) . '">Sign out</a>';
    } else {
        $accountActions = '<a class="bos-action bos-create" href="' . e(beyond_url('beyond-id/auth/login.php')) . '">Beyond ID</a>';
    }
    $navTools = '<label class="bos-locale" title="Choose language" style="position:relative;width:38px;height:38px;flex:0 0 38px;display:grid;place-items:center;border:1px solid rgba(255,255,255,.13);border-radius:50%;background:rgba(255,255,255,.06);cursor:pointer"><span aria-hidden="true">🌐</span><span style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0)">Language</span><select id="localePicker" aria-label="Choose language" style="position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer"><option value="en">English</option><option value="fr">Français</option><option value="ht">Kreyòl</option><option value="es">Español</option></select></label><button class="theme-toggle bos-theme-toggle" type="button" aria-label="Switch theme" title="Switch theme" style="width:38px;height:38px;flex:0 0 38px;display:grid;place-items:center;padding:0;border:1px solid rgba(255,255,255,.13);border-radius:50%;background:rgba(255,255,255,.06);color:#fff;cursor:pointer">☀</button>';
    $appStoreAction = '<a class="bos-action bos-app-store" href="' . e(beyond_url('app-store/')) . '">App Store</a>';
    $accountActions = $appStoreAction . $navTools . $accountActions;
    $accountActions .= '<style>#beyond-os-shell .bos-apps{display:none!important}#beyond-os-shell .bos-app-store{background:linear-gradient(100deg,#586cff,#8b5cf6);border-color:transparent;font-weight:900}@media(max-width:650px){#beyond-os-shell .bos-current-icon{display:block;flex:0 0 30px}#beyond-os-shell .bos-app{display:none}}</style>';
    return '<style>#beyond-os-shell{position:sticky;top:0;z-index:2147483000;min-height:58px;padding:max(8px,env(safe-area-inset-top)) 16px 8px;background:rgba(10,10,18,.94);color:#fff;border-bottom:1px solid rgba(255,255,255,.14);backdrop-filter:blur(18px);font:600 13px/1.3 system-ui,sans-serif}#beyond-os-shell *{box-sizing:border-box}#beyond-os-shell .bos-row{width:100%;max-width:1320px;min-width:0;margin:auto;display:flex;align-items:center;gap:12px;overflow:visible}#beyond-os-shell a{color:inherit;text-decoration:none}#beyond-os-shell .bos-home{color:#a5b4fc;font-weight:900;letter-spacing:.04em;display:flex;align-items:center;gap:8px}#beyond-os-shell .bos-home img,#beyond-os-shell .bos-current-icon{width:30px;height:30px;border-radius:9px;object-fit:cover;border:1px solid rgba(255,255,255,.18)}#beyond-os-shell .bos-app{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}#beyond-os-shell .bos-actions{min-width:0;margin-left:auto;display:flex;align-items:center;gap:8px;overflow:visible;scrollbar-width:none}#beyond-os-shell .bos-actions::-webkit-scrollbar{display:none}#beyond-os-shell .bos-action,#beyond-os-shell .bos-apps-toggle{min-height:38px;display:flex;align-items:center;padding:8px 11px;border:1px solid rgba(255,255,255,.13);border-radius:999px;background:rgba(255,255,255,.06);cursor:pointer;color:#fff;font:inherit}#beyond-os-shell .bos-apps{position:relative;z-index:8;flex:0 0 auto}#beyond-os-shell .bos-apps-toggle{appearance:none;-webkit-appearance:none}#beyond-os-shell .bos-apps-toggle[aria-expanded="true"]{background:rgba(255,255,255,.14);border-color:rgba(255,255,255,.28)}#beyond-os-shell .bos-create{background:linear-gradient(100deg,#586cff,#ef4897);border:0}#beyond-os-shell .bos-bits{color:#ffe17a}#beyond-os-shell .bos-menu{position:absolute;right:0;top:calc(100% + 8px);width:230px;padding:8px;border:1px solid #39394c;border-radius:16px;background:#11111d;box-shadow:0 22px 60px #0008;z-index:2147483647;display:grid;grid-template-columns:1fr 1fr;gap:4px}#beyond-os-shell .bos-menu[hidden]{display:none!important}#beyond-os-shell .bos-menu a{padding:10px 9px;border-radius:9px;display:flex;align-items:center;gap:8px}#beyond-os-shell .bos-menu a img,#beyond-os-shell .bos-menu-fallback{width:26px;height:26px;border-radius:8px;object-fit:cover;display:grid;place-items:center;background:#24243a}#beyond-os-shell .bos-avatar{width:34px;height:34px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(135deg,#5b8cff,#e9449f);font-weight:900}.bos-badge{display:inline-grid;place-items:center;min-width:18px;height:18px;padding:0 5px;margin-left:4px;border-radius:999px;background:#ef476f;font-size:10px}@media(max-width:650px){#beyond-os-shell{width:100%;max-width:100vw;padding-left:8px;padding-right:8px;overflow:visible}#beyond-os-shell .bos-row{gap:7px}#beyond-os-shell .bos-current-icon{display:none}#beyond-os-shell .bos-app{max-width:74px;flex:0 1 auto}#beyond-os-shell .bos-home span,#beyond-os-shell .bos-email,#beyond-os-shell .bos-app-label{display:none}#beyond-os-shell .bos-action,#beyond-os-shell .bos-apps-toggle{min-height:42px;padding:7px 9px}#beyond-os-shell .bos-row{overflow:visible}#beyond-os-shell .bos-actions{overflow:visible}#beyond-os-shell .bos-apps{position:static}#beyond-os-shell .bos-menu{position:absolute;left:8px;right:8px;top:100%;width:auto;max-height:min(70vh,560px);overflow:auto;grid-template-columns:1fr 1fr}}</style><nav id="beyond-os-shell" aria-label="Beyond OS navigation"><div class="bos-row"><a class="bos-home" href="' . $home . '"><img src="' . $homeIcon . '" alt=""><span>BEYOND OS 2.1</span></a><span class="bos-app-label">/</span>' . $currentIcon . '<strong class="bos-app">' . $app . '</strong><div class="bos-actions"><div class="bos-apps"><button class="bos-apps-toggle" type="button" aria-expanded="false" aria-controls="beyond-apps-menu">Apps ▾</button><div class="bos-menu" id="beyond-apps-menu" hidden>' . $options . '</div></div>' . $accountActions . '</div></div></nav><script>(function(){var shell=document.getElementById("beyond-os-shell");if(!shell)return;var wrap=shell.querySelector(".bos-apps"),button=shell.querySelector(".bos-apps-toggle"),menu=shell.querySelector(".bos-menu");if(!wrap||!button||!menu)return;function setOpen(open){button.setAttribute("aria-expanded",open?"true":"false");menu.hidden=!open;}button.addEventListener("click",function(e){e.preventDefault();e.stopPropagation();setOpen(menu.hidden);});menu.addEventListener("click",function(e){e.stopPropagation();});document.addEventListener("click",function(e){if(!wrap.contains(e.target))setOpen(false);});document.addEventListener("keydown",function(e){if(e.key==="Escape"){setOpen(false);button.focus();}});menu.querySelectorAll("a").forEach(function(a){a.addEventListener("click",function(){setOpen(false);});});})();</script>';
}

function render_beyond_bar(string $appName, array $wallet = []): void { echo beyond_shell_markup($appName, $wallet ?: beyond_wallet()); }
