<?php
/**
 * VR Fashion - Global Marketplace Header & Navbar
 * File: includes/header.php
 * Purpose: Shared HTML head, Google Fonts (Poppins & Inter), Bootstrap 5 CSS,
 *          clean e-commerce navigation bar, live cart count badge, and Toast notifications container.
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$current_page   = basename($_SERVER['PHP_SELF'] ?? '');
$is_user_logged = is_logged_in();
$user           = $is_user_logged ? current_user() : null;
$cart_count     = get_cart_count($pdo ?? null);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="VR Fashion - Sri Lanka's Online Marketplace for Ready-Made Clothing, Batik Shirts, Dresses, Sarongs and Kids Wear.">
    <meta name="keywords" content="ready-made clothing, redi kada, Sri Lanka fashion marketplace, cotton t-shirts, batik shirts, handloom sarongs, frocks">
    <meta name="author" content="VR Fashion Marketplace">

    <title><?= isset($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') . ' | VR Fashion' : 'VR Fashion | Ready-Made Clothing Marketplace Sri Lanka' ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= url('images/logo.svg') ?>">

    <!-- Google Fonts: Poppins (Brand & Headings) + Inter (Body Copy) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Modern Marketplace Stylesheet -->
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">
</head>
<body class="bg-light text-dark d-flex flex-column min-vh-100">

    <!-- Top Announcement Bar -->
    <div class="top-announcement-bar py-1 text-center small text-white">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="d-none d-md-inline"><i class="bi bi-truck me-1"></i> Island-wide Cash on Delivery Available across Sri Lanka</span>
            <span class="mx-auto mx-md-0 fw-semibold">🎉 Free Delivery on all orders over Rs. 5,000!</span>
            <span class="d-none d-md-inline"><i class="bi bi-telephone me-1"></i> Hotline: +94 11 234 5678</span>
        </div>
    </div>

    <!-- Navigation Bar -->
    <header class="sticky-top bg-white shadow-sm border-bottom">
        <nav class="navbar navbar-expand-lg navbar-light py-2" aria-label="Main Navigation">
            <div class="container">
                <!-- Brand Logo & Tagline -->
                <a class="navbar-brand d-flex align-items-center" href="<?= url('index.php') ?>">
                    <img src="<?= url('images/logo.svg') ?>" alt="VR Fashion Marketplace" height="46" class="d-inline-block">
                </a>

                <!-- Mobile Hamburger Toggler -->
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMarketplace" aria-controls="navbarMarketplace" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation Links & User Controls -->
                <div class="collapse navbar-collapse" id="navbarMarketplace">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                        <li class="nav-item">
                            <a class="nav-link market-nav-link <?= ($current_page === 'index.php' || $current_page === '') ? 'active' : '' ?>" href="<?= url('index.php') ?>">
                                Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link market-nav-link <?= ($current_page === 'collections.php') ? 'active' : '' ?>" href="<?= url('collections.php') ?>">
                                Shop
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link market-nav-link <?= ($current_page === 'about.php') ? 'active' : '' ?>" href="<?= url('about.php') ?>">
                                About Us
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link market-nav-link <?= ($current_page === 'contact.php') ? 'active' : '' ?>" href="<?= url('contact.php') ?>">
                                Contact
                            </a>
                        </li>
                    </ul>

                    <!-- User Actions: Cart, Wishlist, Auth -->
                    <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                        <!-- Shopping Cart Button with Live Counter Badge -->
                        <a href="<?= url('cart.php') ?>" class="btn btn-outline-secondary position-relative btn-sm px-3 rounded-pill" title="View Cart">
                            <i class="bi bi-bag me-1 text-danger"></i> Cart
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartCountBadge">
                                <?= $cart_count ?>
                            </span>
                        </a>

                        <?php if ($is_user_logged): ?>
                            <!-- Wishlist shortcut -->
                            <a href="<?= url('dashboard.php') ?>" class="btn btn-light btn-sm rounded-circle p-2 text-danger" title="My Wishlist">
                                <i class="bi bi-heart-fill"></i>
                            </a>

                            <!-- User Account Dropdown -->
                            <div class="dropdown">
                                <button class="btn btn-outline-dark btn-sm dropdown-toggle rounded-pill px-3 d-flex align-items-center gap-2" type="button" id="userMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle"></i>
                                    <span><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="badge <?= $user['role'] === 'seller' ? 'bg-primary' : 'bg-secondary' ?> text-uppercase small" style="font-size: 0.65rem;">
                                        <?= htmlspecialchars($user['role']) ?>
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border" aria-labelledby="userMenuBtn">
                                    <li class="dropdown-header text-muted small">
                                        <?= $user['role'] === 'seller' ? htmlspecialchars($user['shop_name'] ?? 'Seller Account') : 'Buyer Account' ?>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= url('dashboard.php') ?>">
                                            <i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= url('dashboard.php#orders-section') ?>">
                                            <i class="bi bi-box-seam me-2 text-success"></i> My Orders
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= url('cart.php') ?>">
                                            <i class="bi bi-bag-check me-2 text-danger"></i> Shopping Cart
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="<?= url('auth/logout.php') ?>">
                                            <i class="bi bi-box-arrow-right me-2"></i> Log Out
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <!-- Guest State: Login / Register -->
                            <a href="<?= url('auth/login.php') ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                                Sign In
                            </a>
                            <a href="<?= url('auth/register.php') ?>" class="btn btn-primary-market btn-sm rounded-pill px-3">
                                Register
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Global Flash Notification Alert Container -->
    <main class="flex-grow-1" id="main-content">
        <div class="container mt-3">
            <?php display_flash_message(); ?>
        </div>

    <!-- Bootstrap 5 Toast Container for Live Notifications -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;">
        <div id="liveMarketToast" class="toast align-items-center text-white bg-dark border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2" id="toastMessage">
                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                    <span>Notification</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
