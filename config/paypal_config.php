<?php
// PayPal configuration
// This file stores PayPal sandbox credentials for development/testing

// PayPal Sandbox Client ID
// Get your sandbox client ID from: https://developer.paypal.com/dashboard/applications/sandbox
// Create a sandbox app in your PayPal Developer Dashboard to get your client ID
define('PAYPAL_CLIENT_ID', 'AUO2Jg_mZ6NDkQ24pRZvhOg9GmtGtTRine1LBgNQlivtJtZKo1nlrLSDzJQjBA-hEBxeW2lWVlIeyrbC');

// PayPal Environment
// Set to 'sandbox' for testing, 'production' for live payments
define('PAYPAL_ENVIRONMENT', 'sandbox');

// PayPal Currency
define('PAYPAL_CURRENCY', 'PHP');

/**
 * Get PayPal SDK URL with configured client ID
 * @return string PayPal SDK script URL
 */
function getPayPalSDKUrl() {
    $clientId = PAYPAL_CLIENT_ID;
    $currency = PAYPAL_CURRENCY;
    $env = PAYPAL_ENVIRONMENT === 'sandbox' ? 'sandbox' : '';
    
    // For sandbox, the client ID should be a sandbox client ID
    // The SDK automatically detects sandbox vs production based on the client ID
    return "https://www.paypal.com/sdk/js?client-id={$clientId}&currency={$currency}";
}
?>

