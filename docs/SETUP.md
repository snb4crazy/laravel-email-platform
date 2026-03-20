# Setup & Configuration Guide

## Local Development Setup

### Prerequisites
- PHP 8.2+
- Composer
- MySQL 8.0+ or PostgreSQL 12+ (for production; SQLite for development)
- Node.js 18+ (for frontend assets, if using Vite)
- Git

### Installation Steps

1. **Clone the Repository**
   ```bash
   git clone <repository-url>
   cd email_platform
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install  # if using frontend assets
   ```

3. **Create `.env` File**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure Database**
   ```bash
   # Edit .env
   DB_CONNECTION=sqlite
   DB_DATABASE=/path/to/database.sqlite
   
   # Or for MySQL
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=email_platform
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run Migrations**
   ```bash
   php artisan migrate
   ```

6. **Start Development Server**
   ```bash
   php artisan serve
   ```
   Application available at `http://localhost:8000`

## Environment Configuration

### Core Settings (`.env`)

```env
# Application
APP_NAME="Email Platform"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_KEY=                          # Generated with php artisan key:generate

# Database
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Or MySQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=email_platform
# DB_USERNAME=root
# DB_PASSWORD=secret

# Mail Configuration
MAIL_MAILER=log                   # For development (logs to storage/logs)
MAIL_HOST=smtp.mailtrap.io        # For testing with Mailtrap
MAIL_PORT=465
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="Email Platform"

# For Production: Postmark, Sendgrid, AWS SES, etc.
# MAIL_MAILER=postmark
# POSTMARK_API_KEY=...

# Queue
QUEUE_CONNECTION=sync             # sync for dev, redis/database for production

# Cache
CACHE_STORE=file                  # file for dev, redis for production

# Session
SESSION_DRIVER=file               # file for dev, cookie for production

# Draft Auth (disabled by default)
DRAFT_AUTH_ENFORCE=false          # Set to true when ready to enforce

# Captcha (when implemented)
DRAFT_CONTACT_CAPTCHA_ENABLED=false
DRAFT_CONTACT_CAPTCHA_PROVIDER=turnstile
DRAFT_CONTACT_CAPTCHA_SECRET=

# Azure Blob Storage (for file attachments)
AZURE_STORAGE_ACCOUNT=
AZURE_STORAGE_KEY=
AZURE_CONTAINER=attachments
```

### Configuration Files

#### `config/app.php`
- `APP_NAME` - Application name (used in emails, API responses)
- `APP_ENV` - Environment (local, testing, production)
- `APP_DEBUG` - Debug mode (disable in production)
- `APP_URL` - Base URL for links

#### `config/database.php`
- Database connection settings
- Ensure correct driver and credentials

#### `config/mail.php`
- Mailer driver selection
- From address and name
- For production, configure Postmark, Sendgrid, etc.

#### `config/draft_auth.php`
- API key enforcement settings
- Captcha provider and credentials
- Webhook signature algorithm
- Rate limiting thresholds

#### `config/multisite.php`
- Site resolution field names
- Tenant context provider
- Default auth mode

#### `config/azure_blob.php`
- Azure storage credentials
- Container name
- File upload settings

## Database Setup

### SQLite (Development)

```bash
# Create database file
touch database/database.sqlite

# Set permissions
chmod 644 database/database.sqlite

# Run migrations
php artisan migrate
```

### MySQL (Recommended for Production)

```bash
# Create database
mysql -u root -p
CREATE DATABASE email_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Update .env with credentials
# Run migrations
php artisan migrate
```

### PostgreSQL

```bash
# Create database
createdb email_platform

# Update .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=email_platform
DB_USERNAME=postgres
DB_PASSWORD=

# Run migrations
php artisan migrate
```

## Mail Configuration

### Development: Log Driver
```env
MAIL_MAILER=log
# Emails will be logged to storage/logs/laravel.log
```

