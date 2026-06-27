<?php
require __DIR__ . '/_bootstrap.php';
require_admin_page();
require_user_admin_page();

function require_user_admin_page() {
  $u = current_user();
  if (!$u || (int) $u['is_admin'] !== 1) { http_response_code(403); exit('Forbidden'); }
}

if (empty($_SESSION['users_csrf'])) $_SESSION['users_csrf'] = bin2hex(random_bytes(16));
$notice = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!hash_equals($_SESSION['users_csrf'], $_POST['csrf'] ?? '')) {
    $error = 'Session expired — please retry.';
  } else {
    $action = $_POST['action'] ?? '';
    if ($action === 'invite') {
      $email = strtolower(trim($_POST['email'] ?? ''));
      $name = trim($_POST['name'] ?? '');
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
      } elseif (find_user_by_email($email)) {
        $error = 'A user with that email already exists.';
      } else {
        $uid = db_new_id();
        db()->prepare(
          'INSERT INTO users (id, email, name, is_admin, perm_leads, perm_content, active, created_at)
           VALUES (?, ?, ?, ?, ?, ?, 1, NOW())'
        )->execute([
          $uid, $email, $name,
          !empty($_POST['is_admin']) ? 1 : 0,
          !empty($_POST['perm_leads']) ? 1 : 0,
          !empty($_POST['perm_content']) ? 1 : 0,
        ]);
        $token = issue_reset_token($uid);
        send_reset_email($email, $name, $token, true);
        $notice = 'Invitation sent to ' . $email . '.';
      }
    } elseif ($action === 'toggle_active') {
      $id = $_POST['id'] ?? '';
      $me = current_user();
      if ($id === ($me['id'] ?? null)) {
        $error = "You can't deactivate your own account.";
      } else {
        db()->prepare('UPDATE users SET active = 1 - active WHERE id = ?')->execute([$id]);
        $notice = 'User updated.';
      }
    } elseif ($action === 'resend_invite') {
      $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
      $stmt->execute([$_POST['id'] ?? '']);
      if ($u = $stmt->fetch()) {
        $token = issue_reset_token($u['id']);
        send_reset_email($u['email'], $u['name'], $token, empty($u['password_hash']));
        $notice = 'Email sent to ' . $u['email'] . '.';
      }
    }
  }
}

$rows = db()->query('SELECT * FROM users ORDER BY created_at')->fetchAll();

$page_title = 'Admin Users'; $active = 'users';
require __DIR__ . '/_layout_top.php';
?>
<h1>Admin Users</h1>
<?php if ($notice): ?><div class="alert success"><?php echo e($notice); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?php echo e($error); ?></div><?php endif; ?>

<div class="card">
  <h3 style="font-size:14px;margin-bottom:14px;">Invite a new admin user</h3>
  <form method="POST">
    <input type="hidden" name="csrf" value="<?php echo e($_SESSION['users_csrf']); ?>">
    <input type="hidden" name="action" value="invite">
    <div class="field"><label>Name</label><input type="text" name="name"></div>
    <div class="field"><label>Email</label><input type="email" name="email" required></div>
    <div class="field" style="display:flex;gap:18px;">
      <label style="display:flex;align-items:center;gap:6px;text-transform:none;font-weight:500;"><input type="checkbox" name="perm_leads" style="width:auto;" checked> Manage leads</label>
      <label style="display:flex;align-items:center;gap:6px;text-transform:none;font-weight:500;"><input type="checkbox" name="perm_content" style="width:auto;" checked> Manage blog</label>
      <label style="display:flex;align-items:center;gap:6px;text-transform:none;font-weight:500;"><input type="checkbox" name="is_admin" style="width:auto;"> Full admin</label>
    </div>
    <button type="submit" class="btn">Send invite</button>
  </form>
</div>

<div class="card" style="padding:0;overflow-x:auto;">
<table>
<thead><tr><th>Name</th><th>Email</th><th>Role</th><th>2FA</th><th>Status</th><th></th></tr></thead>
<tbody>
<?php foreach ($rows as $u): ?>
  <tr>
    <td><?php echo e($u['name']); ?></td>
    <td><?php echo e($u['email']); ?></td>
    <td><?php echo (int)$u['is_admin']===1 ? 'Full admin' : trim((((int)$u['perm_leads']?'Leads ':'').((int)$u['perm_content']?'Content':''))); ?></td>
    <td><?php echo (int)$u['mfa_enrolled']===1 ? '✓ enrolled' : '—'; ?></td>
    <td><?php echo (int)$u['active']===1 ? 'Active' : '<span style="color:var(--danger);">Deactivated</span>'; ?></td>
    <td style="display:flex;gap:6px;">
      <form method="POST">
        <input type="hidden" name="csrf" value="<?php echo e($_SESSION['users_csrf']); ?>">
        <input type="hidden" name="action" value="resend_invite">
        <input type="hidden" name="id" value="<?php echo e($u['id']); ?>">
        <button type="submit" class="btn small secondary"><?php echo empty($u['password_hash']) ? 'Resend invite' : 'Send reset'; ?></button>
      </form>
      <form method="POST">
        <input type="hidden" name="csrf" value="<?php echo e($_SESSION['users_csrf']); ?>">
        <input type="hidden" name="action" value="toggle_active">
        <input type="hidden" name="id" value="<?php echo e($u['id']); ?>">
        <button type="submit" class="btn small <?php echo (int)$u['active']===1?'danger':''; ?>"><?php echo (int)$u['active']===1?'Deactivate':'Reactivate'; ?></button>
      </form>
    </td>
  </tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
