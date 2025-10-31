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
  <title>Billing History - iskOPrint</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <style>
    :root { --maroon:#750d0d; --maroon-dark:#5d0a0a; --ink:#1a1a1a; --line:rgba(0,0,0,0.08);} 
    body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial; color: var(--ink); background:#fafafa; }
    .topbar { background:#ffffff; border-bottom:1px solid var(--line); }
    .nav { display:flex; align-items:center; justify-content:space-between; padding:12px 18px; }
    .brand { color:#b10f0f; font-weight:800; letter-spacing:.2px; }
    .container { max-width: 1100px; margin: 0 auto; padding: 28px 18px 48px; }
    .title { display:flex; align-items:center; gap:10px; color: var(--maroon); letter-spacing:.3px; }
    .card { background:#fff; border:1px solid #eaeaea; border-radius:14px; padding:18px; box-shadow:0 8px 24px rgba(0,0,0,.06); margin-top:14px; }
    .row { display:grid; grid-template-columns: 180px 1fr 140px 140px; gap:14px; padding:14px 10px; border-bottom:1px dashed #eee; align-items:center; transition: background-color .15s ease; }
    .row:hover { background:#faf6f6; }
    .row.header { font-weight:700; color:#555; background:#fff; border-bottom:1px solid #eee; }
    .row:last-child { border-bottom:none; }
    .status { padding:5px 10px; border-radius:999px; font-weight:700; font-size:11px; display:inline-block; text-transform: uppercase; letter-spacing:.4px; }
    .status.Unpaid { background:#fff3cd; color:#8a6d3b; border:1px solid #ffe8a1; }
    .status.Paid { background:#d7ffe7; color:#0f7a3c; border:1px solid #b8f5cf; }
    .btn { appearance:none; border:1px solid var(--maroon); color:#fff; background:linear-gradient(180deg, #8a1111, var(--maroon-dark)); border-radius:10px; padding:8px 12px; cursor:pointer; font-weight:700; box-shadow: 0 3px 10px rgba(117,13,13,.2); }
    .btn.secondary { background:#f4f4f6; color:#333; border-color:#e5e7eb; box-shadow:none; }
    .btn:hover { filter: brightness(.97); transform: translateY(-1px); }
    .back { display:inline-flex; align-items:center; gap:8px; text-decoration:none; color: var(--maroon); font-weight:700; }
    .note { color:#666; font-size:13px; }
    .prices { margin-top:8px; font-size:13px; color:#444; display:flex; gap:6px; flex-wrap:wrap; }
    .prices code { background:#fff; padding:4px 8px; border-radius:999px; border:1px solid #eee; }
    @media (max-width: 820px){ .row{ grid-template-columns: 120px 1fr 100px 110px; } }
    @media (max-width: 600px){ .row{ grid-template-columns: 1fr; gap:6px; } .row.header{ display:none; } }
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
    <h1 class="title"><i class="fas fa-receipt"></i> Billing History</h1>
    <div class="card">
      <div class="note">Orders are created automatically when you submit a service request. You can adjust default pricing in code later if needed.</div>
      <div class="prices">Default prices: <code>PRINT ₱60</code> <code>BOOK BIND ₱120</code> <code>LAMINATE ₱40</code> <code>PICTURES ₱25</code> <code>PHOTOCOPY ₱10</code> <code>TARPAULIN ₱200</code></div>
    </div>
    <div class="card" style="margin-top:12px">
      <div class="row header">
        <div>Order ID</div>
        <div>Description</div>
        <div>Amount</div>
        <div>Action</div>
      </div>
      <div id="orders"></div>
      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:12px">
        <div style="font-weight:700">Total Unpaid: ₱<span id="unpaidTotal">0.00</span></div>
        <button class="btn" onclick="payAllUnpaid()">Pay All Unpaid</button>
      </div>
    </div>
  </main>
  <script>
    const SERVICE_NAMES = { printer:'Print', bookbind:'Book Bind', laminate:'Laminate', pictures:'Pictures', photocopy:'Photocopy', tarpaulin:'Tarpaulin' };

    function formatAmount(n){ return (Number(n)||0).toFixed(2); }

    function loadOrders(){
      const container = document.getElementById('orders');
      container.innerHTML = '';
      const orders = JSON.parse(localStorage.getItem('orders')||'[]').sort((a,b)=> new Date(b.createdAt)-new Date(a.createdAt));
      let unpaid = 0;
      orders.forEach(o=>{
        if (o.status === 'Unpaid') unpaid += Number(o.amount)||0;
        const row = document.createElement('div');
        row.className = 'row';
        row.innerHTML = `
          <div>${o.id}</div>
          <div>[${SERVICE_NAMES[o.serviceType]||o.serviceType}] ${o.subject||''}<div class="status ${o.status}" style="margin-top:6px">${o.status}</div></div>
          <div>₱${formatAmount(o.amount)}</div>
          <div>${o.status==='Unpaid' ? `<button class=\"btn\" onclick=\"markPaid('${o.id}')\">Mark as Paid</button>` : '<span style="color:#666">—</span>'}</div>
        `;
        container.appendChild(row);
      });
      document.getElementById('unpaidTotal').textContent = formatAmount(unpaid);
    }

    function markPaid(orderId){
      const orders = JSON.parse(localStorage.getItem('orders')||'[]');
      const order = orders.find(o=>o.id===orderId);
      if (!order) return;
      order.status = 'Paid';
      localStorage.setItem('orders', JSON.stringify(orders));
      // create invoice
      const invoices = JSON.parse(localStorage.getItem('invoices')||'[]');
      const invoice = {
        id: 'INV-' + new Date().getFullYear() + '-' + String(invoices.length+1).padStart(3,'0'),
        issuedAt: new Date().toISOString(),
        orders: [order.id],
        amount: Number(order.amount)||0
      };
      invoices.push(invoice);
      localStorage.setItem('invoices', JSON.stringify(invoices));
      window.dispatchEvent(new StorageEvent('storage', { key: 'invoices', newValue: JSON.stringify(invoices) }));
      loadOrders();
    }

    function payAllUnpaid(){
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
      loadOrders();
    }

    document.addEventListener('DOMContentLoaded', loadOrders);
    window.addEventListener('storage', (e)=>{ if (e.key==='orders') loadOrders(); });
  </script>
</body>
</html>
