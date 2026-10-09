<?php
/**
 * VR Fashion - Core Helper Functions
 * File: includes/functions.php
 * Purpose: Provides essential utility functions: session handling, input
 *          sanitization, email & Sri Lankan phone validation, authentication guards,
 *          buyer/seller role authorization, cart counts, and currency formatting (LKR).
 */

// Start session securely if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Determine relative prefix depending on whether script runs from root or /auth/.
 *
 * @return string Relative path prefix, e.g. '' or '../'
 */
function get_path_prefix() {
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    if (strpos($script, '/auth/') !== false) {
        return '../';
    }
    return '';
}

/**
 * Resolve an internal asset or page link relative to the current script.
 *
 * @param string $path Target relative path from root, e.g. "css/style.css" or "auth/login.php"
 * @return string Resolved relative URL
 */
function url($path = '') {
    $prefix = get_path_prefix();
    return $prefix . ltrim($path, '/');
}

/**
 * Sanitize string input to prevent XSS attacks.
 *
 * @param mixed $data Raw input data
 * @return string Trimmed and HTML-encoded string
 */
function sanitize($data) {
    if (is_null($data)) {
        return '';
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate standard email format.
 *
 * @param string $email Input email
 * @return bool True if valid, false otherwise
 */
function validate_email($email) {
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate Sri Lankan mobile phone number format.
 * Accepts: 07XXXXXXXX, +947XXXXXXXX, 947XXXXXXXX
 *
 * @param string $phone
 * @return bool
 */
function validate_sri_lankan_phone($phone) {
    $cleaned = preg_replace('/[\s\-]/', '', trim($phone));
    return preg_match('/^(?:0|94|\+94)?7[0-9]{8}$/', $cleaned) === 1;
}

/**
 * Safe HTTP redirection helper.
 *
 * @param string $url Target URL to redirect to
 * @return void
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

/**
 * Check if a user is currently authenticated.
 *
 * @return bool True if user session exists, false otherwise
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if the currently logged-in user is a registered seller.
 *
 * @return bool
 */
function is_seller() {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === 'seller';
}

/**
 * Guard function requiring user authentication.
 *
 * @param string|null $redirect_url Custom redirect URL
 * @return void
 */
function require_login($redirect_url = null) {
    if (!is_logged_in()) {
        if ($redirect_url === null) {
            $redirect_url = url('auth/login.php');
        }
        set_flash_message('warning', 'Please sign in to access this page.');
        redirect($redirect_url);
    }
}

/**
 * Guard function requiring a seller role.
 *
 * @return void
 */
function require_seller() {
    require_login();
    if (!is_seller()) {
        set_flash_message('danger', 'Access denied. This portal is reserved for registered sellers.');
        redirect(url('dashboard.php'));
    }
}

/**
 * Set a session flash message.
 *
 * @param string $type Bootstrap alert type: 'success', 'danger', 'warning', 'info'
 * @param string $message Flash message text
 * @return void
 */
function set_flash_message($type, $message) {
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message
    ];
}

/**
 * Retrieve and clear the current flash message.
 *
 * @return array|null
 */
function get_flash_message() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Render flash message as a styled Bootstrap alert.
 *
 * @return void
 */
function display_flash_message() {
    $flash = get_flash_message();
    if ($flash) {
        $alert_type = htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8');
        $message    = htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8');
        echo '<div class="alert alert-' . $alert_type . ' alert-dismissible fade show market-alert shadow-sm" role="alert">';
        echo '  <i class="bi bi-info-circle-fill me-2"></i>';
        echo '  <span>' . $message . '</span>';
        echo '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
}

/**
 * Format price in Sri Lankan Rupees (LKR).
 *
 * @param float|string $price
 * @return string Formatted price, e.g., "Rs. 2,450.00"
 */
function format_lkr($price) {
    return 'Rs. ' . number_format((float)$price, 2);
}

/**
 * Format price alias.
 *
 * @param float|string $price
 * @return string
 */
function format_price($price) {
    return format_lkr($price);
}

/**
 * Retrieve current logged-in user details from session.
 *
 * @return array
 */
function current_user() {
    return [
        'id'        => $_SESSION['user_id'] ?? null,
        'username'  => $_SESSION['username'] ?? 'Customer',
        'email'     => $_SESSION['email'] ?? '',
        'role'      => $_SESSION['role'] ?? 'buyer',
        'shop_name' => $_SESSION['shop_name'] ?? null
    ];
}

/**
 * Retrieve current cart item count for user.
 *
 * @param PDO|null $pdo
 * @return int
 */
function get_cart_count($pdo = null) {
    if (!is_logged_in() || !$pdo) {
        return 0;
    }
    try {
        $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return (int)($stmt->fetchColumn() ?: 0);
    } catch (Exception $e) {
        return 0;
    }
}
