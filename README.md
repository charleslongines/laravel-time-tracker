# Laravel Time Tracker

A modern time tracking application built with Laravel, Vue.js, and Inertia.js. This application allows users to clock in and out while tracking their IP addresses and user agents for security purposes.

## Features

- **User Authentication**: Secure login system with role-based access
- **Time Tracking**: Clock in/out functionality with timestamp recording
- **IP Address Tracking**: Automatically captures and validates user IP addresses
- **User Agent Validation**: Tracks and validates browser user agents for security
- **Admin Panel**: Administrative interface for user management and time tracking oversight
- **Security**: IP address and user agent validation to prevent unauthorized access
- **Modern UI**: Built with Vue.js, Tailwind CSS, and Inertia.js for a smooth user experience

## Prerequisites

Before you begin, ensure you have the following installed on your system:

- **PHP** 8.2 or higher
- **Composer** (PHP package manager)
- **Node.js** 18 or higher
- **npm** or **yarn** (Node.js package manager)
- **SQLite** (default) or **MySQL**/**PostgreSQL** database

### PHP Extensions Required

Make sure you have the following PHP extensions enabled:

- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd laravel-time-tracker
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the environment file and configure your settings:

```bash
cp .env.example .env
```

Edit the `.env` file with your configuration:

```env
APP_NAME="Laravel Time Tracker"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Database Configuration (SQLite by default)
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/your/database.sqlite

# For MySQL/PostgreSQL, use these settings instead:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel_time_tracker
# DB_USERNAME=root
# DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Database Setup

#### Option A: Using SQLite (Default)

Create the SQLite database file:

```bash
touch database/database.sqlite
```

#### Option B: Using MySQL/PostgreSQL

Create a database and update your `.env` file with the database credentials.

### 7. Run Migrations

```bash
php artisan migrate:refresh
```

### 8. Seed the Database

```bash
php artisan db:seed
```

This will create the following default accounts:

**Admin Account:**

- Email: `admin@test.com`
- Password: `P@ssword123!`

**Test User Account:**

- Email: `test_user1@test.com`
- Password: `P@ssword123!`

### 9. Build Frontend Assets

For development:

```bash
npm run dev
```

For production:

```bash
npm run build
```

### 10. Start the Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Usage Guide

### For Regular Users

1. **Login**: Use your assigned email and password to log in
2. **Initial Setup**:
    - Click "Clock In" - the system will display your current IP address
    - Click "Clock In" again - the system will display your current User Agent
    - Copy both the IP address and User Agent information
    - Send this information to your administrator
3. **Wait for Approval**: The admin needs to update your account with the valid IP address and User Agent
4. **Start Tracking**: Once approved, you can clock in and out normally

### For Administrators

1. **Login**: Use the admin account (`admin@test.com` / `P@ssword123!`)
2. **User Management**: Access the admin panel to manage users
3. **Update User Settings**:
    - Navigate to user management
    - Update the user's IP address and User Agent with the information they provided
    - Save the changes
4. **Monitor Time Tracking**: View and manage all user time tracking records

## Deployment

### Production Deployment

#### 1. Environment Setup

Update your `.env` file for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Use a strong database for production
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-db-username
DB_PASSWORD=your-db-password
```

#### 2. Optimize for Production

```bash
# Clear and cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build production assets
npm run build
```

#### 3. Set Proper Permissions

```bash
# Set proper permissions for storage and bootstrap/cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 4. Web Server Configuration

Configure your web server (Apache/Nginx) to point to the `public` directory.

#### Example Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/laravel-time-tracker/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Docker Deployment

If you prefer using Docker, you can use Laravel Sail:

```bash
# Install Laravel Sail
composer require laravel/sail --dev

# Publish Sail configuration
php artisan sail:install

# Start the application
./vendor/bin/sail up -d
```

## Security Features

- **IP Address Validation**: Users can only clock in from pre-approved IP addresses
- **User Agent Validation**: Browser user agents are tracked and validated
- **Role-Based Access**: Separate admin and user roles with appropriate permissions
- **Secure Authentication**: Laravel's built-in authentication system
- **CSRF Protection**: Cross-site request forgery protection enabled

## Troubleshooting

### Common Issues

1. **Permission Denied Errors**

    ```bash
    chmod -R 775 storage bootstrap/cache
    ```

2. **Database Connection Issues**
    - Verify your `.env` file configuration
    - Ensure the database server is running
    - Check database credentials

3. **Asset Loading Issues**

    ```bash
    npm run build
    php artisan view:clear
    ```

4. **Migration Errors**
    ```bash
    php artisan migrate:fresh --seed
    ```

### Logs

Check Laravel logs for detailed error information:

```bash
tail -f storage/logs/laravel.log
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions, please contact the development team or create an issue in the repository.
