<?php
/**
 * CynTour Autoloader
 * 
 * Handles automatic class loading using PSR-4 standard.
 * 
 * @package CynTour
 * @version 2.0
 */

spl_autoload_register(function ($class) {
    // Project-specific namespace prefix
    $prefix = 'CynTour\\';
    
    // Base directory for the namespace prefix
    $baseDir = __DIR__ . '/../';
    
    // Check if the class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered autoloader
        return;
    }
    
    // Get the relative class name
    $relativeClass = substr($class, $len);
    
    // Map namespace parts to directory path
    // CynTour\Core\Application -> core/Application.php
    $parts = explode('\\', $relativeClass);
    
    // Convert first part (like 'Core') to lowercase for directory
    if (count($parts) > 0) {
        $parts[0] = strtolower($parts[0]);
    }
    
    $file = $baseDir . implode('/', $parts) . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
        return;
    }
    
    // Try fully lowercase for backward compatibility
    $file = $baseDir . strtolower(str_replace('\\', '/', $relativeClass)) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

/**
 * Helper function for backward compatibility - get PDO connection
 * 
 * @return PDO
 */
function getDbConnection(): PDO
{
    return \CynTour\Core\Application::getInstance()->getPdo();
}

/**
 * Helper function for backward compatibility - get MySQLi connection
 * 
 * @return mysqli
 */
function getMysqliConnection(): mysqli
{
    return \CynTour\Core\Application::getInstance()->getMysqli();
}

/**
 * Helper function for backward compatibility - safe redirect
 * 
 * @param string $url URL to redirect to
 */
function safe_redirect(string $url): void
{
    \CynTour\Core\Helper::redirect($url);
}

/**
 * Helper function for backward compatibility - get currency symbol
 * 
 * @param string $code Currency code
 * @return string Currency symbol
 */
function get_currency_symbol(string $code): string
{
    return \CynTour\Core\Helper::getCurrencySymbol($code);
}

/**
 * Helper function for backward compatibility - generate initials avatar
 * 
 * @param string $name Full name
 * @return string Avatar HTML
 */
function generate_initials_avatar(string $name): string
{
    return \CynTour\Core\Helper::generateInitialsAvatar($name);
}

/**
 * Components function compatibility aliases
 * Only define if not already defined to prevent conflicts
 */
if (!function_exists('cyn_is_logged_in')) {
    function cyn_is_logged_in(): bool
    {
        return \CynTour\Core\Application::getInstance()->isAuthenticated();
    }
}

if (!function_exists('cyn_is_admin')) {
    function cyn_is_admin(): bool
    {
        return \CynTour\Core\Application::getInstance()->isAdmin();
    }
}

if (!function_exists('cyn_get_current_page')) {
    function cyn_get_current_page(): string
    {
        return \CynTour\Core\Helper::getCurrentPage();
    }
}

if (!function_exists('cyn_nav_active')) {
    function cyn_nav_active(string $page): string
    {
        return \CynTour\Core\Helper::isActivePage($page);
    }
}

if (!function_exists('cyn_get_display_name')) {
    function cyn_get_display_name(): string
    {
        return \CynTour\Core\Application::getInstance()->getDisplayName();
    }
}

if (!function_exists('cyn_render_head')) {
    function cyn_render_head(string $title = 'CynTour - Premium Travel Services', string $additionalCss = ''): void
    {
        \CynTour\Core\View::renderHead($title, $additionalCss);
    }
}

if (!function_exists('cyn_render_navbar')) {
    function cyn_render_navbar(bool $transparent = false): void
    {
        \CynTour\Core\View::renderNavbar($transparent);
    }
}

if (!function_exists('cyn_render_sidebar')) {
    function cyn_render_sidebar(): void
    {
        \CynTour\Core\View::renderSidebar();
    }
}

if (!function_exists('cyn_render_dashboard_header')) {
    function cyn_render_dashboard_header(string $pageTitle = 'Dashboard'): void
    {
        \CynTour\Core\View::renderDashboardHeader($pageTitle);
    }
}

if (!function_exists('cyn_render_footer')) {
    function cyn_render_footer(): void
    {
        \CynTour\Core\View::renderFooter();
    }
}

if (!function_exists('cyn_render_scripts')) {
    function cyn_render_scripts(): void
    {
        \CynTour\Core\View::renderScripts();
    }
}
