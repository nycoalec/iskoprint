<?php
require_once 'email_handler.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST requests allowed']);
    exit;
}

$serviceType = $_POST['service_type'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

// Validate required fields
if (empty($serviceType) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Subject and message are required']);
    exit;
}

// Handle file uploads
$attachments = [];
if (isset($_FILES['file_upload']) && !empty($_FILES['file_upload']['name'][0])) {
    $files = $_FILES['file_upload'];
    $fileCount = count($files['name']);
    
    for ($i = 0; $i < $fileCount; $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $attachments[] = [
                'name' => $files['name'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'size' => $files['size'][$i],
                'type' => $files['type'][$i]
            ];
        }
    }
}

// Validate file upload requirement
if (empty($attachments)) {
    echo json_encode(['success' => false, 'message' => 'Please select at least one file to upload!']);
    exit;
}

try {
    $emailHandler = new EmailHandler();
    $result = $emailHandler->sendPrintingServiceEmail($serviceType, $subject, $message, $attachments);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
