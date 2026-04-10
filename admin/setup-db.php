<?php
/**
 * Database Setup Script
 * 
 * Run this once to create all necessary tables.
 * Then delete this file for security.
 */

require_once __DIR__ . '/../config/db.php';

// ── Create contact_messages table ──────────────────────────────
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS contact_messages (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            phone VARCHAR(20),
            message LONGTEXT NOT NULL,
            is_read TINYINT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX(is_read),
            INDEX(created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Table 'contact_messages' created successfully.<br>";
} catch (Exception $e) {
    echo "✗ Error creating 'contact_messages': " . $e->getMessage() . "<br>";
}

// ── Create articles table ──────────────────────────────────────
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS articles (
            id INT PRIMARY KEY AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            category VARCHAR(100),
            excerpt TEXT,
            content LONGTEXT,
            thumbnail VARCHAR(255),
            meta_title VARCHAR(255),
            meta_description VARCHAR(255),
            status ENUM('draft','published') DEFAULT 'draft',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX(status),
            INDEX(slug),
            INDEX(created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Table 'articles' created successfully.<br>";
} catch (Exception $e) {
    echo "✗ Error creating 'articles': " . $e->getMessage() . "<br>";
}

// ── Create services table ──────────────────────────────────────
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS services (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            description TEXT,
            icon VARCHAR(100),
            image VARCHAR(255),
            sort_order INT DEFAULT 0,
            is_active TINYINT DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX(is_active),
            INDEX(slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Table 'services' created successfully.<br>";
} catch (Exception $e) {
    echo "✗ Error creating 'services': " . $e->getMessage() . "<br>";
}

// ── Create settings table ──────────────────────────────────────
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            key_name VARCHAR(100) UNIQUE NOT NULL,
            value LONGTEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Table 'settings' created successfully.<br>";
} catch (Exception $e) {
    echo "✗ Error creating 'settings': " . $e->getMessage() . "<br>";
}

// ── Create admin_users table (for admin login) ────────────────────────
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            username VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            is_active TINYINT DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX(username),
            INDEX(is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Table 'admin_users' created successfully.<br>";
} catch (Exception $e) {
    echo "✗ Error creating 'admin_users': " . $e->getMessage() . "<br>";
}

// ── Create default admin user if not exists ────────────────────
try {
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = 'admin' LIMIT 1");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $default_pass = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO admin_users (name, username, password, is_active) VALUES (?, ?, ?, 1)")
            ->execute(['Administrator', 'admin', $default_pass]);
        echo "✓ Default admin user created (username: admin, password: admin123)<br>";
        echo "<strong>⚠️ IMPORTANT: Change your password immediately after first login!</strong><br>";
    }
} catch (Exception $e) {
    echo "✗ Error creating admin user: " . $e->getMessage() . "<br>";
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Database Setup</title>
    <style>
        body { font-family: sans-serif; margin: 40px; line-height: 1.6; }
        .success { color: green; }
        .error { color: red; }
        .box { background: #f5f5f5; padding: 20px; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>✓ Database Setup Complete</h1>
    <div class="box">
        <p>All tables have been created successfully!</p>
        <p><strong>Important:</strong> Delete this file (setup-db.php) immediately for security reasons.</p>
        <p><a href="<?php echo getenv('BASE_URL') ?? 'http://localhost/company_profile'; ?>/admin/login.php">Go to Admin Login</a></p>
    </div>
</body>
</html>