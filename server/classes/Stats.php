<?php
class Stats {
    public static function count(string $table): int {
        $allowed = ['users','clients','dailybreath_posts','activity_logs','api_usage'];
        if (!in_array($table, $allowed, true)) return 0;
        try { return (int)Database::conn()->query("SELECT COUNT(*) c FROM `$table`")->fetch()['c']; }
        catch (Throwable $e) { return 0; }
    }
    public static function recentActivity(int $limit=8): array {
        try { return Database::conn()->query("SELECT a.*, u.email FROM activity_logs a LEFT JOIN users u ON u.id=a.user_id ORDER BY a.id DESC LIMIT ".(int)$limit)->fetchAll(); }
        catch (Throwable $e) { return []; }
    }
}
