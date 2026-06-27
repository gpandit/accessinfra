<?php
/**
 * Central configuration. Secrets are read via getenv() so this file carries
 * none — set real env vars if you control the PHP process, or use lib/.env
 * on shared hosting (Bluehost) where you can't set process env vars (copy
 * lib/.env.example to lib/.env and fill it in; .env is gitignored).
 */

require_once __DIR__ . '/env.php';
load_env_file(__DIR__ . '/.env');

function env_value($key, $default = '') {
  $v = getenv($key);
  return ($v === false || $v === '') ? $default : $v;
}

// ════════ SITE ════════
define('SITE_NAME_ADMIN', env_value('SITE_NAME', 'Access Infra'));
define('DEBUG_ERRORS', env_value('DEBUG_ERRORS', 'false') === 'true');

// ════════ ADMIN AUTH ════════
define('ADMIN_ACCOUNT', env_value('ADMIN_ACCOUNT', 'admin@accessinfraconsulting.com'));
define('ADMIN_PASSWORD_HASH', env_value('ADMIN_PASSWORD_HASH'));
define('MFA_ISSUER', env_value('MFA_ISSUER', 'Access Infra Admin'));

// ════════ DATABASE (MySQL — Bluehost provides this on shared hosting) ════════
// Create an empty database + user in cPanel → MySQL Databases, then set
// DB_* below (env vars or lib/.env). Tables are created automatically.
define('DB_HOST', env_value('DB_HOST', 'localhost'));
define('DB_NAME', env_value('DB_NAME', 'accessinfra_admin'));
define('DB_USER', env_value('DB_USER', 'root'));
define('DB_PASS', env_value('DB_PASS', ''));
define('DB_CHARSET', env_value('DB_CHARSET', 'utf8mb4'));

// ════════ ENCRYPTION KEYS ════════
// WARNING: if ENCRYPTION_KEY is lost, encrypted lead/PII data is unrecoverable.
// Generate with: php -r "echo base64_encode(sodium_crypto_secretbox_keygen());"
define('ENCRYPTION_KEY', env_value('ENCRYPTION_KEY'));
define('BLIND_INDEX_KEY', env_value('BLIND_INDEX_KEY'));

// Uploaded post cover images — kept inside the web root under assets/ so they
// can be served directly (no blob storage, simpler than the original).
define('UPLOADS_DIR', __DIR__ . '/../assets/uploads/posts');
define('UPLOADS_URL_PATH', 'assets/uploads/posts');

// ════════ MAIL (admin invite / reset emails — uses PHP mail(), like contact.php) ════════
define('MAIL_FROM', env_value('MAIL_FROM', ADMIN_ACCOUNT));
define('MAIL_FROM_NAME', env_value('MAIL_FROM_NAME', SITE_NAME_ADMIN . ' Admin'));

// ════════ STARTUP VALIDATION ════════
(function () {
  $required = ['DB_NAME', 'DB_USER', 'ENCRYPTION_KEY', 'BLIND_INDEX_KEY', 'ADMIN_PASSWORD_HASH'];
  $missing = [];
  foreach ($required as $const) {
    if (!defined($const) || constant($const) === '') $missing[] = $const;
  }
  if (!in_array('ENCRYPTION_KEY', $missing, true)) {
    $k = base64_decode(ENCRYPTION_KEY, true);
    if ($k === false || strlen($k) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
      $missing[] = 'ENCRYPTION_KEY (invalid base64 or wrong length — expected 32 bytes)';
    }
  }
  if (!in_array('BLIND_INDEX_KEY', $missing, true)) {
    $k = base64_decode(BLIND_INDEX_KEY, true);
    if ($k === false || strlen($k) < 16) $missing[] = 'BLIND_INDEX_KEY (invalid base64)';
  }
  if ($missing) {
    $msg = 'CONFIG ERROR — missing/invalid: ' . implode(', ', $missing)
      . '. Set as env vars, or via lib/.env (copy lib/.env.example).';
    error_log($msg);
    http_response_code(500);
    if (DEBUG_ERRORS) { echo $msg; } else { echo 'Server configuration error.'; }
    exit;
  }
})();
