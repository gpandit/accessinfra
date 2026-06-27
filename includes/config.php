<?php
/**
 * Access Infra — standalone PHP site config
 * No WordPress dependency. Works on any plain PHP host (Bluehost shared hosting included).
 */

// ── Site URL (auto-detected, works locally and on Bluehost) ────────────────
$scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
$dir     = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
define('SITE_URL', $scheme . '://' . $host . $dir);
define('ADMIN_EMAIL', 'contact@accessinfraconsulting.com');

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
