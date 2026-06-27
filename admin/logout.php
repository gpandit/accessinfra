<?php
require __DIR__ . '/_bootstrap.php';
reset_session();
header('Location: ' . admin_url('login.php'));
