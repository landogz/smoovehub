# SmooveHub

AI-powered multi-vendor furniture marketplace. Your Furniture. Our Promise.

## Prerequisites

- PHP 8.3+
- Composer
- Node.js 20+ and npm
- MySQL (XAMPP works well locally)

## First-time setup (after clone)

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
```

Create an empty MySQL database (for example `smoovehub`), then open the web installer.

## Auto-deploy (GitHub → IONOS)

Pushes to `main` trigger [`.github/workflows/deploy.yml`](.github/workflows/deploy.yml), which pulls on the server and uploads `public/build`.

Add these **GitHub → Settings → Secrets and variables → Actions** secrets:

| Secret | Example |
|--------|---------|
| `SSH_HOST` | `access-5017961219.webspace-host.com` |
| `SSH_USER` | `a970971` |
| `SSH_PASSWORD` | *(your SSH password)* |
| `SSH_PORT` | `22` |
| `SSH_PATH` | `/homepages/32/d4299061785/htdocs/smoovehub` |

## Web installer

1. Point your web server document root at `public/` (XAMPP: `http://localhost/Lamar/smoovehub/public`).
2. Visit `/install`.
3. Confirm server requirements are green.
4. Enter App URL, MySQL credentials, and your admin name/email/password.
5. Click **Install SmooveHub**.

The installer will:

- Write `.env` database settings
- Generate `APP_KEY` if needed
- Run migrations and seeders
- Create your admin account (`super-admin`)
- Create the `public/storage` link
- Lock further installs via `storage/app/installed`

After success you are redirected to `/admin`.

To re-run the installer (local/dev only), delete `storage/app/installed` and refresh `/install`.

## CLI setup alternative

```bash
composer setup
php artisan storage:link
```

Then edit `.env` for your database and run `php artisan migrate --seed` if needed.

## License

This application is built on the [Laravel framework](https://laravel.com), which is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
