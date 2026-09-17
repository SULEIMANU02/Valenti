# VALENTI ATELIER — B2C E-COMMERCE CLOTHING BRAND PORTAL
### Course Project: SEN 803 Software Technology
**Project Component**: Prototype Business Portal Development (60 Marks)

---

## 1. Executive Summary & Business Concept

**Valenti Atelier** is a direct-to-consumer (B2C) luxury fashion label specializing in architectural tailoring, pure silk garments, and contemporary streetwear. 

As outlined in the **SEN 803 Course Project Brief**, the portal demonstrates how modern Information Technology creates competitive advantage and consumer value across key business operations:
1. **Interactive Digital Storefront (Retailing & Merchandising)**: High-resolution catalog, multi-facet filtering (by category, gender, size, and price), dynamic stock counters, and customer fitting reviews.
2. **Frictionless Conversion Engine**: Real-time shopping bag subtotal calculation, automated voucher discount engine (`SEN803`, `WELCOME10`), and multi-method checkout simulation (Card, Mobile Money, Cash on Delivery, Bank Transfer).
3. **Consumer Relationship & Transparency**: Automated invoice generation and interactive 4-stage dispatch tracking timeline (*Order Placed* → *Quality Check & Boxing* → *Courier Dispatched* → *Doorstep Delivery*).
4. **Administrative Back-Office (Operations Management)**: Executive dashboard with gross sales KPIs, automated low-stock warnings, complete garment CRUD inventory management with image uploads, and order dispatch status controls.

---

## 2. Default Demo Credentials (For Presentation & Evaluation)

| Role | Email | Password | Access Rights |
|---|---|---|---|
| **Administrator** | `admin@valenti.com` | `admin123` | Full back-office dashboard, inventory CRUD, order logistics, voucher creation |
| **Patron / Consumer** | `customer@valenti.com` | `customer123` | Storefront checkout, order history tracking, profile address management |

### Active Promotional Vouchers
- **`SEN803`**: 20% Academic Course Discount (Min spend £50.00)
- **`WELCOME10`**: 10% Inaugural Order Discount
- **`VALENTI50`**: £50.00 Flat VIP Discount (Min spend £200.00)

---

## 3. How to Run Locally

### Option A: One-Click Launcher (Windows)
Double-click [`run_portal.bat`](file:///c:/Users/Sulaiman%20Adamu%20Ahmad/Desktop/Sufago/websites/clothing/run_portal.bat). It will automatically detect your XAMPP PHP installation, seed the database, launch your browser, and start the local server on `http://localhost:8000`.

### Option B: Command Line (PowerShell / Terminal)
```powershell
# Using XAMPP PHP
& "C:\xampp\php\php.exe" -S localhost:8000

# Or standard PHP (if in PATH)
php -S localhost:8000
```
Then navigate to:
- **Public Storefront**: [http://localhost:8000](http://localhost:8000)
- **Operations Console**: [http://localhost:8000/admin/index.php](http://localhost:8000/admin/index.php)

---

## 4. Technical Architecture

- **Backend**: Native PHP 8.2 (Zero external frameworks required; 100% portable).
- **Database Engine**: **MySQL / MariaDB on XAMPP** (Default driver: `mysql`, Host: `127.0.0.1:3306`, DB: `valenti_atelier`, User: `root`, Password: empty).
  - Automatically initializes the database `valenti_atelier`, executes [`database/schema_mysql.sql`](file:///c:/Users/Sulaiman%20Adamu%20Ahmad/Desktop/Sufago/websites/clothing/database/schema_mysql.sql), and auto-seeds all data upon first connection.
  - Can be inspected and managed visually via **phpMyAdmin** at [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
  - Seamless zero-config **SQLite fallback** (`DB_DRIVER = 'sqlite'`) built-in for portable examiner evaluation.
- **Frontend**: Custom CSS3 design system with custom properties, glassmorphism, responsive CSS flex/grid, and vanilla JavaScript.
- **Security**: Prepared PDO statements, CSRF protection, password hashing with `PASSWORD_BCRYPT`, and role-based access control.

---

## 5. Portal Map & File Structure

```
clothing/
├── run_portal.bat          # One-click Windows presentation launcher
├── index.php               # Homepage (Hero capsule, value props, categories, featured pieces, newsletter)
├── shop.php                # Master catalog with multi-facet filters (gender, category, price slider, sort)
├── product.php             # Garment detail page (gallery, size/color selector, stock indicator, reviews)
├── cart.php                # Shopping bag with live quantity modifiers and voucher engine
├── checkout.php            # Checkout flow with multi-method payment simulation & address autofill
├── order_confirmation.php  # Official invoice, printable receipt, and 4-stage visual order tracker
├── account.php             # Patron member dashboard with past order tracking & profile management
├── login.php               # Client & staff sign-in
├── register.php            # New patron registration
├── logout.php              # Session termination
├── config/
│   ├── config.php          # Brand constants, tax rates, shipping thresholds
│   └── database.php        # PDO connection with automatic self-migration & auto-seeding
├── database/
│   ├── schema.sql          # SQL schema definition
│   ├── seed.php            # Seeder with 12+ garments, categories, test accounts, orders, reviews
│   └── valenti_atelier.db  # Portable SQLite database file
├── includes/
│   ├── header.php          # Responsive luxury header with live bag counter
│   ├── footer.php          # Footer with lookbook links, newsletter form, and academic notice
│   ├── auth.php            # Role authentication and CSRF security
│   └── functions.php       # Cart calculations, formatters, flash messages, star ratings
├── admin/
│   ├── index.php           # Operations dashboard: KPIs, low-stock alerts, recent orders
│   ├── products.php        # Product inventory CRUD management
│   ├── product_form.php    # Add / Edit garment with image upload and department tags
│   ├── orders.php          # Orders fulfillment and dispatch status updater
│   ├── order_detail.php    # Packing slip and order inspector
│   ├── coupons.php         # Promotional vouchers creator
│   ├── customers.php       # Client directory with spending metrics
│   ├── login.php           # Dedicated administrator login
│   ├── admin_header.php    # Sidebar and admin shell
│   └── admin_footer.php    # Admin layout close
└── assets/
    ├── css/
    │   └── style.css       # Bespoke luxury design system stylesheet
    ├── js/
    │   └── main.js         # Interactive quantity modifiers, size pills, flash alerts
    └── images/
        ├── cat_*.jpg       # High-resolution category editorial photography
        └── products/*.jpg  # Curated studio garment photography (12 pieces)
```
