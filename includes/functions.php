<?php
// ============================================================
//  includes/functions.php
//  Global helper functions used across all pages
// ============================================================

/**
 * Sanitize output — prevent XSS
 */
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a URL-friendly slug from any string
 */
function make_slug(string $text): string {
    // Indonesian/common char replacements
    $text = mb_strtolower(trim($text), 'UTF-8');
    $replace = [
        'à'=>'a','á'=>'a','â'=>'a','ã'=>'a','ä'=>'a','å'=>'a',
        'è'=>'e','é'=>'e','ê'=>'e','ë'=>'e',
        'ì'=>'i','í'=>'i','î'=>'i','ï'=>'i',
        'ò'=>'o','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'o',
        'ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u',
        'ñ'=>'n','ç'=>'c','&'=>'dan','/'=>'-',
    ];
    $text = strtr($text, $replace);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Truncate a string to a given character limit
 */
function truncate(string $text, int $limit = 150, string $suffix = '...'): string {
    $text = strip_tags($text);
    return mb_strlen($text) > $limit
        ? mb_substr($text, 0, $limit) . $suffix
        : $text;
}

/**
 * Get a single setting value from the DB
 */
function get_setting(string $key, PDO $pdo): string {
    static $cache = [];
    if (isset($cache[$key])) return $cache[$key];
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE `key` = ? LIMIT 1");
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $cache[$key] = ($row ? (string)$row['value'] : '');
}

/**
 * Redirect helper
 */
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

/**
 * Format date to Indonesian locale
 */
function format_date(string $date, string $format = 'd M Y'): string {
    $months = [
        '01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr',
        '05'=>'Mei','06'=>'Jun','07'=>'Jul','08'=>'Agt',
        '09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des',
    ];
    $formatted = date($format, strtotime($date));
    foreach ($months as $en => $id) {
        $formatted = str_replace(date('M', mktime(0,0,0,(int)$en,1)), $id, $formatted);
    }
    return $formatted;
}

/**
 * Check if a file upload is a valid image
 */
function is_valid_image(array $file): bool {
    if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) return false;
    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    return in_array(mime_content_type($file['tmp_name']), $allowed);
}

/**
 * Move uploaded image to destination folder, return relative path or false
 */
function upload_image(array $file, string $folder = 'articles'): string|false {
    if (!is_valid_image($file)) return false;
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = $folder . '_' . time() . '_' . rand(100, 999) . '.' . $ext;
    
    // Get upload directory path
    $upload_root = __DIR__ . '/../uploads/';
    $dir = $upload_root . $folder . '/';
    
    // Ensure directory exists and is writable
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    
    // Check if directory is writable
    if (!is_writable($dir)) {
        @chmod($dir, 0777);
        // If still not writable, return false
        if (!is_writable($dir)) {
            return false;
        }
    }
    
    $dest = $dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        @chmod($dest, 0666); // Make file writable
        return 'uploads/' . $folder . '/' . $filename;
    }
    
    return false;
}

/**
 * Delete a file by its relative path (e.g. uploads/articles/xxx.jpg)
 */
function delete_file(string $relative_path): void {
    if (empty($relative_path)) return;
    
    // Gunakan path absolut dari root project
    $full = dirname(__DIR__) . '/' . ltrim($relative_path, '/');
    
    if (file_exists($full) && is_file($full)) {
        unlink($full);
    }
}

/**
 * Flash message — set
 */
function flash(string $message, string $type = 'success'): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash']      = $message;
    $_SESSION['flash_type'] = $type;
}

/**
 * Flash message — render and clear
 */
function render_flash(): string {
    if (empty($_SESSION['flash'])) return '';
    $msg  = $_SESSION['flash'];
    $type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash'], $_SESSION['flash_type']);

    $styles = [
        'success' => ['bg-green-50 border-green-200 text-green-700',  'solar:check-circle-linear text-green-500'],
        'error'   => ['bg-red-50 border-red-200 text-red-700',        'solar:danger-circle-linear text-red-500'],
        'info'    => ['bg-blue-50 border-blue-200 text-blue-700',     'solar:info-circle-linear text-blue-500'],
    ];
    [$cls, $icon] = $styles[$type] ?? $styles['success'];
    [$iconName, $iconColor] = explode(' ', $icon);

    return '<div data-autohide class="mb-5 rounded-xl ' . $cls . ' border px-4 py-3 flex items-center gap-3">
        <iconify-icon icon="' . $iconName . '" class="text-lg ' . $iconColor . '"></iconify-icon>
        <p class="text-sm">' . e($msg) . '</p>
    </div>';
}

/**
 * Display article content safely (HTML from TinyMCE)
 * Simple sanitization to prevent XSS
 */
function display_content(string $html_content): string {
    if (empty($html_content)) return '';
    
    // Remove any script tags and dangerous attributes
    $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $html_content);
    $html = preg_replace('/javascript:/i', '', $html);
    $html = preg_replace('/on\w+\s*=/i', '', $html);
    
    // Remove dangerous event handlers
    $dangerous = ['onerror', 'onload', 'onclick', 'onmouseover', 'onkeydown', 'onkeyup'];
    foreach ($dangerous as $event) {
        $html = preg_replace('/' . $event . '\s*=/i', '', $html);
    }
    
    return $html;
}

/**
 * Simplified alias for display_content (backward compatibility)
 */
function editor_js_to_html(string $content): string {
    return display_content($content);
}