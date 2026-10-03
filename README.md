# MarketMate POS

MarketMate POS is a beginner-friendly CodeIgniter 4 application for managing customer and staff account records. TFA3 adds validated add/edit forms and prepared user avatar images to the database-backed pages from TFA2.

## Features

- Landing and About pages
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

6. For a new installation, import `database/marketmate_pos.sql` into an empty database through phpMyAdmin or the MySQL command line. Do not import it over an existing MarketMate database. It includes the `users.avatar` column and sample records.
7. For an existing TFA2 installation, keep the current database and add the new column with:

   ```powershell
   php spark migrate
   ```

   This migration preserves existing user records. Check `php -m` for `gd`; if it is missing, enable `extension=gd` in the PHP configuration used by the command line, then restart the PHP server.
8. Start the application:

   ```powershell
   php spark serve
   ```

9. Visit `http://localhost:8080/`.

## Routes

- `/` - Landing page
- `/about` - About page
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

## Database Flow

The `CustomerModel` and `UserModel` retrieve and save database records. The controllers validate only the expected form fields before calling `insert()` or `update()`. Forms include CSRF tokens and show field errors while keeping previously entered values. The listing views use `foreach` to display the records.

For avatars, the user edit form sends multipart data. The controller checks the file type, extension, size, and image dimensions. `AvatarUploader` moves the upload to a private staging folder and uses CodeIgniter's Image service to create a 300 × 300 public image. The database stores only its generated filename. Uploaded pictures are kept in `public/uploads/avatars/` and are excluded from Git; back up or transfer that folder when deploying. Without its picture file, a record displays the placeholder. No login or password form is part of TFA3.

This is a classroom prototype without authentication. Its add/edit pages should not be exposed on a public server with real customer or staff information until access control is added.

## Testing

Run the automated checks from the project root:

```powershell
php -d extension=sqlite3 vendor\bin\phpunit --no-coverage
```

The SQLite extension is used for isolated form tests; they do not modify the live MySQL records.

The tables retain the required TFA2 fields (`id`, `full_name`, `created_at`, and the specified contact and username fields). They also include optional POS fields from the initial schema draft. `created_at` represents the registration date. The nullable `password_hash` field is reserved for a future login feature; no passwords are stored or displayed in this activity.

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
| `password_hash` | Reserved for a future login feature |
| `role` | Admin, Manager, Cashier, or Inventory |
| `account_status` | Active or Inactive |
| `last_login` | Optional last sign-in time |
| `created_at` | Account creation date and time |
| `avatar` | Optional prepared picture filename |
