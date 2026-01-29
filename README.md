# Cyntour - Tourism Management System

A comprehensive tourism management system for CYN Turizm, featuring hotel booking, tour management, and transfer services.

## Features

- **Hotel Management**: Add, edit, and manage hotel listings with pricing
- **Tour Management**: Create and manage tour packages
- **Transfer Services**: Airport and point-to-point transfer booking
- **Voucher System**: Generate vouchers for hotels, tours, and transfers
- **User Management**: Admin dashboard with role-based access control
- **Receipt & Invoice Generation**: Create and manage financial documents

## Project Structure

```
cyntour/
├── config.php              # Database configuration (create from example)
├── config.example.php      # Database config template
├── database/
│   └── schema.sql          # Complete database schema with sample data
├── includes/
│   ├── navigation.php      # Shared navigation component
│   └── footer.php          # Shared footer component
├── img/                    # Images and assets
├── css/                    # Stylesheets
├── js/                     # JavaScript files
├── pdf/                    # Tour program PDFs
├── uploads/                # User uploads
└── vendor/                 # PHP dependencies
```

## Setup

### 1. Database Configuration

1. Copy `config.example.php` to `config.php`:
   ```bash
   cp config.example.php config.php
   ```

2. Edit `config.php` and update the database credentials:
   ```php
   $db_config = [
       'host'     => 'localhost',
       'database' => 'cyntour_db',
       'username' => 'your_username',
       'password' => 'your_password',
       'charset'  => 'utf8mb4'
   ];
   ```

   Or set environment variables:
   - `DB_HOST` - Database host
   - `DB_NAME` - Database name
   - `DB_USER` - Database username
   - `DB_PASS` - Database password

### 2. Database Setup

Import the database schema:
```bash
mysql -u username -p < database/schema.sql
```

This will create:
- All necessary tables
- Sample data for testing
- Reporting views

### 3. Default Login

After importing the schema, you can login with:
- **Email**: admin@cyntour.com
- **Password**: Admin123!

## Main Pages

| Page | Description |
|------|-------------|
| `index.php` | Home page with hotel listings |
| `admin.php` | Admin dashboard |
| `tours.php` | Tour listings |
| `transfer.php` | Transfer services |
| `login.php` | User login |
| `register.php` | User registration |

## Voucher System

The system supports three types of vouchers:

1. **Hotel Vouchers** (`h_vouchers`)
   - Accommodation bookings with check-in/check-out dates
   - Room type and meal plan information

2. **Tour Vouchers** (`city_tour_vouchers`)
   - Tour bookings with multiple tours per voucher
   - Customer list management

3. **Transfer Vouchers** (`transfer_vouchers`)
   - Airport transfers and point-to-point services
   - Vehicle type and passenger count

## Security Notes

- Never commit `config.php` with real credentials to version control
- The `config.php` file is excluded from git via `.gitignore`
- Use environment variables in production environments
- All user passwords are hashed using `password_hash()`

## Technologies Used

- PHP 7.4+
- MySQL 5.7+ / MariaDB 10+
- Bootstrap 5
- Font Awesome 6
- jQuery 3.6

## License

© 2006-2024 CYN TURIZM. All Rights Reserved.
