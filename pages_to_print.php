<?php
require_once 'auth.php';
$auth = new Auth();
if (!$auth->isLoggedIn()) { header('Location: index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pages to Print - iskOPrint</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <style>
    :root { --maroon:#750d0d; --maroon-dark:#5d0a0a; --ink:#1a1a1a; --line:rgba(0,0,0,0.08);} 
    body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial; color: var(--ink); background:#fff; }
    .topbar { background:#fff; border-bottom:1px solid var(--line); }
    .nav { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; }
    .brand { color:#b10f0f; font-weight:800; letter-spacing:.2px; }
    .container { max-width: 900px; margin: 0 auto; padding: 20px 16px 40px; }
    .title { display:flex; align-items:center; gap:10px; color: var(--maroon); }
    .card { background:#fff; border:1px solid #eee; border-radius:10px; padding:18px; box-shadow:0 2px 10px rgba(0,0,0,.06); margin-top:12px; }
    label { font-weight:600; display:block; margin:10px 0 6px; }
    input[type="text"] { width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:8px; }
    .btn { appearance:none; border:1px solid var(--maroon); color:#fff; background:var(--maroon); border-radius:8px; padding:10px 14px; cursor:pointer; font-weight:600; }
    .back { display:inline-flex; align-items:center; gap:8px; text-decoration:none; color: var(--maroon); font-weight:600; }
  </style>
</head>
<body>
  <header class="topbar">
    <div class="nav">
      <a class="back" href="index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
      <div class="brand">iskOPrint</div>
    </div>
  </header>
  <main class="container">
    <h1 class="title"><i class="fas fa-file-alt"></i> Pages to Print</h1>
    <div class="card">
      <p>Specify which pages to include (e.g., 1-3,5,8-10).</p>
      <label for="pages">Pages</label>
      <input id="pages" type="text" placeholder="1-3,5,8-10" />
      <div style="margin-top:12px">
        <button class="btn">Save</button>
      </div>
    </div>
  </main>
</body>
</html>


