# CynTour - Receipt Management System v2.0

A receipt and payment management system for CYN Turizm.

## Features

- **Receipt Dashboard**: View and manage all company receipts
- **Receipt Creation**: Create new receipts with multiple payment support
- **Payment Tracking**: Track payments with multiple currencies
- **User Authentication**: Secure login and session management
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
└── uploads/                # Legacy uploads directory
```

## Main Entry Points

| Page | Description |
|------|-------------|
| `index.php` | Redirect to dashboard |
| `dashboard.php` | Main receipts dashboard |
| `receipt-create.php` | Create new receipt |
| `receipt-form.php` | Simple receipt form |
| `receipt-view.php` | View existing receipt |
| `receipt.php` | Receipt output display |
| `login.php` | User authentication |
| `logout.php` | User logout |

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

## Database Configuration

### Environment Variables (Recommended)

```bash
export DB_HOST=localhost
export DB_NAME=your_database
export DB_USER=your_username
export DB_PASS=your_password
export APP_DEBUG=false
```

### Auto-initialization

Tables are created automatically on first database connection using `database/schema.sql`.

## Default Login

- **Email**: admin@cyntour.com
- **Password**: Admin123!

## Technologies

- PHP 7.4+
- MySQL 5.7+ / MariaDB 10+
- Bootstrap 4.5
- Font Awesome 6

## License

© 2006-2024 CYN TURIZM. All Rights Reserved.
