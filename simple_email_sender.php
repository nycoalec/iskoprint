<?php
// Simple email sender using SendGrid (NO APP PASSWORDS NEEDED!)
// Free tier: 100 emails/day forever

class SimpleEmailSender {
    
    public static function sendToAdmin($fromEmail, $fromName, $subject, $message) {
        // Send to admin using SendGrid API
        // NO Gmail App Passwords needed!
        
        $adminEmail = 'iskoprint6@gmail.com';
        
        // Configure SendGrid (get free API key at sendgrid.com)
        $apiKey = 'YOUR_SENDGRID_API_KEY'; // Free - 100 emails/day
        
        $emailData = [
            'personalizations' => [
                [
                    'to' => [['email' => $adminEmail]]
                ]
            ],
            'from' => ['email' => 'noreply@yourdomain.com'],
            'reply_to' => ['email' => $fromEmail],
            'subject' => $subject,
            'content' => [
                [
                    'type' => 'text/html',
                    'value' => $message
                ]
            ]
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 202) {
            return ['success' => true, 'message' => 'Email sent successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to send email.'];
        }
    }
    
    public static function sendPrintingRequest($serviceType, $subject, $message) {
        require_once 'auth.php';
        $auth = new Auth();
        
        if (!$auth->isLoggedIn()) {
            return ['success' => false, 'message' => 'Please login first.'];
        }
        
        $user = $auth->getCurrentUser();
        
        $emailContent = "
        <html>
        <body style='font-family: Arial, sans-serif; padding: 20px;'>
            <h2 style='color: #750d0d;'>Printing Service Request: " . ucfirst($serviceType) . "</h2>
            <div style='background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <p><strong>Customer Name:</strong> {$user['first_name']} {$user['last_name']}</p>
                <p><strong>Email:</strong> {$user['email']}</p>
                <p><strong>Phone:</strong> {$user['phone_number']}</p>
                <p><strong>Service:</strong> {$serviceType}</p>
                <p><strong>Subject:</strong> {$subject}</p>
            </div>
            <h3>Message:</h3>
            <div style='background: white; padding: 15px; border-left: 4px solid #750d0d;'>
                " . nl2br(htmlspecialchars($message)) . "
            </div>
        </body>
        </html>
        ";
        
        return self::sendToAdmin(
            $user['email'],
            $user['first_name'] . ' ' . $user['last_name'],
            "[{$serviceType}] " . $subject,
            $emailContent
        );
    }
}
?>

<?php
// ALTERNATIVE: Even simpler - use Mailgun (10,000 emails/month free!)
// No configuration needed - just sign up at mailgun.com

function sendSimpleEmail($fromEmail, $fromName, $subject, $message) {
    $adminEmail = 'iskoprint6@gmail.com';
    
    // Mailgun settings
    $mgDomain = 'your-domain.com';
    $mgApiKey = 'your-mailgun-api-key';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.mailgun.net/v3/{$mgDomain}/messages");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'from' => "{$fromName} <{$fromEmail}>",
        'to' => $adminEmail,
        'subject' => $subject,
        'html' => $message
    ]);
    curl_setopt($ch, CURLOPT_USERPWD, "api:{$mgApiKey}");
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response);
}
?>
