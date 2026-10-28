# 📅 Dischedule - Schedule & Event Management Platform

> **A high-performance schedule and event management web application deeply integrated with Discord via Webhooks and OAuth2.**  
> Engineered in **pure native PHP 8.1+** with **zero external PHP libraries, no Composer, and no heavy frameworks**.

---

## 🌟 Overview

**Dischedule** is a personal and team scheduling platform where users can create, organize, and share events/reminders. Every event can trigger automated rich embeds to Discord channels via webhooks. Users can link their Discord accounts via OAuth2 to receive personalized notifications and direct-message reminders, while teams collaborate through shared calendars and categories.

---

## 🚀 Key Features

### 1. User Authentication & Authorization (RBAC)
- **Secure Registration & Login**: Email and username login, Bcrypt/Argon2 password hashing with strict password complexity enforcement (min 8 characters, uppercase, lowercase, number, special character).
- **Email Verification & Password Reset**: Time-limited cryptographic token verification and password reset flows with transactional HTML email templates.
- **Secure "Remember Me"**: Signed, HTTP-only, 30-day persistent cookie authentication.
- **Role-Based Access Control (RBAC)**: Distinct permissions for `admin`, `moderator`, and `user`.
- **Account Management**: Profile bio, custom avatar uploads, timezone selector (all IANA timezones), language picker, dark/light theme toggle, and account deactivation.
- **Brute Force Rate Limiting**: Built-in sliding window rate limiter on login, registration, password reset, and API tokens.

### 2. Interactive Calendar & Views
- **Four Core Views**:
  - **Month View**: Classic grid with colored event bars, multi-day support, and overflow counters.
  - **Week View**: Vertical 24-hour time grid (00:00–23:59) with live current-time indicator line.
  - **Day View**: Single day schedule with 15-minute slot resolution.
  - **Agenda View**: Chronological upcoming activity feed grouped by date with category pills and direct location links.
- **Drag-and-Drop Scheduling**: Fast drag-to-move date rescheduling and drag-to-resize duration adjustments via lightweight AJAX endpoints.
- **Keyboard Shortcuts**: `T` (Today), `M` (Month), `W` (Week), `D` (Day), `A` (Agenda), `N` (New Event), `/` (Focus Global Search).
- **Timezone Awareness**: Events automatically convert to and display in the user's localized timezone.

### 3. Comprehensive Event Management
- **Rich Event Properties**: Title, rich description, start/end datetime, localized timezone, physical location or video meeting URL, custom hex color accent, priority (`low`, `medium`, `high`), status (`draft`, `published`, `archived`), visibility (`private`, `public`, `shared`).
- **Recurrence Engine (RFC 5545 RRULE)**: Daily, weekly (custom days e.g., Mon/Wed/Fri), monthly, yearly with recurrence end dates and occurrence count limits.
- **Automated Multi-Channel Reminders**: Multiple reminders per event (10m, 15m, 30m, 1h, 1d before start) dispatched via Discord Webhook, Email, or In-App alerts.
- **File Attachments**: Drag-and-drop file uploader with MIME type verification (PDF, images, documents) and secure download routing.
- **Public Event Sharing**: Generate unique shareable URLs with optional access password protection and expiration dates.
- **Import & Export**: Full RFC 5545 iCalendar (`.ics`) export/import, CSV spreadsheet export/import.
- **Event Collaboration**: Comments and discussion feed on events.
- **Bulk Actions**: Delete, archive, or reassign categories in batch.

### 4. Deep Discord Integration
- **Discord OAuth2 SSO & Linking**: 1-click Discord login and account linking using native cURL, fetching Discord ID, avatar, and username.
- **Rich Discord Webhook Embeds**: Formatted Discord embed payloads with event title, formatted timestamp, location link, category color, priority badges, and author avatar.
- **Discord Role Mentions**: Configurable mentions per webhook (`@everyone`, `@here`, or `<@&ROLE_ID>`).
- **Instant Webhook Tester**: Built-in test button to verify Discord channel connectivity with real-time HTTP response diagnostics.
- **Global Administrator Webhook**: Centralized channel logging for system-wide public events.
- **Audit Logs**: All outbound webhook attempts, HTTP codes, and responses stored in `webhook_logs`.

### 5. In-App Notification Center
- **Live Notification Bell**: Navbar dropdown with unread count badge, real-time polling, and mark as read / mark all read actions.
- **Granular Preferences**: User-configurable notification channel toggles (Email, Discord, In-App).

### 6. Teams & Shared Calendars
- **Team Workspaces**: Create teams, invite members by username/email, and manage roles (`owner`, `admin`, `member`).
- **Shared Calendar View**: Coordinated scheduling for team projects, release deadlines, and meetings.

