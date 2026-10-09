<?php
/**
 * VR Fashion - User Logout
 * File: auth/logout.php
 * Purpose: Securely terminates the active user session, removes session cookies,
 *          and redirects the user to index.php with a confirmation message.
 */

require_once __DIR__ . '/../includes/functions.php';

// Unset all session variables
$_SESSION = [];

// Delete session cookie if set
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session on server
session_destroy();

// Start fresh session to pass confirmation flash message
session_start();
set_flash_message('info', 'You have been successfully signed out of VR Fashion. Come back soon!');

// Redirect to home page
redirect(url('index.php'));
