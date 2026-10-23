# K-Connect (Laravel)

This is K-Connect rebuilt on Laravel 12. It uses **the same database and the same 61 tables** as the
old plain-PHP site. Nothing in the database is created, renamed or dropped.
Pages have clean addresses without `.php` (`/employee-directory`, `/admin/dashboard`). Old addresses
such as `/employee-directory.php` still work: they redirect to the clean one, so bookmarks, menu links
stored in the database and emails already sent keep working. `public/style.css` is an unchanged copy
of the old one.

## What changed and what didn't

| Stays the same | Now done by Laravel |
| --- | --- |
| Database `kiboko_intranet_portal` and all 61 tables, columns, indexes and data | DB access (`DB::` and one Eloquent model per table in `app/Models`) |
| K-Academy database `kiboko_academy` and the user sync | A second DB connection called `kacademy` |
| Every page, form field, AJAX endpoint and JSON response (now at clean URLs) | Routes in `routes/kconnect/*.php`, controllers in `app/Http/Controllers` |
| The HTML of every page and `style.css` | Blade views in `resources/views` |
| Uploaded files (`uploads/`, `admin/uploads/`, `intranet/uploads/`) | Served from `public/` |
| Gmail emails (IT tickets, rewards, welcome mails) | Laravel Mail, settings in `.env` |
| Logins for employees and admins | Laravel sessions (stored as files, so no new table) |

New: every form is protected against CSRF automatically, passwords live only in `.env` (no longer
in `config.php`), and uploaded folders cannot run PHP.

## Where images and uploaded files go

Everything the browser loads directly lives in `public/`, in the same sub-folders as on the old site:

| Old site folder | Laravel folder |
| --- | --- |
| `uploads/` (employees, news, gallery, brand_assets, contacts, rewards, it_tickets, backups...) | `public/uploads/` |
| `admin/uploads/` | `public/admin/uploads/` |
| `intranet/uploads/` | `public/intranet/uploads/` |
| `rewards/uploads/` | `public/rewards/uploads/` |
| `K-CONNECT.png`, `K CONNECT PNG.png`, `favi.png` | `public/` |

`php artisan kconnect:import-files <old site folder>` copies all of these for you (it never copies
`.php` files and never changes the old folder). New uploads made in the Laravel site are saved in
the same places.

## Run it on XAMPP (your PC)

1. The project goes in `C:\xampp\htdocs\kconnect-laravel` (next to the old site, which is not touched).
2. Make sure `.env` exists (copy `.env.example` to `.env` and fill in the passwords if it is missing).
   For local testing set `APP_URL=http://localhost/kconnect-laravel/public`.
3. Open a terminal in that folder and run:
   ```
   C:\xampp\php\php.exe artisan key:generate        (only if APP_KEY in .env is empty)
   C:\xampp\php\php.exe artisan kconnect:verify-schema
   C:\xampp\php\php.exe artisan kconnect:import-files C:\xampp\htdocs\kconnect.kibokogroup.com
   ```
   `verify-schema` only reads the database and should say `61 identical`.
   `import-files` copies the uploaded photos, documents and logos from the old site into `public/`.
4. Open http://localhost/kconnect-laravel/public/login.php

## Put it live on cPanel

1. Upload the folder to `/home2/kiboko/kconnect-laravel` (beside the current site, keep the old one as backup).
2. Fill in `.env` with the live passwords, `APP_ENV=production`, `APP_DEBUG=false`,
   `APP_URL=https://kconnect.kibokogroup.com`. Select PHP 8.2 or 8.3 for the domain.
3. In the cPanel terminal:
   ```
   php artisan kconnect:verify-schema
   php artisan kconnect:import-files /home2/kiboko/kconnect.kibokogroup.com
   chmod -R 775 storage bootstrap/cache
   php artisan config:cache && php artisan route:cache && php artisan view:cache
   ```
4. Point the domain's document root to `/home2/kiboko/kconnect-laravel/public`.
   If you cannot change the document root, point it at the project folder instead: the `.htaccess`
   there forwards everything into `public/` and blocks `.env`, `vendor/` and the rest.

After editing `.env` on the live server, run `php artisan config:cache` again.

## .env settings

| Setting | What it is |
| --- | --- |
| `DB_*` | The K-Connect database (was `DB_HOST/DB_USER/DB_PASS/DB_NAME` in config.php) |
| `KA_DB_*` | The K-Academy database (was `KA_DB_*` in config.php) |
| `MAIL_*` | Gmail account and app password (was in it-mailer.php) |
| `APP_TIMEZONE` | Leave empty to use php.ini's timezone, like the old site |
| `KC_CACHE_ENABLED` | `true` turns on the same 5-minute page-data cache the old site had |
| `KC_STRICT_ERRORS` | Keep `false` on the live site; `true` makes PHP warnings show as errors while developing |

## Where things are

- `routes/kconnect/` URL list, one file per area (home, people, IT, projects, nominations, admin...)
- `app/Http/Controllers/` the code of each page
- `resources/views/` the HTML of each page (Blade)
- `app/Support/legacy.php` the old `config.php` helper functions (`isLoggedIn`, `sanitize`, `getNewsPosts`...)
- `app/Support/kacademy.php`, `projects.php`, `it_mailer.php` the other old shared files
- `app/Support/Academy.php`, `academy_helpers.php`, `app/Http/Controllers/Academy/`, `resources/views/academy/`, `routes/kconnect/academy.php` the K-Academy module
- `app/Models/` one model per table, mapped to the existing table names
- `database/migrations/` a single migration with the exact current table definitions. It only creates
  tables that do not exist yet (for a brand new empty database) and never changes existing ones.

## K-Academy (learning module)

K-Academy (courses, lessons, quizzes, certificates) now runs inside K-Connect on the same login.
It still uses its own database, `kiboko_academy` (the `KA_DB_*` settings), with no table changes.

- Employees open it from the user menu, **My Learning (K-Academy)**, or at `/academy`.
  Admins manage it from the K-Connect admin sidebar, **K-Academy**, or at `/academy/admin`.
- The first time an employee opens K-Academy, they are linked to their K-Academy account
  by employee code (then by e-mail), or a new one is made. A learner set to inactive in
  K-Academy admin gets "access denied".
- Lesson videos can be a YouTube, Vimeo or Google Drive link, a direct video address, or an uploaded file.
  **Upload Videos** on a course's lessons page uploads several videos at once and makes one lesson per video.
- Passing a course's quiz requests a certificate. An admin approves it under Certificates, and then
  the learner can download the PDF. Anyone can check a certificate at `/academy/verify-certificate`.
- Uploaded files are stored in `public/academy-files/assets/uploads/` (`courses`, `videos`,
  `attachments`, `profiles`). They are not in git. To copy them from the old K-Academy site, run
  `php artisan kconnect:import-academy-files <old K-Academy folder>`. This only copies.
- The `KA_CERT_*` settings are the names printed on certificates. `KA_MAX_VIDEO_MB` is the largest video upload (default 500).
  PHP must allow uploads that big. `public/.user.ini` (cPanel) and `public/.htaccess` (XAMPP) set the limit to 512M.
  On XAMPP, if uploads are still refused, raise `upload_max_filesize` and `post_max_size` in `php.ini`.
- The old K-Academy addresses (`login.php`, `course-player.php?id=...`, `admin/manage_courses.php`...)
  still work under `/academy/`.

## Never do this on the live database

Do not run `php artisan migrate:fresh`, `migrate:reset`, `migrate:rollback` or `db:wipe`.
They are not needed for anything; the database is already complete.
