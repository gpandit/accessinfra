<?php
/**
 * MySQL/MariaDB data layer (PDO). Schema is created automatically on first
 * connection — deployment only requires an empty database + creds in
 * lib/.env. Sensitive columns hold ciphertext (see lib/crypto.php).
 */

require_once __DIR__ . '/config.php';

function db() {
  static $pdo = null;
  if ($pdo !== null) return $pdo;

  $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
  $pdo = new PDO($dsn, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);
  db_migrate($pdo);
  return $pdo;
}

function db_migrate(PDO $pdo) {
  // Admin/staff user accounts. Email is the unique login id.
  $pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id CHAR(16) PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255),
    password_hash VARCHAR(255) NULL,
    totp_secret_enc TEXT NULL,
    mfa_enrolled TINYINT(1) NOT NULL DEFAULT 0,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    perm_leads TINYINT(1) NOT NULL DEFAULT 0,
    perm_content TINYINT(1) NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1,
    reset_token_hash CHAR(64) NULL,
    reset_expires DATETIME NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_users_reset (reset_token_hash)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  // Leads — encrypted PII columns are TEXT (base64 of nonce+ciphertext).
  $pdo->exec("CREATE TABLE IF NOT EXISTS leads (
    id CHAR(16) PRIMARY KEY,
    source VARCHAR(20) NOT NULL,
    name_enc TEXT,
    email_bi CHAR(64),
    email_enc TEXT,
    company_enc TEXT,
    phone_enc TEXT,
    service VARCHAR(255),
    message_enc TEXT,
    status VARCHAR(20) NOT NULL DEFAULT 'new',
    lead_group VARCHAR(120) NULL,
    tags TEXT NULL,
    ip VARCHAR(64),
    archived TINYINT(1) NOT NULL DEFAULT 0,
    archived_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_leads_email_bi (email_bi),
    INDEX idx_leads_created (created_at)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  // Reusable author profiles for blog posts.
  $pdo->exec("CREATE TABLE IF NOT EXISTS authors (
    id CHAR(16) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    bio TEXT,
    avatar_url VARCHAR(500) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  // Blog posts.
  $pdo->exec("CREATE TABLE IF NOT EXISTS posts (
    id CHAR(16) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    body MEDIUMTEXT,
    author_id CHAR(16) NULL,
    author_name VARCHAR(255) NULL,
    cover_image VARCHAR(500) NULL,
    tags TEXT,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(320) NULL,
    status VARCHAR(10) NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    published_at DATETIME NULL,
    INDEX idx_posts_status (status),
    INDEX idx_posts_published (published_at)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  // Reader comments — no login; name + email (encrypted) required.
  $pdo->exec("CREATE TABLE IF NOT EXISTS post_comments (
    id CHAR(16) PRIMARY KEY,
    post_id CHAR(16) NOT NULL,
    name VARCHAR(255) NOT NULL,
    email_enc TEXT,
    body TEXT NOT NULL,
    status VARCHAR(10) NOT NULL DEFAULT 'pending',
    ip VARCHAR(64),
    created_at DATETIME NOT NULL,
    INDEX idx_pc_post_status (post_id, status)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $pdo->exec("CREATE TABLE IF NOT EXISTS admin_settings (
    skey VARCHAR(64) PRIMARY KEY,
    sval TEXT,
    updated_at DATETIME NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  seed_admin($pdo);
}

/** Ensure the default admin account exists, seeded from ADMIN_ACCOUNT / ADMIN_PASSWORD_HASH. */
function seed_admin(PDO $pdo) {
  $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
  $stmt->execute([ADMIN_ACCOUNT]);
  if ((int) $stmt->fetchColumn() > 0) return;

  $ins = $pdo->prepare(
    'INSERT INTO users (id, email, name, password_hash, mfa_enrolled, is_admin, perm_leads, perm_content, active, created_at)
     VALUES (?, ?, ?, ?, 0, 1, 1, 1, 1, NOW())'
  );
  $ins->execute([
    db_new_id(), ADMIN_ACCOUNT, 'Administrator',
    defined('ADMIN_PASSWORD_HASH') ? ADMIN_PASSWORD_HASH : null,
  ]);
}

function setting_get($key) {
  $stmt = db()->prepare('SELECT sval FROM admin_settings WHERE skey = ?');
  $stmt->execute([$key]);
  $row = $stmt->fetch();
  return $row ? $row['sval'] : null;
}

function setting_set($key, $value) {
  $stmt = db()->prepare(
    'INSERT INTO admin_settings (skey, sval, updated_at) VALUES (?, ?, NOW())
     ON DUPLICATE KEY UPDATE sval = VALUES(sval), updated_at = NOW()'
  );
  $stmt->execute([$key, $value]);
}

function db_new_id() {
  return bin2hex(random_bytes(8));
}
