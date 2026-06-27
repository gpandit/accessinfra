<?php
require __DIR__ . '/_bootstrap.php';
require_admin_page();
if (!has_perm('content')) { http_response_code(403); exit('Forbidden'); }

if (empty($_SESSION['posts_csrf'])) $_SESSION['posts_csrf'] = bin2hex(random_bytes(16));
$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!hash_equals($_SESSION['posts_csrf'], $_POST['csrf'] ?? '')) {
    $notice = 'Session expired — please retry.';
  } else {
    $id = $_POST['id'] ?? '';
    $action = $_POST['action'] ?? '';
    if ($action === 'delete' && $id) {
      db()->prepare('DELETE FROM posts WHERE id = ?')->execute([$id]);
      $notice = 'Post deleted.';
    } elseif ($action === 'toggle' && $id) {
      $stmt = db()->prepare('SELECT status FROM posts WHERE id = ?');
      $stmt->execute([$id]);
      $cur = $stmt->fetchColumn();
      $new = $cur === 'published' ? 'draft' : 'published';
      $publishedAt = $new === 'published' ? 'NOW()' : 'NULL';
      db()->prepare("UPDATE posts SET status = ?, published_at = " . ($new === 'published' ? 'COALESCE(published_at, NOW())' : 'published_at') . " WHERE id = ?")
        ->execute([$new, $id]);
      $notice = $new === 'published' ? 'Post published.' : 'Post unpublished.';
    }
  }
}

$rows = db()->query('SELECT * FROM posts ORDER BY created_at DESC')->fetchAll();

$page_title = 'Blog Posts'; $active = 'posts';
require __DIR__ . '/_layout_top.php';
?>
<div class="topbar">
  <h1 style="margin-bottom:0;">Blog Posts</h1>
  <a href="<?php echo e(admin_url('post-edit.php')); ?>" class="btn">+ New post</a>
</div>
<?php if ($notice): ?><div class="alert success"><?php echo e($notice); ?></div><?php endif; ?>
<div class="card" style="padding:0;overflow-x:auto;">
<table>
<thead><tr><th>Title</th><th>Author</th><th>Status</th><th>Updated</th><th></th></tr></thead>
<tbody>
<?php foreach ($rows as $p): ?>
  <tr>
    <td><a href="<?php echo e(admin_url('post-edit.php?id=' . $p['id'])); ?>"><?php echo e($p['title']); ?></a><br><span class="muted">/<?php echo e($p['slug']); ?></span></td>
    <td><?php echo e($p['author_name'] ?: '—'); ?></td>
    <td><span class="badge <?php echo e($p['status']); ?>"><?php echo e($p['status']); ?></span></td>
    <td class="muted"><?php echo e(date('d M Y', strtotime($p['updated_at']))); ?></td>
    <td style="display:flex;gap:6px;">
      <form method="POST">
        <input type="hidden" name="csrf" value="<?php echo e($_SESSION['posts_csrf']); ?>">
        <input type="hidden" name="id" value="<?php echo e($p['id']); ?>">
        <input type="hidden" name="action" value="toggle">
        <button type="submit" class="btn small secondary"><?php echo $p['status']==='published'?'Unpublish':'Publish'; ?></button>
      </form>
      <form method="POST" onsubmit="return confirm('Delete this post?');">
        <input type="hidden" name="csrf" value="<?php echo e($_SESSION['posts_csrf']); ?>">
        <input type="hidden" name="id" value="<?php echo e($p['id']); ?>">
        <input type="hidden" name="action" value="delete">
        <button type="submit" class="btn small danger">Delete</button>
      </form>
    </td>
  </tr>
<?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="5" class="muted" style="padding:20px;text-align:center;">No posts yet.</td></tr><?php endif; ?>
</tbody>
</table>
</div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
