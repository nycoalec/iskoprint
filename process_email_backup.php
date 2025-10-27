<?php
// Ultra simple backup email method - NO CONFIGURATION NEEDED!
header('Content-Type: application/json');

require_once 'auth.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first.']);
    exit;
}

$user = $auth->getCurrentUser();
$serviceType = $_POST['service_type'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

if (empty($serviceType) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Simple PHP mail() function - works without PHPMailer!
$adminEmail = 'iskoprint6@gmail.com';
$emailContent = "
New Printing Service Request

Customer: {$user['first_name']} {$user['last_name']}
Email: {$user['email']}
Phone: {$user['phone_number']}
Service: {$serviceType}

Subject: {$subject}

Message:
{$message}
";

$headers = "From: {$user['email']}" . "\r\n";
$headers .= "Reply-To: {$user['email']}" . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8";

if (mail($adminEmail, "[{$serviceType}] " . $subject, $emailContent, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Email sent successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
}
?>
