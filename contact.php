<?php
/**
 * VR Fashion - Contact & Customer Support
 * File: contact.php
 * Purpose: Allows customers, boutique sellers, and partners to submit inquiries.
 *          Implements client-side & server-side validation, stores records into
 *          the MySQL messages table, and provides optional commented PHPMailer logic.
 */

$page_title = "Contact Support - Customer Care & Inquiries";
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$errors = [];
$form_data = [
    'name'    => '',
    'email'   => '',
    'message' => ''
];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data['name']    = trim($_POST['name'] ?? '');
    $form_data['email']   = trim($_POST['email'] ?? '');
    $form_data['message'] = trim($_POST['message'] ?? '');

    // Server-side validation
    if (empty($form_data['name'])) {
        $errors['name'] = 'Full name is required.';
    } elseif (strlen($form_data['name']) < 2) {
        $errors['name'] = 'Name must be at least 2 characters.';
    }

    if (empty($form_data['email'])) {
        $errors['email'] = 'Email address is required.';
    } elseif (!validate_email($form_data['email'])) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if (empty($form_data['message'])) {
        $errors['message'] = 'Inquiry message cannot be empty.';
    } elseif (strlen($form_data['message']) < 10) {
        $errors['message'] = 'Message must be at least 10 characters long.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([
                $form_data['name'],
                $form_data['email'],
                $form_data['message']
            ]);

            // =================================================================
            // OPTIONAL: PHPMailer Notification (Commented Out for Local Testing)
            // =================================================================
            /*
            require_once __DIR__ . '/../vendor/autoload.php';
            use PHPMailer\PHPMailer\PHPMailer;
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.mailtrap.io';
                $mail->SMTPAuth = true;
                $mail->Username = 'your_smtp_user';
                $mail->Password = 'your_smtp_pass';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('noreply@vrfashion.lk', 'VR Fashion Support');
                $mail->addAddress('admin@vrfashion.lk');
                $mail->Subject = 'New Inquiry from ' . $form_data['name'];
                $mail->Body = $form_data['message'];
                $mail->send();
            } catch (Exception $e) {}
            */

            set_flash_message('success', 'Your inquiry has been received! Our support team will respond within 24 hours.');
            redirect(url('contact.php'));

        } catch (PDOException $e) {
            set_flash_message('danger', 'Database error: ' . htmlspecialchars($e->getMessage()));
        }
    } else {
        set_flash_message('danger', 'Please correct the highlighted errors in the form.');
    }
}
?>

<!-- ====================================================================
     CONTACT HEADER
     ==================================================================== -->
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-1">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact</li>
            </ol>
        </nav>
        <h1 class="h2 fw-bold text-dark mb-0">Contact VR Fashion Support</h1>
    </div>
</section>

<!-- ====================================================================
     CONTACT FORM & DETAILS
     ==================================================================== -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Left Form Column -->
            <div class="col-lg-7 reveal-on-scroll">
                <div class="card border-0 shadow-sm p-4 p-md-5">
                    <h4 class="fw-bold text-dark mb-2">Send Us a Message</h4>
                    <p class="text-muted small mb-4">
                        Have a question about garment sizing, order tracking, or selling on VR Fashion? Fill in the form below and we'll be in touch.
                    </p>

                    <form action="<?= url('contact.php') ?>" method="POST" class="needs-validation" id="contactForm" novalidate>
                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="contactName" class="form-label small fw-bold text-dark">
                                Your Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                   id="contactName" 
                                   name="name" 
                                   value="<?= htmlspecialchars($form_data['name']) ?>" 
                                   placeholder="e.g. Priyantha Silva" 
                                   required 
                                   minlength="2">
                            <div class="invalid-feedback">
                                <?= $errors['name'] ?? 'Please provide your full name.' ?>
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="contactEmail" class="form-label small fw-bold text-dark">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                   id="contactEmail" 
                                   name="email" 
                                   value="<?= htmlspecialchars($form_data['email']) ?>" 
                                   placeholder="e.g. priyantha@gmail.com" 
                                   required>
                            <div class="invalid-feedback">
                                <?= $errors['email'] ?? 'Please provide a valid email address.' ?>
                            </div>
                        </div>

                        <!-- Message -->
                        <div class="mb-4">
                            <label for="contactMessage" class="form-label small fw-bold text-dark">
                                Inquiry Message <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control <?= isset($errors['message']) ? 'is-invalid' : '' ?>" 
                                      id="contactMessage" 
                                      name="message" 
                                      rows="5" 
                                      placeholder="Describe your question or feedback..." 
                                      required 
                                      minlength="10"><?= htmlspecialchars($form_data['message']) ?></textarea>
                            <div class="invalid-feedback">
                                <?= $errors['message'] ?? 'Message must be at least 10 characters long.' ?>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary-market btn-lg w-100 rounded-pill">
                            <i class="bi bi-send me-1"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Details Column -->
            <div class="col-lg-5 reveal-on-scroll delay-100">
                <div class="d-flex flex-column gap-4">
                    <!-- Headquarters Card -->
                    <div class="card border-0 shadow-sm p-4">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="fs-2 text-danger"><i class="bi bi-geo-alt"></i></div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Colombo Head Office</h6>
                                <small class="text-muted">Operations &amp; Logistics Hub</small>
                            </div>
                        </div>
                        <p class="small text-muted mb-0">
                            No. 128, Galle Road, Colombo 03, Western Province, Sri Lanka.
                        </p>
                    </div>

                    <!-- Direct Contact Hotline -->
                    <div class="card border-0 shadow-sm p-4">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="fs-2 text-danger"><i class="bi bi-telephone"></i></div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Hotlines &amp; WhatsApp</h6>
                                <small class="text-muted">Monday – Saturday: 9:00 AM – 7:00 PM</small>
                            </div>
                        </div>
                        <ul class="list-unstyled small text-muted mb-0">
                            <li class="mb-1"><strong>Phone:</strong> +94 (11) 234-5678</li>
                            <li class="mb-1"><strong>WhatsApp:</strong> +94 (77) 123-4567</li>
                            <li><strong>Email:</strong> support@vrfashion.lk</li>
                        </ul>
                    </div>

                    <!-- Seller Support Information -->
                    <div class="card border-0 shadow-sm p-4 bg-white border-start border-4 border-danger">
                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-shop me-1 text-danger"></i> Are you a Garment Manufacturer?</h6>
                        <p class="small text-muted mb-2">
                            We invite clothing factories, batik artists, and tailoring shops to join our marketplace. Free onboarding and local support.
                        </p>
                        <div>
                            <a href="<?= url('auth/register.php?role=seller') ?>" class="text-danger fw-bold small text-decoration-none">
                                Register as a Seller &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
