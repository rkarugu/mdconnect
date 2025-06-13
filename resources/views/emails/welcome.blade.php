@extends('emails.layouts.main')

@section('title', 'Welcome to MediConnect')

@section('header', 'Welcome to MediConnect')

@section('content')
    <h3>Hello {{ $user->name ?? 'there' }},</h3>
    
    <p>Welcome to MediConnect! We're excited to have you join our platform.</p>
    
    <p>MediConnect is designed to connect patients with healthcare providers seamlessly. Our platform makes it easy to find, book, and manage healthcare appointments.</p>
    
    <div class="text-center">
        <a href="{{ route('dashboard') }}" class="button">Go to Your Dashboard</a>
    </div>
    
    <p>Here are a few things you can do with your new account:</p>
    <ul>
        <li>Complete your profile information</li>
        <li>Browse available medical facilities</li>
        <li>Connect with healthcare providers</li>
        <li>Schedule appointments</li>
        <li>Manage your health records</li>
    </ul>
    
    <p>If you have any questions or need assistance, our support team is always ready to help.</p>
    
    <p class="text-primary">We're glad you're here!</p>
@endsection
