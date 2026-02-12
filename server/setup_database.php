<?php
/**
 * Database Setup Script
 * Run this file once to create the database and tables
 * Access via: http://localhost/your-path/server/setup_database.php
 */

// Database credentials (same as config.php)
$host = 'localhost';
$user = 'root';          // Change to your MySQL username
$pass = '';              // Change to your MySQL password
$dbname = 'stupidity_ink';

echo "<h1>🎨 Stupidity Ink - Database Setup</h1>";
echo "<pre>";

try {
    // Connect to MySQL (without database selected)
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✓ Connected to MySQL server\n";

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database '$dbname' created/verified\n";

    // Select the database
    $pdo->exec("USE `$dbname`");
    echo "✓ Using database '$dbname'\n";

    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ Users table created/verified\n";

    // Create images table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL,
            filepath VARCHAR(500) NOT NULL,
            alt_text VARCHAR(500),
            uploaded_by VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ Images table created/verified\n";

    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    $adminExists = $stmt->fetch();

    if (!$adminExists) {
        // Create default admin user
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute(['admin', $hashedPassword]);
        echo "✓ Default admin user created\n";
        echo "\n";
        echo "╔════════════════════════════════════════╗\n";
        echo "║  DEFAULT LOGIN CREDENTIALS             ║\n";
        echo "║  Username: admin                       ║\n";
        echo "║  Password: admin123                    ║\n";
        echo "║                                        ║\n";
        echo "║  ⚠️  Change password after first login! ║\n";
        echo "╚════════════════════════════════════════╝\n";
    } else {
        echo "✓ Admin user already exists\n";
    }

    // Create uploads directory
    $uploadDir = __DIR__ . '/../uploads';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
        echo "✓ Uploads directory created\n";
    } else {
        echo "✓ Uploads directory exists\n";
    }

    echo "\n";
    echo "════════════════════════════════════════\n";
    echo "🎉 DATABASE SETUP COMPLETE!\n";
    echo "════════════════════════════════════════\n";
    echo "\n";
    echo "You can now:\n";
    echo "1. Go to login.html to login as admin\n";
    echo "2. Upload images from the admin dashboard\n";
    echo "\n";
    echo "⚠️  DELETE THIS FILE after setup for security!\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nMake sure:\n";
    echo "1. MySQL/XAMPP is running\n";
    echo "2. Database credentials are correct\n";
}

echo "</pre>";
?>
