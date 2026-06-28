<?php
require __DIR__ . '/_bootstrap.php';

if (is_admin()) { header('Location: ' . admin_url('index.php')); exit; }

if (empty($_SESSION['login_csrf'])) {
  $_SESSION['login_csrf'] = bin2hex(random_bytes(16));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $token_ok = isset($_POST['csrf']) && hash_equals($_SESSION['login_csrf'], $_POST['csrf']);
  $email = strtolower(trim($_POST['email'] ?? ''));
  $password = $_POST['password'] ?? '';

  if (!$token_ok) {
    $error = 'Your session expired — please try again.';
  } else {
    usleep(300000); // brute-force slowdown

    $user = find_user_by_email($email);
    $ok = $user
      && (int) $user['active'] === 1
      && !empty($user['password_hash'])
      && is_string($password)
      && password_verify($password, $user['password_hash']);

    if (!$ok) {
      $error = 'Incorrect email or password.';
    } else {
      session_regenerate_id(true);
      $_SESSION = [];
      $_SESSION['mfa_pending'] = true;
      $_SESSION['pending_user_id'] = $user['id'];
      $dest = (int) $user['mfa_enrolled'] === 1 ? 'mfa-verify.php' : 'mfa-setup.php';
      header('Location: ' . admin_url($dest));
      exit;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — Access Infra</title>
<?php $site_root = preg_replace('#/admin$#', '', admin_url()); ?>
<link rel="icon" type="image/x-icon" href="<?php echo e($site_root . '/favicon.ico'); ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo e($site_root . '/assets/img/favicon-32x32.png'); ?>">
<style>
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:'Inter',-apple-system,sans-serif;background:#0c1f3f;min-height:100vh;display:flex;align-items:center;justify-content:center;}
  .card{background:#fff;border-radius:14px;padding:36px 34px;width:380px;box-shadow:0 12px 48px rgba(0,0,0,0.3);}
  h1{font-size:19px;font-weight:700;margin-bottom:4px;color:#0f172a;}
  p.sub{font-size:13px;color:#64748b;margin-bottom:22px;}
  label{display:block;font-size:12.5px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:5px;}
  .field{margin-bottom:16px;}
  input{width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;}
  button{width:100%;padding:11px;background:linear-gradient(135deg,#1a56db,#0d9488);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;margin-top:6px;}
  .error{background:rgba(220,38,38,0.08);color:#dc2626;border:1px solid rgba(220,38,38,0.2);padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;}
</style>
</head>
<body>
  <div class="card">
    <div style="text-align:center;margin-bottom:6px;"><img src="<?php echo e($site_root . '/assets/img/logo.png'); ?>" alt="Access Infra" style="height:46px;width:auto;"></div>
    <h1>Admin</h1>
    <p class="sub">Sign in to manage leads and blog content.</p>
    <?php if ($error): ?><div class="error"><?php echo e($error); ?></div><?php endif; ?>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?php echo e($_SESSION['login_csrf']); ?>">
      <div class="field">
        <label>Email</label>
        <input type="email" name="email" required autofocus value="<?php echo e($_POST['email'] ?? ''); ?>">
      </div>
      <div class="field">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit">Sign in</button>
    </form>
    <p style="text-align:center;margin-top:16px;"><a href="<?php echo e(admin_url('request-reset.php')); ?>" style="color:#64748b;font-size:13px;">Forgot password?</a></p>
  </div>
</body>
</html>
