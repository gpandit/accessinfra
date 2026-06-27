<?php
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/../lib/totp.php';

$uid = $_SESSION['pending_user_id'] ?? null;
if (!is_mfa_pending() || !$uid) { header('Location: ' . admin_url('login.php')); exit; }

$stmt = db()->prepare('SELECT * FROM users WHERE id = ? AND active = 1');
$stmt->execute([$uid]);
$user = $stmt->fetch();
if (!$user) { header('Location: ' . admin_url('login.php')); exit; }
if ((int) $user['mfa_enrolled'] === 1) { header('Location: ' . admin_url('mfa-verify.php')); exit; }

if (empty($_SESSION['totp_setup_secret'])) {
  $_SESSION['totp_setup_secret'] = totp_generate_secret();
}
$secret = $_SESSION['totp_setup_secret'];
$otpauth = totp_provisioning_uri($secret, $user['email'], MFA_ISSUER);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $code = $_POST['code'] ?? '';
  if (!totp_verify($secret, $code)) {
    $error = 'Incorrect code — try again.';
  } else {
    db()->prepare('UPDATE users SET totp_secret_enc = ?, mfa_enrolled = 1 WHERE id = ?')
      ->execute([encrypt_value($secret), $uid]);
    unset($_SESSION['totp_setup_secret'], $_SESSION['mfa_pending'], $_SESSION['pending_user_id']);
    session_regenerate_id(true);
    $_SESSION['admin_authed'] = true;
    $_SESSION['user_id'] = $uid;
    header('Location: ' . admin_url('index.php'));
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Set up two-factor authentication — Access Infra Admin</title>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<style>
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:'Inter',-apple-system,sans-serif;background:#0c1f3f;min-height:100vh;display:flex;align-items:center;justify-content:center;}
  .card{background:#fff;border-radius:14px;padding:36px 34px;width:420px;box-shadow:0 12px 48px rgba(0,0,0,0.3);}
  h1{font-size:19px;font-weight:700;margin-bottom:4px;color:#0f172a;}
  p.sub{font-size:13px;color:#64748b;margin-bottom:18px;line-height:1.6;}
  #qr{display:flex;justify-content:center;margin:18px 0;}
  .secret{font-family:monospace;font-size:13px;text-align:center;background:#f1f5f9;padding:10px;border-radius:8px;margin-bottom:18px;letter-spacing:1px;word-break:break-all;}
  label{display:block;font-size:12.5px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:5px;}
  input{width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:18px;text-align:center;letter-spacing:4px;}
  button{width:100%;padding:11px;background:linear-gradient(135deg,#1a56db,#0d9488);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;margin-top:14px;}
  .error{background:rgba(220,38,38,0.08);color:#dc2626;border:1px solid rgba(220,38,38,0.2);padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;}
</style>
</head>
<body>
  <div class="card">
    <h1>Set up two-factor authentication</h1>
    <p class="sub">Scan this QR code with Google Authenticator, Authy, or 1Password, then enter the 6-digit code it shows.</p>
    <div id="qr"></div>
    <div class="secret"><?php echo e($secret); ?></div>
    <p class="sub" style="margin-bottom:0;">Can't scan? Enter the code above manually in your authenticator app.</p>
    <?php if ($error): ?><div class="error" style="margin-top:16px;"><?php echo e($error); ?></div><?php endif; ?>
    <form method="POST">
      <label style="margin-top:16px;">6-digit code</label>
      <input type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" autofocus required>
      <button type="submit">Verify & enable</button>
    </form>
  </div>
  <script>
    QRCode.toCanvas(document.createElement('canvas'), <?php echo json_encode($otpauth); ?>, { width: 200 }, function (err, canvas) {
      if (!err) document.getElementById('qr').appendChild(canvas);
    });
  </script>
</body>
</html>
