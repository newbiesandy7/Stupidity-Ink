<?php
/**
 * Quick Database Viewer
 * Access via: http://localhost/Stupidity-ink/Stupidity-Ink/server/view_db.php
 * DELETE THIS FILE after use for security!
 */

require_once 'config.php';

echo "<h1>📊 Database Viewer</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #1a1a1a; color: #fff; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; background: #2a2a2a; }
    th, td { border: 1px solid #444; padding: 10px; text-align: left; }
    th { background: #333; color: #ffd700; }
    tr:nth-child(even) { background: #333; }
    h2 { color: #ffd700; margin-top: 30px; }
    .warning { background: #ff4444; color: white; padding: 10px; border-radius: 5px; }
</style>";

try {
    $pdo = getDBConnection();
    
    // Show Images Table
    echo "<h2>🖼️ Images Table</h2>";
    $stmt = $pdo->query("SELECT * FROM images ORDER BY created_at DESC");
    $images = $stmt->fetchAll();
    
    if (count($images) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Filename</th><th>Filepath</th><th>Alt Text</th><th>Uploaded By</th><th>Created At</th></tr>";
        foreach ($images as $img) {
            echo "<tr>";
            echo "<td>{$img['id']}</td>";
            echo "<td>{$img['filename']}</td>";
            echo "<td>{$img['filepath']}</td>";
            echo "<td>{$img['alt_text']}</td>";
            echo "<td>{$img['uploaded_by']}</td>";
            echo "<td>{$img['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No images in database yet.</p>";
    }
    
    // Show Users Table
    echo "<h2>👤 Users Table</h2>";
    $stmt = $pdo->query("SELECT id, username, created_at FROM users");
    $users = $stmt->fetchAll();
    
    if (count($users) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Username</th><th>Created At</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['username']}</td>";
            echo "<td>{$user['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<br><p class='warning'>⚠️ DELETE THIS FILE (view_db.php) after use for security!</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Make sure you've run setup_database.php first!</p>";
}
?>