### 7. RESTful API (v1)
- **Token-Based Authentication**: Personal API tokens with custom permissions (`read`, `write`, `delete`) and expiration dates.
- **Endpoints**: Full CRUD for events, categories, notifications, and webhooks with pagination and search filters.
- **Bearer Token Auth**: Authorization header `Authorization: Bearer <TOKEN>`.

### 8. Administration Control Center
- **Analytics Dashboard**: User counts, event metrics, webhook deliveries, memory usage, and PHP/database environment diagnostics.
- **User Moderation**: Search, role assignment, account ban/unban, and deletion.
- **Audit Logs & System Logs Viewer**: Live file log stream (`storage/logs/app-YYYY-MM-DD.log`) with search and clear capabilities.
- **Platform Configuration**: Application name, global webhook URL, registration toggle, and maintenance mode.

---

## 📂 Project Architecture

```
dischedule/
├── public/                     # Web server document root
│   ├── index.php               # Front controller & request bootstrap
│   ├── .htaccess               # Apache URL rewriting & security headers
│   └── assets/
│       ├── css/app.css         # Master stylesheet & CSS variable design system
│       ├── js/
│       │   ├── app.js          # Core JS (CSRF fetch, toasts, modals, shortcuts, theme)
│       │   ├── calendar.js     # Native interactive calendar engine
│       │   └── notifications.js# Real-time notification poller & dropdown
│       └── images/             # SVG brand assets and icons
├── app/
│   ├── Config/                 # Application configuration arrays
│   │   ├── app.php             # Core application settings
│   │   ├── database.php        # MySQL PDO connection settings
│   │   ├── mail.php            # Mail driver (mail / custom socket SMTP)
│   │   ├── discord.php         # Discord OAuth2 and Webhook configurations
│   │   └── routes.php          # HTTP route definitions & middleware mapping
│   ├── Controllers/            # MVC Controller layer
│   │   ├── HomeController.php
│   │   ├── AuthController.php
│   │   ├── DiscordAuthController.php
│   │   ├── CalendarController.php
│   │   ├── EventController.php
│   │   ├── CategoryController.php
│   │   ├── WebhookController.php
│   │   ├── NotificationController.php
│   │   ├── TeamController.php
│   │   ├── ProfileController.php
│   │   ├── ShareController.php
│   │   ├── AdminController.php
│   │   └── ApiController.php
│   ├── Models/                 # Active Record / Query Model layer
│   │   ├── User.php
│   │   ├── Event.php
│   │   ├── Category.php
│   │   ├── Tag.php
│   │   ├── Webhook.php
│   │   ├── Notification.php
│   │   ├── Reminder.php
│   │   ├── Team.php
│   │   ├── Attachment.php
│   │   ├── Comment.php
│   │   ├── ActivityLog.php
│   │   ├── ApiToken.php
│   │   └── Setting.php
│   ├── Services/               # Business Logic & Integrations
│   │   ├── DiscordService.php  # Discord cURL Webhook & OAuth2 Client
│   │   ├── EmailService.php    # Transactional HTML email generator
│   │   ├── NotificationService.php # Multi-channel dispatcher
│   │   ├── RecurrenceService.php # RFC 5545 RRULE occurrence generator
│   │   ├── ICSExportService.php# iCalendar standard generator
│   │   ├── ICSImportService.php# iCalendar parser
│   │   └── CSVService.php      # CSV spreadsheet import/export
│   ├── Middleware/             # Request lifecycle filters
│   │   ├── AuthMiddleware.php
│   │   ├── AdminMiddleware.php
│   │   ├── CsrfMiddleware.php
│   │   ├── RateLimitMiddleware.php
│   │   ├── ApiAuthMiddleware.php
│   │   └── MaintenanceMiddleware.php
│   ├── Helpers/                # Global helper functions & utilities
│   │   ├── functions.php
│   │   ├── DateHelper.php
│   │   └── StringHelper.php
│   ├── Languages/              # Multi-language translation files
│   │   ├── en.php              # English
│   │   ├── id.php              # Bahasa Indonesia
│   │   └── es.php              # Español
│   └── Views/                  # PHP layout templates & partials
│       ├── layouts/            # main.php, auth.php, admin.php
│       ├── partials/           # head, navbar, sidebar, footer, toasts, event_modal
│       ├── home/               # Landing page
│       ├── auth/               # Login, register, forgot, reset
│       ├── calendar/           # Interactive calendar view
│       ├── events/             # List, create, edit, show, share, import/export
│       ├── categories/         # Categories management
│       ├── webhooks/           # Discord webhooks management
│       ├── notifications/      # Notification center
│       ├── teams/              # Teams & members
│       ├── profile/            # Profile, security, discord, API tokens
│       ├── admin/              # Admin dashboard, users, events, webhooks, logs, settings
│       └── errors/             # 403, 404, 500, maintenance
├── core/                       # Core Framework Engine (Zero dependencies)
│   ├── Autoloader.php          # Custom PSR-4 SPL autoloader
│   ├── Database.php            # Singleton PDO wrapper with transactions
│   ├── Router.php              # Fast RESTful Router with regex & middlewares
│   ├── Controller.php          # Base controller
│   ├── Model.php               # Base ActiveRecord query model
│   ├── View.php                # Layout & partials rendering engine
│   ├── Request.php             # HTTP request encapsulation & JSON parser
│   ├── Response.php            # HTTP response handler
│   ├── Session.php             # Secure session & CSRF management
│   ├── Validator.php           # Form validation engine
│   ├── Cache.php               # File-based JSON caching system
│   ├── Mailer.php              # Socket SMTP & mail() engine
│   ├── Logger.php              # Daily rotating log file engine
│   └── I18n.php                # Localization engine
├── database/
│   ├── schema.sql              # Consolidated MySQL database schema
│   ├── migrations/             # Sequential migration files (001 - 014)
│   └── seeds/                  # Master seed data (seed.sql, users, events, etc.)
├── cron/
│   ├── send_reminders.php      # CLI worker executing due reminders every minute
│   ├── cleanup_old_logs.php    # CLI worker rotating logs and cache daily
│   └── discord_bot.php         # Standalone CLI Discord interaction worker
├── storage/                    # Internal file storage
│   ├── logs/                   # Daily rotating log files
│   ├── cache/                  # File-based cached JSON payloads
│   └── uploads/                # User uploaded avatars and event attachments
├── tests/
│   └── run_tests.php           # Standalone unit test suite
├── .env.example                # Configuration template
├── .htaccess                   # Root Apache directory rewrite
└── README.md                   # Complete documentation
```

