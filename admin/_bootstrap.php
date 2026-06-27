<?php
/** Shared bootstrap for every admin page: auth/session, db, helpers. */
require __DIR__ . '/../lib/auth.php';
require __DIR__ . '/../lib/db.php';
require __DIR__ . '/../lib/crypto.php';
require __DIR__ . '/../lib/users.php';

if (!function_exists('e')) {
  function e($str) { return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8'); }
}
