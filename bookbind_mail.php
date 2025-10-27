<?php
// Printer-themed Email UI (HTML only in a PHP file)
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>BookBinder Mail UI</title>
  <style>
    :root {
      --paper: #fffef8;
      --ink: #1a1a1a;
      --maroon: #750d0d;
      --maroon-dark: #5d0a0a;
      --accent: var(--maroon);
      --muted: #8a8a8a;
      --shadow: rgba(0, 0, 0, 0.08);
      --line: rgba(117,13,13,0.22);
      --dot: rgba(117,13,13,0.30);
    }
    
    * { box-sizing: border-box; }
    html, body { height: 100%; }
    body {
      margin: 0;
      font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace;
      color: var(--ink);
      background: linear-gradient(180deg, rgba(255,249,249,0.8) 0%, rgba(251,238,238,0.8) 100%), 
                  url('assets/pup_bg.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
    }

    .app {
      max-width: 1100px;
      margin: 32px auto;
      padding: 0 16px;
    }

    .printer {
      background: white;
      border-radius: 16px;
      box-shadow: 0 20px 50px var(--shadow), inset 0 -2px 0 #f0f0f0;
      padding: 24px;
      position: relative;
    }

    .printer-head {
      display: flex;
      align-items: center;
      gap: 12px;
      padding-bottom: 16px;
      border-bottom: 1px dashed var(--line);
    }

    .printer-icon {
      width: 40px; height: 40px;
      border-radius: 8px;
      background: linear-gradient(145deg, #e9edf5, #f7f9fc);
      display: grid; place-items: center;
      box-shadow: inset 0 1px 0 #fff, inset 0 -1px 0 #dfe3ea;
    }

    .printer-title {
      font-size: 18px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      color: var(--maroon);
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
      border-radius: 4px;
      box-shadow: 0 1px 0 #fff, 0 3px 8px var(--shadow);
    }

    .paper {
      background: var(--paper);
      margin-top: 20px;
      border-radius: 12px;
      box-shadow: 0 1px 0 #f1f1f1, 0 10px 30px var(--shadow);
      position: relative;
      overflow: hidden;
      border: 1px solid #eee;
    }

    .paper::before, .paper::after {
      content: "";
      position: absolute;
      left: 0; right: 0;
      height: 12px;
      background-image: radial-gradient(var(--dot) 1px, transparent 1px);
      background-size: 8px 8px;
      background-position: 0 0;
    }
    .paper::before { top: 0; border-bottom: 1px dashed var(--line); }
    .paper::after { bottom: 0; border-top: 1px dashed var(--line); }

    .paper-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 18px;
      border-bottom: 1px dashed var(--line);
    }
    .paper-header strong { color: var(--maroon); }

    .controls { display: flex; gap: 8px; }

    button, .button {
      appearance: none;
      border: 1px solid #d9deea;
      background: linear-gradient(180deg, #ffffff, #fbf2f2);
      color: var(--maroon);
      padding: 12px 20px;
      border-radius: 10px;
      font-family: inherit;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1), inset 0 -1px 0 #eaeef6;
      transition: all 0.2s ease;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* Colorize header action icons to match theme */
    .controls button svg path,
    .controls button svg rect,
    .controls button svg circle {
      stroke: var(--maroon) !important;
    }

    /* Keep icons white on primary (Send) button */
    .button-primary svg path,
    .button-primary svg rect,
    .button-primary svg circle {
      stroke: #ffffff !important;
      fill: #ffffff !important;
    }

    button:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(117,13,13,0.2), inset 0 -1px 0 #eaeef6;
      background: linear-gradient(180deg, #fef2f2, #f8e8e8);
    }

    .button-primary {
      border-color: var(--maroon);
      background: linear-gradient(180deg, #8a1111, var(--maroon-dark));
      color: white;
      box-shadow: 0 4px 12px rgba(117,13,13,0.3), inset 0 -2px 0 #4a0808;
      font-weight: 700;
    }
    
    .button-primary:hover {
      background: linear-gradient(180deg, #6d0e0e, #4a0808);
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(117,13,13,0.4), inset 0 -2px 0 #2d0505;
    }

    .section {
      padding: 18px;
      border-bottom: 1px dashed var(--line);
    }

    .section:last-child { border-bottom: 0; }

    .grid {
      display: grid;
      grid-template-columns: 1.1fr 1fr;
      gap: 18px;
    }

    @media (max-width: 900px) {
      .grid { grid-template-columns: 1fr; }
    }

    .field {
      display: grid;
      grid-template-columns: 120px 1fr;
      align-items: start;
      gap: 10px;
      padding: 8px 0;
    }

    .field label {
      color: var(--muted);
      text-transform: uppercase;
      font-size: 12px;
      letter-spacing: 0.8px;
      padding-top: 10px;
    }

    input[type="text"], input[type="email"], textarea {
      width: 100%;
      border: 1px solid #e3e6ef;
      background: #ffffff;
      border-radius: 8px;
      padding: 12px 12px;
      font-family: inherit;
      font-size: 14px;
      outline: none;
      box-shadow: inset 0 1px 0 #f7f9fc;
    }

    textarea { min-height: 160px; resize: vertical; }

    .file-upload-wrapper {
      position: relative;
    }
    
    input[type="file"] {
      position: absolute;
      opacity: 0;
      width: 0.1px;
      height: 0.1px;
      pointer-events: none;
    }
    
    .file-upload-area {
      width: 100%;
      border: 2px dashed #d1d5db;
      background: #f9fafb;
      border-radius: 10px;
      padding: 24px;
      text-align: center;
      cursor: pointer;
      transition: all 0.2s ease;
      position: relative;
      min-height: 80px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    
    .file-upload-area:hover {
      border-color: var(--maroon);
      background: #fef2f2;
      transform: translateY(-1px);
    }
    
    .file-upload-area.dragover {
      border-color: var(--maroon);
      background: #fef2f2;
      transform: scale(1.02);
    }
    
    .file-upload-icon {
      font-size: 24px;
      color: var(--maroon);
      margin-bottom: 8px;
    }
    
    .file-upload-text {
      font-size: 14px;
      font-weight: 600;
      color: var(--maroon);
      margin-bottom: 4px;
    }
    
    .file-upload-subtext {
      font-size: 12px;
      color: var(--muted);
    }
    
    .ticker.error {
      color: #dc2626;
      background: #fef2f2;
      border: 1px solid #fecaca;
      animation: shake 0.5s ease-in-out;
    }
    
    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-5px); }
      75% { transform: translateX(5px); }
    }

    .compose-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 8px;
    }

    .ticker {
      font-size: 12px;
      color: var(--muted);
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .columns {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
    }

    @media (max-width: 800px) {
      .columns { grid-template-columns: 1fr; }
    }

    .list {
      border: 1px solid #eceff6;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
    }

    .list-item {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 12px;
      padding: 12px 14px;
      border-bottom: 1px dotted var(--line);
    }

    .list-item:last-child { border-bottom: 0; }

    .subject { font-weight: 600; }
    .meta { color: var(--muted); font-size: 12px; }

    .preview {
      margin-top: 6px;
      color: #5b5b5b;
      font-size: 13px;
      line-height: 1.5;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .badge {
      font-size: 11px;
      padding: 4px 8px;
      border-radius: 999px;
      border: 1px dashed #cdd6e7;
      color: #41557a;
      background: #f5f8ff;
      align-self: start;
    }

    .footer {
      padding: 12px 18px 18px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: var(--muted);
      font-size: 12px;
    }

    .dotmatrix-lines {
      position: absolute; inset: 12px 12px auto 12px; height: 10px;
      background-image: repeating-linear-gradient(90deg, rgba(0,0,0,0.08) 0 2px, transparent 2px 8px);
      opacity: 0.5;
      border-radius: 4px;
    }

    .sr-only {
      position: absolute;
      width: 1px; height: 1px;
      padding: 0; margin: -1px; overflow: hidden;
      clip: rect(0, 0, 1px, 1px); white-space: nowrap; border: 0;
    }
  </style>
  <script>
    // Optional: purely UI feedback without backend
    function sendEmail(event) {
      event.preventDefault();
      const form = event.target.closest('form');
      const subject = form.querySelector('[name="subject"]').value.trim();
      const message = form.querySelector('[name="message"]').value.trim();
      const fileInput = form.querySelector('[name="file_upload"]');
      const files = fileInput.files;
      const ticker = document.getElementById('ticker');
      
      if (!subject || !message) {
        ticker.textContent = 'Please complete subject and message fields.';
        return;
      }
      
      // Validate file upload - require at least one file
      if (files.length === 0) {
        ticker.textContent = 'Error: Please select at least one file to upload!';
        ticker.classList.add('error');
        ticker.classList.remove('sending', 'sent');
        
        // Add visual feedback to file upload area
        const fileUploadArea = document.querySelector('.file-upload-area');
        fileUploadArea.style.borderColor = '#dc2626';
        fileUploadArea.style.backgroundColor = '#fef2f2';
        
        // Reset styling after 3 seconds
        setTimeout(() => {
          fileUploadArea.style.borderColor = '';
          fileUploadArea.style.backgroundColor = '';
          ticker.classList.remove('error');
          ticker.textContent = 'Ready.';
        }, 3000);
        
        return;
      }
      
      // Show sending status
      ticker.textContent = 'Sending…';
      ticker.classList.add('sending');
      ticker.classList.remove('error', 'sent');
      
      // Prepare form data
      const formData = new FormData();
      formData.append('service_type', 'bookbind');
      formData.append('subject', subject);
      formData.append('message', message);
      
      // Add files to form data
      for (let i = 0; i < files.length; i++) {
        formData.append('file_upload[]', files[i]);
      }
      
      // Send email
      fetch('process_email_simple.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          ticker.textContent = `Email sent successfully to admin!`;
          ticker.classList.remove('sending', 'error');
          ticker.classList.add('sent');
          form.reset();
          updateFileCount();
          
          // Store email in localStorage for inbox
          const emailData = {
            serviceType: 'bookbind',
            subject: subject,
            timestamp: new Date().toISOString()
          };
          
          // Get existing emails or create new array
          const recentEmails = JSON.parse(localStorage.getItem('recentEmails') || '[]');
          recentEmails.push(emailData);
          localStorage.setItem('recentEmails', JSON.stringify(recentEmails));
          
          // Trigger storage event for inbox page
          window.dispatchEvent(new StorageEvent('storage', {
            key: 'recentEmails',
            newValue: JSON.stringify(recentEmails)
          }));
        } else {
          ticker.textContent = `Error: ${data.message}`;
          ticker.classList.remove('sending', 'sent');
          ticker.classList.add('error');
        }
      })
      .catch(error => {
        ticker.textContent = 'Error: Failed to send email.';
        ticker.classList.remove('sending', 'sent');
        ticker.classList.add('error');
        console.error('Error:', error);
      });
    }

    function updateFileCount() {
      const fileInput = document.getElementById('file_upload');
      const fileInfo = document.getElementById('file-info');
      const fileCount = document.getElementById('file-count');
      const files = fileInput.files;
      
      if (files.length > 0) {
        fileInfo.style.display = 'flex';
        fileCount.textContent = `${files.length} file(s) selected`;
      } else {
        fileInfo.style.display = 'none';
        fileCount.textContent = '0 files selected';
      }
    }

    function handleDragOver(e) {
      e.preventDefault();
      e.currentTarget.classList.add('dragover');
    }

    function handleDragLeave(e) {
      e.preventDefault();
      e.currentTarget.classList.remove('dragover');
    }

    function handleDrop(e) {
      e.preventDefault();
      e.currentTarget.classList.remove('dragover');
      
      const files = e.dataTransfer.files;
      const fileInput = document.getElementById('file_upload');
      
      // Create a new FileList-like object
      const dt = new DataTransfer();
      for (let i = 0; i < files.length; i++) {
        dt.items.add(files[i]);
      }
      fileInput.files = dt.files;
      
      updateFileCount();
    }

    // Add event listener for file input change
    document.addEventListener('DOMContentLoaded', function() {
      const fileInput = document.getElementById('file_upload');
      const fileUploadArea = document.querySelector('.file-upload-area');
      
      if (fileInput) {
        fileInput.addEventListener('change', updateFileCount);
      }
      
      if (fileUploadArea) {
        fileUploadArea.addEventListener('dragover', handleDragOver);
        fileUploadArea.addEventListener('dragleave', handleDragLeave);
        fileUploadArea.addEventListener('drop', handleDrop);
      }
    });

  </script>
</head>
<body>
  <div class="app">
    <div class="printer" role="region" aria-label="Printer Mail UI">
      <div class="printer-head">
        <a class="logo" href="./" title="Go to index">
          <img src="assets/logo.png" alt="Printer Logo" />
          <span class="printer-title">Book Bind Mail Console</span>
        </a>
      </div>

      <div class="paper" role="document">
        <div class="dotmatrix-lines" aria-hidden="true"></div>
        <div class="paper-header">
          <strong>Compose</strong>
          <div class="controls">
            <button type="button" onclick="window.location.href='inbox.php'" title="Go to inbox">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="#3d4a66" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 6L12 13L2 6" stroke="#3d4a66" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              INBOX
            </button>
          </div>
        </div>

        <div class="section">
          <form onsubmit="sendEmail(event)" enctype="multipart/form-data">
            <div class="field">
              <label for="subject">Subject</label>
              <input id="subject" name="subject" type="text" placeholder="Enter subject" />
            </div>
            <div class="field">
              <label for="message">Message</label>
              <textarea id="message" name="message" placeholder="Type your message..."></textarea>
            </div>
            <div class="field">
              <label for="file_upload">Attach File/Photo</label>
              <div class="file-upload-wrapper">
                <input id="file_upload" name="file_upload" type="file" accept="image/*,.pdf,.doc,.docx,.txt" multiple />
                <label for="file_upload" class="file-upload-area">
                  <div class="file-upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                  </div>
                  <div class="file-upload-text">Click to upload files or drag and drop</div>
                  <div class="file-upload-subtext">Images, PDF, DOC, DOCX, TXT files</div>
                  <div class="file-info" id="file-info" style="display: none;">
                    <i class="fas fa-paperclip"></i>
                    <span id="file-count">0 files selected</span>
                  </div>
                </label>
              </div>
            </div>
            <div class="compose-actions">
              <span id="ticker" class="ticker" aria-live="polite">Ready.</span>
              <div class="controls">
                <button type="reset">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 12a8 8 0 1 0 2.343-5.657" stroke="#3d4a66" stroke-width="1.6" stroke-linecap="round"/>
                    <path d="M4 7v5h5" stroke="#3d4a66" stroke-width="1.6" stroke-linecap="round"/>
                  </svg>
                  Clear
                </button>
                <button type="submit" class="button-primary">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 12l18-9-9 18-2-7-7-2z" stroke="#ffffff" stroke-width="1.6" stroke-linejoin="round"/>
                  </svg>
                  Send
                </button>
              </div>
            </div>
          </form>
        </div>


        <div class="footer">
        </div>
      </div>
    </div>
  </div>
</body>
</html>

