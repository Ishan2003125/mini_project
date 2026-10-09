<?php
/**
 * VR Fashion - User Registration (Buyer & Seller)
 * File: auth/register.php
 * Purpose: Allows new members to register as either a Customer (Buyer) or
 *          Shop Owner (Seller). Enforces client-side & server-side validation,
 *          duplicate username/email verification, and bcrypt password hashing.
 */

$page_title = "Register Account - Buyer & Seller";
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect(url('dashboard.php'));
}

$errors = [];
$form_data = [
    'username'  => '',
    'email'     => '',
    'role'      => $_GET['role'] ?? 'buyer',
    'shop_name' => ''
];

// Handle Registration Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data['username']  = trim($_POST['username'] ?? '');
    $form_data['email']     = trim($_POST['email'] ?? '');
    $form_data['role']      = in_array($_POST['role'] ?? '', ['buyer', 'seller']) ? $_POST['role'] : 'buyer';
    $form_data['shop_name'] = trim($_POST['shop_name'] ?? '');
    $password               = $_POST['password'] ?? '';
    $confirm_password       = $_POST['confirm_password'] ?? '';

    // 1. Username
    if (empty($form_data['username'])) {
        $errors['username'] = 'Username is required.';
    } elseif (strlen($form_data['username']) < 3 || strlen($form_data['username']) > 50) {
        $errors['username'] = 'Username must be between 3 and 50 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $form_data['username'])) {
        $errors['username'] = 'Username may only contain letters, numbers, underscores, and hyphens.';
    }

    // 2. Email
    if (empty($form_data['email'])) {
        $errors['email'] = 'Email address is required.';
    } elseif (!validate_email($form_data['email'])) {
        $errors['email'] = 'Please provide a valid email address.';
    }

    // 3. Shop name if seller
    if ($form_data['role'] === 'seller' && empty($form_data['shop_name'])) {
        $errors['shop_name'] = 'Shop or Brand name is required for seller accounts.';
    }

    // 4. Password
    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters long.';
    }

    // 5. Confirm password
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    // 6. Duplicate checks
    if (empty($errors)) {
        try {
            // Check username
            $stmt_u = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt_u->execute([$form_data['username']]);
            if ($stmt_u->fetch()) {
                $errors['username'] = 'This username is already taken.';
            }

            // Check email
            $stmt_e = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt_e->execute([$form_data['email']]);
            if ($stmt_e->fetch()) {
                $errors['email'] = 'An account with this email address already exists.';
            }

            if (empty($errors)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $shop_val = ($form_data['role'] === 'seller') ? $form_data['shop_name'] : null;

                $ins = $pdo->prepare("
                    INSERT INTO users (username, email, password, role, shop_name, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $ins->execute([
                    $form_data['username'],
                    $form_data['email'],
                    $hashed_password,
                    $form_data['role'],
                    $shop_val
                ]);

                set_flash_message('success', 'Account registered successfully! Please sign in with your credentials.');
                redirect(url('auth/login.php'));
            }
        } catch (PDOException $e) {
            set_flash_message('danger', 'Registration error: ' . htmlspecialchars($e->getMessage()));
        }
    }

    if (!empty($errors)) {
        set_flash_message('danger', 'Please correct the highlighted issues below.');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5 reveal-on-scroll">
                <div class="card border-0 shadow-sm p-4 p-md-5 bg-white">
                    <div class="text-center mb-4">
                        <img src="<?= url('images/logo.svg') ?>" alt="VR Fashion" height="44" class="mb-2">
                        <h3 class="fw-bold text-dark">Create Your Account</h3>
                        <p class="text-muted small">Join Sri Lanka's online marketplace for ready-made clothing.</p>
                    </div>

                    <form action="<?= url('auth/register.php') ?>" method="POST" class="needs-validation" id="registerForm" novalidate>
                        <!-- Account Role Selector -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-dark d-block">I want to:</label>
                            <div class="d-flex gap-3">
                                <div class="form-check card p-2 flex-grow-1 border">
                                    <input class="form-check-input ms-0 me-2" 
                                           type="radio" 
                                           name="role" 
                                           id="roleBuyer" 
                                           value="buyer" 
                                           <?= $form_data['role'] === 'buyer' ? 'checked' : '' ?>>
                                    <label class="form-check-label small fw-bold text-dark" for="roleBuyer">
                                        <i class="bi bi-bag me-1 text-danger"></i> Shop Clothes (Buyer)
                                    </label>
                                </div>
                                <div class="form-check card p-2 flex-grow-1 border">
                                    <input class="form-check-input ms-0 me-2" 
                                           type="radio" 
                                           name="role" 
                                           id="roleSeller" 
                                           value="seller" 
                                           <?= $form_data['role'] === 'seller' ? 'checked' : '' ?>>
                                    <label class="form-check-label small fw-bold text-dark" for="roleSeller">
                                        <i class="bi bi-shop me-1 text-primary"></i> Sell Clothes (Seller)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Shop Name (Conditional for Sellers) -->
                        <div class="mb-3" id="shopNameContainer" style="<?= $form_data['role'] === 'seller' ? '' : 'display: none;' ?>">
                            <label for="regShopName" class="form-label small fw-bold text-dark">
                                Shop / Brand Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control <?= isset($errors['shop_name']) ? 'is-invalid' : '' ?>" 
                                   id="regShopName" 
                                   name="shop_name" 
                                   value="<?= htmlspecialchars($form_data['shop_name']) ?>" 
                                   placeholder="e.g. Araliya Handlooms &amp; Garments">
                            <div class="invalid-feedback">
                                <?= $errors['shop_name'] ?? 'Please provide your shop or brand name.' ?>
                            </div>
                        </div>

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="regUsername" class="form-label small fw-bold text-dark">
                                Username <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                                   id="regUsername" 
                                   name="username" 
                                   value="<?= htmlspecialchars($form_data['username']) ?>" 
                                   placeholder="e.g. kasun99" 
                                   required 
                                   minlength="3" 
                                   maxlength="50">
                            <div class="invalid-feedback">
                                <?= $errors['username'] ?? 'Username must be at least 3 characters long.' ?>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="regEmail" class="form-label small fw-bold text-dark">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                   id="regEmail" 
                                   name="email" 
                                   value="<?= htmlspecialchars($form_data['email']) ?>" 
                                   placeholder="e.g. user@gmail.com" 
                                   required>
                            <div class="invalid-feedback">
                                <?= $errors['email'] ?? 'Please provide a valid email address.' ?>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="regPassword" class="form-label small fw-bold text-dark">
                                Password (Min. 8 characters) <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                                   id="regPassword" 
                                   name="password" 
                                   placeholder="••••••••" 
                                   required 
                                   minlength="8">
                            <div class="invalid-feedback">
                                <?= $errors['password'] ?? 'Password must be at least 8 characters long.' ?>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="regConfirmPassword" class="form-label small fw-bold text-dark">
                                Confirm Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                                   id="regConfirmPassword" 
                                   name="confirm_password" 
                                   placeholder="••••••••" 
                                   required>
                            <div class="invalid-feedback">
                                <?= $errors['confirm_password'] ?? 'Passwords must match identically.' ?>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary-market btn-lg w-100 rounded-pill mb-3">
                            Create Account
                        </button>
                    </form>

                    <div class="text-center pt-3 border-top small text-muted">
                        Already have an account? <a href="<?= url('auth/login.php') ?>" class="text-danger fw-bold text-decoration-none">Sign In Here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
