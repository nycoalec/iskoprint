<?php
require_once 'auth.php';

$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();
$currentUser = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>iskOPrint</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<style>
    :root {
      --maroon: #750d0d;
      --maroon-dark: #5d0a0a;
      --ink: #1a1a1a;
      --muted: #8a8a8a;
      --line: rgba(0,0,0,0.08);
    }

    * { box-sizing: border-box; }
    html, body { height: 100%; }
    body {
      margin: 0;
      font-family: system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";
      color: var(--ink);
      background: #fff;
    }

    .topbar {
      background: #ffffff;
      border-bottom: 1px solid var(--line);
    }
    .nav {
      width: 100%;
      margin: 0;
      padding: 10px 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: relative;
      gap: 20px;
    }
    .logo {
      display: inline-flex; align-items: center; gap: 0; font-weight: 700;
      flex-shrink: 0;
    }
    .logo img { height: 60px; width: auto; border-radius: 4px; display: block; }
    .logo-dot { width: 22px; height: 22px; border-radius: 50%; background:#111; border: 2px solid #333; }
    .brand { color:#b10f0f; display:inline-flex; align-items:center; gap:4px; letter-spacing:.2px; font-size: 22px; margin-left:-15px; }
    .brand .isk { font-weight:800; }
    .brand .print { font-weight:800; }
    .brand .star { display:inline-flex; align-items:center; margin-left:-8px; position: relative; top: 1px; }
    .brand .star img { width:22px; height:22px; display:block; border-radius: 2px; vertical-align: middle; }
    .brand .print { margin-left:-6px; }
    .brand .print { margin-left:-6px; }

    .tabs { 
      display: inline-flex; 
      gap: 8px; 
      flex: 1; 
      justify-content: center; 
      flex-wrap: wrap;
    }
    .tab {
      padding: 13px 30px; background: transparent; color: #222; text-decoration: none; font-size: 14px; border-radius: 2px;
      border: 1px solid transparent; transition: background-color .15s ease, color .15s ease, border-color .15s ease;
    }
    .tab:hover { background: var(--maroon); color: #fff; border-color: var(--maroon); border-radius: 0; box-shadow: 0 10px 0 var(--maroon), 0 -10px 0 var(--maroon); }

    .user-icon { 
      width: 40px; 
      height: 40px; 
      background: var(--maroon); 
      border-radius: 50%; 
      display: flex; 
      align-items: center; 
      justify-content: center; 
      color: white; 
      font-size: 18px; 
      cursor: pointer; 
      transition: all 0.2s ease;
      flex-shrink: 0;
    }
    .user-icon:hover { 
      background: var(--maroon-dark); 
      transform: scale(1.05); 
    }

    /* Sidebar Styles */
    .sidebar {
      position: fixed;
      top: 0;
      right: -350px;
      width: 350px;
      height: 100vh;
      background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
      box-shadow: -5px 0 25px rgba(0,0,0,0.15);
      transition: right 0.3s ease;
      z-index: 1000;
      overflow-y: auto;
      border-left: 1px solid #e9ecef;
    }
    .sidebar.open {
      right: 0;
    }
    .sidebar-header {
      padding: 25px 20px 20px;
      background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-dark) 100%);
      color: white;
      position: relative;
    }
    .sidebar-header::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.3) 50%, transparent 100%);
    }
    .sidebar-title {
      font-size: 20px;
      font-weight: 700;
      color: white;
      margin: 0;
    }
    .sidebar-close {
      background: rgba(117,13,13,0.1);
      border: none;
      border-radius: 50%;
      width: 36px;
      height: 36px;
      font-size: 14px;
      cursor: pointer;
      color: var(--maroon);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
      position: absolute;
      top: 20px;
      right: 20px;
    }
    .sidebar-close:hover {
      background: rgba(117,13,13,0.2);
      transform: scale(1.1);
    }
    .sidebar-close i {
      font-size: 14px;
    }
    .sidebar-content {
      padding: 0;
    }
    .sidebar-section {
      margin-bottom: 0;
    }
    .sidebar-section h3 {
      font-size: 12px;
      font-weight: 700;
      color: #6c757d;
      margin: 0;
      padding: 20px 20px 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
      background: #f8f9fa;
      border-bottom: 1px solid #e9ecef;
    }
    .sidebar-item {
      display: flex;
      align-items: center;
      padding: 16px 20px;
      cursor: pointer;
      transition: all 0.2s ease;
      border-bottom: 1px solid #f0f0f0;
      position: relative;
    }
    .sidebar-item:hover {
      background: linear-gradient(90deg, rgba(117,13,13,0.05) 0%, rgba(117,13,13,0.02) 100%);
      padding-left: 25px;
    }
    .sidebar-item.active {
      background: linear-gradient(90deg, var(--maroon) 0%, var(--maroon-dark) 100%);
      color: white;
      box-shadow: inset 3px 0 0 #fff;
    }
    .sidebar-item.active i {
      color: white;
    }
    .sidebar-item.active span {
      color: white;
      font-weight: 600;
    }
    .sidebar-item i {
      width: 24px;
      margin-right: 16px;
      color: var(--maroon);
      font-size: 16px;
      transition: all 0.2s ease;
    }
    .sidebar-item span {
      font-size: 15px;
      color: #333;
      font-weight: 500;
      transition: all 0.2s ease;
    }
    .sidebar-item:hover i {
      transform: scale(1.1);
    }
    
    /* User Profile Section */
    .user-profile-section {
      padding: 25px 20px;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-bottom: 1px solid #dee2e6;
      display: flex;
      align-items: center;
      gap: 15px;
      position: relative;
    }
    .profile-avatar-large {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-dark) 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 24px;
      box-shadow: 0 4px 12px rgba(117,13,13,0.3);
    }
    .profile-details {
      flex: 1;
    }
    .profile-name {
      font-size: 18px;
      font-weight: 700;
      color: #333;
      margin: 0 0 5px 0;
    }
    .profile-email {
      font-size: 14px;
      color: #6c757d;
      margin: 0 0 8px 0;
    }
    .profile-status-badge {
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .status-dot {
      width: 8px;
      height: 8px;
      background: #28a745;
      border-radius: 50%;
      animation: pulse 2s infinite;
    }
    .status-text {
      font-size: 12px;
      color: #28a745;
      font-weight: 600;
    }
    .logout-item {
      border-top: 1px solid #e9ecef !important;
      margin-top: 8px;
      padding-top: 16px !important;
    }
    .logout-item i {
      color: #dc3545 !important;
    }
    .logout-item:hover {
      background: rgba(220, 53, 69, 0.1) !important;
    }
    .logout-item:hover i {
      color: #c82333 !important;
    }
    @keyframes pulse {
      0% { opacity: 1; }
      50% { opacity: 0.5; }
      100% { opacity: 1; }
    }
    
    /* Hide auth-only sections for guests */
    .guest .sidebar-section[data-auth="true"] { display: none; }
    
    /* Guest profile section */
    .guest-profile-section {
      padding: 25px 20px;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-bottom: 1px solid #dee2e6;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: relative;
    }
    .guest-profile-text {
      font-size: 18px;
      font-weight: 700;
      color: #333;
      margin: 0;
    }
    .guest .user-profile-section { display: none; }
    .guest .guest-profile-section { display: flex; }
    .authenticated .guest-profile-section { display: none; }

    .sidebar-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 999;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
    }
    .sidebar-overlay.active {
      opacity: 1;
      visibility: visible;
    }

    .hero {
      background: var(--maroon);
      padding: 0;
    }
    .hero-inner { max-width: 100%; margin: 0; padding: 0; }
    .hero-frame { background:#c6c6c6; height: 100%; border: none; border-radius: 0; box-shadow: none; }

    /* Slideshow Styles */
    .slideshow-container {
      position: relative;
      height: 280px;
      border: none;
      border-radius: 0;
      box-shadow: none;
      overflow: hidden;
      width: 100%;
      margin: 0;
    }

    .slide {
      display: none;
      position: relative;
      width: 100%;
      height: 100%;
    }

    .slide.active {
      display: block;
    }

    .slide img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .slide-content {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(transparent, rgba(0,0,0,0.7));
      color: white;
      padding: 20px;
      text-align: center;
    }

    .slide-content h2 {
      margin: 0 0 8px 0;
      font-size: 24px;
      font-weight: 700;
    }

    .slide-content p {
      margin: 0;
      font-size: 14px;
      opacity: 0.9;
    }

    /* Navigation arrows */
    .prev, .next {
      cursor: pointer;
      position: absolute;
      top: 50%;
      width: auto;
      margin-top: -22px;
      padding: 16px;
      color: white;
      font-weight: bold;
      font-size: 18px;
      transition: 0.6s ease;
      border-radius: 0 3px 3px 0;
      user-select: none;
      background: rgba(0,0,0,0.3);
      border: none;
    }

    .next {
      right: 0;
      border-radius: 3px 0 0 3px;
    }

    .prev:hover, .next:hover {
      background-color: rgba(0,0,0,0.8);
    }

    /* Dots indicator */
    .dots {
      text-align: center;
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translateX(-50%);
    }

    .dot {
      cursor: pointer;
      height: 8px;
      width: 8px;
      margin: 0 3px;
      background-color: rgba(255,255,255,0.5);
      border-radius: 50%;
      display: inline-block;
      transition: background-color 0.6s ease;
    }

    .dot.active, .dot:hover {
      background-color: white;
    }

    /* Fade animation */
    .slide {
      animation-name: fade;
      animation-duration: 1.5s;
    }

    @keyframes fade {
      from {opacity: .4}
      to {opacity: 1}
    }

    .section { max-width: 1100px; margin: 0 auto; padding: 24px 16px; }

    .services { text-align: center; }
    .services h2 { margin: 8px 0 18px; letter-spacing: 1px; }
    .service-grid { display:grid; grid-template-columns: repeat(6, minmax(0,1fr)); gap: 16px; }
    .service {
      height: 84px; display:grid; place-items:center; color:#801414; background: transparent;
    }
    .service:hover { background: var(--maroon); color:#fff; }
    .service > div { font-size: 44px; }
    .service i { font-size: 44px; line-height: 1; display:block; }
    .service small { display:block; margin-top: 6px; font-size: 11px; color:#801414; }
    .service:hover small { color:#fff; }

    .about-band { background: var(--maroon); color: #fff; }
    .about { max-width: 1100px; margin: 0 auto; padding: 24px 16px 28px; }
    .about h3 { margin: 0 0 10px; letter-spacing: 0.6px; }
    .about p { margin: 0; max-width: 880px; line-height: 1.5; font-size: 14px; }

    .contact { padding: 28px 16px 44px; }
    .contact h3 { margin: 0 0 18px; text-align: center; font-size: 24px; color: var(--maroon); }
    .contact-grid { display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 22px; max-width:1100px; margin:0 auto; }
    .contact-card { 
      background:#fff; 
      border:1px solid #eee; 
      border-radius: 8px; 
      padding: 20px; 
      min-height: 120px; 
      text-align: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    .contact-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }
    .contact h4 {
      margin: 0 0 12px 0;
      font-size: 16px;
      font-weight: 600;
      color: var(--maroon);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .contact-card a {
      color: var(--maroon);
      text-decoration: none;
      font-weight: 500;
    }
    .contact-card a:hover {
      text-decoration: underline;
    }

    .contact-icon {
      font-size: 40px;
      color: var(--maroon);
      margin-bottom: 4px;
      display: block;
    }

    /* Auth Modal */
    .modal-overlay {
      position: fixed; inset: 0; background: rgba(0,0,0,0.55);
      display: none; align-items: center; justify-content: center; z-index: 1200;
    }
    .modal-overlay.active { display: flex; }
    .modal {
      width: 92%; max-width: 420px; background: #fff; border-radius: 12px;
      box-shadow: 0 12px 40px rgba(0,0,0,0.25); overflow: hidden;
      border: 1px solid #eee;
    }
    .modal-header {
      padding: 16px 18px; background: var(--maroon); color: #fff; display:flex; align-items:center; justify-content: space-between;
    }
    .modal-title { font-weight: 700; }
    .modal-close { background: transparent; border: 0; color: #fff; font-size: 18px; cursor: pointer; }
    .modal-body { padding: 18px; color: #333; }
    .modal-actions { padding: 0 18px 18px; display:flex; gap:10px; }
    .btn { appearance: none; border: 1px solid transparent; border-radius: 8px; padding: 10px 14px; cursor: pointer; font-weight:600; }
    .btn-primary { background: var(--maroon); color:#fff; border-color: var(--maroon); }
    .btn-secondary { background: #f4f4f6; color:#333; border-color:#e5e7eb; }
    .btn:hover { filter: brightness(0.95); }

    @media (max-width: 900px) {
      .service-grid { grid-template-columns: repeat(3, minmax(0,1fr)); }
      .contact-grid { grid-template-columns: 1fr; }
      .nav { 
        flex-direction: column; 
        gap: 12px; 
        padding: 16px;
      }
      .tabs { 
        justify-content: center; 
        gap: 4px; 
      }
      .tab { 
        padding: 10px 16px; 
        font-size: 13px; 
      }
    }
    
    @media (max-width: 600px) {
      .nav { 
        flex-direction: row; 
        justify-content: space-between; 
        align-items: center;
      }
      .tabs { 
        display: none; 
      }
      .logo img { 
        height: 50px; 
      }
      .brand { 
        font-size: 18px; 
      }
    }
</style>
</head>
<body>
  <header class="topbar">
    <div class="nav" role="navigation" aria-label="Top Navigation">
      <div class="logo">
        <img src="assets/logo.png" alt="iskOPrint logo" />
        <span class="brand" aria-label="Isk star Print">
          <span class="isk">Isk</span>
          <span class="star" aria-hidden="true"><img src="assets/pup_star.png" alt="" /></span>
          <span class="print">Print</span>
        </span>
      </div>
      <div class="tabs">
        <a class="tab" href="#home">HOME</a>
        <a class="tab" href="#services">SERVICES</a>
        <a class="tab" href="#about">ABOUT US</a>
        <a class="tab" href="#contact">CONTACT</a>
      </div>
      <div class="user-icon" onclick="toggleSidebar()" aria-label="User Menu">
        <i class="fas fa-user"></i>
      </div>
    </div>
  </header>

  <main id="home">
    <section class="hero">
      <div class="hero-inner">
        <div class="slideshow-container">
          <div class="slide active">
            <img src="assets/slide_pic_1.jpg" alt="Photo printing and display services" />
            <div class="slide-content">
              <h2>Photo Printing & Display</h2>
              <p>Professional photo printing and mounting services</p>
            </div>
          </div>
          <div class="slide">
            <img src="assets/slide_pic_2.jpg" alt="Office printing and copying services" />
            <div class="slide-content">
              <h2>Office Printing & Copying</h2>
              <p>Professional document printing and copying services</p>
            </div>
          </div>
          <div class="slide">
            <img src="assets/slide_pic_3.jpg" alt="Large format printing services" />
            <div class="slide-content">
              <h2>Large Format Printing</h2>
              <p>Professional banners, posters, and large format prints</p>
            </div>
          </div>
          <div class="slide">
            <img src="assets/slide_pic_4.jpg" alt="Lamination services" />
            <div class="slide-content">
              <h2>Lamination Services</h2>
              <p>Professional document and photo lamination</p>
            </div>
          </div>
          
          
          <!-- Dots indicator -->
          <div class="dots">
            <span class="dot active" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
            <span class="dot" onclick="currentSlide(4)"></span>
          </div>
        </div>
      </div>
    </section>

    <section id="services" class="section services">
      <h2>SERVICES</h2>
      <div class="service-grid">
        <div class="service" onclick="handleService('printer_mail.php')" style="cursor: pointer;"><div><i class="fas fa-print"></i><small>PRINT</small></div></div>
        <div class="service" onclick="handleService('bookbind_mail.php')" style="cursor: pointer;"><div><i class="fas fa-book"></i><small>BOOK BIND</small></div></div>
        <div class="service" onclick="handleService('laminate_mail.php')" style="cursor: pointer;"><div><i class="fas fa-layer-group"></i><small>LAMINATE</small></div></div>
        <div class="service" onclick="handleService('pictures_mail.php')" style="cursor: pointer;"><div><i class="fas fa-image"></i><small>PICTURES</small></div></div>
        <div class="service" onclick="handleService('photocopy_mail.php')" style="cursor: pointer;"><div><i class="fas fa-copy"></i><small>PHOTOCOPY</small></div></div>
        <div class="service" onclick="handleService('tarpaulin_mail.php')" style="cursor: pointer;"><div><i class="fas fa-file-alt"></i><small>TARPAULIN</small></div></div>
      </div>
    </section>

    <section id="about" class="about-band">
      <div class="about">
        <h3>ABOUT US:</h3>
        <p>
        Welcome to Isk⭐Print, your trusted partner for all things print.
        We specialize in providing high-quality, professional printing services with the ease and convenience of online ordering.
        From business cards and brochures to banners and custom apparel,
        we're dedicated to helping you bring your ideas to life with exceptional quality and attention to detail.
        </p>
      </div>
    </section>

    <section id="contact" class="contact">
      <h3>CONTACT</h3>
      <div class="contact-grid">
        <div class="contact-card">
          <i class="fab fa-facebook-square contact-icon" aria-hidden="true"></i>
          <a href="https://facebook.com/IskoPrintOfficial" target="_blank" aria-label="Facebook">
            facebook.com/IskoPrintOfficial
          </a>
        </div>
        <div class="contact-card">
          <i class="fab fa-instagram contact-icon" aria-hidden="true"></i>
          <a href="https://instagram.com/iskoprint_official" target="_blank" aria-label="Instagram">
            @iskoprint_official
          </a>
        </div>
        <div class="contact-card">
          <i class="fab fa-telegram-plane contact-icon" aria-hidden="true"></i>
          <a href="https://t.me/IskoPrintOfficial" target="_blank" aria-label="Telegram">
            t.me/IskoPrintOfficial
          </a>
        </div>
      </div>
    </section>
  </main>

  <!-- Auth Modal -->
  <div id="authModal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="authTitle">
    <div class="modal">
      <div class="modal-header">
        <div id="authTitle" class="modal-title">Sign up required</div>
        <button class="modal-close" onclick="closeAuthModal()">&times;</button>
      </div>
      <div class="modal-body">
        You're not signed in yet. Please register a new account to continue.
      </div>
      <div class="modal-actions">
        <button class="btn btn-primary" onclick="goSignUp()">Sign up</button>
      </div>
    </div>
  </div>

  <!-- Sidebar -->
  <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
  <div class="sidebar" id="sidebar">
    
    <!-- User Profile Section -->
    <div class="user-profile-section">
      <div class="profile-avatar-large">
        <i class="fas fa-user"></i>
      </div>
      <div class="profile-details">
        <h3 class="profile-name">John Doe</h3>
        <p class="profile-email">john.doe@example.com</p>
        <div class="profile-status-badge">
          <span class="status-dot"></span>
          <span class="status-text">Online</span>
        </div>
      </div>
      <button class="sidebar-close" onclick="closeSidebar()">
        <i class="fas fa-arrow-left"></i>
      </button>
    </div>
    
    <!-- Guest Profile Section -->
    <div class="guest-profile-section">
      <div class="guest-profile-text">User Profile</div>
      <button class="sidebar-close" onclick="closeSidebar()">
        <i class="fas fa-arrow-left"></i>
      </button>
    </div>
    
    <div class="sidebar-content">
      <div class="sidebar-section" data-auth="true">
        <h3>Payment</h3>
        <div class="sidebar-item" onclick="setActiveItem(this)" onmouseenter="setActiveItem(this)">
          <i class="fas fa-credit-card"></i>
          <span>Payment Methods</span>
        </div>
        <div class="sidebar-item" onclick="setActiveItem(this)" onmouseenter="setActiveItem(this)">
          <i class="fas fa-receipt"></i>
          <span>Billing History</span>
        </div>
        <div class="sidebar-item" onclick="setActiveItem(this)" onmouseenter="setActiveItem(this)">
          <i class="fas fa-file-invoice"></i>
          <span>Invoices</span>
        </div>
      </div>
      
      <div class="sidebar-section" data-auth="true">
        <h3>Printer Settings</h3>
        <div class="sidebar-item" onclick="setActiveItem(this)" onmouseenter="setActiveItem(this)">
          <i class="fas fa-file-alt"></i>
          <span>Pages to Print</span>
        </div>
        <div class="sidebar-item" onclick="setActiveItem(this)" onmouseenter="setActiveItem(this)">
          <i class="fas fa-copy"></i>
          <span>Copies</span>
        </div>
        <div class="sidebar-item" onclick="setActiveItem(this)" onmouseenter="setActiveItem(this)">
          <i class="fas fa-th-large"></i>
          <span>Layout</span>
        </div>
        <div class="sidebar-item" onclick="setActiveItem(this)" onmouseenter="setActiveItem(this)">
          <i class="fas fa-file"></i>
          <span>Paper Size</span>
        </div>
        <div class="sidebar-item" onclick="setActiveItem(this)" onmouseenter="setActiveItem(this)">
          <i class="fas fa-ruler-combined"></i>
          <span>Margins</span>
        </div>
        <div class="sidebar-item" onclick="setActiveItem(this)" onmouseenter="setActiveItem(this)">
          <i class="fas fa-palette"></i>
          <span>Color Options</span>
        </div>
        <div class="sidebar-item" onclick="setActiveItem(this)" onmouseenter="setActiveItem(this)">
          <i class="fas fa-expand-arrows-alt"></i>
          <span>Scale / Fit to Page</span>
        </div>
        <div class="sidebar-item" onclick="setActiveItem(this)" onmouseenter="setActiveItem(this)">
          <i class="fas fa-retweet"></i>
          <span>Duplex Printing</span>
        </div>
        <div class="sidebar-item logout-item" onclick="logout()">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </div>
      </div>
    </div>
  </div>

  <footer class="section" style="text-align:center">
    <!-- Printer Mail Console functionality moved to printer icon -->
  </footer>

  <script>
    let slideIndex = 1;
    let slideInterval;

    // Initialize slideshow
    function initSlideshow() {
      showSlides(slideIndex);
      startAutoSlide();
    }

    // Show slides function
    function showSlides(n) {
      let slides = document.getElementsByClassName("slide");
      let dots = document.getElementsByClassName("dot");
      
      if (n > slides.length) {slideIndex = 1}
      if (n < 1) {slideIndex = slides.length}
      
      // Hide all slides
      for (let i = 0; i < slides.length; i++) {
        slides[i].classList.remove("active");
      }
      
      // Remove active class from all dots
      for (let i = 0; i < dots.length; i++) {
        dots[i].classList.remove("active");
      }
      
      // Show current slide and activate corresponding dot
      slides[slideIndex-1].classList.add("active");
      dots[slideIndex-1].classList.add("active");
    }

    // Change slide function
    function changeSlide(n) {
      slideIndex += n;
      showSlides(slideIndex);
      resetAutoSlide();
    }

    // Current slide function
    function currentSlide(n) {
      slideIndex = n;
      showSlides(slideIndex);
      resetAutoSlide();
    }

    // Auto slide function
    function startAutoSlide() {
      slideInterval = setInterval(function() {
        slideIndex++;
        showSlides(slideIndex);
      }, 5000); // Change slide every 5 seconds
    }

    // Reset auto slide timer
    function resetAutoSlide() {
      clearInterval(slideInterval);
      startAutoSlide();
    }

    // Pause slideshow on hover
    function pauseSlideshow() {
      clearInterval(slideInterval);
    }

    // Resume slideshow when mouse leaves
    function resumeSlideshow() {
      startAutoSlide();
    }

    // Sidebar functions
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebarOverlay');
      sidebar.classList.toggle('open');
      overlay.classList.toggle('active');
      
      // Update UI every time sidebar opens
      if (sidebar.classList.contains('open')) {
        updateAuthUI();
      }
    }

    function closeSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebarOverlay');
      sidebar.classList.remove('open');
      overlay.classList.remove('active');
    }

    // --- Auth gating for Services ---
    function isSignedIn() {
      return <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
    }

    function handleService(url) {
      if (isSignedIn()) {
        window.open(url, '_blank');
      } else {
        openAuthModal();
      }
    }

    function openAuthModal() {
      const m = document.getElementById('authModal');
      m.classList.add('active');
    }
    function closeAuthModal() {
      const m = document.getElementById('authModal');
      m.classList.remove('active');
    }
    function goSignUp() {
      window.location.href = 'register_acc.php';
    }

    function setActiveItem(clickedItem) {
      // Remove active class from all sidebar items
      const allItems = document.querySelectorAll('.sidebar-item');
      allItems.forEach(item => item.classList.remove('active'));
      
      // Add active class to clicked item
      clickedItem.classList.add('active');
    }

    function updateAuthUI() {
      const signedIn = isSignedIn();
      if (!signedIn) {
        document.body.classList.add('guest');
        document.body.classList.remove('authenticated');
      } else {
        document.body.classList.remove('guest');
        document.body.classList.add('authenticated');
        updateUserProfile();
      }
    }

    function updateUserProfile() {
      <?php if ($currentUser): ?>
        const nameElement = document.querySelector('.profile-name');
        const emailElement = document.querySelector('.profile-email');
        
        if (nameElement) nameElement.textContent = '<?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?>';
        if (emailElement) emailElement.textContent = '<?php echo htmlspecialchars($currentUser['email']); ?>';
      <?php endif; ?>
    }

    function logout() {
      // Redirect to logout page
      window.location.href = 'logout.php';
    }

    // Initialize slideshow when page loads
    document.addEventListener('DOMContentLoaded', function() {
      initSlideshow();
      updateAuthUI();
      
      // Add hover events to pause/resume slideshow
      const slideshowContainer = document.querySelector('.slideshow-container');
      slideshowContainer.addEventListener('mouseenter', pauseSlideshow);
      slideshowContainer.addEventListener('mouseleave', resumeSlideshow);
    });
  </script>