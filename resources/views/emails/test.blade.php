@extends('emails.layouts.main')

@section('title', 'MediConnect Test Email')

@section('header', 'MediConnect Email System')

@section('content')
    <h3>Email Test Successful!</h3>
    <p>Congratulations! If you're seeing this email, it means your MediConnect email system is configured correctly.</p>
    <p>This is a test email sent from your MediConnect application using the uptownnvintage.com domain.</p>
    
    <div class="text-center">
        <a href="{{ route('dashboard') }}" class="button">Go to Dashboard</a>
    </div>
    
    <p>Email configuration details:</p>
    <ul>
        <li><strong>Sender:</strong> {{ config('mail.from.address') }}</li>
        <li><strong>Mail Driver:</strong> {{ config('mail.default') }}</li>
        <li><strong>Host:</strong> {{ config('mail.mailers.smtp.host') }}</li>
    </ul>
    
    <p class="text-success">All systems operational!</p>
@endsection
