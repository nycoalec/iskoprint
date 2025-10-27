<?php
require_once 'auth.php';

$auth = new Auth();
$error_message = '';
$success_message = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phoneNumber = trim($_POST['phone_number'] ?? '');
    
    if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($password) && !empty($phoneNumber)) {
        $result = $auth->register($firstName, $lastName, $email, $password, $phoneNumber);
        if ($result['success']) {
            $success_message = $result['message'];
            // Auto-login after successful registration
            $loginResult = $auth->login($email, $password);
            if ($loginResult['success']) {
                header('Location: index.php');
                exit();
            }
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
  <title>Register / Sign in - iskOPrint</title>
  <style>
    :root {
      --maroon: #750d0d;
      --maroon-dark: #5d0a0a;
      --ink: #1a1a1a;
      --muted: #6b7280;
      --line: #e5e7eb;
    }
    * { box-sizing: border-box; }
    html, body { height: 100%; }
    body {
      margin: 0; font-family: system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial;
      background: linear-gradient(180deg, rgba(255,249,249,0.88) 0%, rgba(251,238,238,0.88) 100%), url('assets/pup_bg.jpg');
      background-size: cover; background-position: center; background-attachment: fixed;
      color: var(--ink);
      display: grid; place-items: center;
    }

    .container {
      width: 96%; max-width: 980px; background: #fff; border-radius: 16px;
      box-shadow: 0 30px 80px rgba(0,0,0,0.25); overflow: hidden; display: grid;
      grid-template-columns: 380px 1fr; min-height: 540px; border: 1px solid rgba(255,255,255,0.6);
    }
    @media (max-width: 900px) {
      .container { grid-template-columns: 1fr; }
      .left { min-height: 180px; }
    }

    .left {
      background: radial-gradient( circle at 20% 20%, rgba(255,255,255,0.18), transparent 60%),
                  radial-gradient( circle at 80% 60%, rgba(255,255,255,0.08), transparent 70%),
                  linear-gradient(135deg, #811414 0%, #5a0b0b 100%);
      color: #fff; padding: 36px 28px; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 16px;
      position: relative;
    }
    .left::after{
      content:""; position:absolute; top:0; right:-24px; width:48px; height:100%; background:#fff; border-top-left-radius:24px; border-bottom-left-radius:24px;
    }
    .brand { display:flex; flex-direction: column; align-items:center; gap: 12px; align-self: center; text-align: center; }
    .brand img { height: 150px; width: auto; border-radius: 8px; display:block; }
    .brand .brand-text { font-weight: 900; font-size: 34px; letter-spacing: .5px; display:flex; align-items:center; gap:10px; }
    .brand .star-img { height: 26px; width: 26px; border-radius: 4px; display:inline-block; }
    .left h1 { margin: 0 0 6px 0; font-size: 28px; }
    .left p { margin: 0 0 18px 0; opacity: 0.9; }

    .cta-group { margin-top: 22px; display: flex; gap: 10px; flex-wrap: wrap; }
    .btn {
      appearance: none; border: 1px solid transparent; border-radius: 10px; padding: 10px 14px; cursor: pointer;
      font-weight: 600; transition: filter .15s ease, transform .02s ease; display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-primary { background: #ffffff; color: var(--maroon); border-color: rgba(255,255,255,0.35); }
    .btn-outline { background: transparent; color: #fff; border-color: rgba(255,255,255,0.6); }
    .btn:hover { filter: brightness(0.95); }

    .right { padding: 40px 36px; background: #fff; }
    .title { font-size: 30px; font-weight: 900; margin: 0 0 10px; color: var(--maroon-dark); letter-spacing:.3px; }
    .subtitle { margin: 0 0 22px; color: var(--muted); font-size: 13px; }

    form { display: grid; grid-template-columns: 1fr; gap: 14px; max-width: 520px; }

    .field { display: flex; flex-direction: column; gap: 6px; }
    .field label { font-size: 12px; color: var(--muted); }
    .field input, .field select, .field textarea {
      width: 100%; border: 1px solid var(--line); border-radius: 10px; padding: 12px 12px; font-size: 14px; outline: none;
      background: #fff; transition: border-color .2s ease, box-shadow .2s ease;
    }
    .field input:focus, .field select:focus, .field textarea:focus {
      border-color: var(--maroon); box-shadow: 0 0 0 3px rgba(117,13,13,0.15);
    }

    .form-actions { margin-top: 6px; display: flex; gap: 10px; align-items:center; }
    .submit { background: var(--maroon); color:#fff; border-color: var(--maroon); border-radius: 20px; padding: 10px 24px; }
    .muted { color: var(--muted); font-size: 12px; }
    .btn-outline-maroon { background: transparent; color: var(--maroon); border: 1px solid var(--maroon); border-radius: 20px; padding: 10px 18px; cursor: pointer; }
    .btn-outline-maroon:hover { background: rgba(117,13,13,0.08); }

    .note { margin-top: 10px; font-size: 12px; color: var(--muted); }
    .alert{ padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px; }
    .alert-error{ background:#fee; border:1px solid #fcc; color:#c33; }
    .alert-success{ background:#efe; border:1px solid #cfc; color:#363; }
  </style>
</head>
<body>
  <div class="container">
    <aside class="left">
      <div class="brand">
        <img src="assets/iskoprinter.png" alt="IskPrint logo" />
        <div class="brand-text">Isk<img class="star-img" src="assets/pup_star.png" alt="" />Print</div>
      </div>
    </aside>
    <main class="right">
      <h2 class="title">Create Account</h2>
      <p class="subtitle">or use your email for registration</p>

      <?php if ($error_message): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
      <?php endif; ?>
      
      <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="field">
          <label for="first_name">First Name</label>
          <input id="first_name" name="first_name" required value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" />
        </div>
        <div class="field">
          <label for="last_name">Last Name</label>
          <input id="last_name" name="last_name" required value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" />
        </div>
        <div class="field">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" minlength="6" required />
        </div>
        <div class="field">
          <label for="phone_number">Phone Number</label>
          <input id="phone_number" name="phone_number" type="tel" required value="<?php echo htmlspecialchars($_POST['phone_number'] ?? ''); ?>" />
        </div>
        <div class="form-actions">
          <button class="btn submit" type="submit">Sign up</button>
          <a class="btn-outline-maroon" href="login_acc.php">Login</a>
        </div>
      </form>
    </main>
  </div>
</body>
</html>


