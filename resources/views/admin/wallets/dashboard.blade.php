@extends('layouts.app')

@section('title', 'Wallet Dashboard')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Wallet Overview</h1>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <!-- Facility Balance Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-university text-blue-500 text-3xl"></i>
                    </div>
                    <div class="ml-5">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Facility Balance</dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ number_format($facilities_balance, 2) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Worker Balance Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-nurse text-green-500 text-3xl"></i>
                    </div>
                    <div class="ml-5">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Worker Balance</dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ number_format($workers_balance, 2) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Payouts Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-yellow-500 text-3xl"></i>
                    </div>
                    <div class="ml-5">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Payouts</dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ $pending_payouts }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('admin.wallets.payouts') }}" 
                       class="font-medium text-blue-600 hover:text-blue-500">
                        View Payouts
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
