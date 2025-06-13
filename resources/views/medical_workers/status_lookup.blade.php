@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-blue-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">Medical Worker Application Status</h2>
        </div>
        
        <div class="px-6 py-6">
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            
            <p class="mb-6">Please enter the email address you used when applying to check your application status.</p>
            
            <form action="{{ route('medical-workers.lookup-status') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" id="email" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Check Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
