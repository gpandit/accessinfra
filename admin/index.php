<?php
require __DIR__ . '/_bootstrap.php';
require_admin_page();

$leadCount = (int) db()->query("SELECT COUNT(*) FROM leads WHERE archived = 0")->fetchColumn();
$newLeadCount = (int) db()->query("SELECT COUNT(*) FROM leads WHERE archived = 0 AND status = 'new'")->fetchColumn();
$postCount = (int) db()->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
$draftCount = (int) db()->query("SELECT COUNT(*) FROM posts WHERE status = 'draft'")->fetchColumn();

$page_title = 'Dashboard'; $active = 'dashboard';
require __DIR__ . '/_layout_top.php';
?>
<h1>Dashboard</h1>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;">
  <div class="card">
    <div class="muted">Active leads</div>
    <div style="font-size:32px;font-weight:800;margin-top:6px;"><?php echo $leadCount; ?></div>
    <div class="muted" style="margin-top:4px;"><?php echo $newLeadCount; ?> new</div>
  </div>
  <div class="card">
    <div class="muted">Published posts</div>
    <div style="font-size:32px;font-weight:800;margin-top:6px;"><?php echo $postCount; ?></div>
    <div class="muted" style="margin-top:4px;"><?php echo $draftCount; ?> drafts</div>
  </div>
</div>
<div class="card">
  <a href="<?php echo e(admin_url('leads.php')); ?>" class="btn secondary" style="margin-right:8px;">View leads →</a>
  <a href="<?php echo e(admin_url('posts.php')); ?>" class="btn secondary">Manage blog →</a>
</div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
