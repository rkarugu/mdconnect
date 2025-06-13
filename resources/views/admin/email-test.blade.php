@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Email Configuration Test</h1>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-700 mb-2">Current Email Configuration</h2>
                <div class="bg-gray-50 p-4 rounded">
                    <p><strong>Mail Driver:</strong> {{ config('mail.default') }}</p>
                    <p><strong>Mail Host:</strong> {{ config('mail.mailers.smtp.host') }}</p>
                    <p><strong>Mail Port:</strong> {{ config('mail.mailers.smtp.port') }}</p>
                    <p><strong>Mail Encryption:</strong> {{ config('mail.mailers.smtp.encryption') ?? 'None' }}</p>
                    <p><strong>Mail From Address:</strong> {{ config('mail.from.address') }}</p>
                    <p><strong>Mail From Name:</strong> {{ config('mail.from.name') }}</p>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h2 class="text-lg font-medium text-gray-700 mb-4">Send Test Email</h2>
                <form action="{{ route('email.test.send') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Recipient Email</label>
                        <input type="email" name="email" id="email" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Enter email address to receive test email">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Type</label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="radio" id="type-test" name="type" value="test" checked
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <label for="type-test" class="ml-2 block text-sm text-gray-700">
                                    Basic Test Email
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="type-welcome" name="type" value="welcome"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <label for="type-welcome" class="ml-2 block text-sm text-gray-700">
                                    Welcome Email
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Send Test Email
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-8 border-t border-gray-200 pt-6">
                <h2 class="text-lg font-medium text-gray-700 mb-4">Email Configuration Instructions</h2>
                <div class="bg-blue-50 p-4 rounded text-sm">
                    <p class="mb-2">To configure your email settings:</p>
                    <ol class="list-decimal ml-6 space-y-2">
                        <li>Run the setup script: <code class="bg-gray-100 px-2 py-1 rounded">php setup_email.php</code></li>
                        <li>Follow the prompts to enter your email credentials for the uptownnvintage.com domain</li>
                        <li>Restart your server if needed</li>
                        <li>Use this page to send a test email and verify the configuration</li>
                    </ol>
                    <p class="mt-4">If you encounter issues, check your server logs for more details.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
