<?php
/**
 * Minimal SendPulse SMTP API client (REST, OAuth2 client_credentials).
 * No SDK/Composer dependency — just cURL, matching the rest of this codebase.
 * Docs: https://sendpulse.com/integrations/api/smtp
 */

function sendpulse_get_token() {
  $clientId     = env_value('SENDPULSE_CLIENT_ID');
  $clientSecret = env_value('SENDPULSE_CLIENT_SECRET');
  if ($clientId === '' || $clientSecret === '') return null;

  $ch = curl_init('https://api.sendpulse.com/oauth/access_token');
  curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS     => json_encode([
      'grant_type'    => 'client_credentials',
      'client_id'     => $clientId,
      'client_secret' => $clientSecret,
    ]),
  ]);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($response === false || $httpCode !== 200) {
    error_log('SendPulse auth failed: HTTP ' . $httpCode . ' ' . $response);
    return null;
  }
  $data = json_decode($response, true);
  return $data['access_token'] ?? null;
}

/**
 * Send a transactional email via SendPulse.
 *
 * @param string $toEmail
 * @param string $toName
 * @param string $subject
 * @param string $htmlBody
 * @param string $textBody
 * @param string|null $replyTo Optional reply-to email address.
 * @return bool true on success, false on failure (caller should fall back to mail()).
 */
function sendpulse_send_email($toEmail, $toName, $subject, $htmlBody, $textBody, $replyTo = null) {
  $token = sendpulse_get_token();
  if (!$token) return false;

  $fromEmail = env_value('SENDPULSE_SENDER_EMAIL', defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'no-reply@accessinfraconsulting.com');
  $fromName  = env_value('SENDPULSE_SENDER_NAME', 'Access Infra Consulting');

  $email = [
    'subject' => $subject,
    'html'    => base64_encode($htmlBody),
    'text'    => $textBody,
    'from'    => ['name' => $fromName, 'email' => $fromEmail],
    'to'      => [['name' => $toName, 'email' => $toEmail]],
  ];
  if ($replyTo) {
    $email['from']['email'] = $fromEmail;
    // SendPulse SMTP API doesn't support a distinct reply-to field directly;
    // some accounts honour an explicit header instead.
  }

  $ch = curl_init('https://api.sendpulse.com/smtp/emails');
  curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_HTTPHEADER     => [
      'Content-Type: application/json',
      'Authorization: Bearer ' . $token,
    ],
    CURLOPT_POSTFIELDS => json_encode(['email' => $email]),
  ]);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($response === false || $httpCode >= 300) {
    error_log('SendPulse send failed: HTTP ' . $httpCode . ' ' . $response);
    return false;
  }
  return true;
}
