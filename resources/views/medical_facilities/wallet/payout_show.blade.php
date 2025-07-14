@extends('layouts.app')

@section('title', 'Payout Details - ' . $facility->facility_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Payout Details</h1>
            <p class="text-sm text-gray-500">Reference: {{ $payout->reference }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('facility.wallet.payouts') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Payouts
            </a>
            @if($payout->status === 'pending')
                <button 
                    onclick="confirmCancelPayout('{{ $payout->id }}')"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    <i class="fas fa-times-circle mr-2"></i> Cancel Request
                </button>
                <form id="cancel-payout-form-{{ $payout->id }}" 
                      action="{{ route('facility.wallet.payouts.cancel', $payout) }}" 
                      method="POST" 
                      class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
            <button onclick="window.print()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                <i class="fas fa-print mr-2"></i> Print
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Payout Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h2 class="text-lg font-medium text-gray-800">Payout Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'processing' => 'bg-blue-100 text-blue-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                ][$payout->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <p class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                    {{ ucfirst($payout->status) }}
                                </span>
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Requested On</h3>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $payout->created_at->format('F j, Y, g:i a') }}
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Amount</h3>
                            <p class="mt-1 text-lg font-semibold text-gray-900">
                                KES {{ number_format($payout->amount, 2) }}
                            </p>
                        </div>
                        
                        @if($payout->fee > 0)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Processing Fee</h3>
                            <p class="mt-1 text-sm text-gray-900">
                                KES {{ number_format($payout->fee, 2) }}
                            </p>
                        </div>
                        @endif
                        
                        @if($payout->completed_at)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Completed On</h3>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $payout->completed_at->format('F j, Y, g:i a') }}
                            </p>
                        </div>
                        @endif
                        
                        @if($payout->cancelled_at)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Cancelled On</h3>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $payout->cancelled_at->format('F j, Y, g:i a') }}
                            </p>
                        </div>
                        @endif
                        
                        @if($payout->failure_reason)
                        <div class="md:col-span-2">
                            <h3 class="text-sm font-medium text-gray-500">Failure Reason</h3>
                            <p class="mt-1 text-sm text-red-600">
                                {{ $payout->failure_reason }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Payment Method -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h2 class="text-lg font-medium text-gray-800">Payment Method</h2>
                </div>
                <div class="p-6">
                    @php
                        $paymentMethod = json_decode($payout->payment_method, true);
                        $methodType = $paymentMethod['type'] ?? 'unknown';
                        $methodLabel = [
                            'mpesa' => 'M-Pesa',
                            'bank_transfer' => 'Bank Transfer',
                            'card' => 'Credit/Debit Card',
                        ][$methodType] ?? ucfirst($methodType);
                    @endphp
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                            @if($methodType === 'mpesa')
                                <img src="{{ asset('images/mpesa-logo.png') }}" alt="M-Pesa" class="h-6">
                            @elseif($methodType === 'bank_transfer')
                                <i class="fas fa-university text-blue-600 text-xl"></i>
                            @elseif($methodType === 'card')
                                <i class="far fa-credit-card text-blue-600 text-xl"></i>
                            @else
                                <i class="fas fa-wallet text-blue-600 text-xl"></i>
                            @endif
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ $methodLabel }}</h3>
                            
                            @if($methodType === 'mpesa' && !empty($paymentMethod['phone_number']))
                                <p class="text-sm text-gray-500">
                                    {{ $paymentMethod['phone_number'] }}
                                </p>
                            @elseif($methodType === 'bank_transfer')
                                <div class="mt-2 space-y-1">
                                    @if(!empty($paymentMethod['bank_name']))
                                        <p class="text-sm text-gray-700">
                                            <span class="font-medium">Bank:</span> {{ $paymentMethod['bank_name'] }}
                                        </p>
                                    @endif
                                    
                                    @if(!empty($paymentMethod['account_name']))
                                        <p class="text-sm text-gray-700">
                                            <span class="font-medium">Account Name:</span> {{ $paymentMethod['account_name'] }}
                                        </p>
                                    @endif
                                    
                                    @if(!empty($paymentMethod['account_number']))
                                        <p class="text-sm text-gray-700">
                                            <span class="font-medium">Account Number:</span> {{ $paymentMethod['account_number'] }}
                                        </p>
                                    @endif
                                    
                                    @if(!empty($paymentMethod['swift_code']))
                                        <p class="text-sm text-gray-700">
                                            <span class="font-medium">SWIFT/BIC:</span> {{ $paymentMethod['swift_code'] }}
                                        </p>
                                    @endif
                                    
                                    @if(!empty($paymentMethod['branch_code']))
                                        <p class="text-sm text-gray-700">
                                            <span class="font-medium">Branch Code:</span> {{ $paymentMethod['branch_code'] }}
                                        </p>
                                    @endif
                                </div>
                            @elseif($methodType === 'card' && !empty($paymentMethod['card_last_four']))
                                <p class="text-sm text-gray-500">
                                    •••• {{ $paymentMethod['card_last_four'] }}
                                </p>
                            @endif
                            
                            @if(!empty($paymentMethod['nickname']))
                                <p class="mt-1 text-xs text-gray-500">
                                    <i class="fas fa-tag mr-1"></i> {{ $paymentMethod['nickname'] }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            @if($payout->admin_notes)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Admin Note
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>{{ $payout->admin_notes }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Timeline -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h2 class="text-lg font-medium text-gray-800">Payout Timeline</h2>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <!-- Requested -->
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Payout requested</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $payout->created_at->toIso8601String() }}">{{ $payout->created_at->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            
                            @if($payout->status === 'processing' || $payout->status === 'completed' || $payout->status === 'failed')
                            <!-- Processing -->
                            <li>
                                <div class="relative pb-8">
                                    @if($payout->status !== 'completed' && $payout->status !== 'failed')
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Payout processing</p>
                                            </div>
                                            @if($payout->processed_at)
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $payout->processed_at->toIso8601String() }}">{{ $payout->processed_at->diffForHumans() }}</time>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif
                            
                            @if($payout->status === 'completed')
                            <!-- Completed -->
                            <li>
                                <div class="relative">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Payout completed</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $payout->completed_at->toIso8601String() }}">{{ $payout->completed_at->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @elseif($payout->status === 'failed')
                            <!-- Failed -->
                            <li>
                                <div class="relative">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Payout failed</p>
                                                @if($payout->failure_reason)
                                                <p class="text-xs text-red-600">{{ $payout->failure_reason }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $payout->updated_at->toIso8601String() }}">{{ $payout->updated_at->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @elseif($payout->status === 'cancelled')
                            <!-- Cancelled -->
                            <li>
                                <div class="relative">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Payout cancelled</p>
                                                @if($payout->cancellation_reason)
                                                <p class="text-xs text-gray-600">{{ $payout->cancellation_reason }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $payout->cancelled_at->toIso8601String() }}">{{ $payout->cancelled_at->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                    
                    @if($payout->status === 'pending')
                    <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Your payout request is being reviewed. You can cancel this request if needed.
                                </p>
                            </div>
                        </div>
                    </div>
                    @elseif($payout->status === 'processing')
                    <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h.01a1 1 0 100-2H10V9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Your payout is being processed. This usually takes 1-2 business days.
                                </p>
                            </div>
                        </div>
                    </div>
                    @elseif($payout->status === 'completed')
                    <div class="mt-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">
                                    Your payout has been successfully processed and the funds have been sent to your account.
                                </p>
                            </div>
                        </div>
                    </div>
                    @elseif($payout->status === 'failed')
                    <div class="mt-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Payout Failed
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p>
                                        We couldn't process your payout. Please check your payment method details and try again.
                                    </p>
                                    @if($payout->failure_reason)
                                    <p class="mt-2 font-medium">Reason: {{ $payout->failure_reason }}</p>
                                    @endif
                                    <div class="mt-4">
                                        <a href="{{ route('facility.wallet.payouts.request') }}" class="text-sm font-medium text-red-600 hover:text-red-500">
                                            Request a new payout <span aria-hidden="true">&rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @elseif($payout->status === 'cancelled')
                    <div class="mt-6 bg-gray-50 border-l-4 border-gray-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 0l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L7.414 11H15a1 1 0 100-2H7.414l1.293-1.293a1 1 0 000-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-gray-800">
                                    Payout Cancelled
                                </h3>
                                <div class="mt-2 text-sm text-gray-700">
                                    <p>
                                        This payout request has been cancelled.
                                        @if($payout->cancellation_reason)
                                        <br>Reason: {{ $payout->cancellation_reason }}
                                        @endif
                                    </p>
                                    <div class="mt-4">
                                        <a href="{{ route('facility.wallet.payouts.request') }}" class="text-sm font-medium text-gray-600 hover:text-gray-500">
                                            Request a new payout <span aria-hidden="true">&rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmCancelPayout(payoutId) {
        if (confirm('Are you sure you want to cancel this payout request? This action cannot be undone.')) {
            document.getElementById('cancel-payout-form-' + payoutId).submit();
        }
    }
    
    // Auto-print if print parameter is in URL
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === 'true') {
            window.print();
        }
    });
</script>
@endpush

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
        
        .print-mt-0 {
            margin-top: 0 !important;
        }
        
        .print-pt-0 {
            padding-top: 0 !important;
        }
    }
    
    /* Timeline dot connector */
    .timeline-connector {
        position: absolute;
        top: 0.5rem;
        left: 1.5rem;
        height: 100%;
        width: 2px;
        background-color: #e5e7eb;
    }
    
    /* Status badge styles */
    .status-badge {
        @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
    }
    
    .status-pending {
        @apply bg-yellow-100 text-yellow-800;
    }
    
    .status-processing {
        @apply bg-blue-100 text-blue-800;
    }
    
    .status-completed {
        @apply bg-green-100 text-green-800;
    }
    
    .status-failed {
        @apply bg-red-100 text-red-800;
    }
    
    .status-cancelled {
        @apply bg-gray-100 text-gray-800;
    }
</style>
@endpush
@endsection
