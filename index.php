<?php
/**
 * VR Fashion - Home Page (Ready-Made Clothing Marketplace)
 * File: index.php
 * Purpose: Marketplace landing page featuring hero search, auto-playing promotional
 *          slider, category browse cards, featured ready-made garments from DB,
 *          marketplace value propositions, customer reviews, and newsletter.
 */

$page_title = "Home - Sri Lanka's Ready-Made Clothing Marketplace";
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

// Fetch 8 featured ready-made products from MySQL
$featured_products = [];
try {
    $stmt = $pdo->query("
        SELECT p.*, u.shop_name 
        FROM products p 
        JOIN users u ON p.seller_id = u.id 
        ORDER BY p.id ASC 
        LIMIT 8
    ");
    $featured_products = $stmt->fetchAll();
} catch (PDOException $e) {
    $featured_products = [];
}

// Fetch user wishlist IDs for active heart icons
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
?>

<!-- ====================================================================
     1. HERO SECTION WITH SEARCH & CTAS
     ==================================================================== -->
<section class="py-5 bg-white border-bottom">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 reveal-on-scroll">
                <span class="badge bg-danger bg-opacity-10 text-danger text-uppercase px-3 py-2 mb-3 fw-bold">
                    🇱🇰 Sri Lanka's Fashion Marketplace
                </span>
                <h1 class="display-4 fw-black text-dark mb-3">
                    Shop Ready-Made Fashion From <span class="text-danger">Local Sellers</span>.
                </h1>
                <p class="lead text-muted mb-4">
                    Discover quality cotton t-shirts, artisan batik linen shirts, traditional handloom sarongs, stylish frocks, and kids' wear directly from trusted makers across Sri Lanka.
                </p>

                <!-- Search Form on Hero -->
                <form action="<?= url('collections.php') ?>" method="GET" class="mb-4">
                    <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-4 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-0 ps-2" placeholder="Search cotton tees, frocks, linen shirts, sarongs...">
                        <button class="btn btn-primary-market px-4" type="submit">Search</button>
                    </div>
                </form>

                <!-- Action CTAs -->
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= url('collections.php') ?>" class="btn btn-primary-market btn-lg px-4 rounded-pill">
                        <i class="bi bi-bag-heart me-2"></i> Shop All Clothes
                    </a>
                    <a href="<?= url('auth/register.php?role=seller') ?>" class="btn btn-outline-dark btn-lg px-4 rounded-pill">
                        <i class="bi bi-shop me-2"></i> Become a Seller
                    </a>
                </div>
            </div>

            <!-- Hero Art Image -->
            <div class="col-lg-6 text-center reveal-on-scroll delay-100">
                <img src="<?= url('images/hero-banner.svg') ?>" class="img-fluid rounded-4 shadow-sm" alt="VR Fashion Ready-Made Clothing Collection">
            </div>
        </div>
    </div>
</section>

<!-- ====================================================================
     2. AUTO-PLAYING PROMOTIONAL BANNERS SLIDER (JS Feature 2)
     ==================================================================== -->
<section class="py-4">
    <div class="container">
        <div id="marketPromoCarousel" class="carousel slide market-carousel" data-bs-ride="carousel" tabindex="0" aria-label="Promotional Banners">
            <!-- Carousel Indicators -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#marketPromoCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#marketPromoCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#marketPromoCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>

            <!-- Slides -->
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="<?= url('images/banner-1.svg') ?>" alt="New Season Ready-Made Drop">
                </div>
                <div class="carousel-item">
                    <img src="<?= url('images/banner-2.svg') ?>" alt="Authentic Handloom and Batik Collection">
                </div>
                <div class="carousel-item">
                    <img src="<?= url('images/banner-3.svg') ?>" alt="Island-wide Free Delivery over Rs 5000">
                </div>
            </div>

            <!-- Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#marketPromoCarousel" data-bs-slide="prev" aria-label="Previous">
                <span class="carousel-control-prev-icon p-3 bg-dark rounded-circle bg-opacity-50" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#marketPromoCarousel" data-bs-slide="next" aria-label="Next">
                <span class="carousel-control-next-icon p-3 bg-dark rounded-circle bg-opacity-50" aria-hidden="true"></span>
            </button>
        </div>
    </div>
</section>

<!-- ====================================================================
     3. SHOP BY CATEGORY CARDS
     ==================================================================== -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 reveal-on-scroll">
            <div>
                <span class="text-danger fw-bold small text-uppercase">Curated Departments</span>
                <h2 class="display-6 fw-bold mb-0">Shop by Category</h2>
            </div>
            <a href="<?= url('collections.php') ?>" class="text-decoration-none fw-semibold text-danger">
                All Departments <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row g-3">
            <div class="col-6 col-md-4 col-lg reveal-on-scroll delay-100">
                <a href="<?= url('collections.php?category=Men') ?>" class="category-card">
                    <div class="category-icon"><i class="bi bi-gender-male"></i></div>
                    <h6 class="fw-bold mb-1">Men</h6>
                    <small class="text-muted">Shirts, Tees &amp; Sarongs</small>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg reveal-on-scroll delay-200">
                <a href="<?= url('collections.php?category=Women') ?>" class="category-card">
                    <div class="category-icon" style="background-color: #fdf2f8; color: #db2777;"><i class="bi bi-gender-female"></i></div>
                    <h6 class="fw-bold mb-1">Women</h6>
                    <small class="text-muted">Frocks, Kurtis &amp; Tops</small>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg reveal-on-scroll delay-300">
                <a href="<?= url('collections.php?category=Kids') ?>" class="category-card">
                    <div class="category-icon" style="background-color: #fefce8; color: #ca8a04;"><i class="bi bi-balloon"></i></div>
                    <h6 class="fw-bold mb-1">Kids</h6>
                    <small class="text-muted">Dungarees &amp; Playwear</small>
                </a>
            </div>
            <div class="col-6 col-md-6 col-lg reveal-on-scroll delay-400">
                <a href="<?= url('collections.php?category=Footwear') ?>" class="category-card">
                    <div class="category-icon" style="background-color: #f0fdf4; color: #16a34a;"><i class="bi bi-shoe-prints"></i></div>
                    <h6 class="fw-bold mb-1">Footwear</h6>
                    <small class="text-muted">Casual Shoes &amp; Sandals</small>
                </a>
            </div>
            <div class="col-12 col-md-6 col-lg reveal-on-scroll delay-400">
                <a href="<?= url('collections.php?category=Accessories') ?>" class="category-card">
                    <div class="category-icon" style="background-color: #f0f9ff; color: #0284c7;"><i class="bi bi-bag"></i></div>
                    <h6 class="fw-bold mb-1">Accessories</h6>
                    <small class="text-muted">Jute Totes, Caps &amp; Belts</small>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ====================================================================
     4. FEATURED / NEW ARRIVALS PRODUCT GRID (From MySQL)
     ==================================================================== -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 reveal-on-scroll">
            <div>
                <span class="text-danger fw-bold small text-uppercase">Trending Drops</span>
                <h2 class="display-6 fw-bold mb-0">Featured Ready-Made Clothes</h2>
            </div>
            <a href="<?= url('collections.php') ?>" class="btn btn-outline-market btn-sm rounded-pill px-3">
                Browse Full Shop (18 Garments) <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($featured_products as $index => $prod): 
                $in_wishlist = in_array($prod['id'], $user_wishlist_ids);
                $first_size = explode(',', $prod['sizes'])[0] ?? 'M';
            ?>
                <div class="col-sm-6 col-lg-3 reveal-on-scroll delay-<?= (($index % 4) + 1) * 100 ?>">
                    <div class="product-card h-100 d-flex flex-column">
                        <!-- Thumbnail Wrapper -->
                        <div class="product-card-img-wrapper">
                            <img src="<?= url($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                            <span class="product-category-badge"><?= htmlspecialchars($prod['category']) ?></span>

                            <!-- Wishlist Toggle Button -->
                            <button type="button" 
                                    class="product-wishlist-btn btn-toggle-wishlist <?= $in_wishlist ? 'active text-danger' : '' ?>" 
                                    data-id="<?= $prod['id'] ?>"
                                    title="<?= $in_wishlist ? 'Remove from wishlist' : 'Save to wishlist' ?>">
                                <i class="bi <?= $in_wishlist ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                            </button>
                        </div>

                        <!-- Card Body -->
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <span class="product-seller-label mb-1">
                                <i class="bi bi-shop me-1"></i> <?= htmlspecialchars($prod['shop_name']) ?>
                            </span>
                            <h6 class="fw-bold mb-1 text-dark text-truncate" title="<?= htmlspecialchars($prod['name']) ?>">
                                <?= htmlspecialchars($prod['name']) ?>
                            </h6>
                            <div class="small text-muted mb-2">Sizes: <span class="fw-semibold"><?= htmlspecialchars($prod['sizes']) ?></span></div>

                            <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between">
                                <span class="product-price text-danger"><?= format_lkr($prod['price']) ?></span>
                                
                                <div class="d-flex gap-1">
                                    <!-- View Details Modal Trigger -->
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
                                            data-stock="<?= $prod['stock'] > 0 ? 'In Stock (' . $prod['stock'] . ' units)' : 'Out of Stock' ?>"
                                            title="Quick View">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <!-- Direct Add To Cart Button -->
                                    <button type="button" 
                                            class="btn btn-primary-market btn-sm rounded-circle p-2 btn-add-to-cart" 
                                            data-id="<?= $prod['id'] ?>" 
                                            data-size="<?= trim($first_size) ?>"
                                            title="Add to Cart">
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
</section>

<!-- ====================================================================
     5. "WHY SHOP WITH VR FASHION" VALUE CARDS
     ==================================================================== -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5 reveal-on-scroll">
            <span class="text-danger fw-bold small text-uppercase">Direct From Sri Lanka's Garment Hubs</span>
            <h2 class="display-6 fw-bold mb-2">Why Shop with VR Fashion?</h2>
            <p class="text-muted col-lg-6 mx-auto">
                We make shopping for ready-made clothes simple, transparent, and affordable by connecting local fashion sellers directly to your doorstep.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3 reveal-on-scroll delay-100">
                <div class="card h-100 border-0 shadow-sm p-4 text-center">
                    <div class="fs-1 text-danger mb-3"><i class="bi bi-patch-check"></i></div>
                    <h5 class="fw-bold mb-2">Verified Local Sellers</h5>
                    <p class="text-muted small mb-0">Every boutique and garment maker is vetted for fabric quality, authentic stitching, and accurate sizing.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal-on-scroll delay-200">
                <div class="card h-100 border-0 shadow-sm p-4 text-center">
                    <div class="fs-1 text-danger mb-3"><i class="bi bi-cash-coin"></i></div>
                    <h5 class="fw-bold mb-2">Cash on Delivery (COD)</h5>
                    <p class="text-muted small mb-0">Inspect your package at your doorstep and pay safely in cash across all 25 districts of Sri Lanka.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal-on-scroll delay-300">
                <div class="card h-100 border-0 shadow-sm p-4 text-center">
                    <div class="fs-1 text-danger mb-3"><i class="bi bi-arrow-repeat"></i></div>
                    <h5 class="fw-bold mb-2">Hassle-Free 7-Day Exchange</h5>
                    <p class="text-muted small mb-0">Need a different size or fit? Easy size replacement and return coordination directly with sellers.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal-on-scroll delay-400">
                <div class="card h-100 border-0 shadow-sm p-4 text-center">
                    <div class="fs-1 text-danger mb-3"><i class="bi bi-truck"></i></div>
                    <h5 class="fw-bold mb-2">Island-wide Fast Shipping</h5>
                    <p class="text-muted small mb-0">Direct dispatch from local warehouses. 1-2 days within Western Province; 2-4 days outstation.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====================================================================
     6. CUSTOMER TESTIMONIALS SECTION
     ==================================================================== -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container">
        <div class="text-center mb-5 reveal-on-scroll">
            <span class="text-danger fw-bold small text-uppercase">Real Shopper Feedback</span>
            <h2 class="display-6 fw-bold mb-2">What Our Customers Say</h2>
        </div>

        <div class="row g-4">
            <div class="col-md-4 reveal-on-scroll delay-100">
                <div class="testimonial-card">
                    <div class="rating-stars mb-2">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="text-muted small mb-3">
                        "Ordered 3 cotton t-shirts and a batik shirt for Avurudu. The fabric is 100% genuine breathable cotton and delivery to Kandy took only 2 days! Excellent service."
                    </p>
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary-market rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-weight: bold;">K</div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark small">Kavinda Bandara</h6>
                            <small class="text-muted">Kandy</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 reveal-on-scroll delay-200">
                <div class="testimonial-card">
                    <div class="rating-stars mb-2">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="text-muted small mb-3">
                        "Finding genuine Sri Lankan handloom sarongs online used to be tough. VR Fashion connected me directly to Araliya Store. Superb quality and Cash on Delivery was seamless."
                    </p>
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-dark rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-weight: bold;">S</div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark small">Sahan Jayasinghe</h6>
                            <small class="text-muted">Colombo 05</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 reveal-on-scroll delay-300">
                <div class="testimonial-card">
                    <div class="rating-stars mb-2">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                    </div>
                    <p class="text-muted small mb-3">
                        "The floral wrap frock fits like a charm! True to size measurements and lightweight fabric. I love that I'm supporting local Sri Lankan garment sellers."
                    </p>
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-danger rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-weight: bold;">D</div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark small">Dilini Wijewardena</h6>
                            <small class="text-muted">Galle</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====================================================================
     7. BECOME A SELLER / NEWSLETTER BANNER
     ==================================================================== -->
<section class="py-5">
    <div class="container">
        <div class="card border-0 rounded-4 p-5 text-white shadow-sm reveal-on-scroll" style="background: linear-gradient(135deg, #0f172a, #1e293b);">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge bg-danger text-uppercase px-3 py-1 mb-2">Marketplace Partner Program</span>
                    <h2 class="display-6 fw-bold mb-2">Are You a Clothing Maker or Boutique Owner?</h2>
                    <p class="text-light opacity-75 lead mb-0">
                        Join VR Fashion to list your ready-made garments, reach thousands of buyers island-wide, and manage orders with zero listing fee.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?= url('auth/register.php?role=seller') ?>" class="btn btn-primary-market btn-lg px-4 rounded-pill">
                        Open Seller Account <i class="bi bi-arrow-right ms-1"></i>
                    </a>
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
                <small class="text-muted me-auto"><i class="bi bi-shield-check text-success me-1"></i> 7-Day Replacement Guarantee</small>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
