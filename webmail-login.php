<?php require __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Webmail Login — Access Infra</title>
<link rel="icon" type="image/x-icon" href="<?php echo url('favicon.ico'); ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo url('assets/img/favicon-32x32.png'); ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo url('assets/img/apple-touch-icon.png'); ?>">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Inter:wght@400;500;600&display=swap">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --navy: #0c1f3f;
    --navy-mid: #1d4ed8;
    --accent: #1a56db;
    --gold: #1e3a8a;
    --text: #0f172a;
    --text-muted: #64748b;
    --border: #e2e8f0;
  }
  html, body { min-height: 100vh; }
  body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 55%, var(--accent) 100%);
    display: flex; align-items: center; justify-content: center;
    padding: 24px;
  }
  .login-card {
    width: 100%; max-width: 400px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(4,10,30,0.35);
    padding: 36px 32px 32px;
  }
  .logo-space {
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 22px;
  }
  .logo-space img { height: 56px; width: auto; display: block; }
  h1 {
    font-family: 'Sora', sans-serif;
    font-size: 19px; font-weight: 700;
    color: var(--text);
    text-align: center;
    margin-bottom: 4px;
  }
  .sub {
    text-align: center;
    color: var(--text-muted);
    font-size: 13.5px;
    margin-bottom: 26px;
  }
  .field { margin-bottom: 16px; }
  label {
    display: block;
    font-size: 12.5px; font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase; letter-spacing: 0.04em;
    margin-bottom: 6px;
  }
  input[type="text"], input[type="email"], input[type="password"] {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid var(--border);
    border-radius: 9px;
    font-size: 14.5px;
    font-family: 'Inter', sans-serif;
    color: var(--text);
    transition: border-color 0.2s;
  }
  input:focus { outline: none; border-color: var(--accent); }
  button[type="submit"] {
    width: 100%;
    margin-top: 8px;
    padding: 12px;
    border: none;
    border-radius: 9px;
    background: linear-gradient(135deg, var(--accent), var(--gold));
    color: #2a2300;
    font-size: 14.5px; font-weight: 700;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    transition: transform 0.15s, box-shadow 0.15s;
  }
  button[type="submit"]:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(26,86,219,0.35); }
  .forgot-row { text-align: center; margin-top: 18px; }
  .forgot-link {
    font-size: 13px; color: var(--navy-mid);
    text-decoration: none;
    border: none; background: none;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
  }
  .forgot-link:hover { text-decoration: underline; }
  .forgot-message {
    display: none;
    margin-top: 14px;
    background: rgba(29,78,216,0.1);
    border: 1px solid rgba(29,78,216,0.25);
    color: #3a3f26;
    font-size: 13px;
    line-height: 1.6;
    padding: 12px 14px;
    border-radius: 9px;
    text-align: center;
  }
  .forgot-message.visible { display: block; }
  .footer-note {
    text-align: center;
    margin-top: 28px;
    font-size: 11.5px;
    color: rgba(255,255,255,0.85);
  }
</style>
</head>
<body>
  <div>
    <div class="login-card">
      <div class="logo-space">
        <img src="<?php echo e(url('assets/img/logo.png')); ?>" alt="Access Infra">
      </div>
      <h1>Webmail Sign In</h1>
      <p class="sub">Access your Access Infra email account</p>

      <form method="POST" action="https://mail.hostinger.com/auth/login" autocomplete="on">
        <div class="field">
          <label for="email">Email / User ID</label>
          <input type="text" id="email" name="email" placeholder="you@accessinfraconsulting.com" required autofocus>
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="••••••••" required>
        </div>
        <button type="submit">Sign In</button>
      </form>

      <div class="forgot-row">
        <button type="button" class="forgot-link" id="forgotBtn">Forgot password?</button>
        <div class="forgot-message" id="forgotMessage">
          To reset your email password, please contact your email administrator.
        </div>
      </div>
    </div>
    <p class="footer-note">© <?php echo date('Y'); ?> Access Infra Consulting</p>
  </div>

  <script>
    document.getElementById('forgotBtn').addEventListener('click', function () {
      document.getElementById('forgotMessage').classList.toggle('visible');
    });
  </script>
</body>
</html>
