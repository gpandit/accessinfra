<?php
require __DIR__ . '/_bootstrap.php';
if (is_admin()) { header('Location: ' . admin_url('index.php')); exit; }

if (empty($_SESSION['reset_csrf'])) $_SESSION['reset_csrf'] = bin2hex(random_bytes(16));
$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (hash_equals($_SESSION['reset_csrf'], $_POST['csrf'] ?? '')) {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $user = find_user_by_email($email);
    if ($user && (int) $user['active'] === 1) {
      $token = issue_reset_token($user['id']);
      send_reset_email($user['email'], $user['name'], $token, empty($user['password_hash']));
    }
  }
  // Always show the same message — never reveal whether an email exists.
  $notice = 'If that email matches an admin account, a reset link has been sent.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset password — Access Infra Admin</title>
<style>
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:'Inter',-apple-system,sans-serif;background:#0c1f3f;min-height:100vh;display:flex;align-items:center;justify-content:center;}
  .card{background:#fff;border-radius:14px;padding:36px 34px;width:380px;box-shadow:0 12px 48px rgba(0,0,0,0.3);}
  h1{font-size:19px;font-weight:700;margin-bottom:4px;color:#0f172a;}
  p.sub{font-size:13px;color:#64748b;margin-bottom:22px;}
  label{display:block;font-size:12.5px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:5px;}
  input{width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;}
  button{width:100%;padding:11px;background:linear-gradient(135deg,#1a56db,#0d9488);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;margin-top:14px;}
  .notice{background:rgba(13,148,136,0.08);color:#0d9488;border:1px solid rgba(13,148,136,0.2);padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;}
</style>
</head>
<body>
  <div class="card">
    <h1>Reset your password</h1>
    <p class="sub">Enter your admin email and we'll send a reset link.</p>
    <?php if ($notice): ?><div class="notice"><?php echo e($notice); ?></div><?php endif; ?>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?php echo e($_SESSION['reset_csrf']); ?>">
      <label>Email</label>
      <input type="email" name="email" required autofocus>
      <button type="submit">Send reset link</button>
    </form>
  </div>
</body>
</html>
