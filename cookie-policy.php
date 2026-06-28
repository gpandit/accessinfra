<?php
require __DIR__ . '/includes/config.php';
$page_title = 'Cookie Policy — Access Infra';
require __DIR__ . '/includes/legal-layout-top.php';
?>

<h1>Cookie Policy</h1>
<p class="updated">Last updated: <?php echo date('d F Y'); ?></p>

<p>This Cookie Policy explains how Access Infra Consulting uses cookies and similar storage technologies on accessinfraconsulting.com (the "Site"), and how you can control them.</p>

<h2>1. What Are Cookies?</h2>
<p>Cookies are small text files placed on your device by websites you visit. They are widely used to make websites function properly, remember your preferences, and provide a better browsing experience. We also use your browser's <code>localStorage</code> for similar purposes — it works like a cookie but is stored only on your device and isn't sent with every request.</p>

<h2>2. Cookies We Use</h2>
<table>
  <tr><th>Name</th><th>Purpose</th><th>Type</th><th>Duration</th></tr>
  <tr><td>ai-cookie-consent</td><td>Remembers whether you accepted or declined non-essential cookies</td><td>Necessary</td><td>180 days</td></tr>
  <tr><td>ai-theme</td><td>Remembers your light/dark theme preference</td><td>Functional (localStorage)</td><td>Until cleared</td></tr>
  <tr><td>ai-lang</td><td>Remembers your selected language (English/Kannada/Hindi/Telugu)</td><td>Functional (localStorage)</td><td>Until cleared</td></tr>
  <tr><td>ai-fs</td><td>Remembers your selected font-size preference</td><td>Functional (localStorage)</td><td>Until cleared</td></tr>
  <tr><td>PHPSESSID</td><td>Maintains your session for form security (CSRF protection) on the Contact page</td><td>Necessary</td><td>Session</td></tr>
</table>

<h2>3. Categories of Cookies</h2>
<ul>
  <li><strong>Necessary</strong> — required for the Site's core functionality (e.g. submitting the contact form securely). These cannot be disabled.</li>
  <li><strong>Functional</strong> — remember your preferences (theme, language, font size) to improve your experience. You can decline these; the Site will still work, but won't remember your settings between visits.</li>
</ul>
<p>We do not currently use third-party advertising or cross-site tracking cookies.</p>

<h2>4. Third-Party Resources</h2>
<p>Our Site loads fonts from Google Fonts, which may set cookies or collect IP-address-level data subject to <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Google's Privacy Policy</a>. Submitting the Contact form sends your message via our email delivery provider, SendPulse, subject to <a href="https://sendpulse.com/legal/privacy" target="_blank" rel="noopener">SendPulse's Privacy Policy</a>.</p>

<h2>5. Managing Your Preferences</h2>
<p>When you first visit our Site, a banner lets you accept all cookies or limit to necessary-only cookies. You can change your mind at any time by clearing your browser's site data for accessinfraconsulting.com, which will show the banner again on your next visit. You can also control or delete cookies through your browser's settings — see your browser's help documentation for instructions.</p>

<h2>6. Changes to This Policy</h2>
<p>We may update this Cookie Policy from time to time to reflect changes in the cookies we use or for legal reasons. The "Last updated" date above reflects the most recent revision.</p>

<h2>7. Contact Us</h2>
<p>Questions about this Cookie Policy can be sent to <a href="mailto:<?php echo e(ADMIN_EMAIL); ?>"><?php echo e(ADMIN_EMAIL); ?></a>.</p>

<?php require __DIR__ . '/includes/legal-layout-bottom.php'; ?>
