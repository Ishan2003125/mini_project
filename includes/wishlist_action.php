<?php
/**
 * VR Fashion - Wishlist Action Endpoint
 * File: includes/wishlist_action.php
 * Purpose: JSON API endpoint handling asynchronous Wishlist toggles via fetch().
 */

header('Content-Type: application/json');
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

if (!is_logged_in()) {
    echo json_encode([
        'success'      => false,
        'require_login'=> true,
        'message'      => 'Please log in to add items to your wishlist.'
    ]);
    exit;
}

$user_id    = (int)$_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product selection.']);
    exit;
}

try {
    // Check if already in wishlist
    $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Remove from wishlist
        $del = $pdo->prepare("DELETE FROM wishlist WHERE id = ?");
        $del->execute([$existing['id']]);
        echo json_encode([
            'success'     => true,
            'in_wishlist' => false,
            'message'     => 'Removed from your wishlist.'
        ]);
    } else {
        // Add to wishlist
        $ins = $pdo->prepare("INSERT INTO wishlist (user_id, product_id, created_at) VALUES (?, ?, NOW())");
        $ins->execute([$user_id, $product_id]);
        echo json_encode([
            'success'     => true,
            'in_wishlist' => true,
            'message'     => 'Saved to your wishlist!'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

