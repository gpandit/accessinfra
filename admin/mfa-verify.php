<?php
require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/../lib/totp.php';

$uid = $_SESSION['pending_user_id'] ?? null;
if (!is_mfa_pending() || !$uid) { header('Location: ' . admin_url('login.php')); exit; }

$stmt = db()->prepare('SELECT * FROM users WHERE id = ? AND active = 1');
$stmt->execute([$uid]);
$user = $stmt->fetch();
if (!$user || empty($user['totp_secret_enc'])) { header('Location: ' . admin_url('mfa-setup.php')); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  usleep(300000);
  $code = $_POST['code'] ?? '';
  if (!totp_verify(decrypt_value($user['totp_secret_enc']), $code)) {
    $error = 'Incorrect code.';
  } else {
    unset($_SESSION['mfa_pending'], $_SESSION['pending_user_id']);
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
<title>Two-factor verification — Access Infra Admin</title>
<style>
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:'Inter',-apple-system,sans-serif;background:#0c1f3f;min-height:100vh;display:flex;align-items:center;justify-content:center;}
  .card{background:#fff;border-radius:14px;padding:36px 34px;width:360px;box-shadow:0 12px 48px rgba(0,0,0,0.3);}
  h1{font-size:19px;font-weight:700;margin-bottom:4px;color:#0f172a;}
  p.sub{font-size:13px;color:#64748b;margin-bottom:22px;}
  input{width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:18px;text-align:center;letter-spacing:4px;}
  button{width:100%;padding:11px;background:linear-gradient(135deg,#1a56db,#0d9488);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;margin-top:14px;}
  .error{background:rgba(220,38,38,0.08);color:#dc2626;border:1px solid rgba(220,38,38,0.2);padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;}
</style>
</head>
<body>
  <div class="card">
    <h1>Enter your authenticator code</h1>
    <p class="sub">Open your authenticator app and enter the current 6-digit code.</p>
    <?php if ($error): ?><div class="error"><?php echo e($error); ?></div><?php endif; ?>
    <form method="POST">
      <input type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" autofocus required>
      <button type="submit">Verify</button>
    </form>
  </div>
</body>
</html>
