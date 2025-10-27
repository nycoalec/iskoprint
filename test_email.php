<?php
require_once 'auth.php';
require_once 'email_handler.php';

$auth = new Auth();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_email'])) {
    if (!$auth->isLoggedIn()) {
        $error = 'You must be logged in to test email functionality.';
    } else {
        $user = $auth->getCurrentUser();
        $testEmail = $_POST['test_email'];
        
        try {
            $emailHandler = new EmailHandler();
            $result = $emailHandler->sendEmail(
                $testEmail,
                'Test Email from Print Shop',
                'This is a test email to verify your Gmail configuration is working correctly.',
                []
            );
            
            if ($result['success']) {
                $message = 'Test email sent successfully!';
            } else {
                $error = $result['message'];
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Test - Print Shop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #750d0d;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background: #750d0d;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #5d0a0a;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            margin-bottom: 20px;
        }
        .user-info {
            background: #e2e3e5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Email Configuration Test</h1>
        
        <?php if ($auth->isLoggedIn()): ?>
            <?php $user = $auth->getCurrentUser(); ?>
            <div class="user-info">
                <strong>Logged in as:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?><br>
                <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
            </div>
        <?php else: ?>
            <div class="error">
                <strong>Not logged in!</strong> Please <a href="login_acc.php">login</a> first to test email functionality.
            </div>
        <?php endif; ?>
        
         <div class="info">
             <strong>📋 Setup Instructions:</strong><br>
             1. Enable 2-Factor Authentication on your Gmail account<br>
             2. Generate an App Password for this service<br>
             3. Add your credentials to <code>config/email_credentials.php</code><br>
             4. All emails will be sent TO: iskoprint6@gmail.com (admin)<br>
             5. All emails will be sent FROM: Your Gmail account<br>
             6. Test the email functionality below
         </div>
        
        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($auth->isLoggedIn()): ?>
             <form method="POST">
                 <div class="form-group">
                     <label for="test_email">Send test email to admin:</label>
                     <input type="email" id="test_email" name="test_email" value="iskoprint6@gmail.com" readonly style="background: #f0f0f0;">
                     <small style="color: #666; font-size: 12px;">All emails are sent to iskoprint6@gmail.com (admin)</small>
                 </div>
                 <button type="submit">Send Test Email</button>
             </form>
        <?php endif; ?>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="index.php">← Back to Home</a>
        </div>
    </div>
</body>
</html>