### Development: Mailtrap
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
```

### Production: Postmark
```env
MAIL_MAILER=postmark
POSTMARK_API_KEY=your_postmark_api_key
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your Company"
```

### Production: Sendgrid
```env
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=your_sendgrid_api_key
```

### Production: AWS SES
```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your_aws_key
AWS_SECRET_ACCESS_KEY=your_aws_secret
AWS_DEFAULT_REGION=us-east-1
```

## Queue Configuration

### Development: Sync Driver (Immediate Execution)
```env
QUEUE_CONNECTION=sync
# Jobs execute immediately - no async benefits but easier to debug
```

### Production: Redis
```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Start queue worker
php artisan queue:work
# Or in supervisor/systemd
```

### Production: Database
```env
QUEUE_CONNECTION=database
# Jobs stored in jobs table
# Start queue worker
php artisan queue:work database
```

### Queue Worker CLI Commands (Quick Reference)

Run one worker in foreground (useful for debug):

```bash
php artisan queue:work database --queue=default --sleep=3 --tries=3 --timeout=120 -v
```

Use the configured default connection automatically (recommended when switching
between `redis` / `database` via `.env`):

```bash
php artisan queue:work --queue=default --sleep=3 --tries=3 --timeout=120 -v
```

Explicit Redis worker example:

```bash
php artisan queue:work redis --queue=default --sleep=3 --tries=3 --timeout=120 -v
```

Process only one job and exit:

```bash
php artisan queue:work database --once
```

Restart workers after deploy/config changes:

```bash
php artisan queue:restart
```

### Supervisor Setup (Production)

Install Supervisor (Ubuntu/Debian example):

```bash
sudo apt-get update
sudo apt-get install -y supervisor
sudo systemctl enable supervisor
sudo systemctl start supervisor
```

Create worker config at `/etc/supervisor/conf.d/email-platform-worker.conf`:

```ini
[program:email-platform-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/email_platform/artisan queue:work database --queue=default --sleep=3 --tries=3 --timeout=120 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/email_platform/storage/logs/worker.log
stopwaitsecs=3600
```

Apply config and start workers:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start email-platform-worker:*
sudo supervisorctl status
```

Useful operations:

```bash
sudo supervisorctl restart email-platform-worker:*
sudo supervisorctl stop email-platform-worker:*
sudo supervisorctl tail -f email-platform-worker:email-platform-worker_00
```

## Caching Configuration

### Development
```env
CACHE_STORE=file
```

### Production: Redis
```env
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## Storage Configuration

### Local File Storage (for attachments)
```env
# config/filesystems.php
FILESYSTEM_DRIVER=local
# Files stored in storage/app/
```

### Azure Blob Storage
```env
AZURE_STORAGE_ACCOUNT=youraccountname
AZURE_STORAGE_KEY=your_storage_key
AZURE_CONTAINER=attachments
# config/filesystems.php has azure disk configured
```

### AWS S3
```env
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_URL=
# config/filesystems.php has s3 disk configured
```

## Multi-Tenancy Configuration

### `config/multisite.php`

```php
return [
    'fields' => [
        'site_key' => 'site_key',        // Request field for site public key
        'site_id' => 'site_id',          // Request field for site ID
        'site_domain' => 'site_domain',  // Request field for domain
    ],
    
    'tenant_provider' => 'database',      // How to resolve tenant
    'resolve_via_header' => false,        // Allow X-Tenant-ID header
];
```

## Draft Auth Configuration

### `config/draft_auth.php`

```php
return [
    'enforce' => env('DRAFT_AUTH_ENFORCE', false),
    
    'contact' => [
        'require_api_key' => env('DRAFT_CONTACT_REQUIRE_API_KEY', false),
        'header' => env('DRAFT_CONTACT_API_KEY_HEADER', 'X-Api-Key'),
        
        'captcha' => [
            'enabled' => env('DRAFT_CONTACT_CAPTCHA_ENABLED', false),
            'provider' => env('DRAFT_CONTACT_CAPTCHA_PROVIDER', 'turnstile'),
            'secret' => env('DRAFT_CONTACT_CAPTCHA_SECRET'),
        ],
    ],
    
    'webhook' => [
        'require_signature' => env('DRAFT_WEBHOOK_REQUIRE_SIGNATURE', false),
        'algorithm' => env('DRAFT_WEBHOOK_SIGNATURE_ALGO', 'sha256'),
        'timestamp_ttl' => 300,           // 5 minute window
        'nonce_storage' => 'cache',       // Or 'database'
    ],
];
```

## Logging Configuration

### `config/logging.php`

Default: single file channel logs to `storage/logs/laravel.log`

For production, consider:
```php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'sentry'],
    ],
    'sentry' => [
        'driver' => 'sentry',
        'level' => 'error',
    ],
],
```

## Docker Setup

### Dockerfile
```dockerfile
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install

ENV APP_ENV=production
ENV APP_DEBUG=false

CMD ["php", "artisan", "serve", "--host=0.0.0.0"]
```

### docker-compose.yml
```yaml
version: '3.8'

services:
  app:
    build: .
    ports:
      - "8000:8000"
    environment:
      - DB_CONNECTION=mysql
      - DB_HOST=db
      - DB_DATABASE=email_platform
      - DB_USERNAME=root
      - DB_PASSWORD=secret
    depends_on:
      - db
    volumes:
      - .:/app

  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: email_platform
      MYSQL_ROOT_PASSWORD: secret
    ports:
      - "3306:3306"
    volumes:
      - db_data:/var/lib/mysql

  redis:
    image: redis:7
    ports:
      - "6379:6379"