---

## 🛠️ System Requirements

- **PHP**: Version `8.1` or higher
- **PHP Extensions**: `pdo_mysql`, `curl`, `mbstring`, `fileinfo`, `json`, `openssl`, `session`
- **Database**: MySQL `8.0+` or MariaDB `10.4+`
- **Web Server**: Apache (with `mod_rewrite` and `mod_headers`), Nginx, or PHP Built-in Server
- **Composer / External Libraries**: **None required!**

---

## ⚡ Installation Guide

### Step 1: Clone or Download Repository
Place the repository files into your web root directory (e.g., `C:/xampp/htdocs/dischedule` or `/var/www/dischedule`).

### Step 2: Configure Environment (`.env`)
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```
Edit `.env` with your database credentials and application settings:
```ini
APP_NAME="Dischedule"
APP_ENV=development
APP_DEBUG=true
APP_URL="http://localhost:8000"
APP_TIMEZONE="UTC"

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dischedule
DB_USERNAME=root
DB_PASSWORD=""

# Discord Integration (Optional, can be added later)
DISCORD_CLIENT_ID="YOUR_DISCORD_CLIENT_ID"
DISCORD_CLIENT_SECRET="YOUR_DISCORD_CLIENT_SECRET"
DISCORD_REDIRECT_URI="http://localhost:8000/auth/discord/callback"
```

### Step 3: Initialize Database Schema & Seed Data
Create the database in MySQL:
```sql
CREATE DATABASE `dischedule` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
Import the consolidated schema and seed data:
```bash
mysql -u root -p dischedule < database/schema.sql
mysql -u root -p dischedule < database/seeds/seed.sql
```

### Step 4: Run Application
#### Option A: PHP Built-in Web Server (Recommended for development)
```bash
php -S localhost:8000 -t public
```
Open your browser and navigate to: **`http://localhost:8000`**

