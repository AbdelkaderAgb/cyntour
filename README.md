# Cyntour - Tourism Management System

A tourism management system for CYN Turizm.

## Setup

### Database Configuration

1. Copy `config.example.php` to `config.php`:
   ```bash
   cp config.example.php config.php
   ```

2. Edit `config.php` and update the database credentials:
   ```php
   $db_config = [
       'host'     => 'localhost',
       'database' => 'your_database_name',
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

3. Import the database schema (contact administrator for the schema file).

## Security Notes

- Never commit `config.php` with real credentials to version control
- The `config.php` file is excluded from git via `.gitignore`
- Use environment variables in production environments
