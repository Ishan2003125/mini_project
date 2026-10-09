# VR Fashion — Sri Lanka Online Ready-Made Clothing Marketplace

[![University Project](https://img.shields.io/badge/Project-University%20Mini--Project-blue?style=for-the-badge)](https://github.com)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap 5](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![Vanilla JS](https://img.shields.io/badge/JavaScript-ES6%2B%20Vanilla-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org)

**VR Fashion** is a complete, full-stack multi-vendor online marketplace for **ready-made clothing (redi kada)** in Sri Lanka. The platform allows local clothing makers, batik artisans, and boutique owners to list and sell ready-to-wear apparel, while customers across Sri Lanka browse, search, wishlist, and buy items with **Cash on Delivery (COD)**.

Built entirely with **HTML5**, **CSS3**, **Bootstrap 5 (CDN)**, **Vanilla JavaScript (ES6+)**, and **native PHP + MySQL** (no frameworks), designed to run locally on **XAMPP** or **WAMP**.

---

## 🌟 Marketplace Key Features

### 1. Buyer & Customer Experience
- **Home Portal (`index.php`)**: Hero search bar, promotional banner slider, Shop by Category cards (Men, Women, Kids, Footwear, Accessories), featured ready-made garments from the database, trust badges, customer reviews, and newsletter.
- **Shop & Catalog (`collections.php`)**: 18+ ready-made garments priced in Sri Lankan Rupees (LKR). Real-time client-side filters for Category, Size, Price Range slider, Live Search, and Sorting (Price Low-High, Price High-Low, Newest) without page reloads.
- **Garment Details Modal**: Dynamic pop-up showing garment details, fabric description, color, live stock, size selection, and direct Add-to-Cart.
- **Shopping Cart & Checkout (`cart.php`)**: Live cart table with quantity update, item removal, subtotal, shipping calculation (Free over Rs. 5,000), and a Cash on Delivery checkout form with Sri Lankan mobile phone validation.
- **Order Tracking & Wishlist (`dashboard.php`)**: View past orders with statuses (*Pending*, *Shipped*, *Delivered*), ordered garment summaries, delivery addresses, and personal saved wishlist items.

### 2. Seller Hub & Vendor Management
- **Seller Registration (`auth/register.php`)**: Dedicated role selection for Shop Owners/Sellers with shop/brand name registration.
- **Seller Management Dashboard (`dashboard.php`)**:
  - **My Listed Products**: View, inspect, and delete own clothing items.
  - **Add New Garment**: Form to list new products (name, category, description, price in LKR, sizes, color, stock, and artwork).
  - **Incoming Customer Orders**: View orders placed for their clothes, customer contact info, delivery address, and update order statuses (*Pending*, *Shipped*, *Delivered*, *Cancelled*).

### 3. About Us & Story (`about.php`)
- Marketplace heritage story celebrating Sri Lankan textile craftsmanship.
- Interactive **FAQ Accordion** with JavaScript toggles.

### 4. Contact & Support (`contact.php`)
- Contact form with client-side and server-side validation.
- Messages stored directly in the `messages` table.
- Optional PHPMailer configuration included and documented.

---

## ⚡ Master JavaScript Modules (`js/main.js`)

All six required JavaScript features are fully implemented in vanilla ES6 without third-party dependencies:

1. **Dynamic Content Updates**:
   - Real-time client-side live search and multi-criteria filtering on the Shop page (Category, Size, Price slider, Sorting).
   - Dynamic counter updating visible items.
   - Interactive FAQ accordion on the About page.
2. **Interactive Promotional Slider**:
   - Auto-playing hero carousel on the home page (5000ms interval).
   - Manual controls (next/prev and indicator dots), pause-on-hover, and keyboard arrow navigation.
3. **Multi-Stage Form Validation**:
   - Real-time `input` and `blur` feedback with inline Bootstrap `is-invalid` / `is-valid` styles.
   - Enforces required fields, email regex, password minimum lengths (≥ 8 characters), password matching, and Sri Lankan phone numbers (`07XXXXXXXX`).
4. **Smooth In-Page Navigation & Floating Back-to-Top**:
   - Fluid scroll on internal anchor links.
   - Floating back-to-top button appearing after 300px scroll.
5. **Interactive Event Handling & Asynchronous Actions**:
   - Product detail modal dynamically reading `data-*` attributes.
   - Asynchronous **Add to Cart** (`fetch('includes/cart_action.php')`) and **Wishlist** (`fetch('includes/wishlist_action.php')`) with instant Bootstrap **Toast notifications** and live navbar cart counter badge update.
6. **Custom Animations**:
   - `IntersectionObserver` reveal-on-scroll animations (`.reveal-on-scroll`).
   - Clean card hover lifts and button state transitions.

---

## 🛠️ Technology Stack

- **Front-End**: HTML5 Semantic Elements, CSS3 (Light E-Commerce Palette), Bootstrap 5.3.3 (CDN), Bootstrap Icons (CDN), Vanilla JavaScript (ES6+).
- **Typography**: Google Fonts — *Poppins* (Brand & Headings) and *Inter* (Body Text).
- **Artwork**: 100% Original SVG vector artwork for logos, banners, and 18 ready-made clothing items.
- **Back-End**: Pure Native PHP 8.0+ (No frameworks).
- **Database**: MySQL 5.7+ / 8.0+ or MariaDB 10.4+ (InnoDB engine, UTF-8 mb4 encoding, Foreign Key constraints with `ON DELETE CASCADE`).

---

## 📂 Project Folder Structure

```text
vr-fashion/
│── css/
│   └── style.css            # Light modern marketplace theme, Poppins + Inter, responsive cards
│── js/
│   └── main.js              # All 6 vanilla JS features (filters, slider, validation, scroll, async cart/toast, observer)
│── images/                  # Original SVG artwork for logo, banners, and 18 ready-made clothing items
│   ├── logo.svg
│   ├── hero-banner.svg
│   ├── banner-1.svg ... banner-3.svg
│   ├── avatar-default.svg
│   ├── product-1.svg ... product-18.svg
│── includes/
│   ├── db.php               # PDO database connection (DB: vr_fashion, user: root, pass: '')
│   ├── functions.php        # Core helpers: sanitize, validate_email, validate_sri_lankan_phone, auth guards, cart count
│   ├── header.php           # Shared responsive navbar with live cart count badge and toast container
│   ├── footer.php           # Shared footer with delivery badges, Sri Lankan contact info, back-to-top
│   ├── cart_action.php      # Asynchronous API endpoint for adding, updating, and removing cart items
│   └── wishlist_action.php  # Asynchronous API endpoint for toggling wishlist items
│── auth/
│   ├── register.php         # Buyer & Seller registration with shop name field and validation
│   ├── login.php            # Sign-in with password_verify() and session_regenerate_id()
│   └── logout.php           # Clean session destruction and redirect
│── about.php                # Marketplace story, local manufacturing mission, and interactive FAQ accordion
│── contact.php              # Inquiry contact form with JS+PHP validation and MySQL messages storage
│── cart.php                 # Shopping cart table, quantity adjustments, and Cash on Delivery order checkout
│── collections.php          # Shop catalog with category/size/price filters, live search, sort, and detail modal
│── dashboard.php            # Buyer order history/wishlist + Seller product management & incoming orders
│── README.md                # Comprehensive project documentation
│── database.sql             # Complete database schema and rich ready-made clothing sample data
```

---

## 🚀 Step-by-Step Local Setup Guide (XAMPP / WAMP)

### Step 1: Start Apache and MySQL
1. Open the **XAMPP Control Panel** (or **WAMP Server**).
2. Click **Start** next to **Apache** and **MySQL**.
3. Ensure both services show green status lights.

### Step 2: Copy the Project Folder to Web Root
Copy the `vr-fashion` folder into your web server's root:
- **For XAMPP on Windows**: `C:\xampp\htdocs\vr-fashion\`
- **For WAMP on Windows**: `C:\wamp64\www\vr-fashion\`

### Step 3: Create Database & Import Schema
1. Open your browser and navigate to **phpMyAdmin**:
   ```
   http://localhost/phpmyadmin/
   ```
2. Click **New** in the left sidebar.
3. Enter database name `vr_fashion` and collation `utf8mb4_unicode_ci`.
4. Click **Create**.
5. Select the `vr_fashion` database, then click the **Import** tab.
6. Choose `database.sql` from the `vr-fashion/` folder.
7. Click **Import** (or **Go**).
8. Verify all 7 tables (`users`, `products`, `wishlist`, `cart`, `orders`, `order_items`, `messages`) are imported.

### Step 4: Verify Database Credentials
Open `includes/db.php`. It is pre-configured for standard XAMPP defaults:
```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'vr_fashion');
define('DB_USER', 'root');
define('DB_PASS', ''); // Empty password for default XAMPP
define('DB_PORT', '3306');
```
*(If your MySQL root user has a password set, enter it in `DB_PASS`)*.

### Step 5: Launch the Application
Open your web browser and navigate to:
```
http://localhost/vr-fashion/
```
*(or via PHP CLI server: `http://localhost:8000/`)*

---

## 🔑 Pre-Configured Test Accounts

The following accounts are pre-populated in `database.sql` for instant testing and grading:

| Role | Username | Email | Password | Shop Name |
|---|---|---|---|---|
| **Seller 1** | `araliya_store` | `seller1@vrfashion.lk` | `Password123!` | Araliya Cotton & Handlooms |
| **Seller 2** | `lanka_threads` | `seller2@vrfashion.lk` | `Password123!` | Lanka Threads Colombo |
| **Buyer 1** | `kasun_perera` | `buyer1@vrfashion.lk` | `Password123!` | — |
| **Buyer 2** | `anusha_fernando` | `buyer2@vrfashion.lk` | `Password123!` | — |

---

## 📧 Optional PHPMailer Configuration

A pre-structured PHPMailer block is provided in `contact.php`. To enable live external email delivery:
1. Run `composer require phpmailer/phpmailer` in the project directory.
2. In `contact.php`, uncomment the PHPMailer block.
3. Configure your SMTP provider credentials (e.g., Mailtrap, SendGrid, or Gmail App Password):
   ```php
   $mail->Host       = 'smtp.mailtrap.io';
   $mail->SMTPAuth   = true;
   $mail->Username   = 'YOUR_SMTP_USER';
   $mail->Password   = 'YOUR_SMTP_PASSWORD';
   $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
   $mail->Port       = 587;
   ```

---

## 🛡️ Security Implementation Highlights

- **SQL Injection Defense**: 100% of SQL queries with user inputs use PDO Prepared Statements with bound parameters (`execute([$p1, $p2])`).
- **Cross-Site Scripting (XSS) Prevention**: All dynamic user-submitted outputs are escaped with `htmlspecialchars($data, ENT_QUOTES, 'UTF-8')`.
- **Password Security**: Passwords are encrypted using native bcrypt via `password_hash($password, PASSWORD_DEFAULT)` and verified with `password_verify()`.
- **Session Fixation Defense**: Sessions invoke `session_regenerate_id(true)` upon successful authentication.
- **Authorization & Access Control**: Protected pages (`cart.php`, `dashboard.php`) invoke `require_login()`, and seller actions enforce `require_seller()`.
- **Data Integrity**: Orders, order items, and stock updates use PDO Database Transactions (`beginTransaction()`, `commit()`, `rollBack()`).

---

## 🎓 University Mini-Project Information
- **Project Title**: VR Fashion — Online Ready-Made Clothing Marketplace
- **Domain**: E-Commerce / Ready-Made Apparel & Garments (Sri Lanka)
- **Architecture**: Native Full-Stack (HTML5, CSS3, Vanilla JS, Native PHP, MySQL)
- **Year**: 2026
