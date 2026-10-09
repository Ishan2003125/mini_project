<?php
/**
 * VR Fashion - About Us & Marketplace Story
 * File: about.php
 * Purpose: Presents the brand history, local manufacturing mission in Sri Lanka,
 *          core values, and an interactive FAQ accordion toggled via JavaScript.
 */

$page_title = "About Us - Sri Lanka's Ready-Made Clothing Marketplace";
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ====================================================================
     HERO & MARKETPLACE STORY
     ==================================================================== -->
<section class="py-5 bg-white border-bottom">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 reveal-on-scroll">
                <span class="badge bg-danger bg-opacity-10 text-danger text-uppercase px-3 py-2 mb-3 fw-bold">
                    Our Heritage &amp; Mission
                </span>
                <h1 class="display-5 fw-bold text-dark mb-3">
                    Bridging Sri Lankan Garment Makers Directly to Shoppers.
                </h1>
                <p class="lead text-muted mb-4">
                    Founded in Colombo, <strong class="text-dark">VR Fashion</strong> is an open online marketplace dedicated to ready-made clothing (redi kada) across Sri Lanka.
                </p>
                <p class="text-secondary leading-relaxed">
                    Sri Lanka is internationally renowned for textile manufacturing and handcrafted apparel. Yet, hundreds of talented local tailoring workshops, handloom weaving communities in the Central Province, and independent boutique shops lacked a unified online channel to reach buyers nationwide.
                </p>
                <p class="text-secondary leading-relaxed">
                    "VR" stands for <strong class="text-dark">Value &amp; Reliability</strong>. We provide an affordable, accessible digital storefront where local clothing makers list their collections, and customers across all 25 districts can browse, select sizes, and purchase with the peace of mind of Cash on Delivery.
                </p>
            </div>

            <div class="col-lg-6 text-center reveal-on-scroll delay-100">
                <div class="p-4 bg-light rounded-4 border shadow-sm">
                    <img src="<?= url('images/banner-2.svg') ?>" class="img-fluid rounded-3" alt="Artisanal Handloom and Batik Textiles in Sri Lanka">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====================================================================
     CORE VALUES
     ==================================================================== -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5 reveal-on-scroll">
            <span class="text-danger fw-bold small text-uppercase">What Drives Us</span>
            <h2 class="display-6 fw-bold mb-2">Our Marketplace Values</h2>
        </div>

        <div class="row g-4">
            <div class="col-md-4 reveal-on-scroll delay-100">
                <div class="card h-100 border-0 shadow-sm p-4 text-center">
                    <div class="fs-1 text-danger mb-3"><i class="bi bi-heart-pulse"></i></div>
                    <h5 class="fw-bold mb-2">Supporting Local Makers</h5>
                    <p class="text-muted small mb-0">We empower cottage industries, small apparel workshops, and boutique creators by keeping seller commission fees near zero.</p>
                </div>
            </div>
            <div class="col-md-4 reveal-on-scroll delay-200">
                <div class="card h-100 border-0 shadow-sm p-4 text-center">
                    <div class="fs-1 text-danger mb-3"><i class="bi bi-tag"></i></div>
                    <h5 class="fw-bold mb-2">Fair, Transparent Prices</h5>
                    <p class="text-muted small mb-0">No middleman markups. Customers get direct-from-workshop pricing on durable everyday cottons and festive silks.</p>
                </div>
            </div>
            <div class="col-md-4 reveal-on-scroll delay-300">
                <div class="card h-100 border-0 shadow-sm p-4 text-center">
                    <div class="fs-1 text-danger mb-3"><i class="bi bi-shield-check"></i></div>
                    <h5 class="fw-bold mb-2">Buyer Protection</h5>
                    <p class="text-muted small mb-0">Every transaction is safeguarded with Cash on Delivery and an easy 7-day size replacement guarantee.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====================================================================
     INTERACTIVE FAQ ACCORDION (Bootstrap 5 & Vanilla JS)
     ==================================================================== -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container">
        <div class="text-center mb-5 reveal-on-scroll">
            <span class="text-danger fw-bold small text-uppercase">Got Questions?</span>
            <h2 class="display-6 fw-bold mb-2">Frequently Asked Questions</h2>
            <p class="text-muted col-lg-6 mx-auto">
                Everything you need to know about purchasing, seller onboarding, and island-wide delivery.
            </p>
        </div>

        <div class="col-lg-8 mx-auto reveal-on-scroll">
            <div class="accordion" id="marketplaceFaqAccordion">
                <!-- FAQ 1 -->
                <div class="accordion-item border mb-3 rounded shadow-sm overflow-hidden">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            How does Cash on Delivery (COD) work?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#marketplaceFaqAccordion">
                        <div class="accordion-body text-muted small leading-relaxed">
                            When checking out on VR Fashion, simply enter your Sri Lankan delivery address and contact phone number. You do not need to pay anything online. Once the courier arrives at your doorstep, you inspect the parcel and pay the exact amount in cash.
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="accordion-item border mb-3 rounded shadow-sm overflow-hidden">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            How do I choose the correct garment size?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#marketplaceFaqAccordion">
                        <div class="accordion-body text-muted small leading-relaxed">
                            Each garment listing displays standard Sri Lankan sizes (S, M, L, XL, XXL) or waist inches (e.g. 32, 34 for jeans). If a garment doesn't fit quite right upon delivery, our 7-day return policy allows you to exchange for a different size easily.
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="accordion-item border mb-3 rounded shadow-sm overflow-hidden">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            How can I register my shop as a seller?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#marketplaceFaqAccordion">
                        <div class="accordion-body text-muted small leading-relaxed">
                            Click <a href="<?= url('auth/register.php?role=seller') ?>" class="text-danger fw-bold">Register as a Seller</a>, enter your details and shop name. You will immediately access the Seller Hub in your Dashboard to list ready-made garments, set your own prices, and manage incoming orders.
                        </div>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="accordion-item border mb-3 rounded shadow-sm overflow-hidden">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            How long does island-wide shipping take?
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#marketplaceFaqAccordion">
                        <div class="accordion-body text-muted small leading-relaxed">
                            Orders within Colombo and Gampaha are usually delivered within 1–2 business days. Outstation deliveries (Kandy, Galle, Kurunegala, Jaffna, etc.) take 2–4 business days via registered courier services.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====================================================================
     CALL TO ACTION
     ==================================================================== -->
<section class="py-5">
    <div class="container text-center reveal-on-scroll">
        <h3 class="fw-bold mb-3">Ready to Upgrade Your Wardrobe?</h3>
        <p class="text-muted col-md-6 mx-auto mb-4">
            Join thousands of happy shoppers enjoying high-quality Sri Lankan ready-made clothes delivered straight to their homes.
        </p>
        <a href="<?= url('collections.php') ?>" class="btn btn-primary-market btn-lg px-4 rounded-pill">
            <i class="bi bi-bag me-1"></i> Shop The Collections
        </a>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

