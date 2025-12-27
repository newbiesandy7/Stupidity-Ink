<?php
/**
 * Authentication Helper
 * Validates JWT tokens for protected routes
 */

require_once 'config.php';

function validateToken() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    
    if (empty($authHeader)) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'No authorization token provided']);
        exit;
    }
    
    // Extract token from "Bearer <token>"
    $token = str_replace('Bearer ', '', $authHeader);
    
    try {
        // Decode and verify token
        $decoded = base64_decode($token);
        $parts = explode('.', $decoded);
        
        if (count($parts) !== 2) {
            throw new Exception('Invalid token format');
        }
        
        $tokenData = json_decode($parts[0], true);
        $signature = $parts[1];
        
        // Verify signature
        $expectedSignature = hash('sha256', $parts[0] . JWT_SECRET);
        if ($signature !== $expectedSignature) {
            throw new Exception('Invalid token signature');
        }
        
        // Check expiration
        if ($tokenData['exp'] < time()) {
            throw new Exception('Token has expired');
        }
        
        return $tokenData;
        
    } catch (Exception $e) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid or expired token']);
        exit;
    }
}
?>
