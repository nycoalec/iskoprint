<?php
require_once 'auth.php';

$auth = new Auth();
$error_message = '';
$success_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        $result = $auth->login($email, $password);
        if ($result['success']) {
            $success_message = $result['message'];
            // Redirect to main page after successful login
            header('Location: index.php');
            exit();
        } else {
            $error_message = $result['message'];
        }
    } else {
        $error_message = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - iskOPrint</title>
  <style>
    :root { --maroon:#750d0d; --maroon-dark:#5d0a0a; --ink:#1a1a1a; --muted:#6b7280; --line:#e5e7eb; }
    *{ box-sizing: border-box; }
    html,body{ height:100%; }
    body{
      margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial;
      background: linear-gradient(180deg, rgba(255,249,249,0.88) 0%, rgba(251,238,238,0.88) 100%), url('assets/pup_bg.jpg');
      background-size: cover; background-position:center; background-attachment: fixed; display:grid; place-items:center; color:var(--ink);
    }

    .card{ width:96%; max-width:980px; background:#fff; border-radius:16px; overflow:hidden; display:grid; grid-template-columns:380px 1fr; min-height:520px; box-shadow:0 30px 80px rgba(0,0,0,.25); border:1px solid rgba(255,255,255,.6); }
    @media (max-width:900px){ .card{ grid-template-columns:1fr; } .left{ min-height:180px; } }

    .left{ position:relative; color:#fff; padding:36px 28px; display:flex; flex-direction:column; gap:16px; justify-content:center; align-items:center; background: radial-gradient(circle at 20% 20%, rgba(255,255,255,.18), transparent 60%), radial-gradient(circle at 80% 60%, rgba(255,255,255,.08), transparent 70%), linear-gradient(135deg,#811414 0%, #5a0b0b 100%); }
    .left::after{ content:""; position:absolute; top:0; right:-24px; width:48px; height:100%; background:#fff; border-top-left-radius:24px; border-bottom-left-radius:24px; }
    .brand{ display:flex; flex-direction: column; align-items:center; gap:12px; align-self:center; text-align:center; }
    .brand img{ height:150px; width:auto; border-radius:8px; }
    .brand .brand-text{ font-weight:900; font-size:34px; letter-spacing:.5px; display:flex; align-items:center; gap:10px; }
    .brand .star-img{ height:26px; width:26px; border-radius:4px; display:inline-block; }

    .right{ padding:40px 36px; }
    .title{ font-size:30px; font-weight:900; margin:0 0 10px; color:var(--maroon-dark); }
    .subtitle{ margin:0 0 22px; color:var(--muted); font-size:13px; }
    form{ display:grid; gap:14px; max-width:520px; }
    .field{ display:flex; flex-direction:column; gap:6px; }
    .field label{ font-size:12px; color:var(--muted); }
    .field input{ width:100%; border:1px solid var(--line); border-radius:10px; padding:12px; font-size:14px; outline:none; transition:border-color .2s ease, box-shadow .2s ease; }
    .field input:focus{ border-color:var(--maroon); box-shadow:0 0 0 3px rgba(117,13,13,.15); }
    .actions{ margin-top:6px; display:flex; gap:10px; align-items:center; }
    .submit{ background:var(--maroon); color:#fff; border:1px solid var(--maroon); border-radius:20px; padding:10px 24px; cursor:pointer; }
    .link{ color:var(--maroon); text-decoration:none; font-weight:600; font-size:12px; }
    .alert{ padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px; }
    .alert-error{ background:#fee; border:1px solid #fcc; color:#c33; }
    .alert-success{ background:#efe; border:1px solid #cfc; color:#363; }
  </style>
</head>
<body>
  <div class="card">
    <aside class="left">
      <div class="brand">
        <img src="assets/iskoprinter.png" alt="IskPrint logo" />
        <div class="brand-text">Isk<img class="star-img" src="assets/pup_star.png" alt="" />Print</div>
      </div>
    </aside>
    <main class="right">
      <h2 class="title">Welcome Back</h2>
      <p class="subtitle">Use your email and password</p>
      
      <?php if ($error_message): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
      <?php endif; ?>
      
      <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
      <?php endif; ?>
      
      <form method="POST" action="">
        <div class="field">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" required />
        </div>
        <div class="actions">
          <button class="submit" type="submit">Login</button>
          <a class="link" href="register_acc.php">Create account</a>
        </div>
      </form>
    </main>
  </div>
</body>
</html>


