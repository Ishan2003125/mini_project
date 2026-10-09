<?php
/**
 * VR Fashion - Shop Catalog
 * File: collections.php (Display Name: Shop)
 * Purpose: Complete marketplace product catalog displaying 18+ ready-made garments.
 *          Implements real-time vanilla JS filtering by category, size, price range slider,
 *          live search by name, price sorting, and dynamic product detail modal.
 */

$page_title = "Shop Ready-Made Clothes - Online Marketplace";
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

// Fetch all ready-made products joined with seller shop name
$products = [];
try {
    $stmt = $pdo->query("
        SELECT p.*, u.shop_name 
        FROM products p 
        JOIN users u ON p.seller_id = u.id 
        ORDER BY p.id ASC
    ");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
}

// Fetch user wishlist IDs for active heart buttons
$user_wishlist_ids = [];
if (is_logged_in()) {
    try {
        $w_stmt = $pdo->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
        $w_stmt->execute([$_SESSION['user_id']]);
        $user_wishlist_ids = $w_stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        $user_wishlist_ids = [];
    }
}

// Preset search query from GET if user searched from home page
$get_search = sanitize($_GET['search'] ?? '');
?>

<!-- ====================================================================
     SHOP HEADER & BREADCRUMB
     ==================================================================== -->
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb small mb-1">
                        <li class="breadcrumb-item"><a href="<?= url('index.php') ?>" class="text-decoration-none text-muted">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Shop</li>
                    </ol>
                </nav>
                <h1 class="h2 fw-bold text-dark mb-0">Shop Ready-Made Clothing</h1>
            </div>
            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                <span class="badge bg-light text-dark border px-3 py-2 small" id="shopResultsCount">
                    Showing <?= count($products) ?> garments
                </span>
            </div>
        </div>
    </div>
</section>

<!-- ====================================================================
     SHOP FILTERS & PRODUCT GRID
     ==================================================================== -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Left Filter Sidebar -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm p-4 sticky-top" style="top: 85px; z-index: 10;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-funnel me-1 text-danger"></i> Filters</h5>
                        <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 text-muted" id="clearSearchBtn">
                            Reset
                        </button>
                    </div>

                    <!-- 1. Live Search Box -->
                    <div class="mb-4">
                        <label for="shopSearchInput" class="form-label small fw-bold text-dark">Search by Keyword:</label>
                        <div class="input-group input-group-sm">
                            <input type="text" 
                                   id="shopSearchInput" 
                                   class="form-control" 
                                   placeholder="e.g. Cotton, Batik, Frock, Kurti..." 
                                   value="<?= htmlspecialchars($get_search) ?>">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        </div>
                    </div>

                    <!-- 2. Category Filters -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark mb-2">Garment Category:</label>
                        <div class="d-flex flex-column gap-1">
                            <button type="button" class="btn btn-outline-secondary btn-sm text-start category-filter-btn active" data-category="all">
                                All Categories (<?= count($products) ?>)
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm text-start category-filter-btn" data-category="Men">
                                Men's Wear
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm text-start category-filter-btn" data-category="Women">
                                Women's Wear
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm text-start category-filter-btn" data-category="Kids">
                                Kids &amp; Toddlers
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm text-start category-filter-btn" data-category="Footwear">
                                Footwear
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm text-start category-filter-btn" data-category="Accessories">
                                Accessories &amp; Bags
                            </button>
                        </div>
                    </div>

                    <!-- 3. Size Filter Pills -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark mb-2">Available Size:</label>
                        <div class="d-flex flex-wrap gap-1">
                            <button type="button" class="btn btn-outline-secondary btn-sm size-filter-btn active" data-size="all">All</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm size-filter-btn" data-size="S">S</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm size-filter-btn" data-size="M">M</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm size-filter-btn" data-size="L">L</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm size-filter-btn" data-size="XL">XL</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm size-filter-btn" data-size="XXL">XXL</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm size-filter-btn" data-size="Free Size">Free Size</button>
                        </div>
                    </div>

                    <!-- 4. Price Range Slider -->
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="priceRangeSlider" class="form-label small fw-bold text-dark mb-0">Max Price (LKR):</label>
                            <span class="small fw-bold text-danger" id="priceRangeValue">Up to Rs. 6,000</span>
                        </div>
                        <input type="range" class="form-range" id="priceRangeSlider" min="1000" max="6000" step="100" value="6000">
                        <div class="d-flex justify-content-between text-muted small" style="font-size: 0.75rem;">
                            <span>Rs. 1,000</span>
                            <span>Rs. 6,000</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Products Grid & Top Sort Bar -->
            <div class="col-lg-9">
                <!-- Sorting & View Controls -->
                <div class="bg-white p-3 rounded shadow-sm border mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <span class="small text-muted">Filter Engine: <strong class="text-dark">Instant Vanilla JavaScript</strong></span>
                    
                    <div class="d-flex align-items-center gap-2">
                        <label for="shopSortSelect" class="small fw-bold text-dark mb-0 text-nowrap">Sort By:</label>
                        <select id="shopSortSelect" class="form-select form-select-sm" style="width: 200px;">
                            <option value="default">Featured / Default</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="newest">Newest Arrivals</option>
                        </select>
                    </div>
                </div>

                <!-- Empty State Notice (shown by JS when nothing matches) -->
                <div id="noResultsState" class="card border-0 shadow-sm p-5 text-center my-4" style="display: none;">
                    <i class="bi bi-search text-muted fs-1 mb-3"></i>
                    <h5 class="fw-bold mb-2">No Clothes Found</h5>
                    <p class="text-muted small mb-3">Try clearing your search keyword, adjusting the price slider, or selecting "All Categories".</p>
                    <div>
                        <button type="button" class="btn btn-outline-market btn-sm rounded-pill px-3" onclick="document.getElementById('clearSearchBtn').click();">
                            Reset Filters
                        </button>
                    </div>
                </div>

                <!-- Product Grid (18 Items) -->
                <div class="row g-4" id="shopProductsGrid">
                    <?php foreach ($products as $prod): 
                        $in_wishlist = in_array($prod['id'], $user_wishlist_ids);
                        $first_size = explode(',', $prod['sizes'])[0] ?? 'M';
                    ?>
                        <div class="col-sm-6 col-md-4 shop-product-col"
                             data-id="<?= $prod['id'] ?>"
                             data-name="<?= htmlspecialchars($prod['name'], ENT_QUOTES, 'UTF-8') ?>"
                             data-category="<?= htmlspecialchars($prod['category']) ?>"
                             data-sizes="<?= htmlspecialchars($prod['sizes']) ?>"
                             data-price="<?= (float)$prod['price'] ?>">
                            
                            <div class="product-card h-100 d-flex flex-column">
                                <div class="product-card-img-wrapper">
                                    <img src="<?= url($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" loading="lazy">
                                    <span class="product-category-badge"><?= htmlspecialchars($prod['category']) ?></span>

                                    <!-- Wishlist Button -->
                                    <button type="button" 
                                            class="product-wishlist-btn btn-toggle-wishlist <?= $in_wishlist ? 'active text-danger' : '' ?>" 
                                            data-id="<?= $prod['id'] ?>"
                                            title="<?= $in_wishlist ? 'Remove from wishlist' : 'Add to wishlist' ?>">
                                        <i class="bi <?= $in_wishlist ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                                    </button>
                                </div>

                                <div class="p-3 d-flex flex-column flex-grow-1">
                                    <span class="product-seller-label mb-1">
                                        <i class="bi bi-shop me-1"></i> <?= htmlspecialchars($prod['shop_name']) ?>
                                    </span>
                                    <h6 class="fw-bold mb-1 text-dark text-truncate" title="<?= htmlspecialchars($prod['name']) ?>">
                                        <?= htmlspecialchars($prod['name']) ?>
                                    </h6>
                                    <div class="small text-muted mb-3">Sizes: <span class="fw-semibold text-dark"><?= htmlspecialchars($prod['sizes']) ?></span></div>

                                    <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between">
                                        <span class="product-price text-danger"><?= format_lkr($prod['price']) ?></span>

                                        <div class="d-flex gap-1">
                                            <!-- Details Modal Trigger -->
                                            <button type="button" 
                                                    class="btn btn-outline-secondary btn-sm rounded-circle p-2" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#productDetailModal"
                                                    data-id="<?= $prod['id'] ?>"
                                                    data-name="<?= htmlspecialchars($prod['name'], ENT_QUOTES, 'UTF-8') ?>"
                                                    data-category="<?= htmlspecialchars($prod['category']) ?>"
                                                    data-seller="<?= htmlspecialchars($prod['shop_name']) ?>"
                                                    data-price="<?= format_lkr($prod['price']) ?>"
                                                    data-description="<?= htmlspecialchars($prod['description']) ?>"
                                                    data-image="<?= url($prod['image']) ?>"
                                                    data-sizes="<?= htmlspecialchars($prod['sizes']) ?>"
                                                    data-colour="<?= htmlspecialchars($prod['colour']) ?>"
                                                    data-stock="<?= $prod['stock'] > 0 ? 'In Stock (' . $prod['stock'] . ' available)' : 'Out of Stock' ?>"
                                                    title="View Garment Details">
                                                <i class="bi bi-eye"></i>
                                            </button>

                                            <!-- Direct Add to Cart Button -->
                                            <button type="button" 
                                                    class="btn btn-primary-market btn-sm rounded-circle p-2 btn-add-to-cart" 
                                                    data-id="<?= $prod['id'] ?>" 
                                                    data-size="<?= trim($first_size) ?>"
                                                    title="Add to Shopping Cart">
                                                <i class="bi bi-bag-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====================================================================
     SHARED PRODUCT DETAILS MODAL (Bootstrap 5 Modal)
     ==================================================================== -->
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="modalProductTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <div>
                    <span class="badge bg-danger bg-opacity-10 text-danger text-uppercase mb-1" id="modalProductCategory">Category</span>
                    <h5 class="modal-title fw-bold" id="modalProductTitle">Garment Name</h5>
                    <small class="text-muted"><i class="bi bi-shop text-primary me-1"></i> <span id="modalProductSeller">Seller Name</span></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-5 text-center">
                        <div class="p-3 bg-light rounded border">
                            <img src="<?= url('images/logo.svg') ?>" id="modalProductImage" class="img-fluid rounded" alt="Garment Preview">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="d-flex align-items-baseline gap-3 mb-2">
                            <h3 class="text-danger fw-bold mb-0" id="modalProductPrice">Rs. 0.00</h3>
                            <span class="badge bg-success bg-opacity-10 text-success" id="modalProductStock">In Stock</span>
                        </div>
                        <div class="small text-muted mb-3">Colour: <strong class="text-dark" id="modalProductColour">Standard</strong></div>
                        
                        <p class="text-muted leading-relaxed small mb-4" id="modalProductDesc">
                            Detailed garment description.
                        </p>

                        <!-- Add to Cart Form inside Modal -->
                        <form id="modalAddToCartForm">
                            <input type="hidden" id="modalProductId" value="">
                            
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label for="modalProductSizeSelect" class="form-label small fw-bold text-dark">Select Size:</label>
                                    <select class="form-select form-select-sm" id="modalProductSizeSelect" required></select>
                                </div>
                                <div class="col-6">
                                    <label for="modalProductQty" class="form-label small fw-bold text-dark">Quantity:</label>
                                    <input type="number" class="form-control form-select-sm" id="modalProductQty" value="1" min="1" max="10" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary-market w-100 rounded-pill py-2">
                                <i class="bi bi-bag-plus me-1"></i> Add to Shopping Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <small class="text-muted me-auto"><i class="bi bi-shield-check text-success me-1"></i> Cash on Delivery Available</small>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
