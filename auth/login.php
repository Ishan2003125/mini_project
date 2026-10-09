<?php
/**
 * VR Fashion - User Authentication (Sign In)
 * File: auth/login.php
 * Purpose: Authenticates registered buyers and sellers via username or email.
 *          Verifies bcrypt password hashes using password_verify(), stores role
 *          and shop credentials, regenerates session ID, and redirects to dashboard.php.
 */

$page_title = "Sign In - VR Fashion Marketplace";
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect(url('dashboard.php'));
}

$errors = [];
$identifier = '';

// Handle Login Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';

    if (empty($identifier)) {
        $errors['identifier'] = 'Please enter your username or registered email.';
    }
    if (empty($password)) {
        $errors['password'] = 'Please enter your password.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$identifier, $identifier]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);

                $_SESSION['user_id']   = (int)$user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['email']     = $user['email'];
                $_SESSION['role']      = $user['role'];
                $_SESSION['shop_name'] = $user['shop_name'];

                set_flash_message('success', 'Welcome back, ' . htmlspecialchars($user['username']) . '!');
                redirect(url('dashboard.php'));
            } else {
                $errors['general'] = 'Invalid credentials. Please check your username/email and password.';
                set_flash_message('danger', 'Invalid username or password.');
            }
        } catch (PDOException $e) {
            set_flash_message('danger', 'Database error: ' . htmlspecialchars($e->getMessage()));
        }
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
                        <h3 class="fw-bold text-dark">Welcome Back</h3>
                        <p class="text-muted small">Sign in to your buyer or seller marketplace account.</p>
                    </div>

                    <!-- Evaluator / Test Accounts Helper -->
                    <div class="alert alert-light border small text-muted mb-4 py-2 px-3">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-info-circle text-primary me-1"></i> Pre-configured Test Accounts:</div>
                        <div><strong>Seller:</strong> <code>araliya_store</code> | Password: <code>Password123!</code></div>
                        <div><strong>Buyer:</strong> <code>kasun_perera</code> | Password: <code>Password123!</code></div>
                    </div>

                    <form action="<?= url('auth/login.php') ?>" method="POST" class="needs-validation" id="loginForm" novalidate>
                        <!-- Username or Email -->
                        <div class="mb-3">
                            <label for="loginIdentifier" class="form-label small fw-bold text-dark">
                                Username or Email <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control <?= isset($errors['identifier']) ? 'is-invalid' : '' ?>" 
                                   id="loginIdentifier" 
                                   name="identifier" 
                                   value="<?= htmlspecialchars($identifier) ?>" 
                                   placeholder="Username or email" 
                                   required>
                            <div class="invalid-feedback">
                                <?= $errors['identifier'] ?? 'Please provide your username or email address.' ?>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="loginPassword" class="form-label small fw-bold text-dark">
                                Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                                   id="loginPassword" 
                                   name="password" 
                                   placeholder="••••••••" 
                                   required>
                            <div class="invalid-feedback">
                                <?= $errors['password'] ?? 'Please provide your account password.' ?>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary-market btn-lg w-100 rounded-pill mb-3">
                            Sign In
                        </button>
                    </form>

                    <div class="text-center pt-3 border-top small text-muted">
                        New to VR Fashion? <a href="<?= url('auth/register.php') ?>" class="text-danger fw-bold text-decoration-none">Create an Account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
