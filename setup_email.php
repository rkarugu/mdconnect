<?php

/**
 * MediConnect Email Configuration Setup
 * 
 * This script helps to set up email configuration with uptownnvintage.com domain
 * Run this script from command line: php setup_email.php
 */

// Default email configuration
$emailConfig = [
    'MAIL_MAILER' => 'smtp',
    'MAIL_HOST' => 'smtp.uptownnvintage.com',
    'MAIL_PORT' => '587',
    'MAIL_USERNAME' => 'noreply@uptownnvintage.com',
    'MAIL_PASSWORD' => '', // Will prompt for this
    'MAIL_ENCRYPTION' => 'tls',
    'MAIL_FROM_ADDRESS' => 'noreply@uptownnvintage.com',
    'MAIL_FROM_NAME' => 'MediConnect'
];

echo "MediConnect Email Configuration Setup\n";
echo "------------------------------------\n\n";

// Ask for password
echo "Enter the email password for {$emailConfig['MAIL_USERNAME']}: ";
$password = trim(readline());
$emailConfig['MAIL_PASSWORD'] = $password;

// Check if .env file exists
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    echo "Error: .env file not found. Please ensure you have a .env file in your project root.\n";
    exit(1);
}

// Read the current .env file
$envContent = file_get_contents($envFile);

// Update each configuration item
foreach ($emailConfig as $key => $value) {
    // Check if the key exists in the .env file
    if (preg_match("/^{$key}=.*/m", $envContent)) {
        // Replace existing key
        $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
    } else {
        // Add new key at the end
        $envContent .= "\n{$key}={$value}";
    }
}

// Write the updated content back to the .env file
if (file_put_contents($envFile, $envContent)) {
    echo "\nEmail configuration successfully updated!\n";
    echo "The application will now use the following email settings:\n";
    foreach ($emailConfig as $key => $value) {
        if ($key === 'MAIL_PASSWORD') {
            $value = str_repeat('*', strlen($value)); // Mask the password
        }
        echo "- {$key}: {$value}\n";
    }
    
    echo "\nYou may need to restart your server for changes to take effect.\n";
    echo "Don't forget to test your email configuration using Laravel's built-in mail testing.\n";
    echo "You can do this by running: php artisan tinker\n";
    echo "Then in tinker: Mail::raw('Test email', function(\$message) { \$message->to('your@email.com')->subject('Test Email'); });\n";
} else {
    echo "\nError: Failed to update .env file. Make sure you have write permissions.\n";
}
