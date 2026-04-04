<?php
// Generate URL slug from string
function make_slug(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// Truncate text for excerpts
function truncate(string $text, int $limit = 150): string {
    return strlen($text) > $limit
        ? substr($text, 0, $limit) . '...'
        : $text;
}

// Get a setting value from DB
function get_setting(string $key, PDO $pdo): string {
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE `key` = ?");
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['value'] : '';
}

// Sanitize output to prevent XSS
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}