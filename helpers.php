<?php

// Helper function to get currency symbol
function get_currency_symbol($code) {
    $symbols = [
        'USD' => '$',
        'EUR' => '€',
        'TRY' => '₺',
        'DZD' => 'DZD',
    ];
    return $symbols[$code] ?? $code;
}

// Helper function to generate a simple avatar with initials
function generate_initials_avatar($name) {
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
function safe_redirect($url) {
    // Sanitize the URL for HTML output
    $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    
    // Clear ALL output buffer levels to ensure headers can be sent
    // Use a safety counter to prevent infinite loops if ob_end_clean() fails
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
