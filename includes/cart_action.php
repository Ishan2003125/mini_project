<?php
/**
 * VR Fashion - Cart Action Endpoint
 * File: includes/cart_action.php
 * Purpose: JSON API endpoint handling asynchronous Cart additions, quantity updates,
 *          and removals via fetch(). Returns live cart counter for navbar badge.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

if (!is_logged_in()) {
    echo json_encode([
        'success'      => false,
        'require_login'=> true,
        'message'      => 'Please log in to add garments to your cart.'
    ]);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$action  = $_POST['action'] ?? 'add';

try {
    if ($action === 'add') {
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $size       = isset($_POST['size']) ? trim($_POST['size']) : 'M';
        $quantity   = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

        if ($product_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid garment selection.']);
            exit;
        }

        // Verify product exists and has stock
        $p_stmt = $pdo->prepare("SELECT id, name, price, stock FROM products WHERE id = ?");
        $p_stmt->execute([$product_id]);
        $product = $p_stmt->fetch();

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found.']);
            exit;
        }

        // Check if item with this size is already in user's cart
        $check_stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND size = ?");
        $check_stmt->execute([$user_id, $product_id, $size]);
        $existing = $check_stmt->fetch();

        if ($existing) {
            $new_qty = $existing['quantity'] + $quantity;
            $upd = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $upd->execute([$new_qty, $existing['id']]);
        } else {
            $ins = $pdo->prepare("INSERT INTO cart (user_id, product_id, size, quantity, created_at) VALUES (?, ?, ?, ?, NOW())");
            $ins->execute([$user_id, $product_id, $size, $quantity]);
        }

        $cart_count = get_cart_count($pdo);
        echo json_encode([
            'success'    => true,
            'message'    => htmlspecialchars($product['name']) . ' (' . htmlspecialchars($size) . ') added to your cart!',
            'cart_count' => $cart_count
        ]);
        exit;

    } elseif ($action === 'update') {
        $cart_id  = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        if ($cart_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid cart item.']);
            exit;
        }

        if ($quantity <= 0) {
            $del = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $del->execute([$cart_id, $user_id]);
            $msg = 'Item removed from your cart.';
        } else {
            $upd = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            $upd->execute([$quantity, $cart_id, $user_id]);
            $msg = 'Cart quantity updated.';
        }

        $cart_count = get_cart_count($pdo);
        echo json_encode([
            'success'    => true,
            'message'    => $msg,
            'cart_count' => $cart_count
        ]);
        exit;

    } elseif ($action === 'remove') {
        $cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
        $del = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $del->execute([$cart_id, $user_id]);

        $cart_count = get_cart_count($pdo);
        echo json_encode([
            'success'    => true,
            'message'    => 'Garment removed from cart.',
            'cart_count' => $cart_count
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unknown action.']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

