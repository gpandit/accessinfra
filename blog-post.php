<?php
require __DIR__ . '/includes/config.php';
require_once __DIR__ . '/lib/db.php';

$slug = $_GET['slug'] ?? '';
$stmt = db()->prepare("SELECT * FROM posts WHERE slug = ? AND status = 'published'");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
  http_response_code(404);
  $page_title = 'Post not found — Access Infra';
  require __DIR__ . '/includes/header.php';
  echo '<div style="max-width:700px;margin:0 auto;padding:160px 20px 100px;text-align:center;"><h1>Post not found</h1><p><a href="' . htmlspecialchars(url('blog.php')) . '">← Back to blog</a></p></div>';
  require __DIR__ . '/includes/footer.php';
  exit;
}

$page_title = ($post['meta_title'] ?: $post['title']) . ' — Access Infra';
require __DIR__ . '/includes/header.php';
?>
<style>
  .post-wrap{max-width:720px;margin:0 auto;padding:120px 20px 100px;}
  .post-wrap .back{font-size:13px;color:#64748b;text-decoration:none;}
  .post-wrap h1{font-family:'Sora',sans-serif;font-size:clamp(26px,4vw,40px);font-weight:800;margin:14px 0 10px;}
  .post-meta{color:#64748b;font-size:13px;margin-bottom:24px;}
  .post-cover{width:100%;border-radius:14px;margin-bottom:28px;}
  .post-body{font-size:16px;line-height:1.8;color:#1f2937;}
  .post-body h2{font-size:22px;font-weight:700;margin:28px 0 12px;}
  .post-body p{margin-bottom:16px;}
  .post-body img{max-width:100%;border-radius:10px;}
  .post-tags{margin-top:32px;display:flex;gap:8px;flex-wrap:wrap;}
  .post-tags span{background:#f1f5f9;color:#475569;font-size:12px;padding:4px 10px;border-radius:999px;}
</style>
<div class="post-wrap">
  <a class="back" href="<?php echo htmlspecialchars(url('blog.php')); ?>">← Back to blog</a>
  <h1><?php echo htmlspecialchars($post['title']); ?></h1>
  <div class="post-meta"><?php echo htmlspecialchars($post['author_name'] ?: 'Access Infra'); ?> · <?php echo htmlspecialchars(date('d M Y', strtotime($post['published_at']))); ?></div>
  <?php if ($post['cover_image']): ?><img class="post-cover" src="<?php echo htmlspecialchars($post['cover_image']); ?>" alt=""><?php endif; ?>
  <div class="post-body"><?php echo $post['body']; ?></div>
  <?php
  $tags = array_filter(array_map('trim', explode(',', $post['tags'] ?? '')));
  if ($tags):
  ?>
  <div class="post-tags"><?php foreach ($tags as $t): ?><span><?php echo htmlspecialchars($t); ?></span><?php endforeach; ?></div>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
