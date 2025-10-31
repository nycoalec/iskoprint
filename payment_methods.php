<?php
require_once 'auth.php';
$auth = new Auth();
if (!$auth->isLoggedIn()) {
  header('Location: index.php');
  exit;
}
$currentUser = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Payment Methods - iskOPrint</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <style>
    :root { --maroon:#750d0d; --maroon-dark:#5d0a0a; --ink:#1a1a1a; --line:rgba(0,0,0,0.08);} 
    body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial; color: var(--ink); background:#fafafa; }
    .topbar { background:#fff; border-bottom:1px solid var(--line); }
    .nav { display:flex; align-items:center; justify-content:space-between; padding:12px 18px; }
    .brand { color:#b10f0f; font-weight:800; letter-spacing:.2px; }
    .container { max-width: 1100px; margin: 0 auto; padding: 28px 18px 48px; }
    .title { display:flex; align-items:center; gap:10px; color: var(--maroon); letter-spacing:.3px; }
    .card { background:#fff; border:1px solid #eaeaea; border-radius:14px; padding:18px; box-shadow:0 8px 24px rgba(0,0,0,.06); }
    .btn { appearance:none; border:1px solid var(--maroon); color:#fff; background:linear-gradient(180deg, #8a1111, var(--maroon-dark)); border-radius:10px; padding:10px 14px; cursor:pointer; font-weight:700; box-shadow: 0 3px 10px rgba(117,13,13,.2); }
    .btn.secondary { background:#f4f4f6; color:#333; border-color:#e5e7eb; box-shadow:none; font-weight:600; }
    .btn:hover { filter: brightness(.97); transform: translateY(-1px); }
    .back { display:inline-flex; align-items:center; gap:8px; text-decoration:none; color: var(--maroon); font-weight:700; }
    .method { border:1px solid #eee; border-radius:10px; padding:14px; display:flex; align-items:center; justify-content:space-between; margin-top:10px; background:#fff; }
    .method i { color: var(--maroon); }
    .grid { display:grid; grid-template-columns: 1fr; gap:12px; }
    @media (min-width: 800px){ .grid { grid-template-columns: 1fr 1fr; } }
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
    <h1 class="title"><i class="fas fa-credit-card"></i> Payment Methods</h1>
    <div class="card" style="margin-top:12px">
      <p style="margin-top:0">Manage your saved cards and wallets for quick checkout.</p>
      <div class="grid">
        <div class="method">
          <div style="display:flex; align-items:center; gap:12px"><i class="far fa-credit-card"></i><div>Visa ending •••• 4242</div></div>
          <button class="btn secondary">Remove</button>
        </div>
        <div class="method">
          <div style="display:flex; align-items:center; gap:12px"><i class="fab fa-cc-mastercard"></i><div>Mastercard ending •••• 1881</div></div>
          <button class="btn secondary">Remove</button>
        </div>
      </div>
      <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap">
        <button class="btn"><i class="fas fa-plus" style="margin-right:8px"></i>Add new card</button>
        <button class="btn secondary">Add e-wallet</button>
      </div>
    </div>

    <div class="card" style="margin-top:12px">
      <div style="display:flex; align-items:center; justify-content:space-between">
        <div style="font-weight:700">Unpaid total: ₱<span id="unpaidTotal">0.00</span></div>
        <div style="display:flex; gap:8px">
          <button class="btn" onclick="payAll()">Pay All Unpaid</button>
          <a class="btn secondary" href="billing_history.php">View Orders</a>
        </div>
      </div>
    </div>
  </main>
  <script>
    function refresh(){
      const orders = JSON.parse(localStorage.getItem('orders')||'[]');
      const unpaid = orders.filter(o=>o.status==='Unpaid').reduce((s,o)=> s + (Number(o.amount)||0), 0);
      document.getElementById('unpaidTotal').textContent = (unpaid||0).toFixed(2);
    }
    function payAll(){
      const orders = JSON.parse(localStorage.getItem('orders')||'[]');
      const unpaid = orders.filter(o=>o.status==='Unpaid');
      if (unpaid.length===0) return;
      const amount = unpaid.reduce((s,o)=> s + (Number(o.amount)||0), 0);
      unpaid.forEach(o=> o.status='Paid');
      localStorage.setItem('orders', JSON.stringify(orders));
      const invoices = JSON.parse(localStorage.getItem('invoices')||'[]');
      const invoice = {
        id: 'INV-' + new Date().getFullYear() + '-' + String(invoices.length+1).padStart(3,'0'),
        issuedAt: new Date().toISOString(),
        orders: unpaid.map(o=>o.id),
        amount
      };
      invoices.push(invoice);
      localStorage.setItem('invoices', JSON.stringify(invoices));
      window.dispatchEvent(new StorageEvent('storage', { key: 'invoices', newValue: JSON.stringify(invoices) }));
      refresh();
      alert('Payment successful. Invoice ' + invoice.id + ' generated.');
    }
    document.addEventListener('DOMContentLoaded', refresh);
    window.addEventListener('storage', (e)=>{ if (e.key==='orders') refresh(); });
  </script>
</body>
</html>


