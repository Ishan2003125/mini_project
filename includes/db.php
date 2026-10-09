<?php
/**
 * VR Fashion - Database Connection
 * File: includes/db.php
 * Purpose: Establishes a secure PDO connection to the MySQL database.
 *          Configured for standard XAMPP / WAMP defaults.
 */

// Database configuration constants
if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'vr_fashion');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
if (!defined('DB_PORT')) define('DB_PORT', getenv('DB_PORT') ?: '3306');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

$dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // If running in CLI / web, output a friendly developer message
    $error_message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    
    // In production/local development, render a styled diagnostic banner if connection fails
    if (php_sapi_name() !== 'cli') {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>VR Fashion - Database Connection Error</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { background-color: #0b0c10; color: #c5c6c7; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
                .error-box { max-width: 650px; margin: 80px auto; background: #1f2833; border: 1px solid #ff007f; border-radius: 12px; box-shadow: 0 0 25px rgba(255, 0, 127, 0.25); }
                .glow-text { color: #00f0ff; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="error-box p-4 p-md-5">
                    <h2 class="text-danger mb-3 fw-bold">Database Connection Notice</h2>
                    <p class="lead">Could not connect to the MySQL database <span class="glow-text"><?= DB_NAME ?></span>.</p>
                    <div class="alert alert-dark border border-secondary text-light small my-3">
                        <strong>MySQL Error:</strong> <?= $error_message ?>
                    </div>
                    <h5 class="text-white mt-4">Quick Setup Steps:</h5>
                    <ol class="small text-secondary-emphasis">
                        <li>Start <strong>Apache</strong> and <strong>MySQL</strong> in your XAMPP/WAMP control panel.</li>
                        <li>Open <a href="http://localhost/phpmyadmin" target="_blank" class="glow-text">phpMyAdmin</a> in your browser.</li>
                        <li>Create a new database named <code>vr_fashion</code>.</li>
                        <li>Click <strong>Import</strong> and upload the <code>database.sql</code> file included in this project.</li>
                        <li>If your MySQL root user has a password, update <code>DB_PASS</code> in <code>includes/db.php</code>.</li>
                    </ol>
                    <div class="mt-4">
                        <button onclick="location.reload()" class="btn btn-outline-info">Retry Connection</button>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    } else {
        die("Database Connection Error: " . $e->getMessage() . PHP_EOL);
    }
}

