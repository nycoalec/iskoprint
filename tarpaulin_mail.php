<?php
// Printer-themed Email UI (HTML only in a PHP file)
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tarpaulin Mail UI</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js"></script>
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
    /* Live Preview Editor */
    .editor { padding: 14px 18px 22px; border-bottom: 1px dashed var(--line); }
    .editor .editor-grid { display:grid; grid-template-columns: 340px 1fr; gap:18px; }
    @media (max-width: 980px){ .editor .editor-grid{ grid-template-columns: 1fr; } }
    .toolbox { background:#fff; border:1px solid #eceff6; border-radius:10px; padding:12px; }
    .toolbox h4 { margin:0 0 8px 0; color: var(--maroon); font-size:14px; letter-spacing:.3px; }
    .tool { display:grid; grid-template-columns: 140px 1fr; align-items:center; gap:8px; padding:6px 0; }
    .tool label { font-size:12px; text-transform:uppercase; color:#6b7280; letter-spacing:.6px; }
    .tool input[type="number"], .tool select { width:100%; padding:8px 10px; border:1px solid #e3e6ef; border-radius:8px; }
    .preview-wrap { background:#f9fafb; border:1px dashed #d1d5db; border-radius:12px; padding:16px; display:flex; justify-content:center; align-items:center; }
    .paper-canvas { background:#ffffff; position:relative; box-shadow:0 12px 40px rgba(0,0,0,.08); border:1px solid #eee; overflow:hidden; }
    .paper-inner { width:100%; height:100%; display:flex; align-items:center; justify-content:center; position:relative; overflow:hidden; box-sizing: border-box; }
    .paper-inner img { display:block; max-width:none; max-height:none; }
    .paper-inner embed, .paper-inner object { width:100%; height:100%; border:0; }
    .thumbs { display:flex; gap:10px; overflow:auto; padding:10px 0; border-top:1px dashed var(--line); border-bottom:1px dashed var(--line); }
    .thumb { border:1px solid #e5e7eb; border-radius:6px; background:#fff; padding:6px; box-shadow:0 1px 3px rgba(0,0,0,.06); cursor:pointer; transition: transform .15s ease, box-shadow .15s ease; }
    .thumb:hover { transform: translateY(-2px); box-shadow:0 6px 14px rgba(0,0,0,.12); }
    .thumb.active { outline:2px solid var(--maroon); }
  </style>
  <script>
    // File upload handling
    function updateFileCount() {
      const fileInput = document.getElementById('file_upload');
      const fileInfo = document.getElementById('file-info');
      const fileCount = document.getElementById('file-count');
      
      if (fileInput.files.length > 0) {
        fileInfo.style.display = 'flex';
        fileCount.textContent = `${fileInput.files.length} file(s) selected`;
      } else {
        fileInfo.style.display = 'none';
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
      // Include print settings
      try {
        const copiesEl = document.getElementById('ctl-copies');
        const duplexEl = document.getElementById('ctl-duplex');
        const paperEl = document.getElementById('ctl-paper');
        const orientEl = document.getElementById('ctl-orientation');
        const topEl = document.getElementById('ctl-top');
        const rightEl = document.getElementById('ctl-right');
        const bottomEl = document.getElementById('ctl-bottom');
        const leftEl = document.getElementById('ctl-left');
        const scaleEl = document.getElementById('ctl-scale');
        const fitEl = document.getElementById('ctl-fit');
        const colorEl = document.getElementById('ctl-color');
        if (copiesEl) formData.append('copies', Math.max(1, parseInt(copiesEl.value||'1',10)));
        if (duplexEl) formData.append('duplex', duplexEl.value);
        if (paperEl) formData.append('paper', paperEl.value);
        if (orientEl) formData.append('orientation', orientEl.value);
        if (topEl) formData.append('margin_top_mm', topEl.value);
        if (rightEl) formData.append('margin_right_mm', rightEl.value);
        if (bottomEl) formData.append('margin_bottom_mm', bottomEl.value);
        if (leftEl) formData.append('margin_left_mm', leftEl.value);
        if (scaleEl) formData.append('scale_percent', scaleEl.value);
        if (fitEl) formData.append('fit_mode', fitEl.value);
        if (colorEl) formData.append('color_mode', colorEl.value);
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
      
      if (fileInput) {
        fileInput.addEventListener('change', updateFileCount);
      }
      
      if (fileUploadArea) {
        fileUploadArea.addEventListener('dragover', handleDragOver);
        fileUploadArea.addEventListener('dragleave', handleDragLeave);
        fileUploadArea.addEventListener('drop', handleDrop);
      }

      // Live Preview wiring
      const controls = {
        paper: document.getElementById('ctl-paper'),
        orientation: document.getElementById('ctl-orientation'),
        top: document.getElementById('ctl-top'),
        right: document.getElementById('ctl-right'),
        bottom: document.getElementById('ctl-bottom'),
        left: document.getElementById('ctl-left'),
        scale: document.getElementById('ctl-scale'),
        fit: document.getElementById('ctl-fit'),
        color: document.getElementById('ctl-color'),
        copies: document.getElementById('ctl-copies'),
        duplex: document.getElementById('ctl-duplex'),
      };
      const canvas = document.getElementById('paper-canvas');
      const paperInner = document.getElementById('paper-inner');
      const thumbs = document.getElementById('thumbs');
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
        const size = paperSizePx(controls.paper.value);
        const landscape = controls.orientation.value === 'landscape';
        const pageWmm = landscape ? size.h : size.w;
        const pageHmm = landscape ? size.w : size.h;
        const maxScreenW = 520;
        const displayScale = Math.min(1, maxScreenW / pageWmm);
        const canvasWpx = Math.round(pageWmm * displayScale);
        const canvasHpx = Math.round(pageHmm * displayScale);
        canvas.style.width = canvasWpx + 'px';
        canvas.style.height = canvasHpx + 'px';
        const padTop = Math.max(0, mmToPx(parseFloat(controls.top.value||'0')) * displayScale);
        const padRight = Math.max(0, mmToPx(parseFloat(controls.right.value||'0')) * displayScale);
        const padBottom = Math.max(0, mmToPx(parseFloat(controls.bottom.value||'0')) * displayScale);
        const padLeft = Math.max(0, mmToPx(parseFloat(controls.left.value||'0')) * displayScale);
        paperInner.style.padding = `${padTop}px ${padRight}px ${padBottom}px ${padLeft}px`;

        const innerW = canvasWpx - padLeft - padRight;
        const innerH = canvasHpx - padTop - padBottom;
        const content = paperInner.firstElementChild;
        if (!content) return;
        content.style.transformOrigin = 'center center';
        content.style.transform = 'none';
        if (controls.color.value === 'grayscale') content.style.filter = 'grayscale(100%)';
        else if (controls.color.value === 'bw') content.style.filter = 'grayscale(100%) contrast(160%) brightness(110%)';
        else content.style.filter = 'none';
        if (content.tagName.toLowerCase() === 'img') {
          const naturalW = content.naturalWidth || innerW;
          const naturalH = content.naturalHeight || innerH;
          const scaleToFit = Math.min(innerW / naturalW, innerH / naturalH);
          const fitMode = (controls.fit.value || 'Actual size').toLowerCase();
          let pct = parseInt(controls.scale.value || '100', 10) / 100;
          if (fitMode.includes('fit to printable')) pct = scaleToFit;
          else if (fitMode.includes('shrink')) pct = Math.min(pct, scaleToFit);
          content.style.width = naturalW + 'px';
          content.style.height = 'auto';
          content.style.transform = `scale(${pct})`;
        } else if (content.tagName.toLowerCase() === 'canvas') {
          const fitMode = (controls.fit.value || 'Actual size').toLowerCase();
          const naturalW = content.width;
          const naturalH = content.height;
          const scaleToFit = Math.min(innerW / naturalW, innerH / naturalH);
          let pct = parseInt(controls.scale.value || '100', 10) / 100;
          if (fitMode.includes('fit to printable')) pct = scaleToFit;
          else if (fitMode.includes('shrink')) pct = Math.min(pct, scaleToFit);
          content.style.transform = `scale(${pct})`;
          content.style.transformOrigin = 'center center';
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
        if (file.type.startsWith('image/')) {
          const img = new Image();
          img.onload = () => applyPreviewDims();
          img.src = url;
          paperInner.appendChild(img);
        } else if (file.type === 'application/pdf') {
          pdfjsLib.getDocument(url).promise.then(doc => {
            pdfDoc = doc; currentPdfPage = 1; thumbs.style.display = 'flex';
            renderPdfThumbnails();
            renderPdfPage(currentPdfPage);
          });
        } else {
          const note = document.createElement('div');
          note.textContent = 'Preview not available for this file type. The file will still be sent.';
          note.style.fontSize = '12px'; note.style.color = '#6b7280'; note.style.textAlign = 'center';
          paperInner.appendChild(note);
          applyPreviewDims();
        }
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
          });
        });
      }

      window.openPrintPreview = function openPrintPreview(){
        const size = paperSizePx(controls.paper.value);
        const landscape = controls.orientation.value === 'landscape';
        const pageWmm = landscape ? size.h : size.w;
        const pageHmm = landscape ? size.w : size.h;
        const mTop = parseFloat(controls.top.value||'0');
        const mRight = parseFloat(controls.right.value||'0');
        const mBottom = parseFloat(controls.bottom.value||'0');
        const mLeft = parseFloat(controls.left.value||'0');

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

      if (fileInput) fileInput.addEventListener('change', () => { updateFileCount(); loadPreviewFromFiles(); });
      Object.values(controls).forEach(ctrl => ctrl && ctrl.addEventListener('input', applyPreviewDims));
      applyPreviewDims();
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

        <!-- Live Preview Editor (Tarpaulin defaults: A3, landscape, zero margins, fit) -->
        <div class="editor" aria-label="Live Preview Editor">
          <div class="editor-grid">
            <div class="toolbox">
              <h4>Print Settings</h4>
              <div class="tool"><label for="ctl-paper">Paper Size</label>
                <select id="ctl-paper">
                  <option>A4</option>
                  <option>Letter</option>
                  <option>Legal</option>
                  <option selected>A3</option>
                </select>
              </div>
              <div class="tool"><label for="ctl-orientation">Orientation</label>
                <select id="ctl-orientation">
                  <option value="portrait">Portrait</option>
                  <option value="landscape" selected>Landscape</option>
                </select>
              </div>
              <div class="tool"><label>Margins (mm)</label>
                <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:6px;">
                  <input id="ctl-top" type="number" min="0" value="0" />
                  <input id="ctl-right" type="number" min="0" value="0" />
                  <input id="ctl-bottom" type="number" min="0" value="0" />
                  <input id="ctl-left" type="number" min="0" value="0" />
                </div>
              </div>
              <div class="tool"><label for="ctl-scale">Scale (%)</label>
                <input id="ctl-scale" type="number" min="10" max="200" value="100" />
              </div>
              <div class="tool"><label for="ctl-fit">Fit</label>
                <select id="ctl-fit">
                  <option>Actual size</option>
                  <option selected>Fit to printable area</option>
                  <option>Shrink oversized pages</option>
                </select>
              </div>
              <div class="tool"><label for="ctl-color">Color</label>
                <select id="ctl-color">
                  <option value="color" selected>Color</option>
                  <option value="grayscale">Grayscale</option>
                  <option value="bw">Black & White</option>
                </select>
              </div>
              <div class="tool"><label for="ctl-copies">Copies</label>
                <input id="ctl-copies" type="number" min="1" value="1" />
              </div>
              <div class="tool"><label for="ctl-duplex">Duplex</label>
                <select id="ctl-duplex">
                  <option value="single" selected>Single-sided</option>
                  <option value="long">Double-sided (flip on long edge)</option>
                  <option value="short">Double-sided (flip on short edge)</option>
                </select>
              </div>
              <div style="display:flex; gap:8px; margin-top:8px;">
                <button type="button" class="button" onclick="openPrintPreview()"><i class="fas fa-print"></i> Print Preview</button>
              </div>
            </div>
            <div class="preview-wrap">
              <div id="paper-canvas" class="paper-canvas" aria-label="Paper preview">
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
</body>
</html>

