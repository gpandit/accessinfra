<?php
/**
 * Minimal mail sender using PHP's built-in mail() — same approach as the
 * public contact form (contact.php). No SMTP library/dependency needed;
 * works on Bluehost out of the box via the server's local MTA.
 */
function send_admin_email($toEmail, $toName, $subject, $htmlBody, $altBody) {
  $headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . '>',
  ];
  $ok = @mail($toEmail, $subject, $htmlBody, implode("\r\n", $headers));
  if (!$ok) {
    error_log("send_admin_email failed for $toEmail: $subject");
  }
  return $ok;
}
