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
  <title>Invoices - iskOPrint</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <style>
    :root { --maroon:#750d0d; --maroon-dark:#5d0a0a; --ink:#1a1a1a; --line:rgba(0,0,0,0.08);} 
    body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial; color: var(--ink); background:#fafafa; }
    .topbar { background:#fff; border-bottom:1px solid var(--line); }
    .nav { display:flex; align-items:center; justify-content:space-between; padding:12px 18px; }
    .brand { color:#b10f0f; font-weight:800; letter-spacing:.2px; }
    .container { max-width: 1100px; margin: 0 auto; padding: 28px 18px 48px; }
    .title { display:flex; align-items:center; gap:10px; color: var(--maroon); letter-spacing:.3px; }
    .card { background:#fff; border:1px solid #eaeaea; border-radius:14px; padding:18px; box-shadow:0 8px 24px rgba(0,0,0,.06); margin-top:12px; }
    .invoice { display:flex; align-items:center; justify-content:space-between; padding:14px 6px; border-bottom:1px dashed #eee; }
    .invoice:last-child { border-bottom:none; }
    .btn { appearance:none; border:1px solid var(--maroon); color:#fff; background:linear-gradient(180deg, #8a1111, var(--maroon-dark)); border-radius:10px; padding:8px 12px; cursor:pointer; font-weight:700; box-shadow: 0 3px 10px rgba(117,13,13,.2); }
    .back { display:inline-flex; align-items:center; gap:8px; text-decoration:none; color: var(--maroon); font-weight:700; }
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
    <h1 class="title"><i class="fas fa-file-invoice"></i> Invoices</h1>
    <div class="card" id="invoices"></div>
  </main>
  <script>
    function render(){
      const container = document.getElementById('invoices');
      const invoices = JSON.parse(localStorage.getItem('invoices')||'[]').sort((a,b)=> new Date(b.issuedAt)-new Date(a.issuedAt));
      if (invoices.length===0){
        container.innerHTML = '<div style="text-align:center; color:#666; padding:20px">No invoices yet.</div>'; return;
      }
      container.innerHTML = '';
      invoices.forEach(inv=>{
        const row = document.createElement('div');
        row.className = 'invoice';
        row.innerHTML = `
          <div>
            <div style="font-weight:700">${inv.id}</div>
            <div style="color:#666; font-size:13px">Issued: ${new Date(inv.issuedAt).toLocaleString()} • ₱${(Number(inv.amount)||0).toFixed(2)}</div>
          </div>
          <div style="display:flex; gap:8px">
            <button class=\"btn\" onclick=\"window.print()\"><i class=\"fas fa-print\" style=\"margin-right:6px\"></i>Print</button>
            <a class=\"btn\" href=\"data:text/plain,Invoice%20${encodeURIComponent(inv.id)}%20-%20PHP%20${(Number(inv.amount)||0).toFixed(2)}\" download>Download</a>
          </div>
        `;
        container.appendChild(row);
      });
    }
    document.addEventListener('DOMContentLoaded', render);
    window.addEventListener('storage', (e)=>{ if (e.key==='invoices') render(); });
  </script>
</body>
</html>


