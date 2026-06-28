<?php
/**
 * Access Infra — standalone PHP site config
 * No WordPress dependency. Works on any plain PHP host (Bluehost shared hosting included).
 */

// ── Environment (.env at project root — copy .env.example to .env and fill in) ──
require_once __DIR__ . '/../lib/env.php';
load_env_file(__DIR__ . '/../.env');

if (!function_exists('env_value')) {
    function env_value($key, $default = '') {
        $v = getenv($key);
        return ($v === false || $v === '') ? $default : $v;
    }
}

// ── Site URL (auto-detected, works locally and on Bluehost) ────────────────
$scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
$dir     = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
define('SITE_URL', $scheme . '://' . $host . $dir);
define('ADMIN_EMAIL', env_value('ADMIN_EMAIL', 'hello@accessinfra.co.in'));

// ── SendPulse (contact form delivery) ───────────────────────────────────────
define('SENDPULSE_CLIENT_ID', env_value('SENDPULSE_CLIENT_ID'));
define('SENDPULSE_CLIENT_SECRET', env_value('SENDPULSE_CLIENT_SECRET'));
define('SENDPULSE_SENDER_EMAIL', env_value('SENDPULSE_SENDER_EMAIL', ADMIN_EMAIL));
define('SENDPULSE_SENDER_NAME', env_value('SENDPULSE_SENDER_NAME', 'Access Infra Consulting'));

function url($path = '') {
    return SITE_URL . '/' . ltrim($path, '/');
}
function e($str) {
    return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
}
function esc_js($str) {
    return addslashes((string) $str);
}

session_start();
