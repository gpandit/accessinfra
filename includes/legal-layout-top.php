<?php
/**
 * Shared layout for simple legal/content pages (Privacy Policy, Cookie Policy, etc.)
 * @param string $page_title
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-K8FRPFZD');</script>
<!-- End Google Tag Manager -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo e($page_title); ?></title>
<link rel="icon" type="image/x-icon" href="<?php echo url('favicon.ico'); ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo url('assets/img/favicon-32x32.png'); ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo url('assets/img/apple-touch-icon.png'); ?>">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Inter:wght@400;500;600&display=swap">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --navy: #0c1f3f; --navy-mid: #1d4ed8;
    --accent: #1a56db; --teal: #1e3a8a;
    --bg: #ffffff; --bg2: #f8fafc; --text: #0f172a; --text2: #334155; --text3: #64748b;
    --border: #e2e8f0;
  }
  html { scroll-behavior: smooth; }
  body { font-family:'Inter',sans-serif; color:var(--text); background:var(--bg); line-height:1.7; }
  h1, h2, h3 { font-family:'Sora',sans-serif; font-weight:800; color:var(--text); }

  .ai-nav { position: sticky; top: 0; left: 0; right: 0; z-index: 1000; background: rgba(255,255,255,0.95); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); }
  .ai-nav-inner { max-width: 1280px; margin: 0 auto; padding: 0 2rem; display: flex; align-items: center; justify-content: space-between; height: 64px; }
  .ai-nav-brand { display: flex; align-items: center; text-decoration: none; }
  .ai-nav-brand img { height: 38px; width: auto; display: block; }
  .ai-nav-links { display: flex; gap: 0.25rem; list-style: none; align-items: center; }
  .ai-nav-links a { color: var(--text2); text-decoration: none; padding: 0.45rem 0.85rem; border-radius: 6px; font-size: 0.875rem; font-weight: 500; transition: color 0.2s, background 0.2s; }
  .ai-nav-links a:hover { color: var(--navy); background: var(--bg2); }
  .ai-nav-toggle { display: none; background: none; border: none; cursor: pointer; color: var(--text); font-size: 22px; }
  @media (max-width: 640px) {
    .ai-nav-links { display: none; }
    .ai-nav-toggle { display: block; }
    .ai-nav-links.open { display: flex; flex-direction: column; position: fixed; top: 64px; left: 0; right: 0; background: #fff; padding: 1rem; border-bottom: 1px solid var(--border); gap: 0.25rem; }
  }

  main.legal-content { max-width: 760px; margin: 0 auto; padding: clamp(40px,6vw,72px) 24px 80px; }
  main.legal-content h1 { font-size: clamp(28px,4vw,42px); margin-bottom: 8px; }
  main.legal-content .updated { color: var(--text3); font-size: 13.5px; margin-bottom: 40px; }
  main.legal-content h2 { font-size: 21px; margin-top: 36px; margin-bottom: 12px; }
  main.legal-content p, main.legal-content li { color: var(--text2); font-size: 15px; margin-bottom: 14px; }
  main.legal-content ul { padding-left: 22px; margin-bottom: 14px; }
  main.legal-content a { color: var(--navy-mid); text-decoration: underline; }
  main.legal-content table { width: 100%; border-collapse: collapse; margin: 16px 0 24px; font-size: 13.5px; }
  main.legal-content th, main.legal-content td { border: 1px solid var(--border); padding: 8px 12px; text-align: left; color: var(--text2); }
  main.legal-content th { background: var(--bg2); font-weight: 700; color: var(--text); }

  .ai-footer { background: var(--bg2); border-top: 1px solid var(--border); padding: 2rem 2rem 1.5rem; display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; justify-content: space-between; }
  .ai-footer-brand img { height: 26px; width: auto; display: block; }
  .ai-footer-links { display: flex; gap: 1.25rem; flex-wrap: wrap; }
  .ai-footer-links a { color: var(--text3); text-decoration: none; font-size: 0.85rem; }
  .ai-footer-links a:hover { color: var(--navy); }
  .ai-footer-copy { font-size: 0.78rem; color: var(--text3); width: 100%; }
  .ai-footer-credit { font-size: 0.74rem; color: var(--text3); width: 100%; text-align: center; padding-top: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--border); }
  .ai-footer-credit a { color: var(--accent, #1a56db); text-decoration: none; font-weight: 600; }
</style>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K8FRPFZD"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<script>
  window.AI_PRIVACY_POLICY_URL = '<?php echo esc_js( url('privacy-policy.php') ); ?>';
  window.AI_COOKIE_POLICY_URL  = '<?php echo esc_js( url('cookie-policy.php') ); ?>';
</script>
<script src="<?php echo url('assets/js/cookie-consent.js'); ?>"></script>

<nav class="ai-nav">
  <div class="ai-nav-inner">
    <a class="ai-nav-brand" href="<?php echo e(url('index.php')); ?>"><img src="<?php echo e(url('assets/img/logo.png')); ?>" alt="Access Infra"></a>
    <button class="ai-nav-toggle" id="navToggle" aria-label="Toggle navigation">&#9776;</button>
    <ul class="ai-nav-links" id="navLinks">
      <li><a href="<?php echo e(url('index.php')); ?>">Home</a></li>
      <li><a href="<?php echo e(url('about.php#services')); ?>">Services</a></li>
      <li><a href="<?php echo e(url('about.php#smart-school')); ?>">Smart School</a></li>
      <li><a href="<?php echo e(url('about.php#case-studies')); ?>">Case Studies</a></li>
      <li><a href="<?php echo e(url('government-departments.php')); ?>">Government Departments</a></li>
      <li><a href="<?php echo e(url('about.php')); ?>">About Us</a></li>
      <li><a href="<?php echo e(url('contact.php')); ?>">Contact</a></li>
    </ul>
  </div>
</nav>

<main class="legal-content">
