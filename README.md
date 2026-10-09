# MarketMate POS

MarketMate is a CodeIgniter 4 point-of-sale classroom project for small businesses. Staff can manage products, customers, and team accounts, then record sales that update stock and appear in a shared history. The starter catalog happens to feature electronics, but the system can be used for other products. The public Home and About pages introduce the project; the operational pages require a staff sign-in.

## What it does

- **Products:** add, edit, archive, and restore any catalog item. Each product has a name, price, stock quantity, and optional prepared JPG/PNG image. The starter catalog includes electronics and accessories; archived products remain visible in past sales but cannot be sold.
- **Customers:** add, edit, and delete customer records. Customers can optionally be linked to a sale. A customer already referenced by sales cannot be deleted; mark the account Inactive instead.
- **Team:** add, edit, and delete staff, with an optional avatar and a hashed password. A staff member referenced by sales cannot be deleted, and the last active administrator cannot be removed or deactivated.
- **Sales:** choose an available product, an optional active customer, and a quantity. The server reads the current product price, checks stock, records the transaction, and decreases stock together. Overselling is rejected. Sales history shows the product, customer or walk-in, staff member, quantity, total, and date.
- **Access:** login, logout, session checks, protected routes, validation, CSRF tokens, and no-store headers for staff pages.

This project records one product per sale, matching the midterm activity's `sales.product_id` design. It is not yet a multi-item cart or payment-processing system.

## Requirements

- PHP 8.2 or newer, Composer, MySQL or MariaDB
- PHP extensions: `intl`, `mbstring`, `mysqli`, `fileinfo`, `gd`
- `sqlite3` for the isolated automated tests

## Local setup

Open PowerShell in the project root (the folder containing `spark`):

```powershell
composer install
Copy-Item env .env
```

If `.env` already exists, keep it rather than replacing your local settings. Set the following values in `.env` for your computer:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = marketmate_pos
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

Start MySQL. Then choose **one** database path:

1. **Fresh installation:** create an empty `marketmate_pos` database and import [`database/marketmate_pos.sql`](database/marketmate_pos.sql). It includes the required tables and sample product, customer, and staff records. Do not import it over existing records.
2. **Existing MarketMate installation:** keep your current database and run `php spark migrate` to add the new tables. Then run `php spark db:seed ProductSeeder` to add any missing sample catalog items and attach the bundled photos to matching products that have no image. It can be rerun without changing existing prices, stock, or staff-uploaded photos. If older staff rows have no password hash, run `php spark db:seed StaffPasswordSeeder`.

For either path, assign a private sign-in password to an active staff account. For the sample administrator:

```powershell
php spark marketmate:password admin01
php spark serve
```

The password command displays a new one-time password and stores only its hash. Keep that password private; running the command again resets it. Open `http://localhost:8080/login` and sign in with username `admin01` and the generated password. The SQL export intentionally contains no usable plaintext passwords.

If image preparation fails locally, confirm `gd` appears in `php -m`, enable `extension=gd` in the PHP configuration used by the CLI, and restart the development server.

## Main routes and files

| Area | URL | Implementation |
| --- | --- | --- |
| Public introduction | `/`, `/about` | `Pages` controller; `app/Views/pages/` |
| Staff entry and overview | `/login`, `/dashboard` | `Auth`, `Dashboard`, `StaffAuth` filter |
| Catalog management | `/products` | `Products`, `ProductModel`, `app/Views/products/` |
| Record and review sales | `/sales/new`, `/sales` | `Sales`, `SaleModel`, `app/Views/sales/` |
| Customer accounts | `/customers` | `Customers`, `CustomerModel`, `app/Views/customers/` |
| Team accounts | `/users` | `Users`, `UserModel`, `app/Views/users/` |

The shared header, footer, and styles live in `app/Views/templates/`, `public/assets/css/style.css`, and `public/assets/js/app.js`. The Home hero uses an AI-generated storefront image provided by the project owner and saved locally at `public/assets/images/marketmate-storefront-hero-user.png`. The About hero uses an AI-generated interior image supplied by the project owner and saved locally at `public/assets/images/marketmate-about-interior.png`, and the About page includes the developer portrait at `public/assets/images/developer.jpg`. Uploaded product and staff images are prepared by classes in `app/Libraries/` and stored under `public/uploads/`; generated uploads are intentionally not tracked in Git.

Most sample products use AI-generated, unbranded illustrative photos in `public/assets/images/products/`. The Compact Mechanical Keyboard instead uses a real photograph by [Marcus Richardson on Unsplash](https://unsplash.com/photos/a-black-mechanical-keyboard-rests-on-a-wooden-desk-qXWO1I4iPMM), available under the [Unsplash License](https://unsplash.com/license). The filenames are included in the SQL export and `ProductSeeder`; these catalog images do not claim to depict specific models for sale. Staff can add or replace a photo through the Add/Edit Product form. Products without an image still use a neutral placeholder.

The migration `app/Database/Migrations/2026-10-06-000001_CreateProductsAndSales.php` adds the product and sale tables. Sales refer to products and staff, and optionally to customers. The sale controller uses a conditional stock update inside a database transaction, so a failed insert does not leave stock reduced. The displayed total comes from the stored product price, not an amount posted by the browser.

## Verification

Run the isolated automated checks from the project root:

```powershell
php -d extension=sqlite3 vendor\bin\phpunit --no-coverage
```

The tests use an in-memory SQLite database and do not modify your MySQL records. For a manual check: sign in, create or edit a product, record a sale, confirm its stock decreased and the sale appears in history, then try to sell more than the remaining stock. Log out and confirm `/products`, `/sales`, `/customers`, and `/users` redirect to login. Check the public pages in both desktop and narrow browser widths.

## Deployment note

No live hosting is configured by this repository. Do not commit `.env`, generated passwords, or uploaded private data. Before use with real business data, add role-based permissions, rate limiting, HTTPS, backups, and a production deployment review.
