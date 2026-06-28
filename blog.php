<?php require __DIR__ . '/includes/config.php'; $page_title = 'Blog — Access Infra'; require __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/lib/db.php'; ?>
<style>
  .blog-wrap{max-width:900px;margin:0 auto;padding:120px 20px 80px;}
  .blog-wrap h1{font-family:'Miloner','Sora',sans-serif;font-size:clamp(28px,4vw,42px);font-weight:800;margin-bottom:8px;}
  .blog-list{display:grid;gap:20px;margin-top:32px;}
  .blog-card{display:grid;grid-template-columns:200px 1fr;gap:20px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;padding:0;}
  .blog-card .cover{background:linear-gradient(135deg,#1a56db,#1e3a8a);min-height:140px;}
  .blog-card .cover img{width:100%;height:100%;object-fit:cover;display:block;}
  .blog-card .body{padding:20px 22px;}
  .blog-card h2{font-size:18px;font-weight:700;margin-bottom:8px;}
  .blog-card h2 a{color:#0f172a;text-decoration:none;}
  .blog-card p{color:#475569;font-size:14px;line-height:1.6;}
  .blog-meta{font-size:12.5px;color:#64748b;margin-top:10px;}
  @media (max-width:640px){.blog-card{grid-template-columns:1fr;}}
</style>
<div class="blog-wrap">
  <h1>Blog & Insights</h1>
  <p style="color:#64748b;">Updates on vendor consulting, government procurement and infrastructure partnerships.</p>
  <div class="blog-list">
    <?php
    $rows = db()->query("SELECT * FROM posts WHERE status = 'published' ORDER BY published_at DESC")->fetchAll();
    if (!$rows) {
      echo '<p style="color:#64748b;padding:40px 0;text-align:center;">No posts published yet.</p>';
    }
    foreach ($rows as $p):
    ?>
    <article class="blog-card">
      <div class="cover">
        <?php if ($p['cover_image']): ?><img src="<?php echo htmlspecialchars($p['cover_image']); ?>" alt=""><?php endif; ?>
      </div>
      <div class="body">
        <h2><a href="<?php echo htmlspecialchars(url('blog-post.php?slug=' . $p['slug'])); ?>"><?php echo htmlspecialchars($p['title']); ?></a></h2>
        <p><?php echo htmlspecialchars($p['excerpt']); ?></p>
        <div class="blog-meta"><?php echo htmlspecialchars($p['author_name'] ?: 'Access Infra'); ?> · <?php echo htmlspecialchars(date('d M Y', strtotime($p['published_at']))); ?></div>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
