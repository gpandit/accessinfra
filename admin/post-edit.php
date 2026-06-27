<?php
require __DIR__ . '/_bootstrap.php';
require_admin_page();
if (!has_perm('content')) { http_response_code(403); exit('Forbidden'); }

function slugify($str) {
  $s = strtolower(trim($str));
  $s = preg_replace('/[^a-z0-9]+/', '-', $s);
  return trim($s, '-');
}

$id = $_GET['id'] ?? $_POST['id'] ?? '';
$post = null;
if ($id) {
  $stmt = db()->prepare('SELECT * FROM posts WHERE id = ?');
  $stmt->execute([$id]);
  $post = $stmt->fetch() ?: null;
}

$authors = db()->query('SELECT id, name FROM authors ORDER BY name')->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $slugInput = trim($_POST['slug'] ?? '');
  $slug = slugify($slugInput !== '' ? $slugInput : $title);
  $excerpt = trim($_POST['excerpt'] ?? '');
  $body = $_POST['body'] ?? '';
  $authorId = $_POST['author_id'] ?: null;
  $authorName = '';
  foreach ($authors as $a) { if ($a['id'] === $authorId) $authorName = $a['name']; }
  $coverImage = trim($_POST['cover_image'] ?? '');
  $tags = trim($_POST['tags'] ?? '');
  $metaTitle = trim($_POST['meta_title'] ?? '');
  $metaDesc = trim($_POST['meta_description'] ?? '');

  if ($title === '' || $slug === '') {
    $error = 'Title is required.';
  } else {
    // Ensure slug uniqueness (append -2, -3... on collision with a different post).
    $base = $slug; $n = 2;
    while (true) {
      $stmt = db()->prepare('SELECT id FROM posts WHERE slug = ? AND id != ?');
      $stmt->execute([$slug, $id ?: '']);
      if (!$stmt->fetch()) break;
      $slug = $base . '-' . $n++;
    }

    if ($post) {
      db()->prepare(
        'UPDATE posts SET title=?, slug=?, excerpt=?, body=?, author_id=?, author_name=?, cover_image=?, tags=?, meta_title=?, meta_description=?, updated_at=NOW() WHERE id=?'
      )->execute([$title, $slug, $excerpt, $body, $authorId, $authorName, $coverImage, $tags, $metaTitle, $metaDesc, $post['id']]);
      $id = $post['id'];
    } else {
      $id = db_new_id();
      db()->prepare(
        'INSERT INTO posts (id, title, slug, excerpt, body, author_id, author_name, cover_image, tags, meta_title, meta_description, status, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "draft", NOW(), NOW())'
      )->execute([$id, $title, $slug, $excerpt, $body, $authorId, $authorName, $coverImage, $tags, $metaTitle, $metaDesc]);
    }
    header('Location: ' . admin_url('post-edit.php?id=' . $id . '&saved=1'));
    exit;
  }
  // re-fetch for redisplay on validation error
  $post = ['id' => $id, 'title' => $title, 'slug' => $slug, 'excerpt' => $excerpt, 'body' => $body,
    'author_id' => $authorId, 'cover_image' => $coverImage, 'tags' => $tags,
    'meta_title' => $metaTitle, 'meta_description' => $metaDesc, 'status' => $post['status'] ?? 'draft'];
}

$page_title = $post ? 'Edit Post' : 'New Post'; $active = 'posts';
require __DIR__ . '/_layout_top.php';
?>
<h1><?php echo $post && !empty($post['id']) ? 'Edit Post' : 'New Post'; ?></h1>
<?php if (!empty($_GET['saved'])): ?><div class="alert success">Saved.</div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?php echo e($error); ?></div><?php endif; ?>
<form method="POST" class="card">
  <input type="hidden" name="id" value="<?php echo e($post['id'] ?? ''); ?>">
  <div class="field">
    <label>Title</label>
    <input type="text" name="title" required value="<?php echo e($post['title'] ?? ''); ?>">
  </div>
  <div class="field">
    <label>Slug (URL path — leave blank to auto-generate from title)</label>
    <input type="text" name="slug" value="<?php echo e($post['slug'] ?? ''); ?>" placeholder="my-post-title">
  </div>
  <div class="field">
    <label>Author</label>
    <select name="author_id">
      <option value="">— none —</option>
      <?php foreach ($authors as $a): ?>
        <option value="<?php echo e($a['id']); ?>" <?php echo ($post['author_id'] ?? '')===$a['id']?'selected':''; ?>><?php echo e($a['name']); ?></option>
      <?php endforeach; ?>
    </select>
    <?php if (!$authors): ?><p class="muted" style="margin-top:6px;">No authors yet — <a href="<?php echo e(admin_url('authors.php')); ?>">add one</a>.</p><?php endif; ?>
  </div>
  <div class="field">
    <label>Cover image URL</label>
    <input type="url" name="cover_image" value="<?php echo e($post['cover_image'] ?? ''); ?>" placeholder="https://… or /assets/uploads/posts/…">
  </div>
  <div class="field">
    <label>Excerpt</label>
    <textarea name="excerpt" rows="2"><?php echo e($post['excerpt'] ?? ''); ?></textarea>
  </div>
  <div class="field">
    <label>Body (HTML)</label>
    <textarea name="body" rows="16" style="font-family:monospace;font-size:13px;"><?php echo e($post['body'] ?? ''); ?></textarea>
    <p class="muted" style="margin-top:6px;">Basic HTML tags (&lt;p&gt;, &lt;h2&gt;, &lt;strong&gt;, &lt;a&gt;, &lt;img&gt;, &lt;ul&gt;) are rendered as-is on the public page.</p>
  </div>
  <div class="field">
    <label>Tags (comma-separated)</label>
    <input type="text" name="tags" value="<?php echo e($post['tags'] ?? ''); ?>">
  </div>
  <div class="field">
    <label>SEO meta title</label>
    <input type="text" name="meta_title" value="<?php echo e($post['meta_title'] ?? ''); ?>">
  </div>
  <div class="field">
    <label>SEO meta description</label>
    <textarea name="meta_description" rows="2"><?php echo e($post['meta_description'] ?? ''); ?></textarea>
  </div>
  <button type="submit" class="btn">Save</button>
  <a href="<?php echo e(admin_url('posts.php')); ?>" class="btn secondary">Cancel</a>
</form>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
