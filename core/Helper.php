<?php
/**
 * CynTour Helper Functions
 * 
 * Collection of utility functions used throughout the application.
 * 
 * @package CynTour
 * @version 2.0
 */

namespace CynTour\Core;

class Helper
{
    /**
     * Get currency symbol for a currency code
     * 
     * @param string $code Currency code (USD, EUR, TRY, etc.)
     * @return string Currency symbol
     */
    public static function getCurrencySymbol(string $code): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'TRY' => '₺',
            'GBP' => '£',
            'DZD' => 'DZD',
        ];
        return $symbols[$code] ?? $code;
    }
    
    /**
     * Generate initials avatar HTML
     * 
     * @param string $name Full name
     * @return string HTML for avatar
     */
    public static function generateInitialsAvatar(string $name): string
    {
        $words = explode(' ', $name);
        $initials = '';
        $initials .= isset($words[0][0]) ? strtoupper($words[0][0]) : '';
        $initials .= isset($words[1][0]) ? strtoupper($words[1][0]) : '';
        if (strlen($initials) == 1 && isset($words[0][1])) {
            $initials .= strtoupper($words[0][1]);
        }
        return '<div class="avatar-initials">' . ($initials ?: 'C') . '</div>';
    }
    
    /**
     * Safe redirect function with fallback mechanisms
     * 
     * This function attempts to redirect using PHP headers first.
     * If headers have already been sent, it falls back to JavaScript
     * and HTML meta refresh redirects.
     * 
     * @param string $url The URL to redirect to
     * @return void (exits the script)
     */
    public static function redirect(string $url): void
    {
        // Sanitize the URL for HTML output
        $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        
        // Clear ALL output buffer levels
        $maxIterations = 100;
        $iterations = 0;
        while (ob_get_level() && $iterations++ < $maxIterations) {
            if (!ob_end_clean()) {
                break;
            }
        }
        
        // Try PHP header redirect first
        if (!headers_sent()) {
            header("Location: " . $url);
            exit();
        }
        
        // Fallback to JavaScript redirect
        echo '<script>window.location.href = "' . $safeUrl . '";</script>';
        // Fallback for browsers with JavaScript disabled
        echo '<noscript><meta http-equiv="refresh" content="0;url=' . $safeUrl . '"></noscript>';
        exit();
    }
    
    /**
     * Format date for display
     * 
     * @param string $date Date string
     * @param string $format Output format
     * @return string Formatted date
     */
    public static function formatDate(string $date, string $format = 'd M Y'): string
    {
        $datetime = new \DateTime($date);
        return $datetime->format($format);
    }
    
    /**
     * Format price with currency
     * 
     * @param float $amount Price amount
     * @param string $currency Currency code
     * @return string Formatted price
     */
    public static function formatPrice(float $amount, string $currency = 'EUR'): string
    {
        return self::getCurrencySymbol($currency) . number_format($amount, 2);
    }
    
    /**
     * Sanitize input string
     * 
     * @param string $input Input to sanitize
     * @return string Sanitized input
     */
    public static function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Generate a unique voucher number
     * 
     * @param string $prefix Prefix for the voucher
     * @return string Voucher number
     */
    public static function generateVoucherNumber(string $prefix = 'VCH'): string
    {
        return $prefix . '-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
    }
    
    /**
     * Generate a unique receipt number
     * 
     * @param string $prefix Prefix for the receipt
     * @return string Receipt number
     */
    public static function generateReceiptNumber(string $prefix = 'RCT'): string
    {
        return $prefix . '-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
    }
    
    /**
     * Check if a string is a valid email
     * 
     * @param string $email Email to validate
     * @return bool True if valid
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Get the current page name from URL
     * 
     * @return string Page name
     */
    public static function getCurrentPage(): string
    {
        return basename($_SERVER['PHP_SELF']);
    }
    
    /**
     * Check if current page matches given page
     * 
     * @param string $page Page to check
     * @return string 'active' if match, empty string otherwise
     */
    public static function isActivePage(string $page): string
    {
        return self::getCurrentPage() === $page ? 'active' : '';
    }
    
    /**
     * Get flash message from session
     * 
     * @param string $type Message type (success, error, warning, info)
     * @return string|null Message or null
     */
    public static function getFlashMessage(string $type): ?string
    {
        if (isset($_SESSION['flash_' . $type])) {
            $message = $_SESSION['flash_' . $type];
            unset($_SESSION['flash_' . $type]);
            return $message;
        }
        return null;
    }
    
    /**
     * Set flash message in session
     * 
     * @param string $type Message type
     * @param string $message Message content
     */
    public static function setFlashMessage(string $type, string $message): void
    {
        $_SESSION['flash_' . $type] = $message;
    }
    
    /**
     * Truncate text to specified length
     * 
     * @param string $text Text to truncate
     * @param int $length Maximum length
     * @param string $suffix Suffix to append if truncated
     * @return string Truncated text
     */
    public static function truncate(string $text, int $length = 100, string $suffix = '...'): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length - strlen($suffix)) . $suffix;
    }
}
