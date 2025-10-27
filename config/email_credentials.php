<?php
// Email credentials configuration
// This file stores the customer Gmail account for sending emails

// Customer email account (used for sending all emails)
define('CUSTOMER_EMAIL', 'iskoprintcustomer@gmail.com');
define('CUSTOMER_PASSWORD', 'tbyd krut layw wzye'); // App Password for iskoprintcustomer@gmail.com

// Admin email (recipient of all service emails)
define('ADMIN_EMAIL', 'iskoprint6@gmail.com');

// Service recipient emails (all go to admin)
$service_recipients = [
    'printer' => ADMIN_EMAIL,
    'bookbind' => ADMIN_EMAIL,
    'laminate' => ADMIN_EMAIL,
    'pictures' => ADMIN_EMAIL,
    'photocopy' => ADMIN_EMAIL,
    'tarpaulin' => ADMIN_EMAIL,
];

/**
 * Get recipient email for a specific service
 * @param string $serviceType Service type (printer, bookbind, etc.)
 * @return string Recipient email address
 */
function getServiceRecipient($serviceType) {
    global $service_recipients;
    return isset($service_recipients[$serviceType]) ? $service_recipients[$serviceType] : ADMIN_EMAIL;
}