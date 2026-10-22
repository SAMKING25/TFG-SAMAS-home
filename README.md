# SAMAS home

![SAMAS home](img/portada-samas.png)

**SAMAS home** is a furniture marketplace for small local shops in Málaga (Spain). Shops publish their products, customers browse and buy from several shops in a single cart, and an **interactive floor planner** lets customers draw their room and place the furniture before buying it.

It was built as the final degree project (TFG) of the *Desarrollo de Aplicaciones Web* (Web Application Development) vocational degree at MEDAC, 2024/2025, by a team of five students.

🔗 **Live demo:** https://samashome.xo.je

---

## Demo accounts

Registration sends a verification code by email, which the free hosting does not deliver reliably (see [Known limitations](#known-limitations)). Use these accounts to try the site:

| Role | Email | Password |
|---|---|---|
| Customer (VIP subscription, unlimited planner use) | `demo@example.com` | `SamasDemo2026` |
| Shop / provider | `proveedor1@example.com` | `SamasDemo2026` |

`proveedor2@example.com` and `proveedor3@example.com` use the same password.

---

## Features

### Visitors
- Home page with latest offers, categories and new products
- Product catalogue with search and filters (category, price, offers)
- Product page with dimensions and quantity selector
- Subscription plans and contact form
- Cookie consent banner and cookie policy
- AI chat assistant to help design your home

### Customers
- Sign up with email verification code, log in, edit profile and credentials
- Cart with products from several shops
- Checkout with PayPal
- Order history and order details
- **Interactive floor planner**
  - Draw walls, doors and windows, rotate and move items
  - Place the products from your cart using their real dimensions
  - Zoom, pan, show or hide measurements
  - Export the plan as an image
  - Number of uses limited by subscription: Basic (1), Premium (4), VIP (unlimited)

### Shops (providers)
- Separate login and dashboard
- Create, edit and delete products with images, stock, dimensions and offers
- Profile settings

### Under the hood
- Clean URLs without `.php` extensions and blocked access to private files (`.htaccess`)
- Custom error pages (400, 401, 403, 404, 500, 502, 503)
- Passwords stored with `password_hash()` / `password_verify()`
- Transactional emails for verification, contact and orders

---

## Tech stack

| Layer | Technologies |
|---|---|
| Frontend | HTML5, CSS3, JavaScript, Bootstrap 5.3, Bootstrap Icons, SweetAlert2 |
| Floor planner | Fabric.js (HTML5 canvas) |
| Backend | PHP 8 (no framework) |
| Database | MySQL / MariaDB |
| Payments | PayPal JavaScript SDK |
| AI assistant | Udify bot |
| Tools | Git and GitHub (one branch per member), Trello, XAMPP, VS Code |
| Hosting | Hostinger during the project; InfinityFree for the current demo |

---

## Project structure

```
├── carrito/            Shopping cart
├── contacto/           Contact form
├── login/
│   ├── usuario/        Customer sign up, login, verification, profile
│   └── proveedor/      Shop sign up, login, profile
├── panel-control/      Shop dashboard and product management
├── pasarela-pago/      PayPal checkout and confirmation emails
├── pedidos/            Orders
├── plano/              Interactive floor planner (Fabric.js)
├── productos/          Catalogue and product pages
├── suscripcion/        Subscription plans
├── util/
│   ├── bd/             SQL scripts
│   ├── funciones/      Shared PHP helpers
│   └── error.php       Error pages
├── img/                Images (products, categories, logos, avatars)
├── index.php           Home page
└── .htaccess           URL rewriting and error pages
```

---

## Run it locally

1. Install [XAMPP](https://www.apachefriends.org/) (Apache + PHP 8 + MySQL) and enable `mod_rewrite`.
2. Clone the repository into the web root, so the site is served at `http://localhost/` (the `.htaccess` and some paths assume the site is at the root, not in a subfolder):
   ```bash
   git clone https://github.com/SAMKING25/TFG-SAMAS-home.git
   ```
3. Create a database (for example `samashome`) and import `util/bd/samashome_demo.sql`. It creates the tables, sample products and the demo accounts above.
4. Copy `util/conexion.example.php` to `util/conexion.php` and fill in your database details. `conexion.php` is in `.gitignore`, so your credentials are never committed.
5. Open `http://localhost/`.

---

## Known limitations

This is a student project and the live demo runs on free hosting, so some features are limited:

- **Emails:** the free hosting restricts PHP `mail()`, so verification codes, contact messages and order confirmations may not arrive. New sign-ups are therefore not reliable on the demo; use the demo accounts. Planned fix: send email through an external SMTP service with PHPMailer.
- **Image uploads:** in October 2026 the hosting provider temporarily blocked all `multipart/form-data` requests while it rolled out a PHP security update. Until it is lifted, creating or editing products with images and changing profile pictures return a 403 error on the demo. Forms that do not upload files were switched to standard form encoding so they keep working.
- **AI assistant:** depends on an external Udify bot and may stop responding if the bot is unavailable.
- **Payments:** PayPal checkout is for demonstration only. Do not use real payment details.

---

## Roadmap

- Security hardening following the OWASP Top 10: prepared statements for every database query, error display disabled in production, secrets moved out of the code
- SMTP email delivery with PHPMailer
- Code clean-up and a shared layout to reduce duplicated HTML

---

## Team

Developed by:

- Sergio Álvarez
- Álvaro Saborido
- Samuel Trujillo
- **Manuel Vidal**
- Alejandro Vila

Tutor: Carmina Cervera · MEDAC, 2024/2025
