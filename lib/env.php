<?php
/**
 * Minimal .env loader — no Composer dependency. Lines are KEY=VALUE;
 * blank lines and lines starting with # are ignored. Only sets the var if
 * not already present in the process environment.
 */
function load_env_file($path) {
  if (!is_readable($path)) return;
  foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
    [$key, $value] = array_map('trim', explode('=', $line, 2));
    $value = trim($value, "\"'");
    if (getenv($key) === false) {
      putenv("$key=$value");
      $_ENV[$key] = $value;
    }
  }
}
