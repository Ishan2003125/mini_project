<?php
/**
 * VR Fashion - User Dashboard & Seller Management Center
 * File: dashboard.php
 * Purpose: Unified portal for both Buyers and Sellers.
 *          - Buyer View: Profile details, saved Wishlist, and Past Order History with status.
 *          - Seller View: Shop management, Add New Product form, My Products list with
 *                         delete/edit actions, and incoming customer orders.
 */

$page_title = "User Dashboard - Marketplace Portal";
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Access Control: Require login
require_login();

$user_id   = (int)$_SESSION['user_id'];
$user_role = $_SESSION['role'] ?? 'buyer';

// Fetch updated user profile
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$user_stmt->execute([$user_id]);
$current_user_data = $user_stmt->fetch();

if (!$current_user_data) {
    redirect(url('auth/logout.php'));
}

// --------------------------------------------------------------------
// Handle Seller Actions (Add Product, Delete Product, Update Order Status)
// --------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Action 1: Seller Adds New Product
    if ($action === 'add_product' && $user_role === 'seller') {
        $p_name     = trim($_POST['name'] ?? '');
        $p_category = trim($_POST['category'] ?? 'Men');
        $p_desc     = trim($_POST['description'] ?? '');
        $p_price    = max(1, (float)($_POST['price'] ?? 0));
        $p_sizes    = trim($_POST['sizes'] ?? 'S, M, L, XL');
        $p_colour   = trim($_POST['colour'] ?? 'Standard');
        $p_stock    = max(1, (int)($_POST['stock'] ?? 10));
        $p_image    = trim($_POST['image'] ?? 'images/product-1.svg');

        if (!empty($p_name) && !empty($p_desc) && $p_price > 0) {
            $ins = $pdo->prepare("
                INSERT INTO products (seller_id, name, category, description, price, sizes, colour, stock, image, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $ins->execute([$user_id, $p_name, $p_category, $p_desc, $p_price, $p_sizes, $p_colour, $p_stock, $p_image]);
            set_flash_message('success', 'New ready-made garment listed successfully in the marketplace!');
        } else {
            set_flash_message('danger', 'Please provide valid product details and price.');
        }
        redirect(url('dashboard.php#seller-products'));
    }

    // Action 2: Seller Deletes Own Product
    if ($action === 'delete_product' && $user_role === 'seller') {
        $product_id = (int)($_POST['product_id'] ?? 0);
        if ($product_id > 0) {
            $del = $pdo->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?");
            $del->execute([$product_id, $user_id]);
            set_flash_message('info', 'Product removed from marketplace listings.');
        }
        redirect(url('dashboard.php#seller-products'));
    }

    // Action 3: Seller Updates Incoming Order Status
    if ($action === 'update_order_status' && $user_role === 'seller') {
        $order_id   = (int)($_POST['order_id'] ?? 0);
        $new_status = $_POST['status'] ?? 'Pending';
        if (in_array($new_status, ['Pending', 'Shipped', 'Delivered', 'Cancelled'])) {
            $upd = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $upd->execute([$new_status, $order_id]);
            set_flash_message('success', "Order #{$order_id} status updated to {$new_status}.");
        }
        redirect(url('dashboard.php#seller-orders'));
    }

    // Action 4: Buyer / User Removes Wishlist Item
    if ($action === 'remove_wishlist') {
        $wishlist_id = (int)($_POST['wishlist_id'] ?? 0);
        if ($wishlist_id > 0) {
            $del_w = $pdo->prepare("DELETE FROM wishlist WHERE id = ? AND user_id = ?");
            $del_w->execute([$wishlist_id, $user_id]);
            set_flash_message('info', 'Item removed from your wishlist.');
        }
        redirect(url('dashboard.php#wishlist-section'));
    }
}

// --------------------------------------------------------------------
// Load Buyer Data (Wishlist & Order History)
// --------------------------------------------------------------------
// Wishlist Items
$wishlist_items = [];
try {
    $w_stmt = $pdo->prepare("
        SELECT w.id AS wishlist_id, w.created_at AS saved_at,
               p.id AS product_id, p.name, p.category, p.description, p.price, p.image, p.sizes,
               u.shop_name
        FROM wishlist w
        JOIN products p ON w.product_id = p.id
        JOIN users u ON p.seller_id = u.id
        WHERE w.user_id = ?
        ORDER BY w.created_at DESC
    ");
    $w_stmt->execute([$user_id]);
    $wishlist_items = $w_stmt->fetchAll();
} catch (PDOException $e) {
    $wishlist_items = [];
}

// User Past Orders
$user_orders = [];
try {
    $o_stmt = $pdo->prepare("
        SELECT * FROM orders 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $o_stmt->execute([$user_id]);
    $user_orders = $o_stmt->fetchAll();
} catch (PDOException $e) {
    $user_orders = [];
}

// Helper: fetch items for an order
function get_order_items($pdo, $order_id) {
    $oi_stmt = $pdo->prepare("
        SELECT oi.*, p.name, p.image 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ");
    $oi_stmt->execute([$order_id]);
    return $oi_stmt->fetchAll();
}

// --------------------------------------------------------------------
// Load Seller Data (if user is seller)
// --------------------------------------------------------------------
$seller_products = [];
$seller_incoming_orders = [];

if ($user_role === 'seller') {
    // Seller's listed products
    $sp_stmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY id DESC");
    $sp_stmt->execute([$user_id]);
    $seller_products = $sp_stmt->fetchAll();

    // Seller's incoming orders from customers
    $so_stmt = $pdo->prepare("
        SELECT DISTINCT o.id AS order_id, o.created_at, o.total, o.status, o.address, o.phone,
               u.username AS buyer_name, u.email AS buyer_email
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        JOIN users u ON o.user_id = u.id
        WHERE p.seller_id = ?
        ORDER BY o.created_at DESC
    ");
    $so_stmt->execute([$user_id]);
    $seller_incoming_orders = $so_stmt->fetchAll();
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- ====================================================================
     DASHBOARD PROFILE BANNER
     ==================================================================== -->
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <img src="<?= url('images/avatar-default.svg') ?>" alt="User Profile" width="80" height="80" class="rounded-circle border">
            </div>
            <div class="col">
                <span class="badge <?= $user_role === 'seller' ? 'bg-primary' : 'bg-secondary' ?> text-uppercase small mb-1">
                    <?= $user_role === 'seller' ? 'Verified Seller Account' : 'Verified Buyer Account' ?>
                </span>
                <h2 class="fw-bold text-dark mb-1">
                    Hello, <?= htmlspecialchars($current_user_data['username'], ENT_QUOTES, 'UTF-8') ?>
                </h2>
                <div class="small text-muted">
                    <?php if ($user_role === 'seller'): ?>
                        <i class="bi bi-shop text-primary me-1"></i> Shop: <strong><?= htmlspecialchars($current_user_data['shop_name'] ?? 'My Clothing Shop') ?></strong> | 
                    <?php endif; ?>
                    <i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($current_user_data['email']) ?> | 
                    <i class="bi bi-calendar-event me-1"></i> Member since <?= date('F d, Y', strtotime($current_user_data['created_at'])) ?>
                </div>
            </div>
            <div class="col-md-auto d-flex gap-2">
                <a href="<?= url('cart.php') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-bag me-1 text-danger"></i> View Cart
                </a>
                <a href="<?= url('auth/logout.php') ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                    <i class="bi bi-box-arrow-right me-1"></i> Log Out
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ====================================================================
     DASHBOARD NAVIGATION TABS (BUYER & SELLER VIEWS)
     ==================================================================== -->
<section class="py-5">
    <div class="container">
        <!-- Navigation Pills -->
        <ul class="nav nav-pills mb-4 gap-2" id="dashboardTabs" role="tablist">
            <?php if ($user_role === 'seller'): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-4" id="seller-products-tab" data-bs-toggle="pill" data-bs-target="#seller-products" type="button" role="tab">
                        <i class="bi bi-tag me-1"></i> My Listed Products (<?= count($seller_products) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4" id="seller-orders-tab" data-bs-toggle="pill" data-bs-target="#seller-orders" type="button" role="tab">
                        <i class="bi bi-inbox me-1"></i> Incoming Customer Orders (<?= count($seller_incoming_orders) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4" id="seller-add-tab" data-bs-toggle="pill" data-bs-target="#seller-add" type="button" role="tab">
                        <i class="bi bi-plus-circle me-1"></i> Add New Product
                    </button>
                </li>
            <?php endif; ?>

            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $user_role === 'buyer' ? 'active' : '' ?> rounded-pill px-4" id="orders-tab" data-bs-toggle="pill" data-bs-target="#orders-section" type="button" role="tab">
                    <i class="bi bi-box-seam me-1"></i> My Order History (<?= count($user_orders) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4" id="wishlist-tab" data-bs-toggle="pill" data-bs-target="#wishlist-section" type="button" role="tab">
                    <i class="bi bi-heart me-1 text-danger"></i> My Wishlist (<?= count($wishlist_items) ?>)
                </button>
            </li>
        </ul>

        <!-- Tab Contents -->
        <div class="tab-content" id="dashboardTabsContent">

            <!-- ========================================================
                 TAB 1: SELLER PRODUCTS LIST (Seller view only)
                 ======================================================== -->
            <?php if ($user_role === 'seller'): ?>
                <div class="tab-pane fade show active" id="seller-products" role="tabpanel">
                    <div class="card border-0 shadow-sm p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">Garments Listed by <?= htmlspecialchars($current_user_data['shop_name']) ?></h5>
                            <button class="btn btn-primary-market btn-sm rounded-pill" onclick="document.getElementById('seller-add-tab').click();">
                                <i class="bi bi-plus-circle me-1"></i> List New Garment
                            </button>
                        </div>

                        <?php if (empty($seller_products)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-box2 display-4 d-block mb-2"></i>
                                <p>You have not listed any garments yet. Click "Add New Product" to list your first item!</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead class="table-light small text-uppercase">
                                        <tr>
                                            <th>Garment</th>
                                            <th>Category</th>
                                            <th>Price (LKR)</th>
                                            <th>Sizes</th>
                                            <th>Stock</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($seller_products as $sp): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <img src="<?= url($sp['image']) ?>" alt="<?= htmlspecialchars($sp['name']) ?>" width="45" height="45" class="rounded border">
                                                        <strong class="text-dark"><?= htmlspecialchars($sp['name']) ?></strong>
                                                    </div>
                                                </td>
                                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($sp['category']) ?></span></td>
                                                <td class="fw-bold text-danger"><?= format_lkr($sp['price']) ?></td>
                                                <td><small class="text-muted"><?= htmlspecialchars($sp['sizes']) ?></small></td>
                                                <td>
                                                    <span class="badge <?= $sp['stock'] > 5 ? 'bg-success' : 'bg-warning' ?> bg-opacity-10 text-dark">
                                                        <?= $sp['stock'] ?> in stock
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <form action="<?= url('dashboard.php') ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this garment from marketplace?');">
                                                        <input type="hidden" name="action" value="delete_product">
                                                        <input type="hidden" name="product_id" value="<?= $sp['id'] ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Product">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ========================================================
                     TAB 2: SELLER INCOMING ORDERS (Seller view only)
                     ======================================================== -->
                <div class="tab-pane fade" id="seller-orders" role="tabpanel">
                    <div class="card border-0 shadow-sm p-4">
                        <h5 class="fw-bold text-dark mb-3">Incoming Orders for Your Garments</h5>

                        <?php if (empty($seller_incoming_orders)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                <p>No incoming orders yet. Once buyers order your clothes, they will appear here.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead class="table-light small text-uppercase">
                                        <tr>
                                            <th>Order #</th>
                                            <th>Date</th>
                                            <th>Customer</th>
                                            <th>Delivery Address &amp; Phone</th>
                                            <th>Total (COD)</th>
                                            <th>Status</th>
                                            <th>Update Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($seller_incoming_orders as $s_ord): 
                                            $badge_class = match($s_ord['status']) {
                                                'Delivered' => 'bg-success',
                                                'Shipped'   => 'bg-primary',
                                                'Cancelled' => 'bg-danger',
                                                default     => 'bg-warning text-dark'
                                            };
                                        ?>
                                            <tr>
                                                <td><strong>#<?= $s_ord['order_id'] ?></strong></td>
                                                <td class="small text-muted"><?= date('M d, Y', strtotime($s_ord['created_at'])) ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($s_ord['buyer_name']) ?></strong><br>
                                                    <small class="text-muted"><?= htmlspecialchars($s_ord['buyer_email']) ?></small>
                                                </td>
                                                <td class="small">
                                                    <div><?= nl2br(htmlspecialchars($s_ord['address'])) ?></div>
                                                    <div><i class="bi bi-telephone text-primary me-1"></i><strong><?= htmlspecialchars($s_ord['phone']) ?></strong></div>
                                                </td>
                                                <td class="fw-bold text-danger"><?= format_lkr($s_ord['total']) ?></td>
                                                <td><span class="badge <?= $badge_class ?>"><?= $s_ord['status'] ?></span></td>
                                                <td>
                                                    <form action="<?= url('dashboard.php') ?>" method="POST" class="d-flex align-items-center gap-1">
                                                        <input type="hidden" name="action" value="update_order_status">
                                                        <input type="hidden" name="order_id" value="<?= $s_ord['order_id'] ?>">
                                                        <select name="status" class="form-select form-select-sm" style="width: 120px;" onchange="this.form.submit()">
                                                            <option value="Pending" <?= $s_ord['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                            <option value="Shipped" <?= $s_ord['status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                                            <option value="Delivered" <?= $s_ord['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                                            <option value="Cancelled" <?= $s_ord['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                        </select>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ========================================================
                     TAB 3: ADD NEW PRODUCT FORM (Seller view only)
                     ======================================================== -->
                <div class="tab-pane fade" id="seller-add" role="tabpanel">
                    <div class="card border-0 shadow-sm p-4">
                        <h5 class="fw-bold text-dark mb-3">List a New Ready-Made Garment</h5>
                        
                        <form action="<?= url('dashboard.php') ?>" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="action" value="add_product">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Garment Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Printed Cotton Summer Frock" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Category <span class="text-danger">*</span></label>
                                    <select name="category" class="form-select" required>
                                        <option value="Men">Men's Wear</option>
                                        <option value="Women">Women's Wear</option>
                                        <option value="Kids">Kids' Wear</option>
                                        <option value="Footwear">Footwear</option>
                                        <option value="Accessories">Accessories</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Price in LKR (Rs.) <span class="text-danger">*</span></label>
                                    <input type="number" name="price" class="form-control" placeholder="e.g. 2850" min="100" step="10" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Available Sizes <span class="text-danger">*</span></label>
                                    <input type="text" name="sizes" class="form-control" value="S, M, L, XL" placeholder="e.g. S, M, L, XL or Free Size" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Colour <span class="text-danger">*</span></label>
                                    <input type="text" name="colour" class="form-control" placeholder="e.g. Navy Blue, Coral, Floral Print" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Initial Stock Units <span class="text-danger">*</span></label>
                                    <input type="number" name="stock" class="form-control" value="25" min="1" required>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label small fw-bold">Select Artwork / Image Template</label>
                                    <select name="image" class="form-select">
                                        <option value="images/product-1.svg">Crewneck Cotton T-Shirt Artwork</option>
                                        <option value="images/product-2.svg">Artisanal Batik Linen Shirt Artwork</option>
                                        <option value="images/product-3.svg">Floral Summer Frock Artwork</option>
                                        <option value="images/product-4.svg">Traditional Handloom Sarong Artwork</option>
                                        <option value="images/product-5.svg">Slim Denim Jeans Artwork</option>
                                        <option value="images/product-6.svg">Cotton Kurti Top Artwork</option>
                                        <option value="images/product-7.svg">Kids Dungaree Set Artwork</option>
                                        <option value="images/product-8.svg">Casual Canvas Sneakers Artwork</option>
                                        <option value="images/product-9.svg">Linen Palazzo Pants Artwork</option>
                                        <option value="images/product-10.svg">School Sports Polo Artwork</option>
                                        <option value="images/product-11.svg">Jute Tote Bag Artwork</option>
                                        <option value="images/product-12.svg">Leather Belt Artwork</option>
                                        <option value="images/product-13.svg">Wedge Sandals Artwork</option>
                                        <option value="images/product-14.svg">Boys Summer Set Artwork</option>
                                        <option value="images/product-15.svg">Classic Polo Shirt Artwork</option>
                                        <option value="images/product-16.svg">Silk Blouse Artwork</option>
                                        <option value="images/product-17.svg">Baseball Cap Artwork</option>
                                        <option value="images/product-18.svg">Kids Kurta Set Artwork</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold">Garment Description <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Describe the fabric (e.g. 100% combed cotton), stitching quality, washing instructions, and fit." required></textarea>
                                </div>

                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary-market px-4 rounded-pill">
                                        <i class="bi bi-check2-circle me-1"></i> Publish Garment to Marketplace
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ========================================================
                 TAB 4: BUYER ORDER HISTORY (All users)
                 ======================================================== -->
            <div class="tab-pane fade <?= $user_role === 'buyer' ? 'show active' : '' ?>" id="orders-section" role="tabpanel">
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="fw-bold text-dark mb-3">Your Purchase History (Cash on Delivery)</h5>

                    <?php if (empty($user_orders)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-bag-check display-4 d-block mb-2 text-muted"></i>
                            <h6 class="fw-bold">No Orders Placed Yet</h6>
                            <p class="small">Once you order ready-made clothes via Cash on Delivery, your delivery status will be tracked here.</p>
                            <a href="<?= url('collections.php') ?>" class="btn btn-outline-market btn-sm rounded-pill px-3">
                                Start Shopping
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($user_orders as $ord): 
                                $items = get_order_items($pdo, $ord['id']);
                                $status_badge = match($ord['status']) {
                                    'Delivered' => 'bg-success',
                                    'Shipped'   => 'bg-primary',
                                    'Cancelled' => 'bg-danger',
                                    default     => 'bg-warning text-dark'
                                };
                            ?>
                                <div class="card border p-3 rounded-3">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom pb-2 mb-2">
                                        <div>
                                            <span class="fw-bold text-dark">Order #<?= $ord['id'] ?></span>
                                            <span class="small text-muted ms-2">&bull; Placed on <?= date('M d, Y h:i A', strtotime($ord['created_at'])) ?></span>
                                        </div>
                                        <div>
                                            <span class="badge <?= $status_badge ?> me-2"><?= $ord['status'] ?></span>
                                            <strong class="text-danger"><?= format_lkr($ord['total']) ?> (COD)</strong>
                                        </div>
                                    </div>

                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex flex-wrap gap-3">
                                                <?php foreach ($items as $it): ?>
                                                    <div class="d-flex align-items-center gap-2 small">
                                                        <img src="<?= url($it['image']) ?>" alt="<?= htmlspecialchars($it['name']) ?>" width="40" height="40" class="rounded border">
                                                        <div>
                                                            <div class="fw-semibold text-dark text-truncate" style="max-width: 180px;"><?= htmlspecialchars($it['name']) ?></div>
                                                            <span class="text-muted">Size: <?= htmlspecialchars($it['size']) ?> &times; <?= $it['quantity'] ?></span>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="col-md-4 text-md-end small text-muted border-start-md">
                                            <div><i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($ord['address']) ?></div>
                                            <div><i class="bi bi-telephone text-primary me-1"></i><?= htmlspecialchars($ord['phone']) ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ========================================================
                 TAB 5: BUYER WISHLIST (All users)
                 ======================================================== -->
            <div class="tab-pane fade" id="wishlist-section" role="tabpanel">
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="fw-bold text-dark mb-3">Saved Garments in Your Wishlist</h5>

                    <?php if (empty($wishlist_items)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-heart display-4 d-block mb-2 text-muted"></i>
                            <h6 class="fw-bold">Your Wishlist is Empty</h6>
                            <p class="small">Click the heart icon on any garment in the shop to save it here for later.</p>
                            <a href="<?= url('collections.php') ?>" class="btn btn-primary-market btn-sm rounded-pill px-3">
                                Explore Shop
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($wishlist_items as $item): 
                                $first_size = explode(',', $item['sizes'])[0] ?? 'M';
                            ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border rounded-3 p-3 d-flex flex-column">
                                        <div class="d-flex gap-3 align-items-center mb-2">
                                            <img src="<?= url($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="65" height="65" class="rounded border">
                                            <div class="overflow-hidden">
                                                <h6 class="fw-bold text-dark mb-1 text-truncate"><?= htmlspecialchars($item['name']) ?></h6>
                                                <small class="text-muted d-block"><i class="bi bi-shop me-1 text-primary"></i> <?= htmlspecialchars($item['shop_name']) ?></small>
                                                <span class="fw-bold text-danger"><?= format_lkr($item['price']) ?></span>
                                            </div>
                                        </div>

                                        <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                            <!-- Add to Cart -->
                                            <button type="button" 
                                                    class="btn btn-outline-market btn-sm rounded-pill btn-add-to-cart" 
                                                    data-id="<?= $item['product_id'] ?>" 
                                                    data-size="<?= trim($first_size) ?>">
                                                <i class="bi bi-bag-plus me-1"></i> Add to Cart
                                            </button>

                                            <!-- Remove from Wishlist Form -->
                                            <form action="<?= url('dashboard.php') ?>" method="POST" onsubmit="return confirm('Remove this garment from your wishlist?');">
                                                <input type="hidden" name="action" value="remove_wishlist">
                                                <input type="hidden" name="wishlist_id" value="<?= $item['wishlist_id'] ?>">
                                                <button type="submit" class="btn btn-link text-danger text-decoration-none btn-sm p-0">
                                                    <i class="bi bi-trash"></i> Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
