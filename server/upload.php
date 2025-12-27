<?php
/**
 * Image Upload API Endpoint
 * POST /server/upload.php
 */

require_once 'config.php';
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Validate authentication
$user = validateToken();

// Check if file was uploaded
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload'
    ];
    
    $error = $_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE;
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $errorMessages[$error] ?? 'Upload error']);
    exit;
}

$file = $_FILES['image'];
$altText = $_POST['alt'] ?? '';

// Validate file type
if (!in_array($file['type'], ALLOWED_TYPES)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: JPG, PNG, GIF, WebP']);
    exit;
}

// Validate file size
if ($file['size'] > MAX_FILE_SIZE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum size: 10MB']);
    exit;
}

// Create uploads directory if it doesn't exist
$uploadDir = __DIR__ . '/../uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$newFilename = uniqid() . '_' . time() . '.' . $extension;
$destination = $uploadDir . $newFilename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save file']);
    exit;
}

// Save to database
try {
    $pdo = getDBConnection();
    $filepath = 'uploads/' . $newFilename;
    
    $stmt = $pdo->prepare("INSERT INTO images (filename, filepath, alt_text, uploaded_by) VALUES (?, ?, ?, ?)");
    $stmt->execute([$newFilename, $filepath, $altText, $user['username']]);
    
    $imageId = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Image uploaded successfully',
        'image' => [
            'id' => $imageId,
            'filename' => $newFilename,
            'filepath' => $filepath,
            'alt' => $altText
        ]
    ]);
    
} catch (Exception $e) {
    // Delete the uploaded file if database insert fails
    if (file_exists($destination)) {
        unlink($destination);
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