volumes:
  db_data:
```

**Start containers:**
```bash
docker-compose up -d
docker-compose exec app php artisan migrate
```

## Azure VM Deployment (Portable Profile)

For Azure VM testing (and reusable settings for other clouds), use the
dedicated template file: `.env.azure.example`.

### 1) Prepare environment on the VM

```bash
cd /var/www/email_platform
cp .env.azure.example .env
php artisan key:generate
```

### 2) Fill production values in `.env`

Required to set:
- `APP_URL` (your HTTPS API URL)
- `DB_*` (Azure Database for MySQL/PostgreSQL or your DB host)
- `REDIS_*` (Azure Cache for Redis, if enabled)
- `MAIL_*` (SMTP/provider credentials)

Optional depending on feature usage:
- `AZURE_BLOB_*` (if attachment storage is enabled)
- `AWS_*` (if using S3-compatible storage instead)

### 3) Run first-time deployment commands

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

### 4) Verify app health

```bash
curl -s https://your-api-domain/api/health
curl -s https://your-api-domain/api/version
```

### Notes

- CI/CD host and SSH values (`STAGING_*`, `PRODUCTION_*`, `*_SSH_KEY`) stay in
  GitHub Environments/Secrets, not in app `.env` files.
- Keep `.env.azure.example` as a template only (no real secrets in git).
- The profile is Azure-friendly but portable to other clouds by changing
  `DB_*`, `REDIS_*`, `MAIL_*`, and storage settings.

## Health Checks

### Verify Installation
```bash
# Check version
php artisan --version

# Check routes
php artisan route:list

# Check database connection
php artisan tinker
> DB::connection()->getPdo();

# Check cache
php artisan tinker
> Cache::put('test', 'value'); Cache::get('test');
```

### API Health Endpoint
```bash
curl http://localhost:8000/api/health
# Response: { "status": "ok" }
```

### Contact API Smoke Test (Generic cURL)

Use plain ASCII quotes in JSON. Replace placeholder values:

```bash
curl -i -X POST "https://YOUR_API_BASE_URL/api/contact" \
  -H "Content-Type: application/json" \
  -d '{
    "site_key": "YOUR_PLATFORM_SITE_KEY",
    "name": "Jane Doe",
    "email": "jane@example.com",
    "subject": "Contact form test",
    "message": "Hello from cURL",
    "captcha_token": "ONE_TIME_CAPTCHA_TOKEN_IF_REQUIRED",
    "request_id": "req-demo-001",
    "meta": {
      "source": "curl",
      "environment": "local"
    }
  }'
```

Expected success response:

```json
{
  "message": "Contact request received."
}
```

Notes:
- If `DRAFT_AUTH_ENFORCE=false`, `captcha_token` can be omitted.
- If site auth mode is `captcha`, `captcha_token` must be a real browser-generated token.
- If response is HTML redirect, payload JSON is likely malformed (smart quotes are a common cause).

## Troubleshooting

### Storage Permissions
```bash
# Make storage writable
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### Composer Install Issues
```bash
# Clear composer cache
composer clear-cache

# Update composer
composer self-update

# Re-install
composer install
```

### Database Connection Issues
```bash
# Check .env DB_* values
# MySQL: ensure server is running
# SQLite: ensure database file exists and is writable

# Test connection
php artisan tinker
> DB::connection()->getPdo();
```

### Route Not Found
```bash
# Clear routes cache
php artisan route:clear

# Verify routes registered
php artisan route:list | grep api

# Check bootstrap/app.php routing config
```

## Git Push & Tagging

Push your branch and release tags to the remote repository:

```bash
git push origin master

git push origin v1.0.0 v1.1.0
# or push all tags at once:
git push origin --tags
```

## Bootstrap First User (CLI)

Public signup is intentionally disabled. Create users from CLI:

```bash
php artisan platform:bootstrap-user
```

You will be prompted for:
- full name
- email
- password and confirmation
- whether the email should be marked as verified

The bootstrap command creates this first account with the `admin` role.

## RBAC and User Management

- Roles are currently: `admin`, `user`
- Public `/register` is blocked (HTTP 403)
- Admin users can create users from the admin panel

Login and admin routes:

```bash
GET  /login
POST /login
POST /logout
GET  /admin/users
GET  /admin/users/create
POST /admin/users
```

## Next Steps

1. **Configure Mail**: Set up actual mail driver (Postmark, Sendgrid, etc.)
2. **Implement Auth**: Enable `DRAFT_AUTH_ENFORCE` and wire authentication
3. **Set Up Queue**: Configure Redis and queue worker for production
4. **Add Tests**: Implement test cases from `docs/TESTING.md`
5. **Deploy**: Follow your organization's deployment procedures

---

Last updated: March 19, 2026

