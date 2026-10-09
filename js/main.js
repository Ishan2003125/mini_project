/**
 * VR Fashion - Master Client-Side JavaScript
 * File: js/main.js
 * 
 * Implements ALL Six Required Features for Ready-Made Clothing Marketplace:
 * 1. Dynamic Content Updates (Category filter, price range, size filter, live search, sort dropdown, FAQ accordion)
 * 2. Interactive Image Slider (Promotional banners auto-play, manual controls, indicators, pause-on-hover)
 * 3. Client-Side Form Validation (Email regex, password >= 8 chars, match, Sri Lankan phone 07XXXXXXXX validation)
 * 4. Smooth Scrolling & Back-to-Top Button (Scroll distance watcher, smooth page scroll)
 * 5. Event Handling (Card hovers, tooltips, product detail modal, fetch() Add-to-Cart & Wishlist with Toast and badge count)
 * 6. Custom Animations (IntersectionObserver reveal-on-scroll, CSS hover lifts)
 */

document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    // -------------------------------------------------------------------------
    // HELPER: BOOTSTRAP TOAST NOTIFIER
    // -------------------------------------------------------------------------
    function showToast(message, isSuccess = true) {
        const toastEl = document.getElementById('liveMarketToast');
        const toastBody = document.getElementById('toastMessage');
        if (!toastEl || !toastBody) {
            alert(message);
            return;
        }

        const iconHtml = isSuccess 
            ? '<i class="bi bi-check-circle-fill text-success fs-5"></i>' 
            : '<i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>';

        toastBody.innerHTML = `${iconHtml} <span>${message}</span>`;
        const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
        toast.show();
    }

    // -------------------------------------------------------------------------
    // FEATURE 1: DYNAMIC CONTENT UPDATES (Shop Filtering, Live Search, Sort, FAQ)
    // -------------------------------------------------------------------------
    function initShopFilters() {
        const productGrid = document.getElementById('shopProductsGrid');
        if (!productGrid) return; // Exit if not on shop page

        const searchInput    = document.getElementById('shopSearchInput');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const categoryBtns   = document.querySelectorAll('.category-filter-btn');
        const sizeBtns       = document.querySelectorAll('.size-filter-btn');
        const priceRange     = document.getElementById('priceRangeSlider');
        const priceLabel     = document.getElementById('priceRangeValue');
        const sortSelect     = document.getElementById('shopSortSelect');
        const resultsCountEl = document.getElementById('shopResultsCount');
        const noResultsAlert = document.getElementById('noResultsState');

        const productCards = Array.from(productGrid.querySelectorAll('.shop-product-col'));

        let activeCategory = 'all';
        let activeSize     = 'all';
        let maxPrice       = priceRange ? parseFloat(priceRange.value) : 10000;
        let searchQuery    = '';
        let currentSort    = 'default';

        // Check URL parameter for pre-filtered category (e.g., collections.php?category=Men)
        const urlParams = new URLSearchParams(window.location.search);
        const urlCat = urlParams.get('category');
        if (urlCat) {
            activeCategory = urlCat.toLowerCase();
            categoryBtns.forEach(btn => {
                if (btn.getAttribute('data-category').toLowerCase() === activeCategory) {
                    categoryBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                }
            });
        }

        function filterAndSortProducts() {
            let visibleCount = 0;
            const query = searchQuery.trim().toLowerCase();

            // Filter pass
            productCards.forEach(col => {
                const name     = (col.getAttribute('data-name') || '').toLowerCase();
                const category = (col.getAttribute('data-category') || '').toLowerCase();
                const sizes    = (col.getAttribute('data-sizes') || '').toLowerCase();
                const price    = parseFloat(col.getAttribute('data-price') || '0');

                const matchesCategory = (activeCategory === 'all' || category === activeCategory);
                const matchesSize     = (activeSize === 'all' || sizes.includes(activeSize));
                const matchesPrice    = (price <= maxPrice);
                const matchesSearch   = (!query || name.includes(query) || category.includes(query));

                if (matchesCategory && matchesSize && matchesPrice && matchesSearch) {
                    col.style.display = '';
                    visibleCount++;
                } else {
                    col.style.display = 'none';
                }
            });

            // Sort visible items
            const visibleCards = productCards.filter(col => col.style.display !== 'none');
            if (currentSort === 'price-low') {
                visibleCards.sort((a, b) => parseFloat(a.getAttribute('data-price')) - parseFloat(b.getAttribute('data-price')));
            } else if (currentSort === 'price-high') {
                visibleCards.sort((a, b) => parseFloat(b.getAttribute('data-price')) - parseFloat(a.getAttribute('data-price')));
            } else if (currentSort === 'newest') {
                visibleCards.sort((a, b) => parseInt(b.getAttribute('data-id')) - parseInt(a.getAttribute('data-id')));
            }

            // Re-append sorted cards
            visibleCards.forEach(col => productGrid.appendChild(col));

            // Update visible counter
            if (resultsCountEl) {
                resultsCountEl.textContent = `Showing ${visibleCount} garment${visibleCount === 1 ? '' : 's'}`;
            }

            // Toggle empty results notice
            if (noResultsAlert) {
                noResultsAlert.style.display = (visibleCount === 0) ? 'block' : 'none';
            }
        }

        // Category filter buttons
        categoryBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                categoryBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                activeCategory = (btn.getAttribute('data-category') || 'all').toLowerCase();
                filterAndSortProducts();
            });
        });

        // Size filter buttons
        sizeBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                sizeBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                activeSize = (btn.getAttribute('data-size') || 'all').toLowerCase();
                filterAndSortProducts();
            });
        });

        // Price range slider
        if (priceRange) {
            priceRange.addEventListener('input', (e) => {
                maxPrice = parseFloat(e.target.value);
                if (priceLabel) {
                    priceLabel.textContent = `Up to Rs. ${maxPrice.toLocaleString()}`;
                }
                filterAndSortProducts();
            });
        }

        // Sort dropdown
        if (sortSelect) {
            sortSelect.addEventListener('change', (e) => {
                currentSort = e.target.value;
                filterAndSortProducts();
            });
        }

        // Live text search input
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                searchQuery = e.target.value;
                if (clearSearchBtn) {
                    clearSearchBtn.style.display = searchQuery.length > 0 ? 'inline-block' : 'none';
                }
                filterAndSortProducts();
            });

            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', () => {
                    searchInput.value = '';
                    searchQuery = '';
                    clearSearchBtn.style.display = 'none';
                    searchInput.focus();
                    filterAndSortProducts();
                });
            }
        }

        // Run on load
        filterAndSortProducts();
    }

    // -------------------------------------------------------------------------
    // FEATURE 2: INTERACTIVE IMAGE SLIDER (Promotional Banners on Home)
    // -------------------------------------------------------------------------
    function initPromoSlider() {
        const carouselEl = document.getElementById('marketPromoCarousel');
        if (!carouselEl) return;

        new bootstrap.Carousel(carouselEl, {
            interval: 5000,
            ride: 'carousel',
            pause: 'hover',
            wrap: true,
            touch: true
        });

        // Keyboard arrow navigation
        carouselEl.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                bootstrap.Carousel.getInstance(carouselEl).prev();
            } else if (e.key === 'ArrowRight') {
                bootstrap.Carousel.getInstance(carouselEl).next();
            }
        });
    }

    // -------------------------------------------------------------------------
    // FEATURE 3: FORM VALIDATION (Inline feedback, Email regex, Phone, Password)
    // -------------------------------------------------------------------------
    function initFormValidation() {
        const forms = document.querySelectorAll('.needs-validation');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
        const sriLankaPhoneRegex = /^(?:0|94|\+94)?7[0-9]{8}$/;

        forms.forEach(form => {
            const inputs = form.querySelectorAll('input, textarea, select');

            function validateInput(input) {
                const value = input.value.trim();
                let isValid = true;
                let errorMsg = '';

                // Required check
                if (input.hasAttribute('required') && value === '') {
                    isValid = false;
                    errorMsg = 'This field is required.';
                }
                // Email format check
                else if (input.type === 'email' && value !== '' && !emailRegex.test(value)) {
                    isValid = false;
                    errorMsg = 'Please enter a valid email address.';
                }
                // Sri Lankan phone number check (e.g. 07XXXXXXXX)
                else if (input.name === 'phone' && value !== '' && !sriLankaPhoneRegex.test(value.replace(/[\s\-]/g, ''))) {
                    isValid = false;
                    errorMsg = 'Enter a valid Sri Lankan mobile number (e.g., 0771234567).';
                }
                // Password length check
                else if (input.type === 'password' && input.name === 'password' && value.length < 8) {
                    isValid = false;
                    errorMsg = 'Password must be at least 8 characters long.';
                }
                // Confirm password match check
                else if (input.name === 'confirm_password') {
                    const passInput = form.querySelector('input[name="password"]');
                    if (passInput && value !== passInput.value.trim()) {
                        isValid = false;
                        errorMsg = 'Passwords do not match.';
                    }
                }

                // Update feedback message
                const feedbackEl = input.parentElement.querySelector('.invalid-feedback');
                if (feedbackEl && errorMsg) {
                    feedbackEl.textContent = errorMsg;
                }

                if (isValid) {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                } else {
                    input.classList.remove('is-valid');
                    input.classList.add('is-invalid');
                }

                return isValid;
            }

            // Real-time validation
            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    if (input.classList.contains('is-invalid') || input.classList.contains('is-valid')) {
                        validateInput(input);
                    }
                });

                input.addEventListener('blur', () => {
                    if (input.value.trim() !== '') {
                        validateInput(input);
                    }
                });
            });

            // Form Submit event
            form.addEventListener('submit', (e) => {
                let formValid = true;
                let firstInvalid = null;

                inputs.forEach(input => {
                    const valid = validateInput(input);
                    if (!valid) {
                        formValid = false;
                        if (!firstInvalid) firstInvalid = input;
                    }
                });

                if (!formValid) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (firstInvalid) {
                        firstInvalid.focus();
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        });
    }

    // -------------------------------------------------------------------------
    // FEATURE 4: SMOOTH SCROLLING & BACK-TO-TOP BUTTON
    // -------------------------------------------------------------------------
    function initSmoothScroll() {
        const anchorLinks = document.querySelectorAll('a[href^="#"]:not([data-bs-toggle]):not([data-bs-slide])');
        anchorLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const targetId = link.getAttribute('href');
                if (targetId && targetId !== '#') {
                    const targetEl = document.querySelector(targetId);
                    if (targetEl) {
                        e.preventDefault();
                        targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });
    }

    function initBackToTop() {
        const backToTopBtn = document.getElementById('btnBackToTop');
        if (!backToTopBtn) return;

        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTopBtn.classList.add('show-btn');
            } else {
                backToTopBtn.classList.remove('show-btn');
            }
        }, { passive: true });

        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // -------------------------------------------------------------------------
    // FEATURE 5: EVENT HANDLING (Tooltips, Modal, AJAX Add-to-Cart & Wishlist)
    // -------------------------------------------------------------------------
    function initTooltips() {
        const tooltipElements = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipElements.map(el => new bootstrap.Tooltip(el));
    }

    function initProductModal() {
        const modalEl = document.getElementById('productDetailModal');
        if (!modalEl) return;

        const titleEl     = modalEl.querySelector('#modalProductTitle');
        const imgEl       = modalEl.querySelector('#modalProductImage');
        const categoryEl  = modalEl.querySelector('#modalProductCategory');
        const sellerEl    = modalEl.querySelector('#modalProductSeller');
        const priceEl     = modalEl.querySelector('#modalProductPrice');
        const descEl      = modalEl.querySelector('#modalProductDesc');
        const colourEl    = modalEl.querySelector('#modalProductColour');
        const stockEl     = modalEl.querySelector('#modalProductStock');
        const idInput     = modalEl.querySelector('#modalProductId');
        const sizeSelect  = modalEl.querySelector('#modalProductSizeSelect');

        modalEl.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            if (!button) return;

            const id       = button.getAttribute('data-id') || '';
            const name     = button.getAttribute('data-name') || 'Ready-Made Garment';
            const category = button.getAttribute('data-category') || 'Fashion';
            const seller   = button.getAttribute('data-seller') || 'Verified Seller';
            const price    = button.getAttribute('data-price') || 'Rs. 0.00';
            const desc     = button.getAttribute('data-description') || '';
            const image    = button.getAttribute('data-image') || 'images/logo.svg';
            const sizesStr = button.getAttribute('data-sizes') || 'M';
            const colour   = button.getAttribute('data-colour') || 'Standard';
            const stock    = button.getAttribute('data-stock') || 'In Stock';

            if (titleEl) titleEl.textContent = name;
            if (imgEl) { imgEl.src = image; imgEl.alt = name; }
            if (categoryEl) categoryEl.textContent = category;
            if (sellerEl) sellerEl.textContent = seller;
            if (priceEl) priceEl.textContent = price;
            if (descEl) descEl.textContent = desc;
            if (colourEl) colourEl.textContent = colour;
            if (stockEl) stockEl.textContent = stock;
            if (idInput) idInput.value = id;

            // Populate sizes dropdown
            if (sizeSelect) {
                sizeSelect.innerHTML = '';
                const sizes = sizesStr.split(',').map(s => s.trim());
                sizes.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s;
                    opt.textContent = s;
                    sizeSelect.appendChild(opt);
                });
            }
        });
    }

    // Asynchronous Add to Cart & Wishlist via fetch()
    function initAsyncCartAndWishlist() {
        const cartBadge = document.getElementById('cartCountBadge');

        // Add to Cart via direct button or Modal form
        document.addEventListener('click', async (e) => {
            const cartBtn = e.target.closest('.btn-add-to-cart');
            if (cartBtn) {
                e.preventDefault();
                const productId = cartBtn.getAttribute('data-id');
                const size      = cartBtn.getAttribute('data-size') || 'M';
                const quantity  = 1;

                cartBtn.disabled = true;
                const originalHtml = cartBtn.innerHTML;
                cartBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Adding...';

                try {
                    const formData = new FormData();
                    formData.append('action', 'add');
                    formData.append('product_id', productId);
                    formData.append('size', size);
                    formData.append('quantity', quantity);

                    const response = await fetch('includes/cart_action.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.require_login) {
                        window.location.href = 'auth/login.php';
                        return;
                    }

                    if (data.success) {
                        showToast(data.message, true);
                        if (cartBadge) {
                            cartBadge.textContent = data.cart_count;
                        }
                    } else {
                        showToast(data.message, false);
                    }
                } catch (err) {
                    showToast('Could not add garment to cart. Please try again.', false);
                } finally {
                    cartBtn.disabled = false;
                    cartBtn.innerHTML = originalHtml;
                }
            }

            // Wishlist Toggle Button
            const wishlistBtn = e.target.closest('.btn-toggle-wishlist');
            if (wishlistBtn) {
                e.preventDefault();
                const productId = wishlistBtn.getAttribute('data-id');

                try {
                    const formData = new FormData();
                    formData.append('product_id', productId);

                    const response = await fetch('includes/wishlist_action.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.require_login) {
                        window.location.href = 'auth/login.php';
                        return;
                    }

                    if (data.success) {
                        showToast(data.message, true);
                        if (data.in_wishlist) {
                            wishlistBtn.classList.add('active', 'text-danger');
                            wishlistBtn.innerHTML = '<i class="bi bi-heart-fill"></i>';
                        } else {
                            wishlistBtn.classList.remove('active', 'text-danger');
                            wishlistBtn.innerHTML = '<i class="bi bi-heart"></i>';
                        }
                    } else {
                        showToast(data.message, false);
                    }
                } catch (err) {
                    showToast('Could not update wishlist.', false);
                }
            }
        });

        // Modal Add-to-Cart form submit
        const modalCartForm = document.getElementById('modalAddToCartForm');
        if (modalCartForm) {
            modalCartForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const productId = document.getElementById('modalProductId').value;
                const size      = document.getElementById('modalProductSizeSelect').value;
                const quantity  = document.getElementById('modalProductQty').value;
                const submitBtn = modalCartForm.querySelector('button[type="submit"]');

                submitBtn.disabled = true;
                const originalHtml = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Adding...';

                try {
                    const formData = new FormData();
                    formData.append('action', 'add');
                    formData.append('product_id', productId);
                    formData.append('size', size);
                    formData.append('quantity', quantity);

                    const response = await fetch('includes/cart_action.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.require_login) {
                        window.location.href = 'auth/login.php';
                        return;
                    }

                    if (data.success) {
                        showToast(data.message, true);
                        if (cartBadge) {
                            cartBadge.textContent = data.cart_count;
                        }
                        // Close modal
                        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('productDetailModal'));
                        if (modalInstance) modalInstance.hide();
                    } else {
                        showToast(data.message, false);
                    }
                } catch (err) {
                    showToast('Could not add to cart.', false);
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            });
        }
    }

    // -------------------------------------------------------------------------
    // FEATURE 6: CUSTOM ANIMATIONS (IntersectionObserver Reveal-on-Scroll)
    // -------------------------------------------------------------------------
    function initScrollAnimations() {
        const revealElements = document.querySelectorAll('.reveal-on-scroll');

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-revealed');
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

            revealElements.forEach(el => observer.observe(el));
        } else {
            revealElements.forEach(el => el.classList.add('is-revealed'));
        }
    }

    // Role selector conditional field for registration
    function initRegisterRoleToggle() {
        const roleRadios = document.querySelectorAll('input[name="role"]');
        const shopField  = document.getElementById('shopNameContainer');
        const shopInput  = document.getElementById('regShopName');

        if (!roleRadios.length || !shopField) return;

        roleRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                if (radio.value === 'seller' && radio.checked) {
                    shopField.style.display = 'block';
                    if (shopInput) shopInput.setAttribute('required', 'required');
                } else {
                    shopField.style.display = 'none';
                    if (shopInput) shopInput.removeAttribute('required');
                }
            });
        });
    }

    // Initialize all modules
    initShopFilters();
    initPromoSlider();
    initFormValidation();
    initSmoothScroll();
    initBackToTop();
    initTooltips();
    initProductModal();
    initAsyncCartAndWishlist();
    initScrollAnimations();
    initRegisterRoleToggle();
});
