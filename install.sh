#!/usr/bin/env bash
# ==============================================================================
# Dischedule - Turnkey Automated Installer for Debian (/var/www/html)
# Target Server: 192.168.3.101
# ==============================================================================

set -e

echo "========================================================="
echo "   DISCHEDULE AUTOMATED INSTALLER (/var/www/html)        "
echo "========================================================="

INSTALL_DIR="/var/www/html"

# 1. Update system packages
echo "[1/7] Updating apt repositories..."
apt-get update -y

# 2. Install Nginx, MariaDB, PHP 8.x and required extensions
echo "[2/7] Installing Nginx, MariaDB, and PHP 8+ extensions..."
apt-get install -y nginx mariadb-server \
    php-cli php-fpm php-mysql php-curl php-mbstring php-fileinfo php-xml php-zip

# 3. Setup MySQL/MariaDB Database & User
echo "[3/7] Setting up database 'dischedule'..."
mysql -e "CREATE DATABASE IF NOT EXISTS dischedule CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS 'dischedule_user'@'localhost' IDENTIFIED BY 'ender';"
mysql -e "GRANT ALL PRIVILEGES ON dischedule.* TO 'dischedule_user'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# 4. Copy/Setup Project Files in /var/www/html
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"

if [ "$SCRIPT_DIR" != "$INSTALL_DIR" ]; then
    echo "[4/7] Copying files to $INSTALL_DIR..."
    mkdir -p "$INSTALL_DIR"
    cp -r "$SCRIPT_DIR"/* "$INSTALL_DIR"/
    cp "$SCRIPT_DIR"/.env.example "$INSTALL_DIR"/.env 2>/dev/null || true
    cp "$SCRIPT_DIR"/.htaccess "$INSTALL_DIR"/.htaccess 2>/dev/null || true
fi

cd "$INSTALL_DIR"

echo "Importing database tables and seeds..."
mysql -u dischedule_user -pender dischedule < "$INSTALL_DIR/database/schema.sql"
mysql -u dischedule_user -pender dischedule < "$INSTALL_DIR/database/seeds/seed.sql"

# Create / Update Super Admin user with password 'ender'
ADMIN_HASH=$(php -r "echo password_hash('ender', PASSWORD_BCRYPT);")
mysql -u dischedule_user -pender dischedule -e "
    INSERT INTO users (username, email, password_hash, role, status, email_verified_at, timezone, language, created_at, updated_at) 
    VALUES ('ender', 'ender@ender.dev', '$ADMIN_HASH', 'admin', 'active', NOW(), 'Asia/Jakarta', 'id', NOW(), NOW())
    ON DUPLICATE KEY UPDATE 
        password_hash='$ADMIN_HASH', 
        role='admin', 
        status='active',
        email='ender@ender.dev',
        timezone='Asia/Jakarta';
"

# 5. Configure .env file
echo "[5/7] Writing .env configuration..."
cat << 'EOF' > "$INSTALL_DIR/.env"
APP_NAME="Dischedule"
APP_ENV=production
APP_DEBUG=false
APP_URL="http://192.168.3.101"
APP_TIMEZONE="Asia/Jakarta"

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dischedule
DB_USERNAME=dischedule_user
DB_PASSWORD="ender"

MAIL_DRIVER=mail
MAIL_FROM_ADDRESS="noreply@ender.dev"
MAIL_FROM_NAME="Dischedule"

DISCORD_CLIENT_ID=""
DISCORD_CLIENT_SECRET=""
DISCORD_REDIRECT_URI="http://192.168.3.101/auth/discord/callback"
EOF

# 6. Set file permissions
echo "[6/7] Setting folder permissions..."
mkdir -p "$INSTALL_DIR/storage/logs" "$INSTALL_DIR/storage/cache" "$INSTALL_DIR/storage/uploads" "$INSTALL_DIR/storage/backups"
ln -sf "$INSTALL_DIR/storage" "$INSTALL_DIR/public/storage"
chown -R www-data:www-data "$INSTALL_DIR"
chmod -R 755 "$INSTALL_DIR"
chmod -R 775 "$INSTALL_DIR/storage"

# 7. Configure Nginx VirtualHost
echo "[7/7] Configuring Nginx web server..."
PHP_SOCK=$(find /var/run/php/ -name "php*-fpm.sock" | head -n 1)
if [ -z "$PHP_SOCK" ]; then
    PHP_SOCK="/var/run/php/php-fpm.sock"
fi

cat << EOF > /etc/nginx/sites-available/dischedule
server {
    listen 80;
    server_name 192.168.3.101;

    root $INSTALL_DIR/public;
    index index.php index.html;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:$PHP_SOCK;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 25M;
}
EOF

ln -sf /etc/nginx/sites-available/dischedule /etc/nginx/sites-enabled/dischedule
rm -f /etc/nginx/sites-enabled/default

# Restart services
systemctl restart nginx
systemctl restart php*-fpm
systemctl restart mariadb

# Setup Cron Job
CRON_JOB="* * * * * php $INSTALL_DIR/cron/send_reminders.php >> /dev/null 2>&1"
(crontab -l 2>/dev/null | grep -Fv "send_reminders.php" ; echo "$CRON_JOB") | crontab -

echo ""
echo "========================================================="
echo "   ✅ INSTALASI DISCHEDULE BERHASIL SELESAI!            "
echo "========================================================="
echo "Aplikasi siap diakses di:"
echo "👉 URL Web  : http://192.168.3.101"
echo "👉 Lokasi   : /var/www/html"
echo "👉 Username : ender"
echo "👉 Password : ender"
echo "========================================================="
