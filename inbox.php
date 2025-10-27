<?php
require_once 'auth.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: login_acc.php');
    exit();
}

$currentUser = $auth->getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox - Print Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace;
            color: #1a1a1a;
            background: linear-gradient(180deg, rgba(255,249,249,0.8) 0%, rgba(251,238,238,0.8) 100%), 
                        url('assets/pup_bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }

        .app {
            max-width: 1100px;
            margin: 32px auto;
            padding: 0 16px;
        }

        .printer {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.08), inset 0 -2px 0 #f0f0f0;
            padding: 24px;
            position: relative;
        }

        .paper {
            position: relative;
        }

        .dotmatrix-lines {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: repeating-linear-gradient(
                transparent,
                transparent 24px,
                #e0e0e0 24px,
                #e0e0e0 25px
            );
            pointer-events: none;
            opacity: 0.3;
        }

        .paper-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px dotted #d4a574;
        }

        .paper-header strong {
            font-size: 18px;
            font-weight: bold;
            color: #750d0d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .printer-head {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: inherit;
            white-space: nowrap;
        }

        .logo img {
            height: 45px;
            width: auto;
        }

        .printer-title {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #750d0d;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8f9fa;
            padding: 8px 15px;
            border-radius: 20px;
            border: 1px solid #e9ecef;
        }

        .user-info i {
            color: #3d4a66;
        }

        .user-info span {
            color: #3d4a66;
            font-weight: 500;
        }

        .controls {
            display: flex;
            gap: 10px;
        }

        .controls button {
            background: white;
            border: 2px solid #3d4a66;
            color: #3d4a66;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .controls button:hover {
            background: #3d4a66;
            color: white;
        }

        .section {
            margin-top: 20px;
        }

        .inbox-nav {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 2px dotted #d4a574;
        }

        .nav-tab {
            padding: 15px 25px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            font-weight: bold;
            color: #3d4a66;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-tab.active {
            background: white;
            border-bottom-color: #3d4a66;
            color: #3d4a66;
        }

        .nav-tab:hover {
            background: #f8f9fa;
        }

        .email-list {
            display: none;
        }

        .email-list.active {
            display: block;
        }

        .email-item {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 20px;
            background: white;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .email-item:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
            border-color: #3d4a66;
        }

        .email-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .email-from {
            font-weight: bold;
            color: #3d4a66;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .email-date {
            color: #6c757d;
            font-size: 14px;
        }

        .email-subject {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .email-preview {
            color: #6c757d;
            line-height: 1.5;
        }

        .email-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-sent {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-received {
            background: #cce5ff;
            color: #004085;
            border: 1px solid #b3d7ff;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
            color: #3d4a66;
        }

        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #3d4a66;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .empty-state p {
            font-size: 16px;
            line-height: 1.5;
        }

        .back-btn {
            background: #3d4a66;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-btn:hover {
            background: #2c3e50;
        }

        .compose-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px dotted #d4a574;
        }

        .ticker {
            color: #3d4a66;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @media (max-width: 768px) {
            .paper-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .inbox-nav {
                flex-wrap: wrap;
            }
            
            .nav-tab {
                flex: 1;
                text-align: center;
            }

            .compose-actions {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="app">
        <div class="printer" role="region" aria-label="Printer Mail UI">
            <div class="printer-head">
                <a class="logo" href="index.php" title="Go to index">
                    <img src="assets/logo.png" alt="Printer Logo" />
                    <span class="printer-title">Inbox Console</span>
                </a>
                <div class="user-info">
                    <i class="fas fa-user"></i>
                    <span><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></span>
                </div>
            </div>

            <div class="paper" role="document">
                <div class="dotmatrix-lines" aria-hidden="true"></div>
                <div class="paper-header">
                    <strong>Inbox</strong>
                </div>

            <div class="section">
                <div class="inbox-nav">
                    <div class="nav-tab active" onclick="showTab('sent')">
                        <i class="fas fa-paper-plane"></i> Sent Emails
                    </div>
                    <div class="nav-tab" onclick="showTab('received')">
                        <i class="fas fa-inbox"></i> Received Emails
                    </div>
                </div>

                <div id="sent" class="email-list active">
                    <div class="empty-state">
                        <i class="fas fa-paper-plane"></i>
                        <h3>No Sent Emails Yet</h3>
                        <p>Your sent emails will appear here once you start using our services.</p>
                    </div>
                </div>

                <div id="received" class="email-list">
                    <div id="received-emails">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>No Received Emails Yet</h3>
                            <p>Emails from our admin team will appear here.</p>
                        </div>
                    </div>
                </div>

                <div class="compose-actions">
                    <span class="ticker" aria-live="polite">Ready.</span>
                    <div class="controls">
                        <a href="index.php" class="back-btn">
                            <i class="fas fa-arrow-left"></i> Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        // Auto-response messages for different services
        const autoResponseMessages = {
            'printer': 'Thank you for your printer service request! We have received your files and will process your printing order within 24 hours. You will receive another email once your order is ready for pickup.',
            'bookbind': 'Thank you for your book binding request! We have received your documents and will begin the binding process. Estimated completion time is 2-3 business days.',
            'laminate': 'Thank you for your lamination request! We have received your documents and will process them within 24 hours. Your laminated items will be ready for pickup soon.',
            'pictures': 'Thank you for your picture printing request! We have received your photos and will print them in high quality. Your photos will be ready for pickup within 24 hours.',
            'photocopy': 'Thank you for your photocopy request! We have received your documents and will process them immediately. Your copies will be ready for pickup within 2 hours.',
            'tarpaulin': 'Thank you for your tarpaulin printing request! We have received your design and will begin printing. Large format printing takes 1-2 business days to complete.'
        };

        const serviceNames = {
            'printer': 'Printer Service',
            'bookbind': 'Book Binding Service',
            'laminate': 'Lamination Service',
            'pictures': 'Picture Printing Service',
            'photocopy': 'Photocopy Service',
            'tarpaulin': 'Tarpaulin Printing Service'
        };

        function showTab(tabName) {
            // Hide all email lists
            document.querySelectorAll('.email-list').forEach(list => {
                list.classList.remove('active');
            });
            
            // Remove active class from all nav tabs
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected email list
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked nav tab
            event.target.classList.add('active');
        }

        // Function to add a received email to the inbox
        function addReceivedEmail(serviceType, subject) {
            const receivedContainer = document.getElementById('received-emails');
            const emptyState = receivedContainer.querySelector('.empty-state');
            
            // Remove empty state if it exists
            if (emptyState) {
                emptyState.remove();
            }
            
            // Create new email item
            const emailItem = document.createElement('div');
            emailItem.className = 'email-item';
            emailItem.innerHTML = `
                <div class="email-header">
                    <div class="email-from">Isko Print Admin</div>
                    <div class="email-date">${new Date().toLocaleString()}</div>
                </div>
                <div class="email-subject">✅ Confirmation: ${serviceNames[serviceType] || 'Service'} Request Received</div>
                <div class="email-preview">${autoResponseMessages[serviceType] || 'Thank you for your service request! We have received your files and will process them as soon as possible.'}</div>
                <div class="email-status status-received">Received</div>
            `;
            
            // Add to top of received emails
            receivedContainer.insertBefore(emailItem, receivedContainer.firstChild);
            
            // Show notification
            showNotification('New email received from admin!');
        }

        // Function to add a sent email to the sent list
        function addSentEmail(serviceType, subject) {
            const sentContainer = document.getElementById('sent');
            const emptyState = sentContainer.querySelector('.empty-state');
            
            // Remove empty state if it exists
            if (emptyState) {
                emptyState.remove();
            }
            
            // Create new email item
            const emailItem = document.createElement('div');
            emailItem.className = 'email-item';
            emailItem.innerHTML = `
                <div class="email-header">
                    <div class="email-from">You</div>
                    <div class="email-date">${new Date().toLocaleString()}</div>
                </div>
                <div class="email-subject">[${serviceNames[serviceType] || 'Service'}] ${subject}</div>
                <div class="email-preview">Service request sent to admin</div>
                <div class="email-status status-sent">Sent</div>
            `;
            
            // Add to top of sent emails
            sentContainer.insertBefore(emailItem, sentContainer.firstChild);
        }

        // Function to show notification
        function showNotification(message) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #28a745;
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 1000;
                font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace;
                font-weight: bold;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                animation: slideIn 0.3s ease;
            `;
            notification.textContent = message;
            
            // Add animation keyframes
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(style);
            
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
                style.remove();
            }, 3000);
        }

        // Check for recent email submissions and add them to inbox
        function checkForRecentEmails() {
            // Check localStorage for recent email submissions
            const recentEmails = JSON.parse(localStorage.getItem('recentEmails') || '[]');
            
            recentEmails.forEach(email => {
                // Add sent email
                addSentEmail(email.serviceType, email.subject);
                
                // Add received auto-response after a short delay
                setTimeout(() => {
                    addReceivedEmail(email.serviceType, email.subject);
                }, 1000);
            });
            
            // Clear the recent emails after processing
            localStorage.removeItem('recentEmails');
        }

        // Listen for email submissions from other pages
        window.addEventListener('storage', function(e) {
            if (e.key === 'recentEmails') {
                const recentEmails = JSON.parse(e.newValue || '[]');
                recentEmails.forEach(email => {
                    addSentEmail(email.serviceType, email.subject);
                    setTimeout(() => {
                        addReceivedEmail(email.serviceType, email.subject);
                    }, 1000);
                });
            }
        });

        // Initialize inbox on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkForRecentEmails();
        });
    </script>
</body>
</html>
