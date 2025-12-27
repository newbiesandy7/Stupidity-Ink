<?php
/**
 * Images API Endpoint
 * GET /server/images.php - Get all images
 * DELETE /server/images.php?id=X - Delete image
 */

require_once 'config.php';

$pdo = getDBConnection();

// GET - Fetch all images
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM images ORDER BY created_at DESC");
        $images = $stmt->fetchAll();
        
        // Format response
        $formattedImages = array_map(function($img) {
            return [
                'id' => $img['id'],
                'filename' => $img['filename'],
                'filepath' => $img['filepath'],
                'alt' => $img['alt_text'],
                'uploaded_by' => $img['uploaded_by'],
                'created_at' => $img['created_at']
            ];
        }, $images);
        
        echo json_encode(['success' => true, 'images' => $formattedImages]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error fetching images']);
    }
    exit;
}

// DELETE - Remove an image
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    require_once 'auth.php';
    $user = validateToken();
    
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Image ID required']);
        exit;
    }
    
    try {
        // Get image info first
        $stmt = $pdo->prepare("SELECT * FROM images WHERE id = ?");
        $stmt->execute([$id]);
        $image = $stmt->fetch();
        
        if (!$image) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Image not found']);
            exit;
        }
        
        // Delete file from filesystem
        $filepath = __DIR__ . '/../' . $image['filepath'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM images WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Image deleted successfully']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error deleting image']);
    }
    exit;
}

// Method not allowed
http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
?>
