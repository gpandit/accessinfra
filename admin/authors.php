<?php
require __DIR__ . '/_bootstrap.php';
require_admin_page();
if (!has_perm('content')) { http_response_code(403); exit('Forbidden'); }

if (empty($_SESSION['authors_csrf'])) $_SESSION['authors_csrf'] = bin2hex(random_bytes(16));
$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!hash_equals($_SESSION['authors_csrf'], $_POST['csrf'] ?? '')) {
    $notice = 'Session expired — please retry.';
  } else {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
      $name = trim($_POST['name'] ?? '');
      if ($name !== '') {
        db()->prepare('INSERT INTO authors (id, name, bio, avatar_url, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())')
          ->execute([db_new_id(), $name, trim($_POST['bio'] ?? ''), trim($_POST['avatar_url'] ?? '')]);
        $notice = 'Author added.';
      }
    } elseif ($action === 'delete') {
      db()->prepare('DELETE FROM authors WHERE id = ?')->execute([$_POST['id'] ?? '']);
      $notice = 'Author removed.';
    }
  }
}

$rows = db()->query('SELECT * FROM authors ORDER BY name')->fetchAll();

$page_title = 'Authors'; $active = 'authors';
require __DIR__ . '/_layout_top.php';
?>
<h1>Authors</h1>
<?php if ($notice): ?><div class="alert success"><?php echo e($notice); ?></div><?php endif; ?>

<div class="card">
  <h3 style="font-size:14px;margin-bottom:14px;">Add author</h3>
  <form method="POST">
    <input type="hidden" name="csrf" value="<?php echo e($_SESSION['authors_csrf']); ?>">
    <input type="hidden" name="action" value="create">
    <div class="field"><label>Name</label><input type="text" name="name" required></div>
    <div class="field"><label>Bio</label><textarea name="bio" rows="2"></textarea></div>
    <div class="field"><label>Avatar URL</label><input type="url" name="avatar_url"></div>
    <button type="submit" class="btn">Add author</button>
  </form>
</div>

<div class="card" style="padding:0;">
<table>
<thead><tr><th>Name</th><th>Bio</th><th></th></tr></thead>
<tbody>
<?php foreach ($rows as $a): ?>
  <tr>
    <td><?php echo e($a['name']); ?></td>
    <td class="muted"><?php echo e($a['bio']); ?></td>
    <td>
      <form method="POST" onsubmit="return confirm('Remove this author?');">
        <input type="hidden" name="csrf" value="<?php echo e($_SESSION['authors_csrf']); ?>">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?php echo e($a['id']); ?>">
        <button type="submit" class="btn small danger">Remove</button>
      </form>
    </td>
  </tr>
<?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="3" class="muted" style="padding:20px;text-align:center;">No authors yet.</td></tr><?php endif; ?>
</tbody>
</table>
</div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
