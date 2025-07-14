@extends('layouts.app')

@section('title', 'Wallet Transactions - ' . $facility->facility_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Wallet Transactions</h1>
        <div class="flex space-x-2">
            <a href="{{ route('facility.wallet.show') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Wallet
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form action="{{ route('facility.wallet.transactions') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Transaction Type</label>
                <select name="type" id="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">All Types</option>
                    <option value="credit" {{ request('type') === 'credit' ? 'selected' : '' }}>Credits</option>
                    <option value="debit" {{ request('type') === 'debit' ? 'selected' : '' }}>Debits</option>
                    <option value="topup" {{ request('type') === 'topup' ? 'selected' : '' }}>Top-ups</option>
                    <option value="payout" {{ request('type') === 'payout' ? 'selected' : '' }}>Payouts</option>
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('facility.wallet.transactions') }}" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    <i class="fas fa-sync-alt"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h2 class="text-lg font-medium text-gray-800">Transaction History</h2>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($transactions as $transaction)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="flex items-start">
                            <div class="p-3 rounded-full {{ $transaction->amount >= 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                @if($transaction->type === 'payout')
                                    <i class="fas fa-money-bill-wave text-xl"></i>
                                @elseif($transaction->type === 'topup')
                                    <i class="fas fa-plus-circle text-xl"></i>
                                @else
                                    <i class="fas fa-exchange-alt text-xl"></i>
                                @endif
                            </div>
                            <div class="ml-4">
                                <h3 class="text-base font-medium text-gray-900">{{ $transaction->description }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ $transaction->created_at->format('M d, Y h:i A') }}
                                    @if($transaction->reference)
                                        <span class="mx-2">•</span>
                                        <span>Ref: {{ $transaction->reference }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0 text-right">
                            <p class="text-lg font-semibold {{ $transaction->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->amount >= 0 ? '+' : '' }}{{ number_format($transaction->amount, 2) }} KES
                            </p>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                    $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                    ($transaction->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') 
                                }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                                <span class="ml-2 text-sm text-gray-500">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @if($transaction->metadata)
                        <div class="mt-3 pt-3 border-t border-gray-100 text-sm text-gray-600">
                            @foreach($transaction->metadata as $key => $value)
                                @if(!in_array($key, ['created_at', 'updated_at', 'deleted_at']))
                                    <div class="grid grid-cols-3 gap-2 py-1">
                                        <span class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                        <span class="col-span-2">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-exchange-alt text-4xl mb-2 text-gray-300"></i>
                    <p class="text-lg">No transactions found</p>
                    <p class="text-sm mt-2">Your transaction history will appear here</p>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($transactions->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $transactions->appends(request()->except('page'))->links() }}
            </div>
        @endif
    </div>
    
    <!-- Export Button -->
    @if($transactions->isNotEmpty())
        <div class="mt-4 text-right">
            <a href="#" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-file-export mr-2"></i> Export to Excel
            </a>
        </div>
    @endif
</div>
@endsection
