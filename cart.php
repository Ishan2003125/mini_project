<?php
/**
 * VR Fashion - Shopping Cart & Cash on Delivery Checkout
 * File: cart.php
 * Purpose: Displays items in the user's cart, quantity adjustment, line-item removal,
 *          subtotal and shipping calculation, and Cash on Delivery order placement.
 *          Requires user authentication.
 */

$page_title = "Shopping Cart & Checkout";
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Access Control: Require login
require_login();

$user_id = (int)$_SESSION['user_id'];
$errors  = [];

// --------------------------------------------------------------------
// Handle Direct Form Actions (Update Qty, Remove Item, Place Order)
// --------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Action 1: Update Cart Item Quantity
    if ($action === 'update_qty') {
        $cart_id = (int)($_POST['cart_id'] ?? 0);
        $qty     = max(1, min(20, (int)($_POST['quantity'] ?? 1)));

        if ($cart_id > 0) {
            $upd = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            $upd->execute([$qty, $cart_id, $user_id]);
            set_flash_message('success', 'Cart quantity updated.');
        }
        redirect(url('cart.php'));
    }

    // Action 2: Remove Cart Item
    if ($action === 'remove_item') {
        $cart_id = (int)($_POST['cart_id'] ?? 0);
        if ($cart_id > 0) {
            $del = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $del->execute([$cart_id, $user_id]);
            set_flash_message('info', 'Item removed from your cart.');
        }
        redirect(url('cart.php'));
    }

    // Action 3: Place Order (Cash on Delivery)
    if ($action === 'place_order') {
        $address = trim($_POST['address'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');

        if (empty($address)) {
            $errors['address'] = 'Delivery address is required for dispatch.';
        }
        if (empty($phone)) {
            $errors['phone'] = 'Contact phone number is required.';
        } elseif (!validate_sri_lankan_phone($phone)) {
            $errors['phone'] = 'Please enter a valid Sri Lankan mobile number (e.g. 0771234567).';
        }

        // Fetch current items to calculate total and verify stock
        $c_stmt = $pdo->prepare("
            SELECT c.*, p.name, p.price, p.stock 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?
        ");
        $c_stmt->execute([$user_id]);
        $order_cart_items = $c_stmt->fetchAll();

        if (empty($order_cart_items)) {
            $errors['cart'] = 'Your cart is empty. Please add garments before checking out.';
        }

        if (empty($errors)) {
            try {
                $pdo->beginTransaction();

                $subtotal = 0;
                foreach ($order_cart_items as $item) {
                    $subtotal += ($item['price'] * $item['quantity']);
                }

                $delivery_fee = ($subtotal >= 5000) ? 0.00 : 350.00;
                $grand_total  = $subtotal + $delivery_fee;

                // Insert into orders table
                $ord_stmt = $pdo->prepare("
                    INSERT INTO orders (user_id, total, status, address, phone, created_at) 
                    VALUES (?, ?, 'Pending', ?, ?, NOW())
                ");
                $ord_stmt->execute([$user_id, $grand_total, $address, $phone]);
                $order_id = $pdo->lastInsertId();

                // Insert into order_items and update stock
                $item_stmt = $pdo->prepare("
                    INSERT INTO order_items (order_id, product_id, size, quantity, price) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stock_stmt = $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?");

                foreach ($order_cart_items as $item) {
                    $item_stmt->execute([
                        $order_id,
                        $item['product_id'],
                        $item['size'],
                        $item['quantity'],
                        $item['price']
                    ]);
                    $stock_stmt->execute([$item['quantity'], $item['product_id']]);
                }

                // Clear user's cart
                $clear_stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
                $clear_stmt->execute([$user_id]);

                $pdo->commit();

                set_flash_message('success', "Order #{$order_id} placed successfully! Cash on Delivery will be collected upon arrival.");
                redirect(url('dashboard.php#orders-section'));

            } catch (Exception $e) {
                $pdo->rollBack();
                set_flash_message('danger', 'Failed to place order: ' . htmlspecialchars($e->getMessage()));
            }
        }
    }
}

// --------------------------------------------------------------------
// Load Active Cart Items for Display
// --------------------------------------------------------------------
$cart_items = [];
try {
    $stmt = $pdo->prepare("
        SELECT c.id AS cart_id, c.size, c.quantity,
               p.id AS product_id, p.name, p.price, p.image, p.stock,
               u.shop_name
        FROM cart c
        JOIN products p ON c.product_id = p.id
        JOIN users u ON p.seller_id = u.id
        WHERE c.user_id = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();
} catch (PDOException $e) {
    $cart_items = [];
}

// Calculate Totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += ($item['price'] * $item['quantity']);
}
$shipping_fee = ($subtotal >= 5000 || $subtotal === 0) ? 0.00 : 350.00;
$grand_total  = $subtotal + $shipping_fee;

require_once __DIR__ . '/includes/header.php';
?>

<!-- ====================================================================
     CART HEADER
     ==================================================================== -->
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-1">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= url('collections.php') ?>" class="text-decoration-none text-muted">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cart &amp; Checkout</li>
            </ol>
        </nav>
        <h1 class="h2 fw-bold text-dark mb-0">Shopping Cart (<?= count($cart_items) ?> items)</h1>
    </div>
</section>

<!-- ====================================================================
     CART & CHECKOUT CONTENT
     ==================================================================== -->
<section class="py-5">
    <div class="container">
        <?php if (empty($cart_items)): ?>
            <!-- Empty Cart Notice -->
            <div class="card border-0 shadow-sm p-5 text-center my-4">
                <div class="display-3 text-muted mb-3"><i class="bi bi-bag-x"></i></div>
                <h4 class="fw-bold text-dark mb-2">Your Shopping Cart is Empty</h4>
                <p class="text-muted small col-md-6 mx-auto mb-4">
                    Looks like you haven't added any ready-made garments yet. Browse our marketplace of local sellers to find your perfect fit!
                </p>
                <div>
                    <a href="<?= url('collections.php') ?>" class="btn btn-primary-market px-4 rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i> Start Shopping Now
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <!-- Left Column: Items Table -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm p-3 p-md-4 mb-4">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light small text-uppercase">
                                    <tr>
                                        <th scope="col" style="min-width: 220px;">Garment</th>
                                        <th scope="col">Price</th>
                                        <th scope="col" style="width: 130px;">Quantity</th>
                                        <th scope="col">Total</th>
                                        <th scope="col" class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): 
                                        $line_total = $item['price'] * $item['quantity'];
                                    ?>
                                        <tr>
                                            <!-- Product image & details -->
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?= url($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="60" height="60" class="rounded border object-fit-cover">
                                                    <div>
                                                        <h6 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 180px;">
                                                            <?= htmlspecialchars($item['name']) ?>
                                                        </h6>
                                                        <small class="text-muted d-block">
                                                            Size: <strong class="text-dark"><?= htmlspecialchars($item['size']) ?></strong> | 
                                                            <span class="text-primary"><?= htmlspecialchars($item['shop_name']) ?></span>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Unit Price -->
                                            <td class="text-nowrap fw-semibold">
                                                <?= format_lkr($item['price']) ?>
                                            </td>

                                            <!-- Quantity Update Form -->
                                            <td>
                                                <form action="<?= url('cart.php') ?>" method="POST" class="d-flex align-items-center gap-1">
                                                    <input type="hidden" name="action" value="update_qty">
                                                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                                    <input type="number" 
                                                           name="quantity" 
                                                           value="<?= $item['quantity'] ?>" 
                                                           min="1" 
                                                           max="20" 
                                                           class="form-control form-control-sm text-center" 
                                                           style="width: 60px;" 
                                                           onchange="this.form.submit()">
                                                </form>
                                            </td>

                                            <!-- Line Total -->
                                            <td class="text-nowrap fw-bold text-danger">
                                                <?= format_lkr($line_total) ?>
                                            </td>

                                            <!-- Remove Button -->
                                            <td class="text-end">
                                                <form action="<?= url('cart.php') ?>" method="POST" onsubmit="return confirm('Remove this item from your cart?');">
                                                    <input type="hidden" name="action" value="remove_item">
                                                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm border-0" title="Remove item">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-3">
                            <a href="<?= url('collections.php') ?>" class="btn btn-link btn-sm text-decoration-none text-muted">
                                <i class="bi bi-arrow-left me-1"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Order Summary & Cash on Delivery Checkout Form -->
                <div class="col-lg-4">
                    <!-- Summary Card -->
                    <div class="card border-0 shadow-sm p-4 mb-4">
                        <h5 class="fw-bold text-dark mb-3">Order Summary</h5>
                        
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Subtotal (<?= count($cart_items) ?> items):</span>
                            <span class="fw-bold text-dark"><?= format_lkr($subtotal) ?></span>
                        </div>

                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Island-wide Delivery:</span>
                            <span class="fw-bold <?= $shipping_fee === 0.00 ? 'text-success' : 'text-dark' ?>">
                                <?= $shipping_fee === 0.00 ? 'FREE' : format_lkr($shipping_fee) ?>
                            </span>
                        </div>

                        <?php if ($subtotal < 5000): ?>
                            <div class="alert alert-warning py-2 px-3 small border-0 mb-3" style="font-size: 0.8rem;">
                                <i class="bi bi-info-circle me-1"></i> Add <strong><?= format_lkr(5000 - $subtotal) ?></strong> more for <strong>FREE Delivery</strong>!
                            </div>
                        <?php endif; ?>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between mb-4">
                            <span class="h6 fw-bold mb-0">Total Payable:</span>
                            <span class="h5 fw-bold text-danger mb-0"><?= format_lkr($grand_total) ?></span>
                        </div>

                        <!-- Cash on Delivery Checkout Form (JS Feature 3 validated) -->
                        <h6 class="fw-bold text-dark mb-2">
                            <i class="bi bi-cash-coin text-success me-1"></i> Cash on Delivery Details
                        </h6>

                        <form action="<?= url('cart.php') ?>" method="POST" class="needs-validation" id="checkoutForm" novalidate>
                            <input type="hidden" name="action" value="place_order">

                            <!-- Delivery Address -->
                            <div class="mb-3">
                                <label for="checkoutAddress" class="form-label small fw-bold text-dark">
                                    Delivery Street Address <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control form-control-sm <?= isset($errors['address']) ? 'is-invalid' : '' ?>" 
                                          id="checkoutAddress" 
                                          name="address" 
                                          rows="3" 
                                          placeholder="House No, Street, City, District" 
                                          required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                                <div class="invalid-feedback">
                                    <?= $errors['address'] ?? 'Please provide your full delivery address in Sri Lanka.' ?>
                                </div>
                            </div>

                            <!-- Sri Lankan Phone Number -->
                            <div class="mb-4">
                                <label for="checkoutPhone" class="form-label small fw-bold text-dark">
                                    Mobile Phone Number <span class="text-danger">*</span>
                                </label>
                                <input type="tel" 
                                       class="form-control form-control-sm <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                                       id="checkoutPhone" 
                                       name="phone" 
                                       placeholder="e.g. 0771234567" 
                                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" 
                                       required>
                                <div class="invalid-feedback">
                                    <?= $errors['phone'] ?? 'Enter a valid Sri Lankan mobile number (e.g. 07XXXXXXXX).' ?>
                                </div>
                                <div class="form-text small text-muted">Courier will call this number before arrival.</div>
                            </div>

                            <!-- Place Order Button -->
                            <button type="submit" class="btn btn-primary-market w-100 rounded-pill py-2">
                                <i class="bi bi-check2-circle me-1"></i> Place Order (Cash on Delivery)
                            </button>
                        </form>

                        <div class="mt-3 text-center">
                            <small class="text-muted"><i class="bi bi-shield-lock me-1"></i> 100% Secure Checkout &bull; No upfront payment required</small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

