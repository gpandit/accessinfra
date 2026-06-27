<?php
require __DIR__ . '/_bootstrap.php';
require_admin_page();
require __DIR__ . '/../lib/totp.php';

$me = current_user();
if (empty($_SESSION['account_csrf'])) $_SESSION['account_csrf'] = bin2hex(random_bytes(16));
$notice = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!hash_equals($_SESSION['account_csrf'], $_POST['csrf'] ?? '')) {
    $error = 'Session expired — please retry.';
  } else {
    $action = $_POST['action'] ?? '';
    if ($action === 'change_password') {
      $current = $_POST['current_password'] ?? '';
      $new = $_POST['new_password'] ?? '';
      $confirm = $_POST['confirm_password'] ?? '';
      if (!empty($me['password_hash']) && !password_verify($current, $me['password_hash'])) {
        $error = 'Current password is incorrect.';
      } elseif (strlen($new) < 10) {
        $error = 'New password must be at least 10 characters.';
      } elseif ($new !== $confirm) {
        $error = 'New password and confirmation do not match.';
      } else {
        db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
          ->execute([password_hash($new, PASSWORD_DEFAULT), $me['id']]);
        $notice = 'Password updated.';
      }
    } elseif ($action === 'disable_mfa') {
      db()->prepare('UPDATE users SET totp_secret_enc = NULL, mfa_enrolled = 0 WHERE id = ?')->execute([$me['id']]);
      $notice = 'Two-factor authentication disabled. You will be asked to re-enroll on next login.';
      $me = current_user();
    }
  }
}

$page_title = 'My Account'; $active = 'account';
require __DIR__ . '/_layout_top.php';
?>
<h1>My Account</h1>
<?php if ($notice): ?><div class="alert success"><?php echo e($notice); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?php echo e($error); ?></div><?php endif; ?>

<div class="card">
  <h3 style="font-size:14px;margin-bottom:14px;">Change password</h3>
  <form method="POST">
    <input type="hidden" name="csrf" value="<?php echo e($_SESSION['account_csrf']); ?>">
    <input type="hidden" name="action" value="change_password">
    <?php if (!empty($me['password_hash'])): ?>
    <div class="field"><label>Current password</label><input type="password" name="current_password" required></div>
    <?php endif; ?>
    <div class="field"><label>New password</label><input type="password" name="new_password" required minlength="10"></div>
    <div class="field"><label>Confirm new password</label><input type="password" name="confirm_password" required minlength="10"></div>
    <button type="submit" class="btn">Update password</button>
  </form>
</div>

<div class="card">
  <h3 style="font-size:14px;margin-bottom:14px;">Two-factor authentication</h3>
  <p class="muted" style="margin-bottom:14px;">
    Status: <?php echo (int)$me['mfa_enrolled']===1 ? '<strong style="color:var(--success);">Enrolled</strong>' : 'Not enrolled'; ?>
  </p>
  <?php if ((int)$me['mfa_enrolled']===1): ?>
  <form method="POST" onsubmit="return confirm('Disable two-factor authentication on this account?');">
    <input type="hidden" name="csrf" value="<?php echo e($_SESSION['account_csrf']); ?>">
    <input type="hidden" name="action" value="disable_mfa">
    <button type="submit" class="btn danger">Disable 2FA</button>
  </form>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
