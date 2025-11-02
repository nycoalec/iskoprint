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
    <title>Inbox - IskPrint</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #1a1a1a;
            background: #ffffff;
            min-height: 100vh;
        }

        .email-app {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        /* Top Header Bar */
        .top-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 24px;
            border-bottom: 1px solid #e0e0e0;
            background: #ffffff;
            z-index: 100;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .menu-toggle {
            background: none;
            border: none;
            font-size: 20px;
            color: #666;
            cursor: pointer;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-toggle:hover {
            background: #f5f5f5;
            border-radius: 50%;
        }

        .logo-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-header img {
            height: 32px;
            width: auto;
        }

        .logo-text {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .logo-star {
            display: inline-flex;
            align-items: center;
            margin-left: -4px;
            margin-right: -4px;
            position: relative;
            top: 1px;
        }

        .logo-star img {
            width: 20px;
            height: 20px;
            display: block;
        }

        .search-bar {
            flex: 1;
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            border: 1px solid #e0e0e0;
            border-radius: 24px;
            font-size: 14px;
            background: #f5f5f5;
            outline: none;
            transition: all 0.2s ease;
        }

        .search-bar input:focus {
            background: #ffffff;
            border-color: #750d0d;
            box-shadow: 0 0 0 3px rgba(117, 13, 13, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 14px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-icon {
            background: none;
            border: none;
            font-size: 18px;
            color: #666;
            cursor: pointer;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .header-icon:hover {
            background: #f5f5f5;
        }

        .profile-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #750d0d 0%, #5d0a0a 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
        }

        /* Main Layout */
        .main-layout {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        /* Left Sidebar */
        .sidebar {
            width: 240px;
            background: #ffffff;
            border-right: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .nav-menu {
            padding: 8px 0;
        }

        .nav-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #666;
            font-size: 14px;
        }

        .nav-item:hover {
            background: #f5f5f5;
        }

        .nav-item.active {
            background: #fff5f5;
            color: #750d0d;
            font-weight: 600;
        }

        .nav-item-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-item i {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }

        .nav-count {
            background: #e0e0e0;
            color: #666;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .nav-item.active .nav-count {
            background: #750d0d;
            color: white;
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .content-header {
            padding: 16px 24px;
            border-bottom: 1px solid #e0e0e0;
        }

        .content-header h2 {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .email-list {
            flex: 1;
            overflow-y: auto;
            padding: 0;
            position: relative;
        }

        .email-tab {
            display: none;
        }

        .email-tab.active {
            display: block;
            height: 100%;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 40px;
            color: #999;
        }

        .empty-state-icon {
            font-size: 80px;
            color: #e0e0e0;
            margin-bottom: 24px;
        }

        .empty-state h3 {
            font-size: 22px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 14px;
            color: #999;
        }

        /* Email Item Styles */
        .email-item {
            display: flex;
            align-items: center;
            padding: 16px 24px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .email-item:hover {
            background: #f9f9f9;
        }

        .email-checkbox {
            margin-right: 12px;
        }

        .email-content {
            flex: 1;
            min-width: 0;
        }

        .email-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .email-from {
            font-weight: 600;
            color: #1a1a1a;
            font-size: 14px;
        }

        .email-date {
            font-size: 12px;
            color: #999;
        }

        .email-subject {
            font-size: 14px;
            color: #666;
            margin-bottom: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .email-preview {
            font-size: 13px;
            color: #999;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Modal Styles (keeping existing) */
        .modal-overlay { 
            position: fixed; 
            inset: 0; 
            background: rgba(0,0,0,0.45); 
            display:none; 
            align-items:center; 
            justify-content:center; 
            z-index: 1200; 
        }
        .modal-overlay.active { display:flex; }
        .modal { 
            width: 92%; 
            max-width: 700px; 
            background:#fff; 
            border-radius:12px; 
            box-shadow: 0 20px 60px rgba(0,0,0,.25); 
            overflow:hidden; 
            border:1px solid #e9ecef; 
        }
        .modal-header { 
            display:flex; 
            align-items:center; 
            justify-content: space-between; 
            padding:16px 18px; 
            background:#750d0d; 
            color:#fff; 
        }
        .modal-title { 
            font-weight:700; 
            letter-spacing:.4px; 
            text-transform: uppercase; 
        }
        .modal-close { 
            background:transparent; 
            border:0; 
            color:#fff; 
            font-size:18px; 
            cursor:pointer; 
        }
        .modal-body { padding:18px; }
        .modal-row { margin-bottom:12px; }
        .modal-label { 
            font-size:12px; 
            color:#6c757d; 
            text-transform: uppercase; 
            letter-spacing:.6px; 
            display:block; 
            margin-bottom:6px; 
        }
        .modal-value { color:#2c3e50; }
        .modal-footer { 
            padding: 0 18px 18px; 
            display:flex; 
            justify-content:flex-end; 
        }
        .btn { 
            appearance:none; 
            border:1px solid #e5e7eb; 
            background:#f7f7f9; 
            color:#333; 
            border-radius:8px; 
            padding:10px 14px; 
            cursor:pointer; 
            font-weight:600; 
        }
        .btn:hover { filter: brightness(.97); }

        /* Responsive */
        @media (max-width: 768px) {
            .search-bar {
                display: none;
            }

            .sidebar {
                width: 200px;
            }

            .logo-text {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="email-app">
        <!-- Top Header Bar -->
        <div class="top-header">
            <div class="header-left">
                <div class="logo-header">
                    <img src="assets/logo.png" alt="IskPrint Logo" />
                    <span class="logo-text">
                        <span>Isk</span>
                        <span class="logo-star" aria-hidden="true">
                            <img src="assets/pup_star.png" alt="" />
                        </span>
                        <span>Print</span>
                    </span>
                </div>
            </div>
            <div class="search-bar">
                <i class="fas fa-search search-icon"></i>
                <input type="text" placeholder="Search mail" />
            </div>
            <div class="header-right">
                <div class="profile-icon">
                    <?php echo strtoupper(substr($currentUser['first_name'], 0, 1)); ?>
                </div>
            </div>
        </div>

        <!-- Main Layout -->
        <div class="main-layout">
            <!-- Left Sidebar -->
            <div class="sidebar">
                <div class="nav-menu">
                    <div class="nav-item active" onclick="showTab('inbox')">
                        <div class="nav-item-left">
                            <i class="fas fa-inbox"></i>
                            <span>Inbox</span>
                        </div>
                        <span class="nav-count" id="inbox-count">0</span>
                    </div>
                    <div class="nav-item" onclick="showTab('sent')">
                        <div class="nav-item-left">
                            <i class="fas fa-paper-plane"></i>
                            <span>Sent</span>
                        </div>
                        <span class="nav-count" id="sent-count">0</span>
                    </div>
                    <div class="nav-item" onclick="showTab('drafts')">
                        <div class="nav-item-left">
                            <i class="fas fa-file-alt"></i>
                            <span>Drafts</span>
                        </div>
                        <span class="nav-count" id="drafts-count">0</span>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="main-content">
                <div class="content-header">
                    <h2 id="content-title">Inbox</h2>
                </div>
                <div class="email-list">
                    <!-- Inbox Tab -->
                    <div id="inbox" class="email-tab active">
                        <div id="received-emails">
                            <div class="empty-state">
                                <i class="fas fa-inbox empty-state-icon"></i>
                                <h3>No emails yet</h3>
                                <p>Your emails will appear here</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sent Tab -->
                    <div id="sent" class="email-tab">
                        <div id="sent-emails">
                            <div class="empty-state">
                                <i class="fas fa-paper-plane empty-state-icon"></i>
                                <h3>No emails yet</h3>
                                <p>Your sent emails will appear here</p>
                            </div>
                        </div>
                    </div>

                    <!-- Drafts Tab -->
                    <div id="drafts" class="email-tab">
                        <div class="empty-state">
                            <i class="fas fa-file-alt empty-state-icon"></i>
                            <h3>No drafts yet</h3>
                            <p>Your draft emails will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Details Modal -->
    <div id="emailModal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="emailModalTitle">
        <div class="modal">
            <div class="modal-header">
                <div id="emailModalTitle" class="modal-title">Email Details</div>
                <button class="modal-close" onclick="closeEmailModal()" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-row">
                    <span class="modal-label">From</span>
                    <div class="modal-value" id="modalFrom"></div>
                </div>
                <div class="modal-row">
                    <span class="modal-label">To</span>
                    <div class="modal-value" id="modalTo">Isko Print Admin</div>
                </div>
                <div class="modal-row">
                    <span class="modal-label">Date</span>
                    <div class="modal-value" id="modalDate"></div>
                </div>
                <div class="modal-row">
                    <span class="modal-label">Subject</span>
                    <div class="modal-value" id="modalSubject"></div>
                </div>
                <div class="modal-row">
                    <span class="modal-label">Message</span>
                    <div class="modal-value" id="modalBody" style="white-space:pre-wrap; line-height:1.6"></div>
                </div>
                <div class="modal-row">
                    <span class="modal-label">Status</span>
                    <div class="modal-value" id="modalStatus"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeEmailModal()">Close</button>
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
            // Hide all email tabs
            document.querySelectorAll('.email-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all nav items
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked nav item
            event.currentTarget.classList.add('active');
            
            // Update content title
            const titles = {
                'inbox': 'Inbox',
                'sent': 'Sent',
                'drafts': 'Drafts'
            };
            document.getElementById('content-title').textContent = titles[tabName] || 'Inbox';
            
            // Update counts
            updateCounts();
        }

        function updateCounts() {
            const inboxEmails = document.querySelectorAll('#received-emails .email-item').length;
            const sentEmails = document.querySelectorAll('#sent-emails .email-item').length;
            
            document.getElementById('inbox-count').textContent = inboxEmails;
            document.getElementById('sent-count').textContent = sentEmails;
            document.getElementById('drafts-count').textContent = '0';
        }

        // Function to create email item element
        function createEmailItem(from, to, date, subject, preview, status) {
            const emailItem = document.createElement('div');
            emailItem.className = 'email-item';
            emailItem.innerHTML = `
                <div class="email-content">
                    <div class="email-header-row">
                        <span class="email-from">${from}</span>
                        <span class="email-date">${date}</span>
                    </div>
                    <div class="email-subject">${subject}</div>
                    <div class="email-preview">${preview}</div>
                </div>
            `;
            
            // Dataset for modal
            emailItem.dataset.from = from;
            emailItem.dataset.to = to;
            emailItem.dataset.date = date;
            emailItem.dataset.subject = subject;
            emailItem.dataset.body = preview;
            emailItem.dataset.status = status;
            emailItem.setAttribute('role', 'button');
            emailItem.setAttribute('tabindex', '0');
            emailItem.addEventListener('click', () => openEmailModal(emailItem));
            emailItem.addEventListener('keydown', (e) => { 
                if (e.key === 'Enter' || e.key === ' ') { 
                    e.preventDefault(); 
                    openEmailModal(emailItem); 
                } 
            });
            
            return emailItem;
        }

        // Function to add a received email to the inbox
        function addReceivedEmail(serviceType, subject) {
            const receivedContainer = document.getElementById('received-emails');
            const emptyState = receivedContainer.querySelector('.empty-state');
            
            // Remove empty state if it exists
            if (emptyState) {
                emptyState.remove();
            }
            
            const bodyText = autoResponseMessages[serviceType] || 'Thank you for your service request! We have received your files and will process them as soon as possible.';
            const dateText = new Date().toLocaleString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric',
                hour: 'numeric',
                minute: '2-digit'
            });
            const subjectText = `✅ Confirmation: ${serviceNames[serviceType] || 'Service'} Request Received`;
            
            const emailItem = createEmailItem(
                'Isko Print Admin',
                'You',
                dateText,
                subjectText,
                bodyText,
                'Received'
            );
            
            // Add to top of received emails
            receivedContainer.insertBefore(emailItem, receivedContainer.firstChild);
            
            // Update counts
            updateCounts();
            
            // Show notification
            showNotification('New email received from admin!');
        }

        // Function to add a sent email to the sent list
        function addSentEmail(serviceType, subject) {
            const sentContainer = document.getElementById('sent-emails');
            const emptyState = sentContainer.querySelector('.empty-state');
            
            // Remove empty state if it exists
            if (emptyState) {
                emptyState.remove();
            }
            
            const dateText = new Date().toLocaleString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric',
                hour: 'numeric',
                minute: '2-digit'
            });
            const subjText = `[${serviceNames[serviceType] || 'Service'}] ${subject}`;
            const bodyText = 'Service request sent to admin';
            
            const emailItem = createEmailItem(
                'You',
                'Isko Print Admin',
                dateText,
                subjText,
                bodyText,
                'Sent'
            );
            
            // Add to top of sent emails
            sentContainer.insertBefore(emailItem, sentContainer.firstChild);
            
            // Update counts
            updateCounts();
        }

        // Modal helpers
        function openEmailModal(item){
            const m = document.getElementById('emailModal');
            document.getElementById('modalFrom').textContent = item.dataset.from || '';
            document.getElementById('modalTo').textContent = item.dataset.to || '';
            document.getElementById('modalDate').textContent = item.dataset.date || '';
            document.getElementById('modalSubject').textContent = item.dataset.subject || '';
            document.getElementById('modalBody').textContent = item.dataset.body || '';
            document.getElementById('modalStatus').textContent = item.dataset.status || '';
            m.classList.add('active');
        }
        function closeEmailModal(){
            const m = document.getElementById('emailModal');
            m.classList.remove('active');
        }
        // Close on overlay click or Esc
        document.addEventListener('click', (e)=>{
            const overlay = document.getElementById('emailModal');
            if (e.target === overlay) closeEmailModal();
        });
        document.addEventListener('keydown', (e)=>{ if (e.key === 'Escape') closeEmailModal(); });

        // Function to show notification
        function showNotification(message) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #750d0d;
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 1000;
                font-weight: 600;
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
            
            // Update counts
            updateCounts();
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
            updateCounts();
        });
    </script>
</body>
</html>
