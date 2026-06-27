<?php
require __DIR__ . '/_bootstrap.php';
require_admin_page();
if (!has_perm('leads')) { http_response_code(403); exit('Forbidden'); }

if (empty($_SESSION['leads_csrf'])) $_SESSION['leads_csrf'] = bin2hex(random_bytes(16));

$notice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!hash_equals($_SESSION['leads_csrf'], $_POST['csrf'] ?? '')) {
    $notice = 'Session expired — please retry.';
  } else {
    $id = $_POST['id'] ?? '';
    $action = $_POST['action'] ?? '';
    if ($action === 'status' && $id) {
      $status = in_array($_POST['status'] ?? '', ['new','contacted','won','lost'], true) ? $_POST['status'] : 'new';
      db()->prepare('UPDATE leads SET status = ? WHERE id = ?')->execute([$status, $id]);
      $notice = 'Lead status updated.';
    } elseif ($action === 'archive' && $id) {
      db()->prepare('UPDATE leads SET archived = 1, archived_at = NOW() WHERE id = ?')->execute([$id]);
      $notice = 'Lead archived.';
    } elseif ($action === 'unarchive' && $id) {
      db()->prepare('UPDATE leads SET archived = 0, archived_at = NULL WHERE id = ?')->execute([$id]);
      $notice = 'Lead restored.';
    }
  }
}

$archived = !empty($_GET['archived']) ? 1 : 0;
$stmt = db()->prepare('SELECT * FROM leads WHERE archived = ? ORDER BY created_at DESC');
$stmt->execute([$archived]);
$rows = $stmt->fetchAll();

$page_title = 'Leads'; $active = 'leads';
require __DIR__ . '/_layout_top.php';
?>
<div class="topbar">
  <h1 style="margin-bottom:0;">Leads <span class="muted">(<?php echo count($rows); ?>)</span></h1>
  <div>
    <a href="<?php echo e(admin_url('leads.php')); ?>" class="btn small <?php echo !$archived?'':'secondary'; ?>">Active</a>
    <a href="<?php echo e(admin_url('leads.php?archived=1')); ?>" class="btn small <?php echo $archived?'':'secondary'; ?>">Archived</a>
  </div>
</div>
<?php if ($notice): ?><div class="alert success"><?php echo e($notice); ?></div><?php endif; ?>

<div class="card" style="padding:0;overflow-x:auto;">
<table>
<thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Company</th><th>Service</th><th>Message</th><th>Status</th><th></th></tr></thead>
<tbody>
<?php foreach ($rows as $l): ?>
  <tr>
    <td class="muted"><?php echo e(date('d M Y', strtotime($l['created_at']))); ?></td>
    <td><?php echo e(decrypt_value($l['name_enc'])); ?></td>
    <td><a href="mailto:<?php echo e(decrypt_value($l['email_enc'])); ?>"><?php echo e(decrypt_value($l['email_enc'])); ?></a></td>
    <td><?php echo e(decrypt_value($l['company_enc'])); ?></td>
    <td><?php echo e($l['service']); ?></td>
    <td style="max-width:280px;"><?php echo nl2br(e(decrypt_value($l['message_enc']))); ?></td>
    <td>
      <form method="POST" style="display:flex;gap:4px;align-items:center;">
        <input type="hidden" name="csrf" value="<?php echo e($_SESSION['leads_csrf']); ?>">
        <input type="hidden" name="id" value="<?php echo e($l['id']); ?>">
        <input type="hidden" name="action" value="status">
        <select name="status" onchange="this.form.submit()">
          <?php foreach (['new','contacted','won','lost'] as $s): ?>
            <option value="<?php echo $s; ?>" <?php echo $l['status']===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option>
          <?php endforeach; ?>
        </select>
      </form>
    </td>
    <td>
      <form method="POST">
        <input type="hidden" name="csrf" value="<?php echo e($_SESSION['leads_csrf']); ?>">
        <input type="hidden" name="id" value="<?php echo e($l['id']); ?>">
        <input type="hidden" name="action" value="<?php echo $archived?'unarchive':'archive'; ?>">
        <button type="submit" class="btn small secondary"><?php echo $archived?'Restore':'Archive'; ?></button>
      </form>
    </td>
  </tr>
<?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="8" class="muted" style="padding:20px;text-align:center;">No leads yet.</td></tr><?php endif; ?>
</tbody>
</table>
</div>
<?php require __DIR__ . '/_layout_bottom.php'; ?>
