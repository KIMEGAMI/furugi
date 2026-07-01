# Production Deployment

This project is a Laravel application with Vite assets.

The production environment assumed here is:

- Ubuntu
- Apache
- PHP 8.3 or newer
- Composer
- Node.js / npm
- MySQL or MariaDB
- GitHub remote repository

## 1. Prepare the Server

Install the required packages.

```bash
sudo apt update
sudo apt install -y apache2 mysql-server git unzip
sudo apt install -y php php-cli php-fpm php-mysql php-xml php-mbstring php-curl php-zip php-bcmath
```

Install Composer and Node.js if they are not already available.

```bash
composer --version
node -v
npm -v
```

## 2. Clone or Update the Application

For the first deployment:

```bash
cd /var/www
sudo git clone https://github.com/KIMEGAMI/furugi.git furugi
sudo chown -R www-data:www-data /var/www/furugi
cd /var/www/furugi
```

For later deployments:

```bash
cd /var/www/furugi
sudo -u www-data git pull origin main
```

## 3. Configure `.env`

Create `.env` on the server if it does not exist.

```bash
sudo -u www-data cp .env.example .env
sudo -u www-data php artisan key:generate
```

Set production values.

```dotenv
APP_NAME="古着管理システム"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://furugi.shinji.work

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=furugi
DB_USERNAME=furugi_user
DB_PASSWORD=your_secure_password

SESSION_DRIVER=database
QUEUE_CONNECTION=database
MAIL_MAILER=log
```

If Google login is used, also set the Google OAuth values in `.env`.

```dotenv
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=https://furugi.shinji.work/auth/google/callback
```

## 4. Install Dependencies and Build Assets

```bash
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm ci
sudo -u www-data npm run build
```

## 5. Database Migration

Run migrations in production mode.

```bash
sudo -u www-data php artisan migrate --force
```

The demo login button expects this user to exist:

- Email: `user@shinji.work`
- Password: `12345678`

Create or update the demo user on the server if needed.

```bash
sudo -u www-data php artisan tinker
```

```php
\App\Models\User::updateOrCreate(
    ['email' => 'user@shinji.work'],
    [
        'name' => 'Demo User',
        'password' => '12345678',
        'email_verified_at' => now(),
    ]
);
```

## 6. Storage and Permissions

```bash
sudo -u www-data php artisan storage:link
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R ug+rw storage bootstrap/cache
```

## 7. Apache Configuration

Create an Apache site file.

```bash
sudo nano /etc/apache2/sites-available/furugi.conf
```

Example:

```apache
<VirtualHost *:80>
    ServerName furugi.shinji.work
    DocumentRoot /var/www/furugi/public

    <Directory /var/www/furugi/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/furugi_error.log
    CustomLog ${APACHE_LOG_DIR}/furugi_access.log combined
</VirtualHost>
```

Enable the site and rewrite module.

```bash
sudo a2enmod rewrite
sudo a2ensite furugi.conf
sudo apache2ctl configtest
sudo systemctl reload apache2
```

Use Certbot or the hosting provider's SSL feature to enable HTTPS.

## 8. Optimize Laravel

Run these after each deployment.

```bash
sudo -u www-data php artisan optimize:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

## 9. Queue Worker

This app uses the database queue connection by default. If queued jobs are used in production, run a worker with Supervisor or systemd.

Simple manual start:

```bash
sudo -u www-data php artisan queue:work --tries=3
```

After each deployment:

```bash
sudo -u www-data php artisan queue:restart
```

## 10. Standard Update Command

For normal deployments after code is pushed to GitHub:

```bash
cd /var/www/furugi
sudo -u www-data git pull origin main
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm ci
sudo -u www-data npm run build
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan optimize:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan queue:restart
sudo systemctl reload apache2
```
