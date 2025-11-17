<?php
// Printer-themed Email UI (HTML only in a PHP file)
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tarpaulin Mail UI</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/theme.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>
  <style>
    :root {
      --paper: #ffffff;
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
      border-radius: 0;
      box-shadow: none;
      position: relative;
      overflow: hidden;
      border: none;
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

    /* Override for compose section */
    .compose-section {
      background: #ffffff;
      border-radius: 0;
      padding: 0;
      border: none;
    }

    .compose-section .field {
      display: grid;
      grid-template-columns: 80px 1fr;
      gap: 12px;
      align-items: center;
      padding: 12px 0;
      border-bottom: 1px solid #f0f0f0;
    }

    .compose-section .field:has(textarea:not([aria-label])) {
      grid-template-columns: 1fr;
    }

    .compose-section .field:has(textarea) {
      align-items: start;
    }

    .compose-section .field:last-of-type {
      border-bottom: none;
      padding-top: 16px;
    }

    .compose-section .field label {
      color: #666;
      font-size: 14px;
      font-weight: 500;
      text-transform: none;
      letter-spacing: 0;
      padding-top: 0;
      text-align: left;
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

    .compose-section input[type="text"], .compose-section input[type="email"], .compose-section textarea {
      border: none;
      background: transparent;
      border-radius: 0;
      padding: 8px 0;
      box-shadow: none;
      color: #333;
    }

    .compose-section input[type="text"]::placeholder, .compose-section input[type="email"]::placeholder, .compose-section textarea::placeholder {
      color: #999;
    }

    .readonly-field {
      color: #666;
      cursor: default;
      background: #f9f9f9;
      padding: 8px 12px;
      border-radius: 4px;
    }

    textarea { min-height: 160px; resize: vertical; }

    .compose-section textarea {
      min-height: 200px;
      padding: 16px 0;
      line-height: 1.6;
    }

    .file-upload-wrapper {
      position: relative;
    }

    .compose-section .file-upload-wrapper {
      grid-column: 1 / -1;
      display: flex;
      justify-content: center;
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

    .compose-section .file-upload-area {
      width: 100%;
      max-width: 600px;
      border: 2px dashed #ccc;
      background: #fafafa;
      border-radius: 8px;
      padding: 32px 24px;
      min-height: 120px;
      gap: 12px;
    }
    
    .file-upload-area:hover {
      border-color: var(--maroon);
      background: #fef2f2;
      transform: translateY(-1px);
    }

    .compose-section .file-upload-area:hover {
      border-color: #999;
      background: #f5f5f5;
      transform: none;
    }
    
    .file-upload-area.dragover {
      border-color: var(--maroon);
      background: #fef2f2;
      transform: scale(1.02);
    }

    .compose-section .file-upload-area.dragover {
      border-color: #666;
      background: #f0f0f0;
      transform: none;
    }
    
    .file-upload-icon {
      font-size: 24px;
      color: var(--maroon);
      margin-bottom: 8px;
    }

    .compose-section .file-upload-icon {
      font-size: 32px;
      color: #666;
      margin-bottom: 4px;
    }
    
    .file-upload-text {
      font-size: 14px;
      font-weight: 600;
      color: var(--maroon);
      margin-bottom: 4px;
    }

    .compose-section .file-upload-text {
      font-size: 13px;
      font-weight: 500;
      color: #333;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .file-upload-subtext {
      font-size: 12px;
      color: var(--muted);
    }

    .compose-section .file-upload-subtext {
      font-size: 11px;
      color: #999;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }
    
    .file-info {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 8px;
      margin-top: 12px;
      padding-top: 12px;
      border-top: 1px dashed #d1d5db;
      width: 100%;
    }
    
    .file-info-header {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 12px;
      color: var(--maroon);
      font-weight: 600;
    }
    
    .file-names-list {
      display: flex;
      flex-direction: column;
      gap: 6px;
      width: 100%;
      max-height: 150px;
      overflow-y: auto;
      padding: 8px;
      background: #ffffff;
      border: 1px solid #e5e7eb;
      border-radius: 6px;
    }
    
    .file-name-item {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 6px 10px;
      background: #f9fafb;
      border-radius: 4px;
      font-size: 12px;
      color: var(--ink);
    }
    
    .file-name-item i {
      color: var(--maroon);
      font-size: 11px;
    }
    
    .file-name-text {
      flex: 1;
      word-break: break-all;
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
    /* Live Preview Editor */
    .editor { padding: 14px 18px 22px; border-bottom: 1px dashed var(--line); }
    .editor .editor-grid { display:grid; grid-template-columns: 340px 1fr; gap:18px; }
    @media (max-width: 980px){ .editor .editor-grid{ grid-template-columns: 1fr; } }
    .toolbox { background:#fff; border:1px solid #eceff6; border-radius:10px; padding:12px; }
    .toolbox h4 { margin:0 0 8px 0; color: var(--maroon); font-size:14px; letter-spacing:.3px; }
    .tool { display:grid; grid-template-columns: 140px 1fr; align-items:center; gap:8px; padding:6px 0; }
    .tool label { font-size:12px; text-transform:uppercase; color:#6b7280; letter-spacing:.6px; }
    .tool input[type="number"], .tool select { width:100%; padding:8px 10px; border:1px solid #e3e6ef; border-radius:8px; }
    .preview-wrap { 
      background:#f9fafb; 
      border:1px dashed #d1d5db; 
      border-radius:12px; 
      padding:16px; 
      display:flex; 
      justify-content:center; 
      align-items:flex-start; 
      min-height: 500px;
      max-height: 600px;
      overflow: auto;
    }
    .paper-canvas { 
      background:#ffffff; 
      position:relative; 
      box-shadow:0 12px 40px rgba(0,0,0,.08); 
      border:1px solid #eee; 
      overflow: visible; 
      min-height: 400px;
      width: 100%;
      max-width: 100%;
    }
    .paper-inner { 
      width:100%; 
      min-height: 100%;
      display:flex; 
      align-items:flex-start; 
      justify-content:center; 
      position:relative; 
      overflow: visible; 
      box-sizing: border-box; 
      padding: 10px 20px;
    }
    .paper-inner img { display:block; max-width:100%; height:auto; }
    .paper-inner canvas { max-width:100%; height:auto; display:block; }
    .paper-inner div {
      width: 100%;
      max-width: 100%;
    }
    .paper-inner pre {
      width: 100%;
      max-width: 100%;
      white-space: pre-wrap;
      word-wrap: break-word;
    }
    .paper-inner embed, .paper-inner object { width:100%; height:100%; border:0; }
    .thumbs { display:flex; gap:10px; overflow:auto; padding:10px 0; border-top:1px dashed var(--line); border-bottom:1px dashed var(--line); }
    .thumb { border:1px solid #e5e7eb; border-radius:6px; background:#fff; padding:6px; box-shadow:0 1px 3px rgba(0,0,0,.06); cursor:pointer; transition: transform .15s ease, box-shadow .15s ease; }
    .thumb:hover { transform: translateY(-2px); box-shadow:0 6px 14px rgba(0,0,0,.12); }
    .thumb.active { outline:2px solid var(--maroon); }

    /* Zoom Controls */
    .zoom-controls {
      position: absolute;
      top: 10px;
      right: 10px;
      display: flex;
      align-items: center;
      gap: 8px;
      background: rgba(255, 255, 255, 0.95);
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 6px 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      z-index: 10;
    }
    .zoom-btn {
      background: #ffffff;
      border: 1px solid #d1d5db;
      border-radius: 4px;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s ease;
      color: var(--maroon);
      font-size: 14px;
    }
    .zoom-btn:hover {
      background: #f9fafb;
      border-color: var(--maroon);
      transform: scale(1.05);
    }
    .zoom-btn:active { transform: scale(0.95); }
    .zoom-level { min-width: 50px; text-align: center; font-size: 12px; font-weight: 600; }
  </style>
  <script>
    // File upload handling
    function updateFileCount() {
      const fileInput = document.getElementById('file_upload');
      const fileInfo = document.getElementById('file-info');
      const fileCount = document.getElementById('file-count');
      const fileNamesList = document.getElementById('file-names-list');
      const files = fileInput.files;
      
      if (files.length > 0) {
        fileInfo.style.display = 'flex';
        fileCount.textContent = `${files.length} FILE(S) SELECTED`;
        
        // Clear existing file names
        fileNamesList.innerHTML = '';
        
        // Display each file name
        for (let i = 0; i < files.length; i++) {
          const fileItem = document.createElement('div');
          fileItem.className = 'file-name-item';
          fileItem.innerHTML = `
            <i class="fas fa-file"></i>
            <span class="file-name-text">${files[i].name}</span>
            <small style="color: var(--muted); font-size: 10px;">${formatFileSize(files[i].size)}</small>
          `;
          fileNamesList.appendChild(fileItem);
        }
      } else {
        fileInfo.style.display = 'none';
        fileNamesList.innerHTML = '';
      }
    }
    
    function formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
    
    function clearFiles() {
      const fileInput = document.getElementById('file_upload');
      const fileInfo = document.getElementById('file-info');
      const fileNamesList = document.getElementById('file-names-list');
      
      if (fileInput) {
        fileInput.value = '';
        updateFileCount();
      }
      
      // Also clear preview if it exists
      const previewWrap = document.querySelector('.preview-wrap');
      if (previewWrap) {
        const paperInner = previewWrap.querySelector('.paper-inner');
        if (paperInner) {
          paperInner.innerHTML = '';
        }
      }
    }

    // Drag and drop handling
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
      
      // Create a new FileList
      const dataTransfer = new DataTransfer();
      for (let i = 0; i < files.length; i++) {
        dataTransfer.items.add(files[i]);
      }
      fileInput.files = dataTransfer.files;
      
      updateFileCount();
    }

    // Email sending function
    function sendEmail(event) {
      event.preventDefault();
      
      const form = event.target.closest('form');
      const subject = form.querySelector('[name="subject"]').value.trim();
      const message = form.querySelector('[name="message"]').value.trim();
      const fileInput = form.querySelector('[name="file_upload"]');
      const ticker = document.getElementById('ticker');
      
      // Validate required fields
      if (!subject || !message) {
        ticker.textContent = 'Please complete all fields before sending.';
        return;
      }
      
      // Validate file upload
      if (fileInput.files.length === 0) {
        ticker.textContent = 'Please select at least one file to upload!';
        
        // Add visual feedback
        const fileUploadArea = document.querySelector('.file-upload-area');
        fileUploadArea.style.borderColor = '#dc3545';
        fileUploadArea.style.backgroundColor = '#f8d7da';
        
        // Shake animation
        fileUploadArea.style.animation = 'shake 0.5s ease-in-out';
        setTimeout(() => {
          fileUploadArea.style.animation = '';
          fileUploadArea.style.borderColor = '';
          fileUploadArea.style.backgroundColor = '';
        }, 500);
        
        return;
      }
      
      // Prepare form data
      const formData = new FormData();
      formData.append('service_type', 'tarpaulin');
      formData.append('subject', subject);
      formData.append('message', message);
      // Include tarpaulin settings
      try {
        const quantityEl = document.getElementById('ctl-quantity');
        const sizeEl = document.getElementById('ctl-size');
        const materialEl = document.getElementById('ctl-material');
        const finishingEl = document.getElementById('ctl-finishing');
        const bleedEl = document.getElementById('ctl-bleed');
        const safeEl = document.getElementById('ctl-safe');
        const grommetEl = document.getElementById('ctl-grommet');
        if (quantityEl) formData.append('quantity', Math.max(1, parseInt(quantityEl.value||'1',10)));
        if (sizeEl) formData.append('size', sizeEl.value);
        if (materialEl) formData.append('material', materialEl.value);
        if (finishingEl) formData.append('finishing', finishingEl.value);
        if (bleedEl) formData.append('bleed_mm', bleedEl.value);
        if (safeEl) formData.append('safe_margin_mm', safeEl.value);
        if (grommetEl) formData.append('grommet_spacing_cm', grommetEl.value);
        // Default values for print settings
        formData.append('copies', '1');
        formData.append('duplex', 'single');
        formData.append('orientation', 'landscape');
        formData.append('margin_top_mm', '0');
        formData.append('margin_right_mm', '0');
        formData.append('margin_bottom_mm', '0');
        formData.append('margin_left_mm', '0');
        formData.append('scale_percent', '100');
        formData.append('fit_mode', 'Fit to printable area');
        formData.append('color_mode', 'color');
        formData.append('paper', 'A3');
      } catch (e) {}
      
      // Add files
      for (let i = 0; i < fileInput.files.length; i++) {
        formData.append('file_upload[]', fileInput.files[i]);
      }
      
      ticker.textContent = 'Sending email...';
      
      // Send email
      fetch('process_email_simple.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          ticker.textContent = data.message;
          form.reset();
          updateFileCount();
          
          // Store email in localStorage for inbox
          const emailData = {
            serviceType: 'tarpaulin',
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

          // Record order for billing with default pricing
          const DEFAULT_PRICES = { tarpaulin: 200 };
          const orders = JSON.parse(localStorage.getItem('orders') || '[]');
          const order = {
            id: 'ORD-' + Date.now(),
            serviceType: 'tarpaulin',
            subject: subject,
            amount: DEFAULT_PRICES.tarpaulin,
            status: 'Unpaid',
            createdAt: new Date().toISOString()
          };
          orders.push(order);
          localStorage.setItem('orders', JSON.stringify(orders));
          window.dispatchEvent(new StorageEvent('storage', { key: 'orders', newValue: JSON.stringify(orders) }));
        } else {
          ticker.textContent = data.message;
        }
      })
      .catch(error => {
        console.error('Error:', error);
        ticker.textContent = 'Error sending email. Please try again.';
      });
    }

    // Add event listener for file input change
    document.addEventListener('DOMContentLoaded', function() {
      const fileInput = document.getElementById('file_upload');
      const fileUploadArea = document.querySelector('.file-upload-area');
      const form = document.querySelector('form');
      
      if (fileInput) {
        fileInput.addEventListener('change', () => { updateFileCount(); loadPreviewFromFiles(); updateJobSummary(); });
      }
      
      if (fileUploadArea) {
        fileUploadArea.addEventListener('dragover', handleDragOver);
        fileUploadArea.addEventListener('dragleave', handleDragLeave);
        fileUploadArea.addEventListener('drop', handleDrop);
      }
      
      // Handle form reset to clear files display
      if (form) {
        form.addEventListener('reset', function(e) {
          // Small delay to ensure form reset completes first
          setTimeout(clearFiles, 0);
        });
      }

      // Live Preview wiring
      const controls = {
        paper: { value: 'A3' }, // Default for tarpaulin
        orientation: { value: 'landscape' }, // Default
        top: { value: '0' }, // Default
        right: { value: '0' }, // Default
        bottom: { value: '0' }, // Default
        left: { value: '0' }, // Default
        scale: { value: '100' }, // Default
        fit: { value: 'Fit to printable area' }, // Default
        color: { value: 'color' }, // Default
        copies: { value: '1' }, // Default
        duplex: { value: 'single' }, // Default
      };
      const canvas = document.getElementById('paper-canvas');
      const paperInner = document.getElementById('paper-inner');
      const thumbs = document.getElementById('thumbs');
      // Zoom controls
      window.currentZoom = 100;
      const zoomIncrement = 25;
      const minZoom = 25;
      const maxZoom = 500;

      function updateZoom() {
        if (!paperInner || !canvas) return;
        const zoomScale = window.currentZoom / 100;
        paperInner.style.transform = `scale(${zoomScale})`;
        paperInner.style.transformOrigin = 'top left';
        canvas.style.overflow = zoomScale <= 1 ? 'hidden' : 'auto';
        const zoomLevelDisplay = document.getElementById('zoom-level');
        if (zoomLevelDisplay) zoomLevelDisplay.textContent = window.currentZoom + '%';
      }

      function zoomIn() {
        if (window.currentZoom < maxZoom) {
          window.currentZoom = Math.min(window.currentZoom + zoomIncrement, maxZoom);
          updateZoom();
        }
      }

      function zoomOut() {
        if (window.currentZoom > minZoom) {
          window.currentZoom = Math.max(window.currentZoom - zoomIncrement, minZoom);
          updateZoom();
        }
      }

      function resetZoom() { window.currentZoom = 100; updateZoom(); }
      window.zoomIn = zoomIn; window.zoomOut = zoomOut; window.resetZoom = resetZoom;
      let currentFileUrl = null;
      let currentFileType = null;
      let pdfDoc = null;
      let currentPdfPage = 1;

      function mmToPx(mm) { return (mm / 25.4) * 96; }
      function paperSizePx(size){
        switch(size){
          case 'Letter': return { w:mmToPx(215.9), h:mmToPx(279.4) };
          case 'Legal': return { w:mmToPx(215.9), h:mmToPx(355.6) };
          case 'A3': return { w:mmToPx(297), h:mmToPx(420) };
          default: return { w:mmToPx(210), h:mmToPx(297) }; // A4
        }
      }

      function applyPreviewDims(){
        const content = paperInner.firstElementChild;
        if (!content) {
          const size = paperSizePx('A3');
          const maxScreenW = 520;
          const displayScale = Math.min(1, maxScreenW / size.w);
          canvas.style.width = Math.round(size.w * displayScale) + 'px';
          canvas.style.minHeight = Math.round(size.h * displayScale) + 'px';
          canvas.style.height = 'auto';
          paperInner.style.padding = '10px 20px';
          return;
        }

        content.style.transformOrigin = 'center center';
        content.style.transform = 'none';

        const colorValue = controls.color ? controls.color.value : 'color';
        if (colorValue === 'grayscale') {
          content.style.filter = 'grayscale(100%)';
        } else if (colorValue === 'bw') {
          content.style.filter = 'grayscale(100%) contrast(160%) brightness(110%)';
        } else {
          content.style.filter = 'none';
        }

        const maxScreenW = 600;
        let contentW, contentH;
        let padding = 20;

        if (content.tagName.toLowerCase() === 'div') {
          content.style.width = 'auto';
          content.style.maxWidth = maxScreenW - (padding * 2) + 'px';
          content.style.margin = '0';
          content.style.display = 'block';
          void content.offsetWidth;
          contentW = Math.max(content.scrollWidth || content.offsetWidth || maxScreenW - (padding * 2), 300);
          contentH = Math.max(content.scrollHeight || content.offsetHeight || 400, 200);
        } else if (content.tagName.toLowerCase() === 'pre') {
          content.style.width = 'auto';
          content.style.maxWidth = maxScreenW - (padding * 2) + 'px';
          content.style.margin = '0';
          content.style.display = 'block';
          void content.offsetWidth;
          contentW = Math.max(content.scrollWidth || content.offsetWidth || maxScreenW - (padding * 2), 300);
          contentH = Math.max(content.scrollHeight || content.offsetHeight || 400, 200);
        } else if (content.tagName.toLowerCase() === 'img') {
          contentW = content.naturalWidth || content.width || maxScreenW;
          contentH = content.naturalHeight || content.height || 600;
          if (contentW > maxScreenW) {
            const scale = maxScreenW / contentW;
            contentW = maxScreenW;
            contentH = contentH * scale;
          }
          content.style.width = contentW + 'px';
          content.style.height = 'auto';
          content.style.maxWidth = '100%';
          content.style.transform = 'none';
        } else if (content.tagName.toLowerCase() === 'canvas') {
          contentW = content.width || maxScreenW;
          contentH = content.height || 800;
          if (contentW > maxScreenW) {
            const scale = maxScreenW / contentW;
            contentW = maxScreenW;
            contentH = contentH * scale;
          }
          content.style.maxWidth = '100%';
          content.style.height = 'auto';
          content.style.transform = 'none';
        } else {
          void content.offsetWidth;
          contentW = Math.max(content.scrollWidth || content.offsetWidth || maxScreenW - (padding * 2), 300);
          contentH = Math.max(content.scrollHeight || content.offsetHeight || 400, 200);
        }

        const canvasW = Math.min(contentW + (padding * 2), maxScreenW);
        const canvasH = contentH + (padding * 2);
        
        canvas.style.width = canvasW + 'px';
        canvas.style.minHeight = canvasH + 'px';
        canvas.style.height = 'auto';
        paperInner.style.padding = padding + 'px';
        
        if (content.tagName.toLowerCase() === 'div' || content.tagName.toLowerCase() === 'pre') {
          content.style.maxWidth = (canvasW - (padding * 2)) + 'px';
          content.style.width = '100%';
        }
      }

      function loadPreviewFromFiles(){
        const files = fileInput.files;
        if (!files || !files[0]) return;
        const file = files[0];
        const url = URL.createObjectURL(file);
        currentFileUrl = url;
        currentFileType = file.type;
        paperInner.innerHTML = '';
        thumbs.style.display = 'none';
        thumbs.innerHTML = '';
        
        const previewWrap = document.querySelector('.preview-wrap');
        if (previewWrap) previewWrap.scrollTop = 0;
        // Reset zoom when loading new file
        window.currentZoom = 100; updateZoom();
        
        if (file.type.startsWith('image/')) {
          const img = new Image();
          img.onload = () => {
            applyPreviewDims();
            setTimeout(() => {
              const previewWrap = document.querySelector('.preview-wrap');
              if (previewWrap) previewWrap.scrollTop = 0;
            }, 100);
          };
          img.src = url;
          paperInner.appendChild(img);
        } else if (file.type === 'application/pdf') {
          pdfjsLib.getDocument(url).promise.then(doc => {
            pdfDoc = doc; currentPdfPage = 1; thumbs.style.display = 'flex';
            renderPdfThumbnails();
            renderPdfPage(currentPdfPage);
            setTimeout(() => {
              const previewWrap = document.querySelector('.preview-wrap');
              if (previewWrap) previewWrap.scrollTop = 0;
            }, 100);
          }).catch(error => {
            console.error('PDF load error:', error);
            const note = document.createElement('div');
            note.textContent = 'Unable to preview PDF. The file will still be sent.';
            note.style.fontSize = '12px'; note.style.color = '#6b7280'; note.style.textAlign = 'center';
            paperInner.appendChild(note);
            applyPreviewDims();
          });
        } else if (file.type === 'text/plain' || file.name.toLowerCase().endsWith('.txt')) {
          const reader = new FileReader();
          reader.onload = function(e) {
            const textContent = e.target.result;
            const pre = document.createElement('pre');
            pre.style.fontFamily = 'monospace';
            pre.style.fontSize = '12px';
            pre.style.padding = '16px';
            pre.style.margin = '0';
            pre.style.whiteSpace = 'pre-wrap';
            pre.style.wordWrap = 'break-word';
            pre.style.color = '#1a1a1a';
            pre.style.backgroundColor = '#ffffff';
            pre.style.maxHeight = '100%';
            pre.style.overflow = 'auto';
            pre.textContent = textContent;
            paperInner.appendChild(pre);
            applyPreviewDims();
            setTimeout(() => {
              const previewWrap = document.querySelector('.preview-wrap');
              if (previewWrap) previewWrap.scrollTop = 0;
            }, 100);
          };
          reader.onerror = function() {
            const note = document.createElement('div');
            note.textContent = 'Unable to read text file. The file will still be sent.';
            note.style.fontSize = '12px'; note.style.color = '#6b7280'; note.style.textAlign = 'center';
            paperInner.appendChild(note);
            applyPreviewDims();
          };
          reader.readAsText(file);
        } else if (file.name.toLowerCase().endsWith('.doc') || file.name.toLowerCase().endsWith('.docx')) {
          if (file.name.toLowerCase().endsWith('.docx') && typeof mammoth !== 'undefined') {
            const reader = new FileReader();
            reader.onload = function(e) {
              const arrayBuffer = e.target.result;
               mammoth.convertToHtml({ arrayBuffer: arrayBuffer })
                 .then(function(result) {
                   const previewDiv = document.createElement('div');
                   previewDiv.style.padding = '16px';
                   previewDiv.style.fontFamily = 'system-ui, -apple-system, sans-serif';
                   previewDiv.style.fontSize = '14px';
                   previewDiv.style.lineHeight = '1.6';
                   previewDiv.style.color = '#1a1a1a';
                   previewDiv.style.width = '100%';
                   previewDiv.style.minHeight = 'auto';
                   previewDiv.style.backgroundColor = '#ffffff';
                   previewDiv.style.boxSizing = 'border-box';
                   previewDiv.innerHTML = result.value;
                   previewDiv.querySelectorAll('p').forEach(p => {
                     p.style.margin = '0 0 8px 0';
                     p.style.maxWidth = '100%';
                     p.style.wordWrap = 'break-word';
                   });
                   previewDiv.querySelectorAll('table').forEach(table => {
                     table.style.width = '100%';
                     table.style.maxWidth = '100%';
                     table.style.borderCollapse = 'collapse';
                     table.style.margin = '8px 0';
                     table.style.wordWrap = 'break-word';
                   });
                   previewDiv.querySelectorAll('*').forEach(el => {
                     el.style.maxWidth = '100%';
                     if (el.tagName === 'IMG') {
                       el.style.maxWidth = '100%';
                       el.style.height = 'auto';
                     }
                   });
                  paperInner.appendChild(previewDiv);
                  applyPreviewDims();
                  setTimeout(() => {
                    const previewWrap = document.querySelector('.preview-wrap');
                    if (previewWrap) previewWrap.scrollTop = 0;
                  }, 100);
                 })
                .catch(function(error) {
                  console.error('Error converting DOCX:', error);
                  showDocFallback(file);
                });
            };
            reader.onerror = function() {
              showDocFallback(file);
            };
            reader.readAsArrayBuffer(file);
          } else {
            showDocFallback(file);
          }
        } else {
          const infoDiv = document.createElement('div');
          infoDiv.style.padding = '24px';
          infoDiv.style.textAlign = 'center';
          infoDiv.style.color = '#1a1a1a';
          const icon = document.createElement('div');
          icon.innerHTML = '<i class="fas fa-file" style="font-size: 48px; color: #750d0d; margin-bottom: 12px;"></i>';
          infoDiv.appendChild(icon);
          const fileName = document.createElement('div');
          fileName.textContent = file.name;
          fileName.style.fontWeight = '600';
          fileName.style.fontSize = '14px';
          fileName.style.marginBottom = '8px';
          fileName.style.wordBreak = 'break-word';
          infoDiv.appendChild(fileName);
          const fileSize = document.createElement('div');
          fileSize.textContent = `Size: ${formatFileSize(file.size)}`;
          fileSize.style.fontSize = '12px';
          fileSize.style.color = '#6b7280';
          fileSize.style.marginBottom = '16px';
          infoDiv.appendChild(fileSize);
          const message = document.createElement('div');
          message.textContent = 'Preview not available for this file type. The file will still be sent.';
          message.style.fontSize = '12px';
          message.style.color = '#6b7280';
          message.style.lineHeight = '1.5';
          infoDiv.appendChild(message);
          paperInner.appendChild(infoDiv);
          applyPreviewDims();
          setTimeout(() => {
            const previewWrap = document.querySelector('.preview-wrap');
            if (previewWrap) previewWrap.scrollTop = 0;
          }, 100);
        }
      }

      function showDocFallback(file) {
        const infoDiv = document.createElement('div');
        infoDiv.style.padding = '24px';
        infoDiv.style.textAlign = 'center';
        infoDiv.style.color = '#1a1a1a';
        const icon = document.createElement('div');
        icon.innerHTML = '<i class="fas fa-file-word" style="font-size: 48px; color: #750d0d; margin-bottom: 12px;"></i>';
        infoDiv.appendChild(icon);
        const fileName = document.createElement('div');
        fileName.textContent = file.name;
        fileName.style.fontWeight = '600';
        fileName.style.fontSize = '14px';
        fileName.style.marginBottom = '8px';
        fileName.style.wordBreak = 'break-word';
        infoDiv.appendChild(fileName);
        const fileSize = document.createElement('div');
        fileSize.textContent = `Size: ${formatFileSize(file.size)}`;
        fileSize.style.fontSize = '12px';
        fileSize.style.color = '#6b7280';
        fileSize.style.marginBottom = '16px';
        infoDiv.appendChild(fileSize);
        const message = document.createElement('div');
        message.textContent = 'Document preview is not available in the browser. The file will be sent as-is for printing.';
        message.style.fontSize = '12px';
        message.style.color = '#6b7280';
        message.style.lineHeight = '1.5';
        infoDiv.appendChild(message);
        paperInner.appendChild(infoDiv);
        applyPreviewDims();
        setTimeout(() => {
          const previewWrap = document.querySelector('.preview-wrap');
          if (previewWrap) previewWrap.scrollTop = 0;
        }, 100);
      }

      function renderPdfThumbnails(){
        if (!pdfDoc) return;
        thumbs.innerHTML = '';
        const count = pdfDoc.numPages;
        for (let p = 1; p <= count; p++) {
          pdfDoc.getPage(p).then(page => {
            const viewport = page.getViewport({ scale: 0.3 });
            const c = document.createElement('canvas');
            c.width = viewport.width; c.height = viewport.height;
            const ctx = c.getContext('2d');
            page.render({ canvasContext: ctx, viewport }).promise.then(() => {
              const wrap = document.createElement('div');
              wrap.className = 'thumb' + (p === currentPdfPage ? ' active' : '');
              wrap.dataset.page = page.pageNumber;
              wrap.appendChild(c);
              wrap.addEventListener('click', () => { currentPdfPage = page.pageNumber; renderPdfPage(currentPdfPage); highlightThumb(); });
              thumbs.appendChild(wrap);
            });
          });
        }
      }

      function highlightThumb(){
        document.querySelectorAll('.thumb').forEach(el => el.classList.remove('active'));
        const active = Array.from(document.querySelectorAll('.thumb')).find(el => parseInt(el.dataset.page,10) === currentPdfPage);
        if (active) active.classList.add('active');
      }

      function renderPdfPage(pageNum){
        if (!pdfDoc) return;
        pdfDoc.getPage(pageNum).then(page => {
          const desiredWidth = 1200;
          const vp = page.getViewport({ scale: 1 });
          const scale = desiredWidth / vp.width;
          const viewport = page.getViewport({ scale });
          const c = document.createElement('canvas');
          c.width = viewport.width; c.height = viewport.height;
          const ctx = c.getContext('2d');
          page.render({ canvasContext: ctx, viewport }).promise.then(() => {
            paperInner.innerHTML = '';
            paperInner.appendChild(c);
            applyPreviewDims();
            highlightThumb();
            setTimeout(() => {
              const previewWrap = document.querySelector('.preview-wrap');
              if (previewWrap) previewWrap.scrollTop = 0;
            }, 100);
          });
        });
      }

      window.openPrintPreview = function openPrintPreview(){
        const size = paperSizePx(controls.paper ? controls.paper.value : 'A3');
        const landscape = (controls.orientation ? controls.orientation.value : 'landscape') === 'landscape';
        const pageWmm = landscape ? size.h : size.w;
        const pageHmm = landscape ? size.w : size.h;
        const mTop = parseFloat((controls.top ? controls.top.value : '0')||'0');
        const mRight = parseFloat((controls.right ? controls.right.value : '0')||'0');
        const mBottom = parseFloat((controls.bottom ? controls.bottom.value : '0')||'0');
        const mLeft = parseFloat((controls.left ? controls.left.value : '0')||'0');

        const win = window.open('', '_blank');
        const style = `@page { size: ${pageWmm}mm ${pageHmm}mm; margin: 0; } body{ margin:0; } .page{ width:${pageWmm}mm; height:${pageHmm}mm; box-sizing:border-box; padding:${mTop}mm ${mRight}mm ${mBottom}mm ${mLeft}mm; display:flex; align-items:center; justify-content:center; } img{ max-width:100%; max-height:100%; } canvas{ max-width:100%; max-height:100%; }`;
        win.document.write('<!DOCTYPE html><html><head><title>Print Preview</title><style>'+style+'</style></head><body>');
        win.document.write('<div class="page">');

        if (currentFileType && currentFileType.startsWith('image/')) {
          win.document.write('<img src="'+ currentFileUrl +'" />');
        } else if (pdfDoc) {
          const currentCanvas = paperInner.querySelector('canvas');
          if (currentCanvas) {
            const dataUrl = currentCanvas.toDataURL('image/png');
            win.document.write('<img src="'+ dataUrl +'" />');
          } else {
            win.document.write('<div>PDF preview unavailable.</div>');
          }
        } else {
          win.document.write('<div>No preview available.</div>');
        }

        win.document.write('</div></body></html>');
        win.document.close();
      }

      // --- Job summary ---
      function updateJobSummary(){
        const badge = document.getElementById('job-summary');
        if (!badge) return;
        const qtyEl = document.getElementById('ctl-quantity');
        const sizeEl = document.getElementById('ctl-size');
        const qty = Math.max(1, parseInt((qtyEl && qtyEl.value) || '1', 10));
        const filesSel = (fileInput && fileInput.files) ? fileInput.files.length : 0;
        const itemsText = filesSel > 0 ? filesSel : '-';
        const sizeText = sizeEl ? sizeEl.value : '-';
        const totalPrints = filesSel > 0 ? (filesSel * qty) : '-';
        badge.textContent = `Items: ${itemsText}, Size: ${sizeText}, Qty: ${qty}, Total Prints: ${totalPrints}`;
      }

      const qtyCtl = document.getElementById('ctl-quantity');
      const sizeCtl = document.getElementById('ctl-size');
      if (qtyCtl) qtyCtl.addEventListener('input', updateJobSummary);
      if (sizeCtl) sizeCtl.addEventListener('change', updateJobSummary);
      applyPreviewDims();
      updateJobSummary();
    });

  </script>
</head>
<body>
  <div class="app">
    <div class="printer" role="region" aria-label="Printer Mail UI">
      <div class="printer-head">
        <a class="logo" href="./" title="Go to index">
          <img src="assets/logo.png" alt="Printer Logo" />
          <span class="printer-title">Tarpaulin Mail Console</span>
        </a>
      </div>

      <div class="paper" role="document">
        <div class="dotmatrix-lines" aria-hidden="true"></div>
        <div class="paper-header">
          <strong>Compose</strong>
        </div>

        <div class="section compose-section">
          <form onsubmit="sendEmail(event)" enctype="multipart/form-data">
            <div class="field">
              <label for="to_email">To</label>
              <input id="to_email" name="to_email" type="email" value="iskoprint6@gmail.com" readonly class="readonly-field" />
            </div>
            <div class="field">
              <label for="subject">Subject</label>
              <input id="subject" name="subject" type="text" placeholder="Add a subject" />
            </div>
            <div class="field">
              <textarea id="message" name="message" placeholder="Compose email..." rows="10"></textarea>
            </div>
            <div class="field">
              <div class="file-upload-wrapper">
                <input id="file_upload" name="file_upload" type="file" accept="image/*,.pdf,.doc,.docx,.txt" multiple />
                <label for="file_upload" class="file-upload-area">
                  <div class="file-upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                  </div>
                  <div class="file-upload-text">Click to upload files or drag and drop</div>
                  <div class="file-upload-subtext">Images, PDF, DOC, DOCX, TXT files</div>
                  <div class="file-info" id="file-info" style="display: none;">
                    <div class="file-info-header">
                      <i class="fas fa-paperclip"></i>
                      <span id="file-count">0 files selected</span>
                    </div>
                    <div class="file-names-list" id="file-names-list"></div>
                  </div>
                </label>
              </div>
            </div>
            <div class="compose-actions">
              <span id="ticker" class="ticker" aria-live="polite">Ready.</span>
              <div class="controls">
                <button type="button" onclick="clearFiles()">
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

        <!-- Live Preview Editor (Tarpaulin defaults: A3, landscape, zero margins, fit) -->
        <div class="editor" aria-label="Live Preview Editor">
          <div class="editor-grid">
            <div class="toolbox">
              <h4>Tarpaulin Settings</h4>
              <div class="tool"><label for="ctl-quantity">Quantity</label>
                <input id="ctl-quantity" type="number" min="1" value="1" />
              </div>
              <div class="tool"><label for="ctl-size">Size</label>
                <select id="ctl-size">
                  <option value="2x3" selected>2' x 3'</option>
                  <option value="3x4">3' x 4'</option>
                  <option value="4x6">4' x 6'</option>
                  <option value="5x8">5' x 8'</option>
                  <option value="6x10">6' x 10'</option>
                  <option value="custom">Custom Size</option>
                </select>
              </div>
              <div class="tool"><label for="ctl-material">Material</label>
                <select id="ctl-material">
                  <option value="canvas" selected>Canvas</option>
                  <option value="vinyl">Vinyl</option>
                  <option value="mesh">Mesh</option>
                  <option value="polyethylene">Polyethylene</option>
                </select>
              </div>
              <div class="tool"><label for="ctl-finishing">Finishing</label>
                <select id="ctl-finishing">
                  <option value="grommets" selected>Grommets</option>
                  <option value="hemmed">Hemmed</option>
                  <option value="rope">Rope</option>
                  <option value="none">No Finishing</option>
                </select>
              </div>
              <div class="tool"><label for="ctl-bleed">Bleed (mm)</label>
                <input id="ctl-bleed" type="number" min="0" value="5" />
              </div>
              <div class="tool"><label for="ctl-safe">Safe Margin (mm)</label>
                <input id="ctl-safe" type="number" min="0" value="10" />
              </div>
            </div>
            <div class="preview-wrap">
              <div id="paper-canvas" class="paper-canvas" aria-label="Paper preview">
                <div class="zoom-controls">
                  <button class="zoom-btn" onclick="zoomOut()" title="Zoom Out" aria-label="Zoom Out">
                    <i class="fas fa-search-minus"></i>
                  </button>
                  <span class="zoom-level" id="zoom-level">100%</span>
                  <button class="zoom-btn" onclick="zoomIn()" title="Zoom In" aria-label="Zoom In">
                    <i class="fas fa-search-plus"></i>
                  </button>
                  <button class="zoom-btn" onclick="resetZoom()" title="Reset Zoom" aria-label="Reset Zoom">
                    <i class="fas fa-undo"></i>
                  </button>
                </div>
                <div id="paper-inner" class="paper-inner"></div>
              </div>
            </div>
          </div>
          <div id="thumbs" class="thumbs" aria-label="PDF Thumbnails" style="display:none"></div>
        </div>

        <div class="footer">
        </div>
      </div>
    </div>
  </div>
  <script>
    window.__iskobotContext = [
      'Page: Tarpaulin Service',
      'Purpose: large-format prints for events, announcements, and booths.',
      'Inputs: dimensions (ft/m), resolution requirement, finishing (hem + grommets), quantity, rush flag.',
      'Users can attach design files, specify bleed, include layout instructions, and request delivery/pickup.',
      'Use this data when explaining tarpaulin pricing, lead times, or file guidelines.'
    ].join('\\n');
  </script>
  <script src="assets/chatbot-widget.js"></script>
</body>
</html>

