<?php
/**
 * ===================================================
 * LOGOUT HANDLER
 * ===================================================
 */

session_start();

// Destroy session
session_destroy();

// Redirect ke halaman login
header("Location: ../index.php?page=login");
exit;

?>
