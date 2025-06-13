# MediConnect Email System Documentation

## Overview

This document provides instructions for setting up and using the MediConnect email system with the uptownnvintage.com domain. The email functionality is integrated throughout the application for various use cases like user registration, notifications, and system alerts.

## Configuration

### Setting Up Email Credentials

1. Run the provided setup script to configure your email settings:

```bash
php setup_email.php
```

2. When prompted, enter the email password for the noreply@uptownnvintage.com account.

3. The script will update your `.env` file with the following configuration:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.uptownnvintage.com
MAIL_PORT=587
MAIL_USERNAME=noreply@uptownnvintage.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@uptownnvintage.com
MAIL_FROM_NAME=MediConnect
```

### Testing Email Functionality

1. Navigate to the Email Settings page in the admin panel.
2. Enter a recipient email and select the email type (Test or Welcome).
3. Click "Send Test Email" to verify your configuration is working.

## Available Email Templates

### 1. Test Email (`emails.test`)
- Purpose: Verify email configuration is working correctly
- View: `resources/views/emails/test.blade.php`
- Controller: `EmailTestController@sendTestEmail`

### 2. Welcome Email (`emails.welcome`)
- Purpose: Sent to new users upon registration
- View: `resources/views/emails/welcome.blade.php`
- Mailable: `App\Mail\WelcomeEmail`

## Email Layout System

All email templates extend the main email layout located at:
`resources/views/emails/layouts/main.blade.php`

This layout provides consistent styling and branding across all outgoing emails, including:
- MediConnect logo header
- Standardized styling for content sections
- Footer with copyright and contact information

## Implementing Custom Email Templates

To create a new email template:

1. Create a new Blade view in `resources/views/emails/` that extends the main layout:
```php
@extends('emails.layouts.main')

@section('title', 'Your Email Title')

@section('header', 'Your Email Header')

@section('content')
    <!-- Your custom email content -->
@endsection
```

2. Create a corresponding Mailable class in `App\Mail`:
```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class YourCustomEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Email Subject',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.your-custom-template',
        );
    }
}
```

3. Send the email from your controller:
```php
Mail::to($user->email)->send(new YourCustomEmail());
```

## Scheduled Email Health Check

A weekly health check is scheduled to run every Monday at 9:00 AM to verify the email system is functioning correctly. This task can be run manually using:

```bash
php artisan email:health-check --email=admin@example.com
```

## Troubleshooting

If emails are not being sent:

1. Verify your SMTP credentials in the `.env` file
2. Check the Laravel log files in `storage/logs/`
3. Run the email health check command to diagnose issues
4. Ensure the SMTP port (587) is not blocked by your server firewall

For further assistance, contact the MediConnect support team.
