<?php
// Simple email handler that uses ONE admin Gmail account
// No App Passwords needed - just use regular password or OAuth

require_once 'auth.php';

if (!isset($auth)) {
    $auth = new Auth();
}

// Admin Gmail account credentials (CHANGE THESE!)
define('ADMIN_GMAIL', 'iskoprint6@gmail.com');
define('ADMIN_GMAIL_PASSWORD', ''); // ⬅️ ADD YOUR GMAIL PASSWORD HERE

class SimpleEmailSender {
    
    public static function sendMail($to, $subject, $message, $fromEmail, $fromName) {
        // Use PHP's built-in mail() function (simplest way)
        $headers = "From: " . $fromName . " <" . ADMIN_GMAIL . ">\r\n";
        $headers .= "Reply-To: " . $fromEmail . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        // Send email
        return mail($to, $subject, $message, $headers);
    }
    
    public static function sendPrintingService($serviceType, $subject, $message, $attachments = []) {
        if (!isset($auth)) {
            $auth = new Auth();
        }
        
        if (!$auth->isLoggedIn()) {
            return ['success' => false, 'message' => 'Please login first.'];
        }
        
        $user = $auth->getCurrentUser();
        
        // All emails go to admin
        $adminEmail = 'iskoprint6@gmail.com';
        
        // Create email content
        $emailContent = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>Printing Service Request</h2>
            <p><strong>From:</strong> {$user['first_name']} {$user['last_name']}</p>
            <p><strong>Email:</strong> {$user['email']}</p>
            <p><strong>Service:</strong> {$serviceType}</p>
            <p><strong>Subject:</strong> {$subject}</p>
            <hr>
            <p><strong>Message:</strong></p>
            <p>" . nl2br($message) . "</p>
        </body>
        </html>
        ";
        
        // Send email
        $result = self::sendMail(
            $adminEmail,
            "[{$serviceType}] " . $subject,
            $emailContent,
            $user['email'],
            $user['first_name'] . ' ' . $user['last_name']
        );
        
        if ($result) {
            return ['success' => true, 'message' => 'Email sent successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to send email.'];
        }
    }
}
?>
