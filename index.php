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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/theme.css" />
  <title>iskOPrint</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<style>
    :root {
      --maroon: #750d0d;
      --maroon-dark: #5d0a0a;
      --ink: #1a1a1a;
      --muted: #8a8a8a;
      --line: rgba(0,0,0,0.08);
      --bg: #fff;
      --bg-noche: linear-gradient(180deg, rgba(30,30,30,0.95) 0%, rgba(40,40,40,0.95) 100%);
    }

    * { box-sizing: border-box; }
    html, body { height: 100%; }
    html { scroll-behavior: smooth; }
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
    .brand .star img { width:22px; height:22px; display:block; border-radius: 2px; }
    .brand .print { margin-left:-6px; }
    .brand .print { margin-left:-6px; }

    .tabs { 
      display: inline-flex; 
      gap: 10px; 
      flex: 1; 
      justify-content: center; 
      flex-wrap: wrap;
    }
    .tab {
      padding: 12px 22px; background: transparent; color: #222; text-decoration: none; font-size: 14px; border-radius: 8px;
      border: 1px solid transparent; transition: background-color .2s ease, color .2s ease, border-color .2s ease, transform .2s ease;
    }
    @media (hover:hover) and (pointer:fine) {
      .tab:hover { background: rgba(117,13,13,0.08); color: var(--maroon); border-color: rgba(117,13,13,0.25); transform: translateY(-1px); }
    }
    /* Neutralize any pre-existing active class */
    .tab.active { background: transparent; color: inherit; border-color: transparent; transform: none; }

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
      height: 420px;
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
      padding: 10px 12px;
      color: white;
      font-weight: bold;
      font-size: 16px;
      transition: background-color .2s ease, transform .2s ease;
      border-radius: 999px;
      user-select: none;
      background: rgba(0,0,0,0.35);
      border: none;
    }

    .next {
      right: 0;
      border-radius: 3px 0 0 3px;
    }

    .prev:hover, .next:hover {
      background-color: rgba(0,0,0,0.6);
      transform: scale(1.05);
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
      height: 10px;
      width: 10px;
      margin: 0 4px;
      background-color: rgba(255,255,255,0.5);
      border-radius: 50%;
      display: inline-block;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .dot.active, .dot:hover {
      background-color: white;
      transform: scale(1.1);
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

    .section { max-width: 1100px; margin: 0 auto; padding: 56px 20px; }

    .services { text-align: center; }
    .services h2 { margin: 10px 0 22px; letter-spacing: 1px; }
    .service-grid { display:grid; grid-template-columns: repeat(6, minmax(0,1fr)); gap: 20px; }
    .service {
      height: 96px; display:grid; place-items:center; color: var(--maroon); background: #fff; border: 1px solid #eee; border-radius: 10px;
      transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease, color .2s ease;
    }
    .service:hover { background: var(--maroon); color:#fff; transform: translateY(-3px); box-shadow: 0 8px 18px rgba(117,13,13,0.25); }
    .service > div { font-size: 42px; }
    .service i { font-size: 42px; line-height: 1; display:block; }
    .service small { display:block; margin-top: 6px; font-size: 12px; color: var(--maroon); letter-spacing: .2px; }
    .service:hover small { color:#fff; }

    .about-band { background: var(--maroon); color: #fff; margin-top: 96px; }
    .about { max-width: 1100px; margin: 0 auto; padding: 36px 18px 40px; }
    .about h3 { margin: 0 0 12px; letter-spacing: 0.6px; }
    .about p { margin: 0; max-width: 920px; line-height: 1.7; font-size: 15px; }

    .contact { padding: 72px 18px 88px; margin-top: 96px; }
    .contact h3 { margin: 0 0 18px; text-align: center; font-size: 24px; color: var(--maroon); }
    .contact-grid { display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 22px; max-width:1100px; margin:0 auto; }
    .contact-card { 
      background:#fff; 
      border:1px solid #eee; 
      border-radius: 12px; 
      padding: 24px; 
      min-height: 120px; 
      text-align: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      transition: transform 0.2s ease, box-shadow 0.2s ease, border-color .2s ease;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }
    .contact-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 24px rgba(0,0,0,0.16);
      border-color: rgba(117,13,13,0.35);
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
      font-size: 42px;
      color: var(--maroon);
      margin-bottom: 4px;
      display: block;
      transition: transform .2s ease;
    }
    .contact-card:hover .contact-icon { transform: scale(1.05); }

    /* Reveal on scroll */
    .reveal { opacity: 0; transform: translateY(16px); transition: opacity .6s ease, transform .6s ease; }
    .reveal.visible { opacity: 1; transform: none; }

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

    /* New: Features, Steps, Pricing, Testimonials, Footer (refined) */
    .glass { background: rgba(255,255,255,0.55); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border:1px solid rgba(255,255,255,0.35); box-shadow:0 12px 30px rgba(17,17,17,.08); }
    .band { background:#fff; }
    .features { max-width:1100px; margin:88px auto; padding:24px; }
    .section-title { margin:0 0 18px; color:var(--maroon); letter-spacing:.6px; text-align:center; font-size:26px; }
    .section-sub { margin:-8px 0 22px; text-align:center; color:#666; font-size:14px; }
    .features-grid { display:grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap:24px; }
    .feature { border:0; border-radius:16px; padding:24px; text-align:center; transition: transform .2s ease, box-shadow .2s ease; }
    .feature:hover { transform: translateY(-4px); box-shadow:0 18px 40px rgba(17,17,17,.12); }
    .feature i { font-size:26px; color: #fff; background: var(--maroon); width:40px; height:40px; display:inline-flex; align-items:center; justify-content:center; border-radius:10px; margin-bottom:10px; }
    .feature h4 { margin:6px 0 6px; font-size:15px; }
    .feature p { margin:0; color:#555; font-size:13px; }

    .steps { max-width:1100px; margin:92px auto; padding:0 20px; }
    .steps-grid { display:grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap:24px; counter-reset: step; align-items:stretch; }
    .step { border:0; border-radius:16px; padding:24px 20px; text-align:center; position:relative; }
    .step::before { counter-increment: step; content: counter(step); position:absolute; top:-14px; left:18px; width:30px; height:30px; background: var(--maroon); color:#fff; display:flex; align-items:center; justify-content:center; border-radius:50%; font-weight:700; box-shadow:0 6px 14px rgba(117,13,13,.25); }
    .step:not(:last-child)::after { content:""; position:absolute; top:1px; right:-9px; width:18px; height:18px; border-right:2px solid rgba(117,13,13,.25); border-top:2px solid rgba(117,13,13,.25); transform: rotate(45deg); }
    .step h4 { margin:6px 0; font-size:15px; }
    .step p { margin:0; color:#555; font-size:13px; }

    .pricing { max-width:1100px; margin:100px auto; padding:0 20px; }
    .price-table { width:100%; border-collapse:separate; border-spacing:0; overflow:hidden; border:0; border-radius:16px; }
    .price-table th, .price-table td { padding:14px 16px; text-align:left; border-bottom:1px solid #f5f5f7; }
    .price-table th { background: rgba(255,255,255,0.75); color:#333; }
    .price-table tr:last-child td { border-bottom:0; }
    .tag { display:inline-block; padding:4px 8px; border-radius:999px; font-size:12px; border:1px solid rgba(117,13,13,.25); color: var(--maroon); background:#fff; }

    .testimonials { max-width:1100px; margin:96px auto; padding:0 20px; }
    .t-grid { display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:24px; }
    .t { border:0; border-radius:16px; padding:24px; }
    .t p { margin:0 0 8px; color:#444; line-height:1.6; }
    .t small { color:#666; font-weight:600; }

    .footer { background:#0f0f0f; color:#ddd; margin-top:40px; }
    .footer-inner { max-width:1100px; margin:0 auto; padding:28px 18px; display:grid; grid-template-columns: 2fr 1fr 1fr; gap:18px; }
    .footer h4 { margin:0 0 10px; color:#fff; }
    .footer a { color:#ddd; text-decoration:none; }
    .footer a:hover { text-decoration:underline; }
    .copyright { text-align:center; color:#999; padding:12px 18px 22px; font-size:13px; }

    @media (max-width: 900px) {
      .service-grid { grid-template-columns: repeat(3, minmax(0,1fr)); gap: 16px; }
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
      .features-grid { grid-template-columns: 1fr 1fr; }
      .steps-grid { grid-template-columns: 1fr 1fr; }
      .t-grid { grid-template-columns: 1fr; }
      .footer-inner { grid-template-columns: 1fr; }
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
          <button class="prev" onclick="changeSlide(-1)" aria-label="Previous slide">&#10094;</button>
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
          <button class="next" onclick="changeSlide(1)" aria-label="Next slide">&#10095;</button>
          
          
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

    <section class="band">
      <div class="features">
        <h3 class="section-title">Why choose Isk⭐Print?</h3>
        <div class="section-sub">Modern output, simple ordering, and campus‑friendly rates</div>
        <div class="features-grid">
          <div class="feature glass">
            <i class="fas fa-bolt"></i>
            <h4>Fast Turnaround</h4>
            <p>Same‑day options for most document jobs.</p>
          </div>
          <div class="feature glass">
            <i class="fas fa-shield-alt"></i>
            <h4>Secure Handling</h4>
            <p>Your files are handled with privacy in mind.</p>
          </div>
          <div class="feature glass">
            <i class="fas fa-ribbon"></i>
            <h4>Shop‑Grade Quality</h4>
            <p>Sharp text, accurate colors, durable finishes.</p>
          </div>
          <div class="feature glass">
            <i class="fas fa-tags"></i>
            <h4>Student Pricing</h4>
            <p>Campus‑friendly rates across all services.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="steps">
      <h3 class="section-title">How it works</h3>
      <div class="steps-grid">
        <div class="step glass"><h4>Choose a Service</h4><p>Select print, bind, laminate, photos, copy, or tarpaulin.</p></div>
        <div class="step glass"><h4>Upload Files</h4><p>Attach documents or images right from your device.</p></div>
        <div class="step glass"><h4>Set Options</h4><p>Pick paper, size, color, copies, margins, and more.</p></div>
        <div class="step glass"><h4>Send & Pay</h4><p>Submit to admin; settle payment from your account.</p></div>
      </div>
    </section>

    <section class="pricing">
      <h3 class="section-title">Quick pricing snapshot</h3>
      <table class="price-table glass">
        <thead>
          <tr><th>Service</th><th>Includes</th><th>Rate</th></tr>
        </thead>
        <tbody>
          <tr><td>Print</td><td>Standard document printing</td><td><span class="tag">from ₱60</span></td></tr>
          <tr><td>Book Bind</td><td>Thermal/soft bind</td><td><span class="tag">from ₱120</span></td></tr>
          <tr><td>Lamination</td><td>ID/photos/documents</td><td><span class="tag">from ₱40</span></td></tr>
          <tr><td>Pictures</td><td>Glossy photo prints</td><td><span class="tag">from ₱25</span></td></tr>
          <tr><td>Photocopy</td><td>Black & white copies</td><td><span class="tag">from ₱10</span></td></tr>
          <tr><td>Tarpaulin</td><td>Large‑format print</td><td><span class="tag">from ₱200</span></td></tr>
        </tbody>
      </table>
    </section>

    <section id="about" class="about-band">
      <div class="about reveal" data-delay="0">
        <h3>ABOUT US:</h3>
        <p>
        At Isk⭐Print, we help students, faculty, and local businesses turn files into professional, ready-to-use materials—fast. 
        Our shop combines campus-friendly pricing with commercial-grade output so you get sharp text, accurate colors, and durable finishes every time.
        </p>
        <p style="margin-top:10px">
        We offer streamlined online ordering, secure file handling, and expert guidance on paper, layout, and finishing.
        Whether you need class handouts, reports, IDs, posters, tarpaulins, or photo displays, we’ll ensure your print looks polished and on-brand.
        </p>
      </div>
    </section>

    <section class="testimonials">
      <h3 class="section-title">What students say</h3>
      <div class="t-grid">
        <div class="t glass"><p>Super bilis! Nakapag‑print ako ng report same day. Quality is great.</p><small>— Alyssa, BSIT</small></div>
        <div class="t glass"><p>Tarpaulin for our org event came out crisp and on‑brand. Salamat!</p><small>— Ken, BSEntrep</small></div>
        <div class="t glass"><p>Affordable rates and friendly staff. The lamination saved my ID.</p><small>— Mei, BSA</small></div>
      </div>
    </section>

    <section id="contact" class="contact">
      <h3>CONTACT</h3>
      <div class="contact-grid">
        <div class="contact-card reveal" data-delay="0">
          <i class="fab fa-facebook-square contact-icon" aria-hidden="true"></i>
          <a href="https://facebook.com/IskoPrintOfficial" target="_blank" aria-label="Facebook">
            facebook.com/IskoPrintOfficial
          </a>
        </div>
        <div class="contact-card reveal" data-delay="100">
          <i class="fab fa-instagram contact-icon" aria-hidden="true"></i>
          <a href="https://instagram.com/iskoprint_official" target="_blank" aria-label="Instagram">
            @iskoprint_official
          </a>
        </div>
        <div class="contact-card reveal" data-delay="200">
          <i class="fab fa-telegram-plane contact-icon" aria-hidden="true"></i>
          <a href="https://t.me/IskoPrintOfficial" target="_blank" aria-label="Telegram">
            t.me/IskoPrintOfficial
          </a>
        </div>
        <div class="contact-card reveal" data-delay="300">
          <i class="fas fa-phone contact-icon" aria-hidden="true"></i>
          <div>+63 900 123 4567</div>
          <small style="color:#666">Mon–Sat, 9:00 AM – 6:00 PM</small>
        </div>
        <div class="contact-card reveal" data-delay="400">
          <i class="fas fa-envelope contact-icon" aria-hidden="true"></i>
          <a href="mailto:support@iskoprint.com" aria-label="Email">
            support@iskoprint.com
          </a>
        </div>
        <div class="contact-card reveal" data-delay="500">
          <i class="fas fa-map-marker-alt contact-icon" aria-hidden="true"></i>
          <div>PUP Sto. Tomas Campus</div>
          <small style="color:#666">Ground floor, Student Services Center</small>
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
        <h3>Mail</h3>
        <div class="sidebar-item" onclick="setActiveItem(this); navigate('inbox.php')" onmouseenter="setActiveItem(this)">
          <i class="fas fa-inbox"></i>
          <span>Inbox</span>
        </div>
      </div>
      <div class="sidebar-section" data-auth="true">
        <h3>Payment</h3>
        <div class="sidebar-item" onclick="setActiveItem(this); navigate('payment_methods.php')" onmouseenter="setActiveItem(this)">
          <i class="fas fa-credit-card"></i>
          <span>Payment Methods</span>
        </div>
        <div class="sidebar-item" onclick="setActiveItem(this); navigate('billing_history.php')" onmouseenter="setActiveItem(this)">
          <i class="fas fa-receipt"></i>
          <span>Billing History</span>
        </div>
        <div class="sidebar-item" onclick="setActiveItem(this); navigate('invoices.php')" onmouseenter="setActiveItem(this)">
          <i class="fas fa-file-invoice"></i>
          <span>Invoices</span>
        </div>
      </div>
      
      <!-- Removed Printer Settings section -->
      <div class="sidebar-section" data-auth="true">
        <h3>Account</h3>
        <div class="sidebar-item logout-item" onclick="logout()">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </div>
      </div>
    </div>
  </div>

  <footer class="footer">
    <div class="footer-inner">
      <div>
        <h4>Isk⭐Print</h4>
        <p style="margin:0; color:#cfcfcf">Reliable campus printing for documents, photos, IDs, and large‑format jobs.</p>
      </div>
      <div>
        <h4>Services</h4>
        <div><a href="#services">Print & Copy</a></div>
        <div><a href="#services">Binding & Lamination</a></div>
        <div><a href="#services">Photos & Tarpaulin</a></div>
      </div>
      <div>
        <h4>Contact</h4>
        <div><a href="#contact">Facebook</a></div>
        <div><a href="#contact">Instagram</a></div>
        <div><a href="#contact">Telegram</a></div>
      </div>
    </div>
    <div class="copyright">© <?php echo date('Y'); ?> Isk⭐Print. All rights reserved.</div>
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

    function navigate(url) {
      if (!isSignedIn()) {
        openAuthModal();
        return;
      }
      closeSidebar();
      window.location.href = url;
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

      // Smooth anchor scrolling and scrollspy active state
      const tabs = document.querySelectorAll('.tabs .tab');
      tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
          const href = this.getAttribute('href');
          if (href && href.startsWith('#')) {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        });
      });

      const sections = [
        document.querySelector('#home'),
        document.querySelector('#services'),
        document.querySelector('#about'),
        document.querySelector('#contact')
      ].filter(Boolean);
      function setActiveTabOnScroll() {
        let currentId = 'home';
        const scrollPos = window.scrollY + 120; // offset under top area
        sections.forEach(sec => {
          if (sec.offsetTop <= scrollPos) currentId = sec.id;
        });
        tabs.forEach(t => {
          t.classList.toggle('active', t.getAttribute('href') === '#' + currentId);
        });
      }
      window.addEventListener('scroll', setActiveTabOnScroll, { passive: true });
      setActiveTabOnScroll();

      // Reveal on scroll using IntersectionObserver
      const revealEls = document.querySelectorAll('.reveal');
      const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const el = entry.target;
            const delay = parseInt(el.getAttribute('data-delay') || '0', 10);
            if (delay) {
              el.style.transitionDelay = delay + 'ms';
            }
            el.classList.add('visible');
            io.unobserve(el);
          }
        });
      }, { threshold: 0.15, rootMargin: '0px 0px -10% 0px' });
      revealEls.forEach(el => io.observe(el));

      // Tag key elements for reveal if not already marked
      document.querySelectorAll('.service, .contact-card').forEach((el, idx) => {
        if (!el.classList.contains('reveal')) {
          el.classList.add('reveal');
          el.setAttribute('data-delay', (idx % 6) * 70);
          io.observe(el);
        }
      });

      // Option B: do not force any tab active state programmatically
    });
  </script>
  <script>
    window.__iskobotContext = [
      'Page: Homepage',
      'Services overview: Print, Book Bind, Laminate, Pictures, Photocopy, Tarpaulin.',
      'Pricing table: Print from PHP 60, Book Bind from PHP 120, Lamination from PHP 40, Pictures from PHP 25, Photocopy from PHP 10, Tarpaulin from PHP 200.',
      'How it works: Choose service, Upload files, Set options, Send & Pay.',
      'Contact options: Facebook IskoPrintOfficial, Instagram @iskoprint_official, Telegram t.me/IskoPrintOfficial, phone +63 900 123 4567, email support@iskoprint.com, location PUP Sto. Tomas Campus.',
      'Unique value: fast turnaround, secure handling, shop-grade quality, student pricing.'
    ].join('\\n');
  </script>
  <script src="assets/chatbot-widget.js"></script>
</body>
</html>