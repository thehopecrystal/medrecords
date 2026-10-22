# MedRecords — Deployment Checklist

## Server requirements
- PHP 8.2+ with extensions: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, fileinfo
- MySQL 8 / MariaDB 10.6+
- Apache 2.4+ with mod_rewrite enabled (or Nginx with equivalent config)
- Composer 2

---

## First deploy

### 1. Upload files
Upload everything **except** `.env`, `node_modules/`, `vendor/`, `storage/logs/*`, `storage/framework/cache/*`.

### 2. Install PHP dependencies (production only)
```bash
composer install --no-dev --optimize-autoloader
```

> **Keep `vendor/` small.** Always install from **dist** (the default), never from
> source. If Composer's GitHub credentials are invalid or its API rate limit is
> exhausted, `composer install` silently falls back to a `git clone` per package.
> Each clone carries that package's full `.git` history inside `vendor/`, which
> can inflate the directory by several hundred megabytes.
>
> If `vendor/` ever balloons, run:
>
> ```bash
> composer config --global --unset github-oauth.github.com   # clear a stale token
> composer reinstall --prefer-dist --no-interaction
> ```
>
> Verify with `composer check-vendor-size` (see `tools/check-vendor-size.sh`).

### 3. Create `.env` on the server
Copy `.env.example` and fill in real values:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com          # ← update this
APP_KEY=                                # ← run: php artisan key:generate

DB_HOST=127.0.0.1
DB_DATABASE=medrecords
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true             # ← requires HTTPS
SESSION_SAME_SITE=strict

LOG_LEVEL=error

MAIL_MAILER=smtp
MAIL_HOST=smtp.yourmailprovider.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your_mail_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="MedRecords"
```

### 4. Generate app key (if new install)
```bash
php artisan key:generate
```

### 5. Run migrations
```bash
php artisan migrate --force
```

### 6. Create storage symlink
```bash
php artisan storage:link
```

### 7. Cache everything
```bash
php artisan optimize
```

### 8. Set file permissions
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## Every subsequent deploy

```bash
# Pull new code
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader

# Run new migrations
php artisan migrate --force

# Rebuild caches
php artisan optimize

# Restart queue workers (if using queues)
php artisan queue:restart
```

---

## Apache virtual host (recommended over .htaccess forwarding)

Point `DocumentRoot` directly at `public/` — no need for the root `.htaccess`:

```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /var/www/medrecords/public

    <Directory /var/www/medrecords/public>
        AllowOverride All
        Require all granted
    </Directory>

    SSLEngine on
    SSLCertificateFile    /etc/letsencrypt/live/yourdomain.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/yourdomain.com/privkey.pem
</VirtualHost>

# Redirect HTTP → HTTPS
<VirtualHost *:80>
    ServerName yourdomain.com
    Redirect permanent / https://yourdomain.com/
</VirtualHost>
```

---

## Scheduled tasks (cron)

Add to the server crontab (`crontab -e`):
```
* * * * * php /var/www/medrecords/artisan schedule:run >> /dev/null 2>&1
```

This runs the consent-expiry notification daily at 08:00 (configured in `bootstrap/app.php`).

---

## Queue worker (systemd service)

Create `/etc/systemd/system/medrecords-worker.service`:
```ini
[Unit]
Description=MedRecords Queue Worker
After=network.target

[Service]
User=www-data
WorkingDirectory=/var/www/medrecords
ExecStart=php artisan queue:work --tries=3 --timeout=90
Restart=on-failure
RestartSec=5

[Install]
WantedBy=multi-user.target
```
```bash
systemctl enable medrecords-worker
systemctl start medrecords-worker
```

---

## Post-deploy verification

- [ ] `https://yourdomain.com/up` returns HTTP 200
- [ ] Login works and redirects to correct dashboard
- [ ] Response headers include `X-Frame-Options`, `X-Content-Type-Options`
- [ ] `/login` over HTTP redirects to HTTPS
- [ ] `/storage` symlink resolves correctly
- [ ] `storage/logs/laravel.log` is writable
