<?php
/**
 * admin/bootstrap.php
 * Include this ONE line at the top of every admin PHP file:
 *   require_once __DIR__ . '/../admin/bootstrap.php';   (from subfolders)
 *   require_once __DIR__ . '/bootstrap.php';            (from admin/ root)
 *
 * It loads config + functions and starts the session.
 */

// Resolve project root (one level up from /admin/)
$_ROOT = dirname(__DIR__);

require_once $_ROOT . '/config/db.php';
require_once $_ROOT . '/includes/functions.php';

// Di bootstrap.php, setelah session_start() atau di awal file

if (session_status() === PHP_SESSION_NONE) session_start();

