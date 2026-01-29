# CynTour - Tourism Management System v2.0

A comprehensive tourism management system for CYN Turizm, featuring hotel booking, tour management, and transfer services. Rebuilt with a modern, organized architecture.

## Features

- **Hotel Management**: Add, edit, and manage hotel listings with pricing
- **Tour Management**: Create and manage tour packages
- **Transfer Services**: Airport and point-to-point transfer booking
- **Voucher System**: Generate vouchers for hotels, tours, and transfers
- **User Management**: Admin dashboard with role-based access control
- **Receipt & Invoice Generation**: Create and manage financial documents
- **Automatic Database Setup**: Tables are created automatically on first connection

## Project Structure

```
cyntour/
├── bootstrap.php           # Application bootstrap (include in all pages)
├── core/                   # Core application classes
│   ├── Application.php     # Main application class (singleton)
│   ├── Helper.php          # Utility helper functions
│   ├── View.php            # View rendering component
│   ├── autoload.php        # PSR-4 autoloader with backward compatibility
│   └── auth-guard.php      # Authentication middleware
├── app/                    # Application logic
│   ├── Controllers/        # Request handlers
│   ├── Models/             # Database models
│   └── Views/              # View templates
│       ├── admin/          # Admin dashboard views
│       ├── auth/           # Authentication views
│       ├── dashboard/      # Dashboard views
│       ├── public/         # Public-facing views
│       ├── vouchers/       # Voucher-related views
│       ├── invoices/       # Invoice views
│       ├── receipts/       # Receipt views
│       └── partials/       # Reusable view components
├── assets/                 # Static assets
│   ├── css/                # Stylesheets
│   │   └── cyntour-style.css
│   ├── js/                 # JavaScript files
│   └── images/             # Images and icons
├── database/
│   └── schema.sql          # Database schema with sample data
├── storage/                # Application storage
│   ├── logs/               # Application logs
│   ├── cache/              # Cache files
│   └── uploads/            # User uploads
├── includes/               # Legacy includes (for backward compatibility)
├── css/                    # Legacy CSS (for backward compatibility)
└── uploads/                # Legacy uploads directory
```

## Main Entry Points

| Page | Description |
|------|-------------|
| `home.php` | Public home/landing page |
| `hotels.php` | Hotel listings with search |
| `tours.php` | Tour packages listing |
| `transfers.php` | Transfer services page |
| `contact.php` | Contact information |
| `login.php` | User authentication |
| `register.php` | User registration |
| `dashboard.php` | Main dashboard (requires auth) |

## Quick Start

### 1. Include Bootstrap

Add this to the top of every PHP file:

```php
<?php
require_once 'bootstrap.php';
```

### 2. Require Authentication (Optional)

For pages that need authentication:

```php
<?php
require_once 'core/auth-guard.php';
```

### 3. Use Application Classes

```php
<?php
use CynTour\Core\Application;
use CynTour\Core\Helper;
use CynTour\Core\View;

$app = Application::getInstance();

// Check authentication
if ($app->isAuthenticated()) {
    echo $app->getDisplayName();
}

// Get database connection
$conn = $app->getMysqli();
$pdo = $app->getPdo();

// Use helper functions
Helper::redirect('dashboard.php');
$symbol = Helper::getCurrencySymbol('EUR');

// Render views
View::renderHead('Page Title');
View::renderNavbar();
View::renderFooter();
View::renderScripts();
```

### 4. Backward Compatibility

Legacy function names still work:

```php
// These functions are available for backward compatibility
getMysqliConnection();  // Returns mysqli
getDbConnection();      // Returns PDO
safe_redirect($url);    // Redirects with fallback
cyn_render_head($title);
cyn_render_navbar();
cyn_render_footer();
cyn_is_logged_in();
cyn_is_admin();
```

## Database Configuration

### Environment Variables (Recommended)

```bash
export DB_HOST=localhost
export DB_NAME=your_database
export DB_USER=your_username
export DB_PASS=your_password
export APP_DEBUG=false
```

### Default Credentials

- **Database**: `barqvkxs_cyn`
- **Username**: `barqvkxs_cyn`
- **Password**: Configured in Application class

### Auto-initialization

Tables are created automatically on first database connection using `database/schema.sql`.

## Default Login

- **Email**: admin@cyntour.com
- **Password**: Admin123!

## Voucher System

Three types of vouchers are supported:

1. **Hotel Vouchers** (`h_vouchers`)
   - Accommodation bookings with check-in/check-out dates
   - Room type and meal plan information

2. **Tour Vouchers** (`city_tour_vouchers`)
   - Tour bookings with multiple tours per voucher
   - Customer list management

3. **Transfer Vouchers** (`transfer_vouchers`)
   - Airport transfers and point-to-point services
   - Vehicle type and passenger count

## Technologies

- PHP 7.4+
- MySQL 5.7+ / MariaDB 10+
- Bootstrap 5
- Font Awesome 6
- jQuery 3.6

## Security Notes

- Never commit `.env` or configuration files with real credentials
- Use environment variables in production
- All passwords are hashed using `password_hash()`
- CSRF protection should be implemented for forms
- All user input is sanitized using prepared statements

## File Naming Convention

- Use lowercase with hyphens for URL-friendly files: `hotel-vouchers.php`
- Use PascalCase for class files: `Application.php`
- Use camelCase for function names: `getCurrencySymbol()`
- Avoid special characters and spaces in file names

## License

© 2006-2024 CYN TURIZM. All Rights Reserved.
