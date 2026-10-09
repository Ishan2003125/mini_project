<?php
/**
 * VR Fashion - Global Marketplace Footer
 * File: includes/footer.php
 * Purpose: Shared HTML footer with marketplace details, payment trust badges,
 *          island-wide delivery reassurance, back-to-top button, and scripts.
 */
?>
    </main>
    <!-- End Main Content -->

    <!-- E-Commerce Marketplace Footer -->
    <footer class="bg-white border-top mt-auto pt-5 pb-4 text-secondary">
        <div class="container">
            <!-- Trust Reassurance Row -->
            <div class="row g-4 pb-4 mb-4 border-bottom text-dark">
                <div class="col-6 col-md-3 d-flex align-items-center gap-3">
                    <div class="footer-trust-icon text-danger fs-2">
                        <i class="bi bi-truck"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Island-wide Delivery</h6>
                        <small class="text-muted">Fast dispatch in 2–4 days</small>
                    </div>
                </div>
                <div class="col-6 col-md-3 d-flex align-items-center gap-3">
                    <div class="footer-trust-icon text-danger fs-2">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Cash on Delivery</h6>
                        <small class="text-muted">Pay securely at your door</small>
                    </div>
                </div>
                <div class="col-6 col-md-3 d-flex align-items-center gap-3">
                    <div class="footer-trust-icon text-danger fs-2">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Easy 7-Day Returns</h6>
                        <small class="text-muted">Hassle-free size exchange</small>
                    </div>
                </div>
                <div class="col-6 col-md-3 d-flex align-items-center gap-3">
                    <div class="footer-trust-icon text-danger fs-2">
                        <i class="bi bi-patch-check"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Verified Local Sellers</h6>
                        <small class="text-muted">100% genuine garment makers</small>
                    </div>
                </div>
            </div>

            <!-- Footer Navigation & Details -->
            <div class="row g-4 mb-4">
                <!-- Col 1: Brand -->
                <div class="col-lg-4 col-md-6">
                    <img src="<?= url('images/logo.svg') ?>" alt="VR Fashion" height="42" class="mb-3">
                    <p class="small text-muted leading-relaxed">
                        VR Fashion is Sri Lanka's dedicated ready-made clothing (redi kada) online marketplace. We bridge local clothing artisans, garment factories, and boutique shops directly with shoppers island-wide.
                    </p>
                    <div class="small text-muted">
                        <i class="bi bi-geo-alt me-1 text-danger"></i> No. 128, Galle Road, Colombo 03, Sri Lanka<br>
                        <i class="bi bi-envelope me-1 text-danger"></i> support@vrfashion.lk<br>
                        <i class="bi bi-telephone me-1 text-danger"></i> +94 (11) 234-5678
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h6 class="text-dark fw-bold mb-3">Marketplace</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="<?= url('index.php') ?>" class="text-decoration-none text-muted hover-primary">Home</a></li>
                        <li class="mb-2"><a href="<?= url('collections.php') ?>" class="text-decoration-none text-muted hover-primary">Shop All Garments</a></li>
                        <li class="mb-2"><a href="<?= url('about.php') ?>" class="text-decoration-none text-muted hover-primary">About Our Story</a></li>
                        <li class="mb-2"><a href="<?= url('contact.php') ?>" class="text-decoration-none text-muted hover-primary">Contact Support</a></li>
                        <li class="mb-2"><a href="<?= url('auth/register.php') ?>" class="text-decoration-none text-muted hover-primary">Become a Seller</a></li>
                    </ul>
                </div>

                <!-- Col 3: Categories -->
                <div class="col-lg-3 col-md-6 col-6">
                    <h6 class="text-dark fw-bold mb-3">Garment Categories</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="<?= url('collections.php?category=Men') ?>" class="text-decoration-none text-muted hover-primary">Men's Shirts &amp; Sarongs</a></li>
                        <li class="mb-2"><a href="<?= url('collections.php?category=Women') ?>" class="text-decoration-none text-muted hover-primary">Women's Frocks &amp; Kurtis</a></li>
                        <li class="mb-2"><a href="<?= url('collections.php?category=Kids') ?>" class="text-decoration-none text-muted hover-primary">Kids &amp; Toddler Wear</a></li>
                        <li class="mb-2"><a href="<?= url('collections.php?category=Footwear') ?>" class="text-decoration-none text-muted hover-primary">Casual Footwear &amp; Sandals</a></li>
                        <li class="mb-2"><a href="<?= url('collections.php?category=Accessories') ?>" class="text-decoration-none text-muted hover-primary">Belts, Caps &amp; Jute Bags</a></li>
                    </ul>
                </div>

                <!-- Col 4: Newsletter & Social -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-dark fw-bold mb-3">Newsletter &amp; Deals</h6>
                    <p class="small text-muted">Subscribe to receive seasonal sale coupons and notifications for new local seller drops.</p>
                    <form onsubmit="event.preventDefault(); alert('Thank you for subscribing to VR Fashion updates!');" class="input-group input-group-sm mb-3">
                        <input type="email" class="form-control" placeholder="Enter your email" required>
                        <button class="btn btn-primary-market" type="submit">Join</button>
                    </form>
                    <div class="d-flex gap-3 social-icons fs-5">
                        <a href="https://facebook.com" target="_blank" rel="noopener" class="text-secondary hover-primary" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="https://instagram.com" target="_blank" rel="noopener" class="text-secondary hover-primary" title="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="https://tiktok.com" target="_blank" rel="noopener" class="text-secondary hover-primary" title="TikTok"><i class="bi bi-tiktok"></i></a>
                        <a href="https://whatsapp.com" target="_blank" rel="noopener" class="text-secondary hover-primary" title="WhatsApp Channel"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="row pt-3 border-top small text-muted align-items-center">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    &copy; <?= date('Y') ?> <strong class="text-dark">VR Fashion™</strong>. Sri Lanka Online Ready-Made Garments Marketplace.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <span>Cash on Delivery (COD) &bull; Bank Transfer &bull; Visa / Mastercard</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Back-to-Top Button -->
    <button type="button" class="btn btn-danger btn-market-scroll-top shadow" id="btnBackToTop" aria-label="Back to Top" title="Back to Top">
        <i class="bi bi-chevron-up"></i>
    </button>

    <!-- Bootstrap 5.3.3 Bundle JS with Popper (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Custom Marketplace JavaScript (All 6 Features) -->
    <script src="<?= url('js/main.js') ?>"></script>
</body>
</html>
