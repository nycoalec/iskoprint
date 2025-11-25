<?php
require_once 'auth.php';
$auth = new Auth();
if (!$auth->isLoggedIn()) {
  header('Location: index.php');
  exit;
}
$currentUser = $auth->getCurrentUser();
$displayName = htmlspecialchars($currentUser['full_name'] ?? $currentUser['username'] ?? 'iskOPrint member', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Payment Methods - isk⭐Print</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <style>
    :root {
      --maroon:#750d0d;
      --maroon-dark:#5d0a0a;
      --ink:#111;
      --muted:#6b7280;
      --line:rgba(0,0,0,0.08);
      --panel:#fff;
      --bg:#f8f5f4;
      --paypal:#142C8E;
    }
    * { box-sizing:border-box; }
    body {
      margin:0;
      font-family: system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;
      color:var(--ink);
      background:var(--bg);
      min-height:100vh;
    }
    .topbar {
      background:#fff;
      border-bottom:1px solid var(--line);
      position:sticky;
      top:0;
      z-index:10;
    }
    .nav {
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding:12px 24px;
      gap:16px;
    }
    .brand {
      color:#b10f0f;
      font-weight:800;
      letter-spacing:.2px;
      display:flex;
      align-items:center;
      gap:6px;
      font-size:1.15rem;
    }
    .brand img {
      width:26px;
      height:26px;
      object-fit:contain;
    }
    .brand .brand-text {
      display:inline-flex;
      align-items:center;
      gap:4px;
    }
    .user-chip {
      background:rgba(117,13,13,.08);
      color:var(--maroon);
      font-weight:600;
      padding:6px 12px;
      border-radius:999px;
      display:flex;
      align-items:center;
      gap:6px;
      font-size:.9rem;
    }
    .back {
      display:inline-flex;
      align-items:center;
      gap:8px;
      text-decoration:none;
      color:var(--maroon);
      font-weight:700;
    }
    .shell {
      max-width:1200px;
      margin:0 auto;
      padding:32px 20px 56px;
      display:flex;
      flex-direction:column;
      gap:24px;
    }
    .page-header {
      background:linear-gradient(135deg,#fff,rgba(255,255,255,0.4));
      border:1px solid #f0e6e4;
      border-radius:18px;
      padding:24px;
      display:flex;
      flex-wrap:wrap;
      gap:24px;
      box-shadow:0 20px 60px rgba(117,13,13,0.08);
    }
    .page-header h1 {
      margin:6px 0 8px;
      font-size:1.9rem;
      color:var(--maroon);
    }
    .eyebrow {
      text-transform:uppercase;
      font-size:.75rem;
      letter-spacing:.2em;
      color:var(--muted);
      margin:0;
    }
    .summary-card {
      margin-left:auto;
      min-width:260px;
      border-left:1px dashed var(--line);
      padding-left:24px;
    }
    .summary-card .label {
      font-size:.9rem;
      color:var(--muted);
      text-transform:uppercase;
      letter-spacing:.12em;
    }
    .summary-card .amount {
      font-size:2.4rem;
      font-weight:700;
      margin:8px 0 16px;
      color:var(--maroon-dark);
    }
    .btn {
      appearance:none;
      border:1px solid transparent;
      border-radius:12px;
      padding:11px 16px;
      font-weight:700;
      cursor:pointer;
      transition:transform .15s ease, box-shadow .15s ease;
    }
    .btn.primary {
      background:linear-gradient(180deg,#8a1111,var(--maroon-dark));
      color:#fff;
      box-shadow:0 12px 30px rgba(117,13,13,.25);
    }
    .btn.outline {
      background:transparent;
      color:var(--maroon);
      border-color:rgba(117,13,13,.3);
    }
    .btn.ghost {
      background:rgba(117,13,13,.08);
      color:var(--maroon);
      border:none;
    }
    .btn.secondary {
      background:#fff;
      color:#333;
      border:1px solid #e5e7eb;
      font-weight:600;
      box-shadow:none;
    }
    .btn:hover { transform:translateY(-1px); }
    .payment-grid {
      display:grid;
      grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
      gap:20px;
    }
    .panel {
      background:var(--panel);
      border-radius:18px;
      padding:22px;
      border:1px solid rgba(0,0,0,.04);
      box-shadow:0 25px 60px rgba(0,0,0,.05);
      display:flex;
      flex-direction:column;
      gap:18px;
    }
    .panel-head {
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:14px;
    }
    .panel-title {
      margin:0;
      font-size:1.2rem;
    }
    .paypal-panel {
      background:radial-gradient(circle at top,#f0f4ff,#fff);
      border:1px solid rgba(20,44,142,.18);
    }
    .paypal-head {
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:14px;
    }
    .paypal-head i {
      font-size:2.5rem;
      color:var(--paypal);
    }
    .paypal-amount {
      font-size:2rem;
      font-weight:700;
      color:var(--paypal);
      margin:8px 0;
    }
    .feedback {
      border-radius:12px;
      padding:12px 14px;
      font-weight:600;
      display:none;
    }
    .feedback.show {
      display:flex;
      align-items:center;
      gap:10px;
    }
    .feedback.success {
      background:rgba(34,197,94,.12);
      color:#166534;
    }
    .feedback.info {
      background:rgba(59,130,246,.12);
      color:#1e40af;
    }
    .feedback.error {
      background:rgba(248,113,113,.15);
      color:#7f1d1d;
    }
    .help-text {
      font-size:.82rem;
      color:var(--muted);
    }
    .paypal-overlay {
      position:fixed;
      inset:0;
      background:rgba(0,0,0,.45);
      display:flex;
      align-items:center;
      justify-content:center;
      z-index:99;
      opacity:0;
      pointer-events:none;
      transition:opacity .2s ease;
    }
    .paypal-overlay.show {
      opacity:1;
      pointer-events:auto;
    }
    .paypal-overlay .loader {
      background:#fff;
      border-radius:16px;
      padding:24px 30px;
      text-align:center;
      box-shadow:0 20px 40px rgba(0,0,0,.18);
      min-width:240px;
    }
    .paypal-overlay .loader svg {
      width:46px;
      height:46px;
      animation:spin 1.1s linear infinite;
      stroke:var(--paypal);
      margin-bottom:12px;
    }
    .paypal-overlay p {
      margin:0;
      color:var(--ink);
      font-weight:600;
    }
    @keyframes spin {
      from { transform:rotate(0deg); }
      to { transform:rotate(360deg); }
    }
    @media (max-width:720px) {
      .summary-card {
        width:100%;
        border-left:none;
        border-top:1px dashed var(--line);
        padding-left:0;
        padding-top:18px;
      }
      .nav {
        flex-wrap:wrap;
        justify-content:center;
      }
      .user-chip {
        order:3;
      }
    }
  </style>
</head>
<body>
  <header class="topbar">
    <div class="nav">
      <a class="back" href="index.php"><i class="fas fa-arrow-left"></i> Back to home</a>
      <div class="brand">
        <span class="brand-text" style="gap:2px">
          <span>Isk</span>
          <img src="assets/pup_star.png" alt="iskOPrint star" style="margin-left:-2px">
        </span>
        <span class="brand-text">Print</span>
      </div>
      <div class="user-chip">
        <i class="far fa-user"></i>
        <span><?= $displayName ?></span>
      </div>
    </div>
  </header>
  <main class="shell">
    <section class="page-header">
      <div>
        <p class="eyebrow">Payments center</p>
        <h1>Manage wallets & settle balances</h1>
        <p style="max-width:560px; color:var(--muted); margin:0">
          Keep your preferred payment methods in one secure place. Connect PayPal for seamless checkouts
          and instantly clear unpaid print jobs without leaving iskOPrint.
        </p>
      </div>
      <div class="summary-card">
        <div class="label">Outstanding</div>
        <div class="amount">₱<span id="unpaidTotal">0.00</span></div>
        <div style="display:flex; gap:10px; flex-wrap:wrap">
          <a class="btn outline" href="billing_history.php">
            <i class="fas fa-file-invoice" style="margin-right:6px"></i>Billing history
          </a>
        </div>
      </div>
    </section>

    <div id="paymentFeedback" class="feedback" role="status"></div>
    <div id="paypalOverlay" class="paypal-overlay" aria-hidden="true">
      <div class="loader">
        <svg viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="10" stroke-width="3" stroke-opacity=".2"></circle>
          <path d="M22 12a10 10 0 0 1-10 10" stroke-width="3" stroke-linecap="round"></path>
        </svg>
        <p>Connecting to PayPal…</p>
      </div>
    </div>

    <section class="payment-grid">
      <article class="panel paypal-panel">
        <div class="paypal-head">
          <div>
            <p class="eyebrow">Express checkout</p>
            <h2 class="panel-title">Pay with PayPal</h2>
          </div>
          <i class="fab fa-paypal"></i>
        </div>
        <p style="margin:0; color:var(--muted)">
          Use your PayPal balance or linked cards to instantly settle the remaining balance.
        </p>
        <div>
          <div class="label">Ready to charge</div>
          <div class="paypal-amount">₱<span id="paypalTotal">0.00</span></div>
        </div>
        <div id="paypal-button" style="min-height:60px"></div>
        <p class="help-text">
          Sandbox mode is enabled. Replace the client ID with your live credentials when deploying.
        </p>
      </article>

      <article class="panel">
        <div class="panel-head">
          <div>
            <p class="eyebrow">Manual actions</p>
            <h2 class="panel-title">Need something else?</h2>
          </div>
        </div>
        <p style="margin:0; color:var(--muted)">
          Download your invoices, dispute a charge, or reach out to our finance desk.
        </p>
        <div style="display:flex; flex-direction:column; gap:10px">
          <button class="btn outline" onclick="window.location.href='invoices.php'">
            <i class="fas fa-file-alt" style="margin-right:6px"></i>View invoices
          </button>
          <a class="btn ghost" href="mailto:finance@iskoprint.local">
            <i class="fas fa-headset" style="margin-right:6px"></i>Contact finance
          </a>
        </div>
      </article>
    </section>
  </main>
  <script src="https://www.paypal.com/sdk/js?client-id=sb&currency=PHP"></script>
  <script>
    const unpaidTotalEl = document.getElementById('unpaidTotal');
    const paypalTotalEl = document.getElementById('paypalTotal');
    const feedbackEl = document.getElementById('paymentFeedback');
    const paypalOverlayEl = document.getElementById('paypalOverlay');

    const storageEventKey = 'orders';
    let lastPayPalAmount = 0;

    const getOrders = () => JSON.parse(localStorage.getItem('orders') || '[]');

    const getUnpaidOrders = (orders = getOrders()) =>
      orders.filter(o => (o.status || '').toLowerCase() === 'unpaid');

    const getUnpaidTotal = () =>
      getUnpaidOrders().reduce((sum, order) => sum + (Number(order.amount) || 0), 0);

    const pushInvoice = (orders, amount, channel) => {
      const invoices = JSON.parse(localStorage.getItem('invoices') || '[]');
      const invoice = {
        id: 'INV-' + new Date().getFullYear() + '-' + String(invoices.length + 1).padStart(3, '0'),
        issuedAt: new Date().toISOString(),
        orders: orders.map(o => o.id),
        amount,
        paidVia: channel
      };
      invoices.push(invoice);
      localStorage.setItem('invoices', JSON.stringify(invoices));
      window.dispatchEvent(new StorageEvent('storage', { key: 'invoices', newValue: JSON.stringify(invoices) }));
      return invoice;
    };

    const setFeedback = (message, variant = 'success') => {
      if (!feedbackEl) return;
      feedbackEl.textContent = '';
      feedbackEl.className = `feedback show ${variant}`;
      feedbackEl.innerHTML = `<i class="fas fa-info-circle"></i> ${message}`;
      setTimeout(() => feedbackEl.classList.remove('show'), 5000);
    };

    const togglePayPalOverlay = (show) => {
      if (!paypalOverlayEl) return;
      if (show) {
        paypalOverlayEl.classList.add('show');
        paypalOverlayEl.setAttribute('aria-hidden', 'false');
      } else {
        paypalOverlayEl.classList.remove('show');
        paypalOverlayEl.setAttribute('aria-hidden', 'true');
      }
    };

    const markUnpaidAsPaid = (channelLabel, options = {}) => {
      const { removePaidRecords = false } = options;
      const orders = getOrders();
      const unpaid = getUnpaidOrders(orders);
      if (!unpaid.length) {
        return { amount: 0, invoice: null };
      }
      const amount = unpaid.reduce((sum, order) => sum + (Number(order.amount) || 0), 0);
      unpaid.forEach(order => {
        order.status = 'Paid';
        order.paidVia = channelLabel;
        order.paidAt = new Date().toISOString();
      });
      const idsToRemove = new Set(removePaidRecords ? unpaid.map(order => order.id) : []);
      const updatedOrders = removePaidRecords
        ? orders.filter(order => !idsToRemove.has(order.id))
        : orders;
      localStorage.setItem('orders', JSON.stringify(updatedOrders));
      window.dispatchEvent(new StorageEvent('storage', { key: storageEventKey, newValue: JSON.stringify(updatedOrders) }));
      const invoice = pushInvoice(unpaid, amount, channelLabel);
      refreshTotals();
      return { amount, invoice };
    };

    function refreshTotals () {
      const total = getUnpaidTotal();
      const formatted = (total || 0).toFixed(2);
      unpaidTotalEl.textContent = formatted;
      paypalTotalEl.textContent = formatted;
    }

    document.addEventListener('DOMContentLoaded', () => {
      refreshTotals();
      initPayPalButtons();
    });

    window.addEventListener('storage', (event) => {
      if (event.key === storageEventKey) {
        refreshTotals();
      }
    });

    function initPayPalButtons () {
      if (!(window.paypal && document.getElementById('paypal-button'))) return;
      paypal.Buttons({
        fundingSource: paypal.FUNDING.PAYPAL,
        funding: {
          disallowed: [paypal.FUNDING.CARD, paypal.FUNDING.CREDIT, paypal.FUNDING.PAYLATER]
        },
        style: {
          color: 'gold',
          shape: 'rect',
          label: 'pay',
          height: 46
        },
        onInit: (_, actions) => {
          if (!getUnpaidTotal()) {
            actions.disable();
          }
          window.addEventListener('storage', (event) => {
            if (event.key && event.key !== storageEventKey) return;
            if (!getUnpaidTotal()) {
              actions.disable();
            } else {
              actions.enable();
            }
          });
        },
        onClick: (data, actions) => {
          const total = getUnpaidTotal();
          if (!total) {
            setFeedback('No unpaid orders to charge with PayPal.', 'info');
            return actions.reject();
          }
          togglePayPalOverlay(true);
          return actions.resolve();
        },
        createOrder: (_, actions) => {
          const total = getUnpaidTotal();
          if (!total) {
            togglePayPalOverlay(false);
            return Promise.reject(new Error('No unpaid orders'));
          }
          lastPayPalAmount = Number(total.toFixed(2));
          return actions.order.create({
            purchase_units: [{
              amount: {
                currency_code: 'PHP',
                value: lastPayPalAmount.toFixed(2)
              },
              description: 'iskOPrint unpaid balance'
            }]
          });
        },
        onApprove: (data, actions) => actions.order.capture().then(details => {
          const { amount, invoice } = markUnpaidAsPaid('PayPal', { removePaidRecords: true });
          const paidAmount = amount || lastPayPalAmount;
          const reference = details.id || data.orderID;
          setFeedback(`Paid ₱${paidAmount.toFixed(2)} via PayPal (Txn ${reference}). Invoice ${invoice.id} saved.`, 'success');
          togglePayPalOverlay(false);
        }),
        onCancel: () => {
          togglePayPalOverlay(false);
          setFeedback('PayPal checkout was cancelled.', 'info');
        },
        onError: (err) => {
          console.error('PayPal error', err);
          setFeedback('PayPal payment failed. Please try again or contact support.', 'error');
          togglePayPalOverlay(false);
        }
      }).render('#paypal-button');
    }
  </script>
</body>
</html>
