# 🚀 Dischedule Server Migration & AI Setup Guide

> **Panduan Lengkap Migrasi & Menghidupkan Website Dischedule untuk AI / Developer Selanjutnya**

---

## 📌 1. Ringkasan Arsitektur Proyek
- **Bahasa & Framework**: **PHP 8.1+ Native / Murni** (Arsitektur MVC kustom, **tanpa Composer**, **tanpa framework pihak ketiga**, zero external vendor dependencies).
- **Frontend**: Native HTML5, Modern Vanilla CSS3 (Custom Design System), Vanilla ES6+ JavaScript, Font Awesome Icons.
- **Database**: MySQL 8.0+ atau MariaDB 10.4+ (menggunakan PDO Prepared Statements).
- **Integrasi Utama**: Discord Webhook Dispatcher (cURL), Discord OAuth2 Single Sign-On, Multi-Channel Reminders (Email, Discord, In-App).
- **Entry Point / DocumentRoot**: **`public/index.php`** (Semua request harus diarahkan ke folder `public/`).

---

## 📂 2. Struktur Direktori Utama

```text
dischedule/
├── app/
│   ├── Config/            # Konfigurasi aplikasi (app.php, database.php, discord.php, mail.php)
│   ├── Controllers/       # Controller MVC (CalendarController, EventController, AuthController, dll.)
│   ├── Helpers/           # Global helper functions (DateHelper, StringHelper, csrf, url, dll.)
│   ├── Languages/         # File translasi i18n (id.php, en.php)
│   ├── Middleware/        # Middleware (AuthMiddleware, ApiAuthMiddleware, CsrfMiddleware, dll.)
│   ├── Models/            # Model database PDO (Event, User, Reminder, Webhook, Category, dll.)
│   ├── Services/          # Service Layer (DiscordService, NotificationService, RecurrenceService, EmailService)
│   └── Views/             # Template tampilan PHP (layouts/, calendar/, events/, auth/, partials/)
├── core/                  # Engine MVC Native (Autoloader, Router, Controller, Model, Database, View, Session, Cache, Logger)
├── cron/                  # CLI Workers & Cron Jobs
│   ├── send_reminders.php # Dispatcher pengingat otomatis per menit (Wajib dijalankan crontab!)
│   ├── cleanup_old_logs.php
│   └── discord_bot.php
├── database/
│   ├── dischedule_dump.sql # DUMP LENGKAP DATABASE (Skema + Seluruh Data Saat Ini)
│   ├── schema.sql         # Skema struktur tabel
│   └── seeds/seed.sql     # Seed data default
├── public/                # Document Root Web Server
│   ├── assets/            # CSS, JS, Gambar, Icons
│   ├── .htaccess          # URL Rewriting untuk Apache
│   └── index.php          # Front Controller utama
├── storage/               # Folder penyimpanan file lokal (Wajib writable 775/777)
│   ├── cache/             # Cache file
│   ├── logs/              # Log aplikasi & cron
│   └── uploads/           # File attachment & avatar pengguna
├── tests/
│   └── run_tests.php      # Native Unit Test Runner (Zero dependency)
├── .env.example           # Template variabel lingkungan
└── .env                   # File konfigurasi aktif
```

---

## ⚡ 3. Langkah-Langkah Menghidupkan di Server Baru (Step-by-Step)

### Langkah 1: Ekstrak File Proyek
Ekstrak file `.zip` ke folder web server tujuan:
```bash
unzip dischedule_full_backup.zip -d /var/www/html/
cd /var/www/html/
```

### Langkah 2: Setup Database MySQL / MariaDB
1. Masuk ke MySQL / MariaDB di server baru:
   ```bash
   mysql -u root -p
   ```
2. Buat database dan user baru (atau sesuaikan dengan kredensial server Anda):
   ```sql
   CREATE DATABASE `dischedule` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'dischedule_user'@'localhost' IDENTIFIED BY 'ender';
   GRANT ALL PRIVILEGES ON `dischedule`.* TO 'dischedule_user'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```
3. **Import database dump lengkap** yang sudah disediakan di folder `database/`:
   ```bash
   mysql -u dischedule_user -pender dischedule < database/dischedule_dump.sql
   ```

