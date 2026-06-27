<?php
require __DIR__ . '/includes/config.php';

// ── CSRF token ──────────────────────────────────────────────────────────────
if (empty($_SESSION['ai_contact_token'])) {
    $_SESSION['ai_contact_token'] = bin2hex(random_bytes(16));
}
$ai_contact_token = $_SESSION['ai_contact_token'];

// ── Form handler ────────────────────────────────────────────────────────────
$status   = isset($_GET['ai_status']) ? preg_replace('/[^a-z]/', '', $_GET['ai_status']) : '';
$old      = ['n' => '', 'e' => '', 'p' => '', 'o' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ai_contact_submit'])) {
    $token_ok = isset($_POST['ai_contact_token']) && hash_equals($_SESSION['ai_contact_token'] ?? '', $_POST['ai_contact_token']);

    $name    = trim($_POST['ai_name']    ?? '');
    $email   = trim($_POST['ai_email']   ?? '');
    $org     = trim($_POST['ai_org']     ?? '');
    $phone   = trim($_POST['ai_phone']   ?? '');
    $service = trim($_POST['ai_service'] ?? '');
    $message = trim($_POST['ai_message'] ?? '');

    if (!$token_ok || $name === '' || $email === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $qs = http_build_query(['ai_status' => 'error', 'n' => $name, 'e' => $email, 'p' => $phone, 'o' => $org]);
        header('Location: ' . url('contact.php') . '?' . $qs);
        exit;
    }

    // Best-effort: record the lead in the admin CRM. The contact form must keep
    // working over plain email even if the database is unavailable/unconfigured.
    try {
        require_once __DIR__ . '/lib/db.php';
        require_once __DIR__ . '/lib/crypto.php';
        db()->prepare(
            'INSERT INTO leads (id, source, name_enc, email_bi, email_enc, company_enc, phone_enc, service, message_enc, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
        )->execute([
            db_new_id(), 'contact_form',
            encrypt_value($name), blind_index($email), encrypt_value($email),
            encrypt_value($org), encrypt_value($phone), $service, encrypt_value($message),
        ]);
    } catch (Throwable $e) {
        error_log('lead insert failed: ' . $e->getMessage());
    }

    $subject = "New Enquiry from {$name} — Access Infra Website";
    $body  = "Name:             {$name}\n";
    $body .= "Email:            {$email}\n";
    $body .= "Phone/WhatsApp:   {$phone}\n";
    $body .= "Organisation:     {$org}\n";
    $body .= "Service Interest: {$service}\n\n";
    $body .= "Message:\n{$message}\n";
    $headers = "Content-Type: text/plain; charset=UTF-8\r\nReply-To: {$name} <{$email}>";

    $sent = @mail(ADMIN_EMAIL, $subject, $body, $headers);

    if ($sent) {
        $reply_subject = 'Thank you for reaching out — Access Infra Consulting';
        $reply_body    = "Dear {$name},\n\nThank you for contacting Access Infra. We have received your message and will get back to you as soon as possible.\n\nBest regards,\nAccess Infra Consulting\n" . ADMIN_EMAIL . "\n" . SITE_URL;
        @mail($email, $reply_subject, $reply_body, "Content-Type: text/plain; charset=UTF-8");
    }

    unset($_SESSION['ai_contact_token']);
    header('Location: ' . url('contact.php') . '?ai_status=' . ($sent ? 'sent' : 'mailfail'));
    exit;
}

if ($status === 'error') {
    $old['n'] = $_GET['n'] ?? '';
    $old['e'] = $_GET['e'] ?? '';
    $old['p'] = $_GET['p'] ?? '';
    $old['o'] = $_GET['o'] ?? '';
}

