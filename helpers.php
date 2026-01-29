<?php
/**
 * CynTour Helper Functions
 * 
 * This file provides backward compatibility by loading the new core.
 * All helper functions are now defined in core/Helper.php
 * 
 * @deprecated Use core/autoload.php directly for new code
 */

// Load the new core system
if (!class_exists('\CynTour\Core\Application')) {
    require_once __DIR__ . '/core/autoload.php';
}

// Legacy functions are provided by core/autoload.php:
// - get_currency_symbol($code)
// - generate_initials_avatar($name)
// - safe_redirect($url)
