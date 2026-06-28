<?php
/**
 * @param string $page_title
 * @param string $active  one of: dashboard, leads, posts, authors, users, account
 */
$page_title = $page_title ?? 'Admin';
$active = $active ?? '';
$me = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($page_title); ?> — Access Infra Admin</title>
<?php $site_root = preg_replace('#/admin$#', '', admin_url()); ?>
<link rel="icon" type="image/x-icon" href="<?php echo e($site_root . '/favicon.ico'); ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo e($site_root . '/assets/img/favicon-32x32.png'); ?>">
<style>
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
  :root{
    --navy:#0c1f3f;--blue:#1a56db;--teal:#0d9488;
    --bg:#f1f5f9;--surface:#ffffff;--border:#e2e8f0;
    --text:#0f172a;--text2:#334155;--text3:#64748b;
    --danger:#dc2626;--success:#0d9488;
    --shadow:0 2px 10px rgba(12,31,63,0.06);
  }
  body{font-family:'Inter',-apple-system,sans-serif;background:var(--bg);color:var(--text);line-height:1.5;}
  a{color:var(--blue);text-decoration:none;}
  .admin-shell{display:flex;min-height:100vh;}
  .admin-sidebar{width:220px;flex-shrink:0;background:var(--navy);color:#fff;padding:24px 0;}
  .admin-sidebar .brand{padding:0 20px 20px;font-weight:800;font-size:17px;border-bottom:1px solid rgba(255,255,255,0.1);margin-bottom:12px;}
  .admin-sidebar nav a{display:block;padding:10px 20px;color:rgba(255,255,255,0.7);font-size:14px;font-weight:500;}
  .admin-sidebar nav a:hover{background:rgba(255,255,255,0.06);color:#fff;}
  .admin-sidebar nav a.active{background:rgba(26,86,219,0.25);color:#fff;border-right:3px solid var(--teal);}
  .admin-sidebar .foot{margin-top:20px;padding:14px 20px 0;border-top:1px solid rgba(255,255,255,0.1);font-size:12.5px;color:rgba(255,255,255,0.55);}
  .admin-sidebar .foot a{color:rgba(255,255,255,0.75);}
  .admin-main{flex:1;padding:32px 36px;max-width:1100px;}
  .admin-main h1{font-size:24px;font-weight:700;margin-bottom:18px;}
  .card{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:22px;box-shadow:var(--shadow);margin-bottom:18px;}
  table{width:100%;border-collapse:collapse;font-size:13.5px;}
  th{text-align:left;color:var(--text3);font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:0.04em;padding:8px 10px;border-bottom:1px solid var(--border);}
  td{padding:10px 10px;border-bottom:1px solid var(--border);vertical-align:top;}
  tr:last-child td{border-bottom:none;}
  .btn{display:inline-block;background:var(--blue);color:#fff;border:none;padding:8px 16px;border-radius:8px;font-size:13.5px;font-weight:600;cursor:pointer;}
  .btn:hover{opacity:0.9;}
  .btn.secondary{background:var(--surface);color:var(--text2);border:1px solid var(--border);}
  .btn.danger{background:var(--danger);}
  .btn.small{padding:5px 11px;font-size:12.5px;}
  input[type=text],input[type=email],input[type=password],input[type=url],select,textarea{
    width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:13.5px;font-family:inherit;color:var(--text);background:#fff;
  }
  label{display:block;font-size:12.5px;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:5px;}
  .field{margin-bottom:16px;}
  .badge{display:inline-block;font-size:11.5px;font-weight:700;padding:3px 9px;border-radius:999px;text-transform:uppercase;letter-spacing:0.03em;}
  .badge.new{background:rgba(26,86,219,0.1);color:var(--blue);}
  .badge.contacted{background:rgba(217,119,6,0.1);color:#d97706;}
  .badge.won{background:rgba(13,148,136,0.12);color:var(--teal);}
  .badge.lost{background:rgba(220,38,38,0.1);color:var(--danger);}
  .badge.draft{background:var(--bg);color:var(--text3);}
  .badge.published{background:rgba(13,148,136,0.12);color:var(--teal);}
  .alert{padding:12px 16px;border-radius:8px;font-size:13.5px;margin-bottom:16px;}
  .alert.error{background:rgba(220,38,38,0.08);color:var(--danger);border:1px solid rgba(220,38,38,0.2);}
  .alert.success{background:rgba(13,148,136,0.08);color:var(--teal);border:1px solid rgba(13,148,136,0.2);}
  .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
  .muted{color:var(--text3);font-size:13px;}
</style>
</head>
<body>
<div class="admin-shell">
  <aside class="admin-sidebar">
    <div class="brand"><img src="<?php echo e($site_root . '/assets/img/logo.png'); ?>" alt="Access Infra" style="height:34px;width:auto;display:block;"><span style="font-weight:400;font-size:12px;opacity:0.7;display:block;margin-top:4px;">Admin</span></div>
    <nav>
      <a href="<?php echo e(admin_url('index.php')); ?>" class="<?php echo $active==='dashboard'?'active':''; ?>">Dashboard</a>
      <a href="<?php echo e(admin_url('leads.php')); ?>" class="<?php echo $active==='leads'?'active':''; ?>">Leads</a>
      <a href="<?php echo e(admin_url('posts.php')); ?>" class="<?php echo $active==='posts'?'active':''; ?>">Blog Posts</a>
      <a href="<?php echo e(admin_url('authors.php')); ?>" class="<?php echo $active==='authors'?'active':''; ?>">Authors</a>
      <?php if ($me && (int)$me['is_admin']===1): ?>
      <a href="<?php echo e(admin_url('users.php')); ?>" class="<?php echo $active==='users'?'active':''; ?>">Admin Users</a>
      <?php endif; ?>
      <a href="<?php echo e(admin_url('account.php')); ?>" class="<?php echo $active==='account'?'active':''; ?>">My Account</a>
    </nav>
    <div class="foot">
      <?php if ($me): ?>Signed in as<br><strong style="color:#fff;"><?php echo e($me['email']); ?></strong><br><?php endif; ?>
      <a href="<?php echo e(admin_url('logout.php')); ?>">Log out</a>
    </div>
  </aside>
  <main class="admin-main">