### Langkah 3: Konfigurasi File `.env`
Pastikan file `.env` sudah ada di root direktori proyek. Jika belum, salin dari `.env.example`:
```bash
cp .env.example .env
```
Sesuaikan isi file `.env` dengan konfigurasi server baru Anda:
```ini
APP_NAME="Dischedule"
APP_ENV=production
APP_DEBUG=false
APP_URL="http://IP_SERVER_ATAU_DOMAIN_ANDA"
APP_TIMEZONE="Asia/Jakarta"
APP_LOCALE="id"

# Kredensial Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dischedule
DB_USERNAME=dischedule_user
DB_PASSWORD="ender"

# Email Configuration
MAIL_DRIVER=mail
MAIL_FROM_ADDRESS="noreply@ender.dev"
MAIL_FROM_NAME="Dischedule"

# Discord Integration (Opsional jika ingin menggunakan Discord OAuth2 Login)
DISCORD_CLIENT_ID=""
DISCORD_CLIENT_SECRET=""
DISCORD_REDIRECT_URI="http://IP_SERVER_ATAU_DOMAIN_ANDA/auth/discord/callback"
```

### Langkah 4: Set Permission Folder `storage/`
Folder `storage/` digunakan untuk menulis cache, file log, dan file upload avatar/attachment. Berikan izin tulis ke web server (`www-data`):
```bash
chmod -R 775 storage/
chown -R www-data:www-data storage/
```

### Langkah 5: Konfigurasi Web Server (Pilih Salah Satu)

#### Pilihan A: Apache (Recommended)
Pastikan `mod_rewrite` aktif dan DocumentRoot diarahkan ke folder **`public`**:
```apache
<VirtualHost *:80>
    ServerName dischedule.local
    DocumentRoot "/var/www/html/public"

    <Directory "/var/www/html/public">
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/dischedule_error.log
    CustomLog ${APACHE_LOG_DIR}/dischedule_access.log combined
</VirtualHost>
```
Aktifkan konfigurasi dan reload Apache:
```bash
a2enmod rewrite headers
systemctl restart apache2
```

#### Pilihan B: Nginx
```nginx
server {
    listen 80;
    server_name dischedule.local;
    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock; # Sesuaikan versi PHP
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

#### Pilihan C: PHP Built-in Server (Untuk Testing / Development Cepat)
```bash
php -S 0.0.0.0:8000 -t public
```

---

## ⏰ 4. Setup Cron Job Pengingat Otomatis Discord (CRITICAL!)

Dischedule memiliki worker pengingat otomatis di `cron/send_reminders.php` yang memeriksa dan mengirimkan notifikasi Discord/Email/In-App pada jam yang tepat.

Buka crontab server:
```bash
crontab -e
```
Tambahkan baris berikut agar worker berjalan otomatis setiap 1 menit:
```cron
* * * * * /usr/bin/php /var/www/html/cron/send_reminders.php >> /var/www/html/storage/logs/cron.log 2>&1
```

---

## 🧪 5. Verifikasi & Pengujian Sistem

Jalankan test suite bawaan untuk memastikan seluruh komponen (Database, Autoloader, Validator, Recurrence Engine, iCal RFC5545, Cache, Discord Helper) bekerja 100%:
```bash
php tests/run_tests.php
```
*Output yang diharapkan: `RESULTS: Passed: 12 | Failed: 0 - ALL UNIT TESTS COMPLETED SUCCESSFULLY!`*

Uji coba manual worker reminder di terminal:
```bash
php cron/send_reminders.php
```

---

## 🔑 6. Akun Pengguna yang Sudah Tersedia

Database dump yang disertakan (`database/dischedule_dump.sql`) telah berisi data akun berikut:

| Username | Email | Role | Timezone | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| **`ender`** | `ender@ender.dev` | `admin` | `Asia/Jakarta` | Akun Utama Pemilik Sistem |
| **`admin`** | `admin@dischedule.local` | `admin` | `UTC` | Password: `password` |
| **`moderator`** | `mod@dischedule.local` | `moderator` | `UTC` | Password: `password` |
| **`demouser`** | `demo@dischedule.local` | `user` | `UTC` | Password: `password` |

---

## 💡 7. Catatan Khusus untuk AI / Developer Selanjutnya
1. **Zero Composer**: Seluruh class di-load melalui `core/Autoloader.php` dengan namespace PSR-4:
   - `Core\` -> direktori `core/`
   - `App\` -> direktori `app/`
   Jangan menjalankan `composer install` karena memang tidak dibutuhkan.
2. **Timezone Handling**:
   - Waktu disimpan di MySQL dalam **standar UTC**.
   - Saat ditampilkan ke browser (Kalender, Detail Event, Edit Form), `DateHelper::formatToUserTimezone()` otomatis mengonversinya ke zona waktu lokal pengguna (`Asia/Jakarta`).
3. **Pemberitahuan Discord**:
   - Discord webhook dikirim menggunakan native cURL di `App\Services\DiscordService`.
   - Pengingat jam mulai acara (`minutes_before = 0`) akan berlabel `⏰ Event Starting Now`.
