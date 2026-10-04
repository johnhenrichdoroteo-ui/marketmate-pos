# MarketMate POS

MarketMate POS is a CodeIgniter 4 classroom project for managing customer and staff accounts. It now includes a staff sign-in, a protected dashboard, and session-based access to the account pages. The login screen includes a locally stored checkout illustration.

## Features

- Landing and About pages
- Staff login and logout using hashed passwords and CodeIgniter sessions
- Protected dashboard, Customer Accounts, and User Accounts routes
- Guest redirects to login; inactive staff accounts cannot sign in
- Customer Accounts page backed by MySQL
- User Accounts page backed by MySQL
- Add and edit forms for customer and user accounts
- Server-side validation with errors and previously entered values preserved
- Optional JPG/PNG user avatar upload on the edit form (2 MB maximum)
- 300 × 300 avatar thumbnails and a placeholder for users without a picture
- Ten sample customer records and ten sample staff records
- Customer type, loyalty points, and account status
- Staff role, contact details, and account status
- CodeIgniter Models and Query Builder
- Responsive navigation and table layout
- Responsive login screen with a checkout illustration

## Requirements

- PHP 8.2 or newer
- PHP extensions `intl`, `mbstring`, `mysqli`, `fileinfo`, and `gd`
- Composer
- MySQL or MariaDB
- XAMPP can be used for Apache, MySQL, and phpMyAdmin

## Setup

1. Clone or download this repository.
2. Open a terminal in the project folder.
3. Install PHP dependencies:

   ```powershell
   composer install
   ```

4. Copy `env` to `.env` if `.env` does not exist.
5. Configure `.env` with your local settings:

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

6. For a new installation, import `database/marketmate_pos.sql` into an empty database through phpMyAdmin or the MySQL command line. Do not import it over an existing MarketMate database. It includes the `users.avatar` and `users.password_hash` columns and sample records. The sample passwords are unknown; set one for your own local use in step 8.
7. For an existing TFA2/TFA3 installation, keep the current database. If the avatar column has not yet been added, run:

   ```powershell
   php spark migrate
   ```

   This migration preserves existing user records. Existing MarketMate databases already have a nullable `password_hash` field. Fill only the missing hashes without changing account details:

   ```powershell
   php spark db:seed StaffPasswordSeeder
   ```

   Check `php -m` for `gd`; if it is missing, enable `extension=gd` in the PHP configuration used by the command line, then restart the PHP server.
8. In your own terminal, generate a private sign-in password for an active staff account. For the sample admin:

   ```powershell
   php spark marketmate:password admin01
   ```

   The command prints a random one-time password. Copy it immediately and keep it private; only its hash is stored in the database. Running the command again resets that account's password. Do not put the printed password in GitHub, screenshots, or the SQL export.
9. Start the application:

   ```powershell
   php spark serve
   ```

10. Visit `http://localhost:8080/login` and sign in with the staff username and generated password.

## Routes

- `/` - Landing page
- `/about` - About page
- `GET /login` - Staff sign-in form
- `POST /login` - Check credentials and create a session
- `POST /logout` - End the session and return to login
- `/dashboard` - Protected staff overview
- `/customers` - Customer Accounts
- `GET /customers/new` - Add Customer form
- `POST /customers` - Validate and create a customer
- `GET /customers/{id}/edit` - Edit Customer form
- `POST /customers/{id}` - Validate and update a customer
- `/users` - User Accounts
- `GET /users/new` - Add User form
- `POST /users` - Validate and create a user
- `GET /users/{id}/edit` - Edit User form with optional avatar upload
- `POST /users/{id}` - Validate and update a user/avatar

All dashboard, customer, and user routes require a valid staff session. Home, About, and Login remain public. Automatic routing is disabled, so there is no unprotected controller URL around the route filter.

## Database Flow

The `CustomerModel` and `UserModel` retrieve and save database records. The controllers validate only the expected form fields before calling `insert()` or `update()`. Forms include CSRF tokens and show field errors while keeping previously entered values. The listing views use `foreach` to display the records.

For avatars, the user edit form sends multipart data. The controller checks the file type, extension, size, and image dimensions. `AvatarUploader` moves the upload to a private staging folder and uses CodeIgniter's Image service to create a 300 × 300 public image. The database stores only its generated filename. Uploaded pictures are kept in `public/uploads/avatars/` and are excluded from Git; back up or transfer that folder when deploying. Without its picture file, a record displays the placeholder.

The login controller verifies a submitted password against `users.password_hash` and regenerates the session ID on successful sign-in. The `StaffAuth` route filter checks the session and that the staff account is still active before every protected request. Logout destroys the session. Adding a user requires a password and confirmation; editing a user can optionally replace the password. Password input is never flashed back to the form. The split login layout is in `app/Views/auth/login.php`; its checkout illustration is saved in `public/assets/images/marketmate-login-custom.jpg` and styled in `public/assets/css/style.css`.

This is still a classroom prototype. Before using it with real business data on a public server, add rate limiting, role-specific permissions, HTTPS, and production deployment configuration. Do not commit `.env`, generated credentials, or uploaded private data.

## Testing

Run the automated checks from the project root:

```powershell
php -d extension=sqlite3 vendor\bin\phpunit --no-coverage
```

The SQLite extension is used for isolated form and authentication tests; they do not modify the live MySQL records. Manual checks: open `/customers` while signed out (redirect to login), sign in, visit `/dashboard`, `/customers`, and `/users`, then log out and try `/users` again (redirect to login). Also test a wrong password and an inactive account.

The tables retain the required TFA2 fields (`id`, `full_name`, `created_at`, and the specified contact and username fields). They also include optional POS fields from the initial schema draft. `created_at` represents the registration date. `password_hash` stores a one-way hash, never a plaintext password.

### Customer fields

| Field | Purpose |
| --- | --- |
| `id` | Unique customer ID |
| `full_name`, `email`, `phone` | Contact information |
| `address` | Optional location |
| `customer_type` | Regular or Member |
| `loyalty_points` | Points balance |
| `account_status` | Active or Inactive |
| `created_at` | Registration date and time |

### Staff fields

| Field | Purpose |
| --- | --- |
| `id` | Unique staff ID |
| `username`, `full_name` | Account identity |
| `email`, `phone` | Optional contact information |
| `password_hash` | One-way hash used to verify staff login |
| `role` | Admin, Manager, Cashier, or Inventory |
| `account_status` | Active or Inactive |
| `last_login` | Optional last sign-in time |
| `created_at` | Account creation date and time |
| `avatar` | Optional prepared picture filename |