$page_title = 'Contact — Access Infra';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo e($page_title); ?></title>
<style>
  /* ── Reset / Base ── */
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --navy: #0a1628;
    --navy-mid: #0d1f3c;
    --blue: #1a3a6e;
    --accent: #2563eb;
    --teal: #0891b2;
    --gold: #f59e0b;
    --text: #e2e8f0;
    --text-muted: #94a3b8;
    --border: rgba(255,255,255,0.08);
    --card-bg: rgba(255,255,255,0.04);
    --card-hover: rgba(255,255,255,0.07);
    --radius: 12px;
    --font-main: 'Sora', 'Inter', sans-serif;
  }
  html { font-size: 16px; }
  body {
    font-family: var(--font-main);
    background: var(--navy);
    color: var(--text);
    min-height: 100vh;
  }

  /* ── Nav ── */
  .ai-nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
    background: rgba(10,22,40,0.95);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--border);
    padding: 0 2rem;
    display: flex; align-items: center; justify-content: space-between;
    height: 64px;
  }
  body.admin-bar .ai-nav { top: 32px; }
  .ai-nav-brand {
    font-size: 1.15rem; font-weight: 700; color: #fff;
    text-decoration: none; letter-spacing: -0.02em;
  }
  .ai-nav-brand span { color: var(--teal); }
  .ai-nav-links { display: flex; gap: 0.25rem; list-style: none; align-items: center; }
  .ai-nav-links a {
    color: var(--text-muted); text-decoration: none;
    padding: 0.45rem 0.85rem; border-radius: 6px;
    font-size: 0.875rem; font-weight: 500;
    transition: color 0.2s, background 0.2s;
  }
  .ai-nav-links a:hover { color: #fff; background: rgba(255,255,255,0.06); }
  .ai-nav-links a.active { color: var(--accent); font-weight: 700; background: rgba(37,99,235,0.12); }
  .ai-nav-toggle { display: none; background: none; border: none; cursor: pointer; color: #fff; }

  /* ── Page wrapper ── */
  .contact-page {
    padding-top: 64px;
    min-height: 100vh;
    background: linear-gradient(135deg, var(--navy) 0%, #0d2040 50%, #0a1628 100%);
  }
  body.admin-bar .contact-page { padding-top: 96px; }

  /* ── Hero strip ── */
  .contact-hero {
    padding: 4rem 2rem 3rem;
    text-align: center;
    background: linear-gradient(180deg, rgba(37,99,235,0.08) 0%, transparent 100%);
    border-bottom: 1px solid var(--border);
  }
  .contact-hero h1 {
    font-size: clamp(1.8rem, 4vw, 2.8rem);
    font-weight: 800; letter-spacing: -0.03em;
    background: linear-gradient(135deg, #fff 0%, var(--teal) 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text; margin-bottom: 0.75rem;
  }
  .contact-hero p { color: var(--text-muted); max-width: 520px; margin: 0 auto; font-size: 1rem; }

  /* ── Status banners ── */
  .ai-status-banner {
    max-width: 960px; margin: 2rem auto 0; padding: 1rem 1.5rem;
    border-radius: var(--radius); font-size: 0.95rem; display: flex;
    align-items: center; gap: 0.75rem;
  }
  .ai-status-banner.success {
    background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: #6ee7b7;
  }
  .ai-status-banner.error {
    background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5;
  }
  .ai-status-banner .icon { font-size: 1.25rem; flex-shrink: 0; }

  /* ── Main grid ── */
  .contact-grid {
    max-width: 960px; margin: 3rem auto; padding: 0 1.5rem;
    display: grid; grid-template-columns: 1fr 1.6fr; gap: 2.5rem;
  }
  @media (max-width: 700px) {
    .contact-grid { grid-template-columns: 1fr; }
  }

  /* ── Info column ── */
  .contact-info h2 {
    font-size: 1.35rem; font-weight: 700; color: #fff; margin-bottom: 0.5rem;
  }
  .contact-info .tagline { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem; line-height: 1.6; }
  .info-item {
    display: flex; gap: 1rem; align-items: flex-start;
    margin-bottom: 1.5rem;
  }
  .info-icon {
    width: 42px; height: 42px; border-radius: 10px; flex-shrink: 0;
    background: rgba(37,99,235,0.15); border: 1px solid rgba(37,99,235,0.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
  }
  .info-text { flex: 1; }
  .info-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); margin-bottom: 0.2rem; }
  .info-value { color: #fff; font-size: 0.95rem; font-weight: 500; text-decoration: none; }
  .info-value:hover { color: var(--teal); }

  .services-chips { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 2rem; }
  .chip {
    font-size: 0.75rem; padding: 0.35rem 0.75rem; border-radius: 20px;
    background: rgba(8,145,178,0.12); border: 1px solid rgba(8,145,178,0.25);
    color: var(--teal); font-weight: 500;
  }

  /* ── Form card ── */
  .contact-form-card {
    background: var(--card-bg); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 2rem;
    backdrop-filter: blur(8px);
  }
  .contact-form-card h3 { font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 1.5rem; }

  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
  @media (max-width: 500px) { .form-row { grid-template-columns: 1fr; } }

  .field { margin-bottom: 1.1rem; }
  .field label {
    display: block; font-size: 0.8rem; font-weight: 600;
    color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em;
    margin-bottom: 0.4rem;
  }
  .field label .req { color: var(--teal); margin-left: 2px; }
  .field input, .field select, .field textarea {
    width: 100%; background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px; padding: 0.7rem 0.9rem;
    color: #fff; font-family: var(--font-main); font-size: 0.9rem;
    outline: none; transition: border-color 0.2s, background 0.2s;
    -webkit-appearance: none;
  }
  .field input::placeholder, .field textarea::placeholder { color: rgba(255,255,255,0.25); }
  .field input:focus, .field select:focus, .field textarea:focus {
    border-color: var(--accent); background: rgba(37,99,235,0.06);
  }
  .field select { cursor: pointer; }
  .field select option { background: var(--navy-mid); color: #fff; }
  .field textarea { resize: vertical; min-height: 130px; }

  .submit-btn {
    width: 100%; padding: 0.9rem;
    background: linear-gradient(135deg, var(--accent) 0%, var(--teal) 100%);
    border: none; border-radius: 8px; color: #fff;
    font-family: var(--font-main); font-size: 0.95rem; font-weight: 700;
    cursor: pointer; letter-spacing: 0.02em;
    transition: opacity 0.2s, transform 0.15s;
  }
  .submit-btn:hover { opacity: 0.9; transform: translateY(-1px); }
  .submit-btn:active { transform: translateY(0); }

  .form-disclaimer {
    margin-top: 0.75rem; font-size: 0.75rem; color: var(--text-muted);
    text-align: center;
  }

  /* ── Footer ── */
  .ai-footer {
    background: var(--navy-mid); border-top: 1px solid var(--border);
    padding: 2rem 2rem 1.5rem; margin-top: 4rem;
    display: flex; flex-wrap: wrap; gap: 1rem;
    align-items: center; justify-content: space-between;
  }
  .ai-footer-brand { font-weight: 700; font-size: 0.95rem; }
  .ai-footer-brand span { color: var(--teal); }
  .ai-footer-links { display: flex; gap: 1.5rem; flex-wrap: wrap; }
  .ai-footer-links a { color: var(--text-muted); text-decoration: none; font-size: 0.85rem; }
  .ai-footer-links a:hover { color: #fff; }
  .ai-footer-copy { font-size: 0.78rem; color: var(--text-muted); width: 100%; }

  /* ── Mobile nav ── */
  @media (max-width: 640px) {
    .ai-nav-links { display: none; }
    .ai-nav-toggle { display: block; }
    .ai-nav-links.open {
      display: flex; flex-direction: column;
      position: fixed; top: 64px; left: 0; right: 0;
      background: rgba(10,22,40,0.98); padding: 1rem;
      border-bottom: 1px solid var(--border); gap: 0.25rem;
    }
    body.admin-bar .ai-nav-links.open { top: 96px; }
  }

  /* ── Scroll reveal ── */
  .reveal { opacity: 0; transform: translateY(24px); transition: opacity 0.6s ease-out, transform 0.6s ease-out; }
  .reveal.in-view { opacity: 1; transform: translateY(0); }
  @media (prefers-reduced-motion: reduce) {
    .reveal { opacity: 1; transform: none; transition: none; }
  }

  /* ── Contact context image ── */
  .contact-image {
    width: 100%; aspect-ratio: 4/3; border-radius: var(--radius);
    background: linear-gradient(135deg, rgba(37,99,235,0.18), rgba(8,145,178,0.18));
    border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 1.5rem;
  }
</style>
</head>
<body>

<nav class="ai-nav">
  <a class="ai-nav-brand" href="<?php echo e(url('index.php')); ?>">Access<span>Infra</span></a>
  <button class="ai-nav-toggle" id="navToggle" aria-label="Toggle navigation">&#9776;</button>
  <ul class="ai-nav-links" id="navLinks">
    <li><a href="<?php echo e(url('index.php')); ?>">Home</a></li>
    <li><a href="<?php echo e(url('about.php')); ?>">About Us</a></li>
    <li><a href="<?php echo e(url('government-departments.php')); ?>">Government Departments</a></li>
    <li><a href="<?php echo e(url('contact.php')); ?>" class="active">Contact</a></li>
  </ul>
</nav>

<div class="contact-page">

  <div class="contact-hero">
    <h1>Contact Us</h1>
    <p>Ready to partner with Access Infra? Reach out and we'll respond within one business day.</p>
  </div>

  <?php if ( $status === 'sent' ) : ?>
  <div class="ai-status-banner success" style="max-width:960px;margin:2rem auto 0;padding:1rem 1.5rem;">
    <span class="icon">✅</span>
    <div><strong>Message sent!</strong> Thank you for reaching out. We'll be in touch within one business day. Check your inbox for a confirmation copy.</div>
  </div>
  <?php elseif ( $status === 'error' ) : ?>
  <div class="ai-status-banner error" style="max-width:960px;margin:2rem auto 0;padding:1rem 1.5rem;">
    <span class="icon">⚠️</span>
    <div><strong>Please fill in all required fields</strong> and try again. If the problem persists, email us directly.</div>
  </div>
  <?php elseif ( $status === 'mailfail' ) : ?>
  <div class="ai-status-banner error" style="max-width:960px;margin:2rem auto 0;padding:1rem 1.5rem;">
    <span class="icon">❌</span>
    <div><strong>Server error — message not delivered.</strong> Please email us directly at <a href="mailto:contact@accessinfra.in" style="color:inherit;text-decoration:underline;">contact@accessinfra.in</a>.</div>
  </div>
  <?php endif; ?>

  <div class="contact-grid">

    <!-- Info column -->
    <div class="contact-info reveal">
      <div class="contact-image" role="img" aria-label="Illustration of the Access Infra team available for vendor and government enquiries">
        <span style="font-size:2.5rem;" aria-hidden="true">🤝</span>
        <!-- placeholder — replace with a real photo of the team or office -->
      </div>
      <h2>Get in Touch</h2>
      <p class="tagline">We help vendors, government departments, and institutions navigate infrastructure partnerships across Karnataka and Telangana.</p>

      <div class="info-item">
        <div class="info-icon">📧</div>
        <div class="info-text">
          <div class="info-label">Email</div>
          <a class="info-value" href="mailto:contact@accessinfra.in">contact@accessinfra.in</a>
        </div>
      </div>

      <div class="info-item">
        <div class="info-icon">🌐</div>
        <div class="info-text">
          <div class="info-label">Website</div>
          <a class="info-value" href="<?php echo e(url('index.php')); ?>"><?php echo e(preg_replace('#^https?://#', '', SITE_URL)); ?></a>
        </div>
      </div>

      <div class="info-item">
        <div class="info-icon">📍</div>
        <div class="info-text">
          <div class="info-label">Presence</div>
          <span class="info-value">Karnataka &amp; Telangana, India</span>
        </div>
      </div>

      <div class="info-item">
        <div class="info-icon">📱</div>
        <div class="info-text">
          <div class="info-label">WhatsApp / Phone</div>
          <span class="info-value">Available on request</span>
        </div>
      </div>

      <div class="services-chips">
        <span class="chip">Vendor Scouting</span>
        <span class="chip">Compliance &amp; Licensing</span>
        <span class="chip">Bid &amp; Tender Support</span>
        <span class="chip">Stakeholder Engagement</span>
        <span class="chip">Project Monitoring</span>
      </div>
    </div>

    <!-- Form column -->
    <div class="contact-form-card reveal">
      <h3>Send us a Message</h3>
      <form method="POST" action="<?php echo e(url('contact.php')); ?>">
        <input type="hidden" name="ai_contact_token" value="<?php echo e($ai_contact_token); ?>">
        <input type="hidden" name="ai_contact_submit" value="1">

        <div class="form-row">
          <div class="field">
            <label>Name <span class="req">*</span></label>
            <input type="text" name="ai_name" placeholder="Your full name" required
              value="<?php echo e($old['n']); ?>">
          </div>
          <div class="field">
            <label>Email <span class="req">*</span></label>
            <input type="email" name="ai_email" placeholder="you@example.com" required
              value="<?php echo e($old['e']); ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label>Phone / WhatsApp</label>
            <input type="tel" name="ai_phone" placeholder="+91 98765 43210"
              value="<?php echo e($old['p']); ?>">
          </div>
          <div class="field">
            <label>Organisation</label>
            <input type="text" name="ai_org" placeholder="Company or department name"
              value="<?php echo e($old['o']); ?>">
          </div>
        </div>

        <div class="field">
          <label>Service Interest</label>
          <select name="ai_service">
            <option value="">— Select a service —</option>
            <option value="Vendor Scouting &amp; Empanelment">Vendor Scouting &amp; Empanelment</option>
            <option value="Compliance &amp; Licensing Support">Compliance &amp; Licensing Support</option>
            <option value="Bid &amp; Tender Support">Bid &amp; Tender Support</option>
            <option value="Stakeholder Engagement">Stakeholder Engagement</option>
            <option value="Project Monitoring &amp; Reporting">Project Monitoring &amp; Reporting</option>
            <option value="General Enquiry">General Enquiry</option>
          </select>
        </div>

        <div class="field">
          <label>Message <span class="req">*</span></label>
          <textarea name="ai_message" placeholder="Tell us about your project or requirement…" required></textarea>
        </div>

        <button type="submit" class="submit-btn">Send Message ➜</button>
        <p class="form-disclaimer">We respond within one business day. Your information is never shared.</p>
      </form>
    </div>

  </div><!-- .contact-grid -->

  <footer class="ai-footer">
    <div class="ai-footer-brand">Access<span>Infra</span></div>
    <nav class="ai-footer-links">
      <a href="<?php echo e(url('index.php')); ?>">Home</a>
      <a href="<?php echo e(url('about.php')); ?>">About Us</a>
      <a href="<?php echo e(url('government-departments.php')); ?>">Government Departments</a>
      <a href="<?php echo e(url('contact.php')); ?>">Contact</a>
    </nav>
    <p class="ai-footer-copy">&copy; <?php echo date('Y'); ?> Access Infra. All rights reserved. Karnataka &amp; Telangana.</p>
  </footer>

</div><!-- .contact-page -->

<script>
(function() {
  var toggle = document.getElementById('navToggle');
  var links  = document.getElementById('navLinks');
  if (toggle && links) {
    toggle.addEventListener('click', function() {
      links.classList.toggle('open');
    });
  }

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var reveals = document.querySelectorAll('.reveal');
  if (reduceMotion || !('IntersectionObserver' in window)) {
    reveals.forEach(function(el) { el.classList.add('in-view'); });
    return;
  }
  var observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('in-view');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
  reveals.forEach(function(el) { observer.observe(el); });
})();
</script>

</body>
</html>
