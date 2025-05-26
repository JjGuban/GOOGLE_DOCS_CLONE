<?php
session_start();

header('Content-Type: application/json');

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Check if image was uploaded
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'No image uploaded or upload failed']);
    exit;
}

$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$fileType = mime_content_type($_FILES['image']['tmp_name']);

if (!in_array($fileType, $allowedTypes)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid image type']);
    exit;
}

// Generate a unique name
$ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
$filename = uniqid('img_', true) . '.' . $ext;

// Upload path
$uploadDir = '../uploads/';
$uploadPath = $uploadDir . $filename;

if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save image']);
    exit;
}

// Return URL relative to your project
$imageUrl = '/googledocs_clone/uploads/' . $filename;
echo json_encode(['status' => 'success', 'url' => $imageUrl]);
exit;
