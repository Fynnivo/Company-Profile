<?php
require_once __DIR__ . '/../bootstrap.php';

// Only logged-in admins can upload
if (empty($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

if (empty($_FILES['file']['name'])) {
    echo json_encode(['error' => 'No file received.']); exit;
}

$path = upload_image($_FILES['file'], 'articles');

if ($path) {
    echo json_encode(['location' => BASE_URL . '/' . $path]);
} else {
    echo json_encode(['error' => 'Upload gagal. Gunakan JPG, PNG, atau WebP.']);
}