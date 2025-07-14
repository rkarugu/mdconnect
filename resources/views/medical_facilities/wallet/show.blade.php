@extends('layouts.app')

@section('title', 'My Wallet - ' . $facility->facility_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">My Wallet</h1>
        <div class="flex space-x-2">
            <a href="{{ route('facility.dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
            <a href="{{ route('facility.wallet.payouts') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <i class="fas fa-money-bill-wave mr-2"></i> View Payouts
            </a>
        </div>
    </div>

    <!-- Wallet Balance Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Available Balance</p>
                    <h3 class="text-3xl font-bold text-gray-800">KES {{ number_format($wallet->balance, 2) }}</h3>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-wallet text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('facility.wallet.top-up.form') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    <i class="fas fa-plus-circle mr-1"></i> Add Funds
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending Payouts</p>
                    <h3 class="text-3xl font-bold text-gray-800">KES {{ number_format($pendingPayouts->sum('amount'), 2) }}</h3>
                </div>
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('facility.wallet.payouts') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    <i class="fas fa-eye mr-1"></i> View All
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Earnings</p>
                    <h3 class="text-3xl font-bold text-gray-800">KES {{ number_format($wallet->total_earnings ?? 0, 2) }}</h3>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('facility.wallet.transactions') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    <i class="fas fa-list-ul mr-1"></i> View Transactions
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-800">Recent Transactions</h2>
                <a href="{{ route('facility.wallet.transactions') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($transactions as $transaction)
                <div class="p-6 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full {{ $transaction->amount >= 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                            @if($transaction->type === 'payout')
                                <i class="fas fa-money-bill-wave"></i>
                            @elseif($transaction->type === 'topup')
                                <i class="fas fa-plus-circle"></i>
                            @else
                                <i class="fas fa-exchange-alt"></i>
                            @endif
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">{{ $transaction->description }}</h3>
                            <p class="text-sm text-gray-500">{{ $transaction->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium {{ $transaction->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->amount >= 0 ? '+' : '' }}{{ number_format($transaction->amount, 2) }} KES
                        </p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500">
                    <i class="fas fa-wallet text-4xl mb-2 text-gray-300"></i>
                    <p>No transactions yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pending Payouts -->
    @if($pendingPayouts->isNotEmpty())
        <div class="mt-8 bg-white rounded-lg shadow overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-800">Pending Payouts</h2>
                    <a href="{{ route('facility.wallet.payouts') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($pendingPayouts as $payout)
                    <div class="p-6 flex items-center justify-between hover:bg-gray-50">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900">Payout Request #{{ $payout->id }}</h3>
                                <p class="text-sm text-gray-500">Requested on {{ $payout->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">-{{ number_format($payout->amount, 2) }} KES</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ ucfirst($payout->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
