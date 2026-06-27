<?php
require __DIR__ . '/_bootstrap.php';

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$user = user_by_reset_token($token);
$error = '';

if (!$user) {
  ?>
  <!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Invalid link</title>
  <style>body{font-family:'Inter',-apple-system,sans-serif;background:#0c1f3f;min-height:100vh;display:flex;align-items:center;justify-content:center;}
  .card{background:#fff;border-radius:14px;padding:36px 34px;width:380px;box-shadow:0 12px 48px rgba(0,0,0,0.3);font-size:14px;}</style></head><body>
  <div class="card"><h1 style="margin-bottom:10px;">This link is invalid or has expired</h1>
  <p>Request a new one from <a href="<?php echo e(admin_url('request-reset.php')); ?>">the password reset page</a>.</p></div>
  </body></html>
  <?php
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $new = $_POST['new_password'] ?? '';
  $confirm = $_POST['confirm_password'] ?? '';
  if (strlen($new) < 10) {
    $error = 'Password must be at least 10 characters.';
  } elseif ($new !== $confirm) {
    $error = 'Passwords do not match.';
  } else {
    db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
      ->execute([password_hash($new, PASSWORD_DEFAULT), $user['id']]);
    clear_reset_token($user['id']);

    session_regenerate_id(true);
    $_SESSION = [];
    $_SESSION['mfa_pending'] = true;
    $_SESSION['pending_user_id'] = $user['id'];
    $dest = (int) $user['mfa_enrolled'] === 1 ? 'mfa-verify.php' : 'mfa-setup.php';
    header('Location: ' . admin_url($dest));
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Set your password — Access Infra Admin</title>
<style>
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:'Inter',-apple-system,sans-serif;background:#0c1f3f;min-height:100vh;display:flex;align-items:center;justify-content:center;}
  .card{background:#fff;border-radius:14px;padding:36px 34px;width:380px;box-shadow:0 12px 48px rgba(0,0,0,0.3);}
  h1{font-size:19px;font-weight:700;margin-bottom:4px;color:#0f172a;}
  p.sub{font-size:13px;color:#64748b;margin-bottom:22px;}
  label{display:block;font-size:12.5px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:5px;}
  input{width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;margin-bottom:14px;}
  button{width:100%;padding:11px;background:linear-gradient(135deg,#1a56db,#0d9488);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;}
  .error{background:rgba(220,38,38,0.08);color:#dc2626;border:1px solid rgba(220,38,38,0.2);padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;}
</style>
</head>
<body>
  <div class="card">
    <h1>Hi <?php echo e($user['name'] ?: $user['email']); ?></h1>
    <p class="sub">Choose a password for your admin account.</p>
    <?php if ($error): ?><div class="error"><?php echo e($error); ?></div><?php endif; ?>
    <form method="POST">
      <input type="hidden" name="token" value="<?php echo e($token); ?>">
      <label>New password</label>
      <input type="password" name="new_password" required minlength="10">
      <label>Confirm password</label>
      <input type="password" name="confirm_password" required minlength="10">
      <button type="submit">Set password & continue</button>
    </form>
  </div>
</body>
</html>
