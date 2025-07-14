@extends('layouts.app')

@section('title', 'Top Up Successful - ' . $facility->facility_name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Success Message -->
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-8 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        @if(session('success'))
                            {{ session('success') }}
                        @else
                            Your wallet top-up was successful!
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Top-up Confirmation
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Transaction #{{ $transaction->reference }}
                </p>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                <dl class="sm:divide-y sm:divide-gray-200">
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Amount
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            KES {{ number_format($transaction->amount, 2) }}
                        </dd>
                    </div>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Payment Method
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @php
                                $paymentMethod = $transaction->metadata['payment_method'] ?? 'unknown';
                                $paymentMethodLabels = [
                                    'mpesa' => 'M-Pesa',
                                    'card' => 'Credit/Debit Card',
                                    'bank_transfer' => 'Bank Transfer'
                                ];
                            @endphp
                            {{ $paymentMethodLabels[$paymentMethod] ?? ucfirst($paymentMethod) }}
                            @if($paymentMethod === 'mpesa' && isset($transaction->metadata['phone_number']))
                                <span class="text-gray-500 text-sm ml-2">({{ $transaction->metadata['phone_number'] }})</span>
                            @elseif($paymentMethod === 'card' && isset($transaction->metadata['card_last_four']))
                                <span class="text-gray-500 text-sm ml-2">(•••• {{ $transaction->metadata['card_last_four'] }})</span>
                            @endif
                        </dd>
                    </div>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Status
                        </dt>
                        <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'processing' => 'bg-blue-100 text-blue-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                ][$transaction->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </dd>
                    </div>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Transaction Date
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $transaction->created_at->format('F j, Y, g:i a') }}
                        </dd>
                    </div>
                    @if($transaction->status === 'completed')
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            New Wallet Balance
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-semibold">
                            KES {{ number_format($wallet->balance, 2) }}
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
            
            @if($transaction->status === 'pending' && $transaction->metadata['payment_method'] === 'bank_transfer')
            <div class="bg-blue-50 border-t border-blue-200 px-4 py-4 sm:px-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h.01a1 1 0 100-2H10V9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Pending Bank Transfer
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>
                                We've received your top-up request. Your wallet will be updated once we confirm your bank transfer. 
                                This usually takes 1-2 business days.
                            </p>
                            @if(isset($transaction->metadata['receipt_path']))
                            <p class="mt-2">
                                <span class="font-medium">Receipt uploaded:</span> 
                                <a href="{{ Storage::url($transaction->metadata['receipt_path']) }}" 
                                   target="_blank" 
                                   class="text-blue-600 hover:text-blue-500 underline">
                                    View Receipt
                                </a>
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="mt-8 flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('facility.wallet.show') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Back to Wallet
            </a>
            
            @if($transaction->status === 'completed')
            <a href="{{ route('facility.wallet.transactions') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                View All Transactions
            </a>
            @endif
            
            <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                </svg>
                Print Receipt
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        
        #print-section,
        #print-section * {
            visibility: visible;
        }
        
        #print-section {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-print if coming from payment gateway
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === 'true') {
            window.print();
        }
    });
</script>
@endpush
@endsection
