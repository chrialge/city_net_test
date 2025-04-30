<?php
// check if the session is already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// destroy the session
session_destroy();
// redirect to the index page
header('Location: ../../index.php');
exit;
