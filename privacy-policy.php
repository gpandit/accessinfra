<?php
require __DIR__ . '/includes/config.php';
$page_title = 'Privacy Policy — Access Infra';
require __DIR__ . '/includes/legal-layout-top.php';
?>

<h1>Privacy Policy</h1>
<p class="updated">Last updated: <?php echo date('d F Y'); ?></p>

<p>Access Infra Consulting ("Access Infra", "we", "us", or "our") respects your privacy. This Privacy Policy explains what information we collect through accessinfraconsulting.com (the "Site"), how we use it, and the choices you have.</p>

<h2>1. Information We Collect</h2>
<p>We collect information in the following ways:</p>
<ul>
  <li><strong>Information you provide directly</strong> — when you submit our Contact form, we collect your name, email address, phone/WhatsApp number, organisation name, service interest, and message content.</li>
  <li><strong>Automatically collected information</strong> — like most websites, our server logs may record your IP address, browser type, device information, pages visited, and timestamps for security and analytics purposes.</li>
  <li><strong>Cookies and local storage</strong> — we use cookies and browser local storage to remember your language preference, theme (light/dark), font-size setting, and cookie-consent choice. See our <a href="<?php echo e(url('cookie-policy.php')); ?>">Cookie Policy</a> for details.</li>
</ul>

<h2>2. How We Use Your Information</h2>
<ul>
  <li>To respond to enquiries submitted through our Contact form.</li>
  <li>To maintain a record of leads and business enquiries in our internal CRM.</li>
  <li>To improve our Site's content, usability, and performance.</li>
  <li>To comply with legal obligations and protect against fraudulent or unauthorised activity.</li>
</ul>

<h2>3. How We Protect Your Information</h2>
<p>Contact form submissions are transmitted over HTTPS. Personally identifiable fields stored in our lead-management database (name, email, phone, organisation, message) are encrypted at rest, and email addresses are stored with a blind index to allow lookups without exposing the underlying value in plain text. Access to this data is restricted to authorised Access Infra personnel.</p>

<h2>4. Sharing of Information</h2>
<p>We do not sell, rent, or trade your personal information. We may share information with:</p>
<ul>
  <li>Service providers who help us operate the Site or process enquiries (for example, our email delivery and contact-form processing provider, SendPulse).</li>
  <li>Government authorities, where required by applicable law.</li>
</ul>

<h2>5. Data Retention</h2>
<p>We retain enquiry and lead information for as long as necessary to respond to your request, maintain our business records, and comply with legal obligations. You may request deletion of your information at any time (see Section 7).</p>

<h2>6. Your Rights</h2>
<p>Depending on your jurisdiction, you may have the right to access, correct, or request deletion of your personal information, or to object to or restrict certain processing. To exercise any of these rights, contact us using the details below.</p>

<h2>7. Contact Us</h2>
<p>For questions about this Privacy Policy or to exercise your data rights, contact us at:</p>
<p>Email: <a href="mailto:<?php echo e(ADMIN_EMAIL); ?>"><?php echo e(ADMIN_EMAIL); ?></a></p>

<h2>8. Changes to This Policy</h2>
<p>We may update this Privacy Policy from time to time. The "Last updated" date at the top of this page reflects the most recent revision. Continued use of the Site after changes are posted constitutes acceptance of the updated policy.</p>

<?php require __DIR__ . '/includes/legal-layout-bottom.php'; ?>