#### Option B: Apache Configuration
Point your `DocumentRoot` to the `public/` directory. Ensure `mod_rewrite` is enabled.
```apache
<VirtualHost *:80>
    ServerName dischedule.local
    DocumentRoot "C:/path/to/dischedule/public"
    <Directory "C:/path/to/dischedule/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Option C: Nginx Configuration
```nginx
server {
    listen 80;
    server_name dischedule.local;
    root /var/www/dischedule/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

---

## 🔑 Default Seed Accounts

The seed script provides ready-to-use testing accounts:

| Role | Username | Email | Password |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `admin@dischedule.local` | `secret` |
| **Moderator** | `moderator` | `mod@dischedule.local` | `secret` |
| **User** | `demouser` | `demo@dischedule.local` | `secret` |

---

## 🤖 Discord Integration Setup

### 1. Setting Up Discord Webhooks (No Bot Required)
1. Open your Discord server channel.
2. Click **Edit Channel (⚙️)** &rarr; **Integrations** &rarr; **Webhooks** &rarr; **New Webhook**.
3. Copy the **Webhook URL**.
4. In Dischedule, navigate to **Discord Webhooks** &rarr; **Connect Webhook**.
5. Paste the URL, set a friendly name, optional role mention (`@everyone` or `<@&ROLE_ID>`), and click **Connect**.
6. Click **Test Webhook** to immediately verify delivery.

### 2. Setting Up Discord OAuth2 Single Sign-On (SSO)
1. Go to the [Discord Developer Portal](https://discord.com/developers/applications).
2. Click **New Application** and name it `Dischedule`.
3. In the **OAuth2** tab:
   - Copy **Client ID** and **Client Secret**.
   - Add Redirect URI: `http://localhost:8000/auth/discord/callback`.
4. Update your `.env` file:
   ```ini
   DISCORD_CLIENT_ID="123456789012345678"
   DISCORD_CLIENT_SECRET="your_client_secret_here"
   DISCORD_REDIRECT_URI="http://localhost:8000/auth/discord/callback"
   ```
5. Users can now click **"Sign in with Discord"** or link their Discord account in **Profile Settings**!

---

## ⏰ Cron Jobs Setup

To automatically dispatch scheduled reminders and perform daily maintenance log rotations, set up standard crontabs on your server:

```bash
# Edit crontab
crontab -e
```
Add the following entries:
```cron
# 1. Run reminder dispatcher every minute
* * * * * php /path/to/dischedule/cron/send_reminders.php >> /dev/null 2>&1

# 2. Run system maintenance & log rotation daily at 02:00 AM
0 2 * * * php /path/to/dischedule/cron/cleanup_old_logs.php >> /dev/null 2>&1
```

To test the reminder worker manually from command line:
```bash
php cron/send_reminders.php
```

---

## 🔌 RESTful API (v1) Documentation

Dischedule includes a complete JSON REST API under `/api/v1/`.

### Authentication
Authenticate by sending a Bearer token in the `Authorization` header:
`Authorization: Bearer <API_TOKEN>`

Tokens can be generated via the UI in **Account Settings &rarr; API Tokens** or via the login endpoint.

### Endpoints Overview

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/v1/auth/login` | Obtain API Bearer token | No |
| `GET` | `/api/v1/events` | List events (with search & category filters) | Yes (`read`) |
| `POST` | `/api/v1/events` | Schedule a new event | Yes (`write`) |
| `GET` | `/api/v1/events/{id}` | Get event details | Yes (`read`) |
| `PUT` | `/api/v1/events/{id}` | Update event | Yes (`write`) |
| `DELETE` | `/api/v1/events/{id}` | Delete event | Yes (`delete`) |
| `GET` | `/api/v1/categories` | List user categories | Yes (`read`) |
| `GET` | `/api/v1/notifications`| List notifications | Yes (`read`) |
| `POST` | `/api/v1/webhooks/test`| Test a Discord webhook URL | Yes (`write`) |

### Example cURL Commands

#### 1. Obtain Token:
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"login":"admin","password":"secret"}'
```

#### 2. Create Event:
```bash
curl -X POST http://localhost:8000/api/v1/events \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Release Sync v2.0",
    "start_datetime": "2026-08-20 14:00:00",
    "end_datetime": "2026-08-20 15:00:00",
    "priority": "high",
    "location": "Discord Stage"
  }'
```

---

## 🧪 Running Unit Tests

Dischedule includes a standalone, zero-dependency unit test runner verifying the autoloader, validation engine, file caching, recurrence engine, iCalendar generation, and string helpers.

Run the test suite from your terminal:
```bash
php tests/run_tests.php
```

---

## 🛡️ Security Features

- **PDO Prepared Statements**: 100% parameter-bound queries preventing SQL Injection.
- **XSS Sanitization**: Strict HTML entity escaping across all view rendering templates.
- **CSRF Token Protection**: Cryptographically secure token verification on all POST/PUT/DELETE forms and AJAX endpoints.
- **Rate Limiting**: Sliding window rate limiting on authentication routes and API endpoints.
- **MIME Type Validation**: Server-side `finfo` MIME validation on uploaded attachments and avatars.
- **Security Headers**: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `X-XSS-Protection: 1; mode=block`.

---

## 📄 License
This project is open-source software licensed under the MIT License.
