# PrimeWater Quezon Metro — AGENTS.md

## Stack
- Flat PHP (no framework, no Composer, no router, no .htaccess)
- MySQL `prime2` on **localhost:8889** (MAMP default), user `root`, password `root`
- All passwords stored as **plain text** in `users` and `admin` tables
- No build/test/lint tooling, no CI, no npm/composer deps

## Frontend
- Meralco-inspired design in `css/meralco.css` (replaces old `css/maynilad.css`)
- Uses Open Sans (Google Fonts), Font Awesome 6, Bootstrap 4
- Color scheme: Deep blue #003A70, Orange accent #F47920
- All pages reference `css/meralco.css`

## File structure (routing)
URL maps directly to filesystem: `domain/login.php` → `login.php`. No MVC — HTML views include their handler at the top via `include`:
- `login.php` includes `logindata.php`
- `reg.php` includes `register.php`
- `application.php` includes `appdata.php`
- `complaint.php` includes `complaintdata.php`
- `adminLogin.php` includes `admindata.php`
- `admin.php` includes `admindata.php`
Forms POST to themselves; handlers check `isset($_POST['submit'])`.

## Auth
- **User**: `logindata.php` — prepared statement `SELECT * FROM users WHERE email=?`, plain-text password compare, session vars set (`email`, `name`, `password`, `user_id`, `accountnum`), redirects to `usermain.php`.
- **Admin**: `admindata.php` — SQL injection vulnerable `SELECT * FROM admin where user='$user' AND password='$password'`, sets `$_SESSION['user']`, redirects to `admin.php`.
- **Registration**: OTP shown in **JavaScript `alert()`** (never actually emailed). 6-digit code, 10-min expiry. User must call `verify_otp.php` to activate.

## Data flow quirks
- `payment_setup.php` must be run once to create `payments` table and `customer.pay_status` column before billing features work.
- `allbill.php` auto-creates missing tables/columns if they don't exist (idempotent).
- `xlsfile.php` is **dead code** (references undefined `$connect` and nonexistent `tbl_order`).
- `complaint-status.php` has **broken SQL** (`UPDATE INTO`) — do not use; `complaint-update.php` is the correct handler.
- `sss.php` is a test artifact (file upload test).

## Session guards
User pages redirect to `login.php` if `$_SESSION['email']` is not set. Admin pages redirect to `adminLogin.php` if `$_SESSION['user']` is not set.

## Companion app
`PrimeWaterApp/` is a C# .NET 9 WinForms app sharing the same `prime2` database. Not needed for PHP work.

## Directories to ignore
- `Claude/` — unrelated iOS Xcode project
- `PrimeWaterApp/` — separate C# desktop app
- `upload/` — user file uploads (public)
