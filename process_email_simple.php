<?php
// SIMPLE EMAIL PROCESSING - NO APP PASSWORDS!
// Uses ONE admin Gmail account to send ALL emails

header('Content-Type: application/json');

// Suppress all PHP errors to avoid breaking JSON
error_reporting(0);
ini_set('display_errors', 0);

// Rest of the code...
require_once 'PHPMailer-master/src/PHPMailer.php';
require_once 'PHPMailer-master/src/SMTP.php';
require_once 'PHPMailer-master/src/Exception.php';
require_once 'auth.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST requests allowed']);
    exit;
}

// Check if user is logged in
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first.']);
    exit;
}

$user = $auth->getCurrentUser();
$serviceType = $_POST['service_type'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

// Get print settings (if available)
$paperSize = $_POST['paper'] ?? 'A4';
$orientation = $_POST['orientation'] ?? 'portrait';
$marginTop = $_POST['margin_top_mm'] ?? '10';
$marginRight = $_POST['margin_right_mm'] ?? '10';
$marginBottom = $_POST['margin_bottom_mm'] ?? '10';
$marginLeft = $_POST['margin_left_mm'] ?? '10';
$scale = $_POST['scale_percent'] ?? '100';
$fitMode = $_POST['fit_mode'] ?? 'Actual size';
$colorMode = $_POST['color_mode'] ?? 'color';
$copies = $_POST['copies'] ?? '1';
$duplex = $_POST['duplex'] ?? 'single';

// Validate required fields
if (empty($serviceType) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
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

// CUSTOMER GMAIL CREDENTIALS (used for sending emails)
$CUSTOMER_GMAIL = 'iskoprintcustomer@gmail.com';
$CUSTOMER_PASSWORD = 'tbyd krut layw wzye';

// ADMIN EMAIL (recipient)
$ADMIN_EMAIL = 'iskoprint6@gmail.com';

try {
    $mail = new PHPMailer(true);
    
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $CUSTOMER_GMAIL;
    $mail->Password   = $CUSTOMER_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';
    
    // Fix SSL certificate verification issue
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    // Send FROM customer Gmail account
    $mail->setFrom($CUSTOMER_GMAIL, $user['first_name'] . ' ' . $user['last_name']);
    $mail->addAddress($ADMIN_EMAIL);
    
    // Set Reply-To to the user's email so admin can reply to them
    $mail->addReplyTo($user['email'], $user['first_name'] . ' ' . $user['last_name']);
    
    // Attachments
    foreach ($attachments as $attachment) {
        $mail->addAttachment($attachment['tmp_name'], $attachment['name']);
    }
    
    // Email content
    $serviceNames = [
        'printer' => 'Printer Service',
        'bookbind' => 'Book Binding Service',
        'laminate' => 'Lamination Service',
        'pictures' => 'Picture Printing Service',
        'photocopy' => 'Photocopy Service',
        'tarpaulin' => 'Tarpaulin Printing Service'
    ];
    
    $serviceName = $serviceNames[$serviceType] ?? 'Printing Service';
    
    // Service-specific settings headings
    $settingsHeadings = [
        'printer' => 'Print Settings',
        'bookbind' => 'Book Binding Settings',
        'laminate' => 'Laminate Settings',
        'pictures' => 'Picture Settings',
        'photocopy' => 'Photocopy Settings',
        'tarpaulin' => 'Tarpaulin Settings'
    ];
    
    $settingsHeading = $settingsHeadings[$serviceType] ?? 'Print Settings';
    
    $emailBody = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Printing Service Request</title>
    </head>
    <body style='font-family: Arial, sans-serif; padding: 20px; background-color: #f8f9fa; margin: 0;'>
        <div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;'>
            
            <!-- Header -->
            <div style='background: linear-gradient(135deg, #750d0d, #a01515); color: white; padding: 20px; text-align: center;'>
                <h1 style='margin: 0; font-size: 24px; font-weight: bold;'>🖨️ New Printing Service Request</h1>
            </div>
            
            <!-- Customer Info -->
            <div style='background: #f8f9fa; padding: 20px; border-bottom: 1px solid #e9ecef;'>
                <h2 style='color: #750d0d; margin: 0 0 15px 0; font-size: 18px;'>👤 Customer Information</h2>
                <div style='background: white; padding: 15px; border-radius: 6px; border-left: 4px solid #750d0d;'>
                    <p style='margin: 8px 0; font-size: 14px;'><strong>📝 Name:</strong> {$user['first_name']} {$user['last_name']}</p>
                    <p style='margin: 8px 0; font-size: 14px;'><strong>📧 Email:</strong> <a href='mailto:{$user['email']}' style='color: #750d0d; text-decoration: none;'>{$user['email']}</a></p>
                    <p style='margin: 8px 0; font-size: 14px;'><strong>📱 Phone:</strong> <a href='tel:{$user['phone_number']}' style='color: #750d0d; text-decoration: none;'>{$user['phone_number']}</a></p>
                </div>
            </div>
            
            <!-- Request Details -->
            <div style='background: #fff3cd; padding: 20px; border-bottom: 1px solid #e9ecef;'>
                <h2 style='color: #856404; margin: 0 0 15px 0; font-size: 18px;'>📋 Request Details</h2>
                <div style='background: white; padding: 15px; border-radius: 6px; border-left: 4px solid #ffc107;'>
                    <p style='margin: 8px 0; font-size: 14px;'><strong>🔧 Service Type:</strong> {$serviceName}</p>
                    <p style='margin: 8px 0; font-size: 14px;'><strong>📌 Subject:</strong> {$subject}</p>
                </div>
            </div>
            
            <!-- Print Settings -->
            <div style='background: #e7f3ff; padding: 20px; border-bottom: 1px solid #e9ecef;'>
                <h2 style='color: #004085; margin: 0 0 15px 0; font-size: 18px;'>🖨️ {$settingsHeading}</h2>
                <div style='background: white; padding: 15px; border-radius: 6px; border-left: 4px solid #0066cc;'>
                    <div style='display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;'>
                        <p style='margin: 8px 0; font-size: 14px;'><strong>📄 Paper Size:</strong> " . htmlspecialchars($paperSize) . "</p>
                        <p style='margin: 8px 0; font-size: 14px;'><strong>🔄 Orientation:</strong> " . htmlspecialchars(ucfirst($orientation)) . "</p>
                        <p style='margin: 8px 0; font-size: 14px;'><strong>📏 Scale:</strong> " . htmlspecialchars($scale) . "%</p>
                        <p style='margin: 8px 0; font-size: 14px;'><strong>🎯 Fit Mode:</strong> " . htmlspecialchars($fitMode) . "</p>
                        <p style='margin: 8px 0; font-size: 14px;'><strong>🎨 Color:</strong> " . htmlspecialchars(ucfirst(str_replace(['bw', 'grayscale'], ['Black & White', 'Grayscale'], $colorMode))) . "</p>
                        <p style='margin: 8px 0; font-size: 14px;'><strong>📑 Copies:</strong> " . htmlspecialchars($copies) . "</p>
                    </div>
                    <div style='margin-top: 10px; padding-top: 10px; border-top: 1px solid #e9ecef;'>
                        <p style='margin: 8px 0; font-size: 14px;'><strong>📐 Margins (mm):</strong> Top: {$marginTop} | Right: {$marginRight} | Bottom: {$marginBottom} | Left: {$marginLeft}</p>
                        <p style='margin: 8px 0; font-size: 14px;'><strong>📖 Duplex:</strong> " . htmlspecialchars(ucfirst(str_replace(['single', 'long', 'short'], ['Single-sided', 'Double-sided (long edge)', 'Double-sided (short edge)'], $duplex))) . "</p>
                    </div>
                </div>
            </div>
            
            <!-- Message -->
            <div style='background: white; padding: 20px;'>
                <h2 style='color: #750d0d; margin: 0 0 15px 0; font-size: 18px;'>💬 Message</h2>
                <div style='background: #f8f9fa; padding: 15px; border-radius: 6px; border-left: 4px solid #750d0d;'>
                    <p style='margin: 0; font-size: 14px; line-height: 1.5;'>" . nl2br(htmlspecialchars($message)) . "</p>
                </div>
            </div>
            
            <!-- Footer -->
            <div style='background: #f8f9fa; padding: 15px; text-align: center; border-top: 1px solid #e9ecef;'>
                <p style='color: #6c757d; font-size: 12px; margin: 0;'>
                    This email was sent from the Print Shop ordering system.<br>
                    <strong>Reply directly to this email to contact the customer.</strong>
                </p>
            </div>
            
        </div>
    </body>
    </html>
    ";
    
    // Format duplex for display
    $duplexDisplay = str_replace(['single', 'long', 'short'], ['Single-sided', 'Double-sided (long edge)', 'Double-sided (short edge)'], $duplex);
    $colorDisplay = ucfirst(str_replace(['bw', 'grayscale'], ['Black & White', 'Grayscale'], $colorMode));
    
    $mail->isHTML(true);
    $mail->Subject = "[{$serviceName}] " . $subject;
    $mail->Body = $emailBody;
    $mail->AltBody = "Service: {$serviceName}\nFrom: {$user['first_name']} {$user['last_name']} ({$user['email']})\nSubject: {$subject}\n\n{$settingsHeading}:\nPaper Size: {$paperSize}\nOrientation: " . ucfirst($orientation) . "\nScale: {$scale}%\nFit Mode: {$fitMode}\nColor: {$colorDisplay}\nCopies: {$copies}\nMargins (mm): Top: {$marginTop}, Right: {$marginRight}, Bottom: {$marginBottom}, Left: {$marginLeft}\nDuplex: {$duplexDisplay}\n\nMessage:\n{$message}";
    
    $mail->send();
    
    // Send automatic response to user
    sendAutoResponseToUser($user, $serviceType, $subject, $serviceName);
    
    echo json_encode(['success' => true, 'message' => 'Email sent successfully to admin!']);
    
} catch (Exception $e) {
    // Get error message
    $errorMessage = $e->getMessage();
    
    // Return error with full details for debugging
    echo json_encode([
        'success' => false,
        'message' => 'Email sending failed. Error: ' . $errorMessage
    ]);
}

// Function to send automatic response to user
function sendAutoResponseToUser($user, $serviceType, $subject, $serviceName) {
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'iskoprintcustomer@gmail.com';
        $mail->Password   = 'tbyd krut layw wzye';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        
        // Fix SSL certificate verification issue
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Send FROM admin account TO user
        $mail->setFrom('iskoprint6@gmail.com', 'Isko Print Admin');
        $mail->addAddress($user['email'], $user['first_name'] . ' ' . $user['last_name']);
        
        // Auto-response content
        $responseMessages = [
            'printer' => 'Thank you for your printer service request! We have received your files and will process your printing order within 24 hours. You will receive another email once your order is ready for pickup.',
            'bookbind' => 'Thank you for your book binding request! We have received your documents and will begin the binding process. Estimated completion time is 2-3 business days.',
            'laminate' => 'Thank you for your lamination request! We have received your documents and will process them within 24 hours. Your laminated items will be ready for pickup soon.',
            'pictures' => 'Thank you for your picture printing request! We have received your photos and will print them in high quality. Your photos will be ready for pickup within 24 hours.',
            'photocopy' => 'Thank you for your photocopy request! We have received your documents and will process them immediately. Your copies will be ready for pickup within 2 hours.',
            'tarpaulin' => 'Thank you for your tarpaulin printing request! We have received your design and will begin printing. Large format printing takes 1-2 business days to complete.'
        ];
        
        $responseMessage = $responseMessages[$serviceType] ?? 'Thank you for your service request! We have received your files and will process them as soon as possible.';
        
        $emailBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Service Request Confirmation</title>
        </head>
        <body style='font-family: Arial, sans-serif; padding: 20px; background-color: #f8f9fa; margin: 0;'>
            <div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;'>
                
                <!-- Header -->
                <div style='background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 20px; text-align: center;'>
                    <h1 style='margin: 0; font-size: 24px; font-weight: bold;'>✅ Request Confirmed</h1>
                </div>
                
                <!-- Customer Info -->
                <div style='background: #f8f9fa; padding: 20px; border-bottom: 1px solid #e9ecef;'>
                    <h2 style='color: #28a745; margin: 0 0 15px 0; font-size: 18px;'>👋 Hello {$user['first_name']}!</h2>
                    <div style='background: white; padding: 15px; border-radius: 6px; border-left: 4px solid #28a745;'>
                        <p style='margin: 8px 0; font-size: 14px;'><strong>📧 Email:</strong> {$user['email']}</p>
                        <p style='margin: 8px 0; font-size: 14px;'><strong>📱 Phone:</strong> {$user['phone_number']}</p>
                    </div>
                </div>
                
                <!-- Request Details -->
                <div style='background: #d1ecf1; padding: 20px; border-bottom: 1px solid #e9ecef;'>
                    <h2 style='color: #0c5460; margin: 0 0 15px 0; font-size: 18px;'>📋 Your Request</h2>
                    <div style='background: white; padding: 15px; border-radius: 6px; border-left: 4px solid #17a2b8;'>
                        <p style='margin: 8px 0; font-size: 14px;'><strong>🔧 Service:</strong> {$serviceName}</p>
                        <p style='margin: 8px 0; font-size: 14px;'><strong>📌 Subject:</strong> {$subject}</p>
                        <p style='margin: 8px 0; font-size: 14px;'><strong>📅 Submitted:</strong> " . date('F j, Y \a\t g:i A') . "</p>
                    </div>
                </div>
                
                <!-- Response Message -->
                <div style='background: white; padding: 20px;'>
                    <h2 style='color: #28a745; margin: 0 0 15px 0; font-size: 18px;'>💬 Our Response</h2>
                    <div style='background: #d4edda; padding: 15px; border-radius: 6px; border-left: 4px solid #28a745;'>
                        <p style='margin: 0; font-size: 14px; line-height: 1.6; color: #155724;'>{$responseMessage}</p>
                    </div>
                </div>
                
                <!-- Contact Info -->
                <div style='background: #f8f9fa; padding: 20px; border-top: 1px solid #e9ecef;'>
                    <h2 style='color: #6c757d; margin: 0 0 15px 0; font-size: 16px;'>📞 Need Help?</h2>
                    <div style='background: white; padding: 15px; border-radius: 6px;'>
                        <p style='margin: 8px 0; font-size: 14px;'><strong>📧 Email:</strong> iskoprint6@gmail.com</p>
                        <p style='margin: 8px 0; font-size: 14px;'><strong>📱 Phone:</strong> (02) 123-4567</p>
                        <p style='margin: 8px 0; font-size: 14px;'><strong>🕒 Hours:</strong> Mon-Fri 8AM-6PM, Sat 9AM-4PM</p>
                    </div>
                </div>
                
                <!-- Footer -->
                <div style='background: #6c757d; color: white; padding: 15px; text-align: center;'>
                    <p style='margin: 0; font-size: 12px;'>
                        This is an automated response from Isko Print Shop.<br>
                        <strong>Please do not reply to this email.</strong>
                    </p>
                </div>
                
            </div>
        </body>
        </html>
        ";
        
        $mail->isHTML(true);
        $mail->Subject = "✅ Confirmation: {$serviceName} Request Received";
        $mail->Body = $emailBody;
        $mail->AltBody = "Hello {$user['first_name']}!\n\nYour {$serviceName} request has been received.\n\nSubject: {$subject}\nSubmitted: " . date('F j, Y \a\t g:i A') . "\n\n{$responseMessage}\n\nContact: iskoprint6@gmail.com\nPhone: (02) 123-4567";
        
        $mail->send();
        
    } catch (Exception $e) {
        // Log error but don't break the main flow
        error_log("Auto-response failed: " . $e->getMessage());
    }
}
?>
