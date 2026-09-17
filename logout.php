<?php
/**
 * Valenti Atelier - Logout Handler
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

session_unset();
session_destroy();

session_start();
setFlash('info', 'You have been signed out safely.');
redirect('/index.php');
