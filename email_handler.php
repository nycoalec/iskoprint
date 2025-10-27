<?php
require_once 'PHPMailer-master/src/PHPMailer.php';
require_once 'PHPMailer-master/src/SMTP.php';
require_once 'PHPMailer-master/src/Exception.php';
require_once 'auth.php';
require_once 'config/email_credentials.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailHandler {
    private $auth;
    
    public function __construct() {
        $this->auth = new Auth();
    }
    
    public function sendEmail($to, $subject, $message, $attachments = []) {
        // Check if user is logged in
        if (!$this->auth->isLoggedIn()) {
            return [
                'success' => false,
                'message' => 'You must be logged in to send emails.'
            ];
        }
        
        $user = $this->auth->getCurrentUser();
        $userEmail = $user['email'];
        
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $userEmail; // Use logged-in user's email
            $mail->Password   = $this->getUserEmailPassword($userEmail); // You'll need to implement this
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            
            // Recipients
            $mail->setFrom($userEmail, $user['first_name'] . ' ' . $user['last_name']);
            $mail->addAddress($to);
            $mail->addReplyTo($userEmail, $user['first_name'] . ' ' . $user['last_name']);
            
            // Attachments
            foreach ($attachments as $attachment) {
                if (isset($attachment['tmp_name']) && file_exists($attachment['tmp_name'])) {
                    $mail->addAttachment($attachment['tmp_name'], $attachment['name']);
                }
            }
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = nl2br($message);
            $mail->AltBody = $message;
            
            $mail->send();
            
            return [
                'success' => true,
                'message' => 'Email sent successfully!'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Email could not be sent. Error: {$mail->ErrorInfo}"
            ];
        }
    }
    
    private function getUserEmailPassword($email) {
        $password = getUserEmailPassword($email);
        
        if (!$password) {
            throw new Exception("No email password configured for user: $email. Please contact administrator.");
        }
        
        return $password;
    }
    
    public function sendPrintingServiceEmail($serviceType, $subject, $message, $attachments = []) {
        $user = $this->auth->getCurrentUser();
        
        // All emails go to admin account
        $adminEmail = 'iskoprint6@gmail.com';
        
        // Add service-specific information to the email
        $serviceInfo = $this->getServiceInfo($serviceType);
        $enhancedMessage = "
        <h3>Printing Service Request: {$serviceInfo['name']}</h3>
        <p><strong>Customer:</strong> {$user['first_name']} {$user['last_name']} ({$user['email']})</p>
        <p><strong>Service Type:</strong> {$serviceInfo['name']}</p>
        <p><strong>Description:</strong></p>
        <div style='background: #f5f5f5; padding: 15px; border-left: 4px solid #750d0d;'>
            " . nl2br($message) . "
        </div>
        ";
        
        return $this->sendEmail($adminEmail, "[{$serviceInfo['name']}] " . $subject, $enhancedMessage, $attachments);
    }
    
    private function getServiceInfo($serviceType) {
        $services = [
            'printer' => ['name' => 'Printer Service', 'email' => getServiceRecipient('printer')],
            'bookbind' => ['name' => 'Book Binding Service', 'email' => getServiceRecipient('bookbind')],
            'laminate' => ['name' => 'Lamination Service', 'email' => getServiceRecipient('laminate')],
            'pictures' => ['name' => 'Picture Printing Service', 'email' => getServiceRecipient('pictures')],
            'photocopy' => ['name' => 'Photocopy Service', 'email' => getServiceRecipient('photocopy')],
            'tarpaulin' => ['name' => 'Tarpaulin Printing Service', 'email' => getServiceRecipient('tarpaulin')]
        ];
        
        return $services[$serviceType] ?? ['name' => 'Printing Service', 'email' => 'info@yourcompany.com'];
    }
}
?>
