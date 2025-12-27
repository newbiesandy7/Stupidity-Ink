<?php
/**
 * Login API Endpoint
 * POST /server/login.php
 */

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Find user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
        exit;
    }
    
    // Generate simple token (in production, use proper JWT library)
    $tokenData = [
        'id' => $user['id'],
        'username' => $user['username'],
        'exp' => time() + (24 * 60 * 60) // 24 hours
    ];
    $token = base64_encode(json_encode($tokenData) . '.' . hash('sha256', json_encode($tokenData) . JWT_SECRET));
    
    echo json_encode([
        'success' => true,
        'token' => $token,
        'username' => $user['username']
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
