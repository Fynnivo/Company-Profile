<?php
require_once __DIR__ . '/../bootstrap.php';

// Only logged-in admins can upload
if (empty($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

// Jodit sends as $_FILES['file'], fallback to any other key
$file_key = isset($_FILES['file']) ? 'file' : (array_key_first($_FILES) ?: null);

if (!$file_key || empty($_FILES[$file_key]['name'])) {
    echo json_encode(['error' => 'No file received.']); exit;
}

// Debug: Check file info
$file = $_FILES[$file_key];
if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds php.ini upload_max_filesize',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds form MAX_FILE_SIZE',
        UPLOAD_ERR_PARTIAL => 'File partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temp folder',
        UPLOAD_ERR_CANT_WRITE => 'Cannot write to disk',
        UPLOAD_ERR_EXTENSION => 'Blocked by PHP extension'
    ];
    $error_msg = $errors[$file['error']] ?? 'Unknown upload error';
    echo json_encode(['error' => $error_msg]);
    exit;
}

$path = upload_image($file, 'articles');

if ($path) {
    $url = BASE_URL . '/' . $path;
    // Return format compatible with TinyMCE
    echo json_encode([
        'location' => $url
    ]);
} else {
    echo json_encode(['error' => 'Upload gagal. Gunakan JPG, PNG, atau WebP. Pastikan folder uploads/articles/ memiliki permission 777']);
}