<?php
// Email Configuration
// This file contains settings for the email system

// Gmail SMTP Settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_ENCRYPTION', 'tls');

// Default recipient emails for each service
$service_emails = [
    'printer' => 'printer@yourcompany.com',
    'bookbind' => 'bookbind@yourcompany.com', 
    'laminate' => 'laminate@yourcompany.com',
    'pictures' => 'pictures@yourcompany.com',
    'photocopy' => 'photocopy@yourcompany.com',
    'tarpaulin' => 'tarpaulin@yourcompany.com'
];

// Email templates
$email_templates = [
    'printer' => [
        'name' => 'Printer Service',
        'subject_prefix' => '[Printer Service]',
        'template' => 'printer_service_template'
    ],
    'bookbind' => [
        'name' => 'Book Binding Service', 
        'subject_prefix' => '[Book Binding]',
        'template' => 'bookbind_service_template'
    ],
    'laminate' => [
        'name' => 'Lamination Service',
        'subject_prefix' => '[Lamination]', 
        'template' => 'laminate_service_template'
    ],
    'pictures' => [
        'name' => 'Picture Printing Service',
        'subject_prefix' => '[Picture Printing]',
        'template' => 'pictures_service_template'
    ],
    'photocopy' => [
        'name' => 'Photocopy Service',
        'subject_prefix' => '[Photocopy]',
        'template' => 'photocopy_service_template'
    ],
    'tarpaulin' => [
        'name' => 'Tarpaulin Printing Service',
        'subject_prefix' => '[Tarpaulin Printing]',
        'template' => 'tarpaulin_service_template'
    ]
];

// File upload settings
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt']);

// Security settings
define('REQUIRE_LOGIN', true);
define('RATE_LIMIT_PER_HOUR', 10); // Max emails per user per hour
?>
