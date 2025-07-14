@extends('layouts.app')

@section('title', 'Payouts - ' . $facility->facility_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Payouts</h1>
        <div class="flex space-x-2">
            <a href="{{ route('facility.wallet.show') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Wallet
            </a>
            <button type="button" 
                    @click="showPayoutModal = true"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center">
                <i class="fas fa-plus mr-2"></i> Request Payout
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Available for Payout</p>
                    <h3 class="text-3xl font-bold text-gray-800">KES {{ number_format($wallet->balance, 2) }}</h3>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-wallet text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending Payouts</p>
                    <h3 class="text-3xl font-bold text-yellow-600">
                        KES {{ number_format($payouts->where('status', 'pending')->sum('amount'), 2) }}
                    </h3>
                </div>
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Paid Out</p>
                    <h3 class="text-3xl font-bold text-blue-600">
                        KES {{ number_format($totalPaidOut, 2) }}
                    </h3>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Payouts Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <h2 class="text-lg font-medium text-gray-800">Payout History</h2>
                <div class="mt-2 md:mt-0">
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               x-model="searchQuery" 
                               @input.debounce.300ms="filterPayouts()"
                               class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md" 
                               placeholder="Search payouts...">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Payout ID
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date Requested
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Payment Method
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payouts as $payout)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $payout->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payout->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                KES {{ number_format($payout->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex items-center">
                                    @if($payout->payment_method === 'mpesa')
                                        <i class="fas fa-mobile-alt mr-2 text-green-600"></i>
                                        M-Pesa
                                    @elseif($payout->payment_method === 'bank_transfer')
                                        <i class="fas fa-university mr-2 text-blue-600"></i>
                                        Bank Transfer
                                    @else
                                        <i class="fas fa-money-bill-wave mr-2 text-gray-600"></i>
                                        {{ ucfirst($payout->payment_method) }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'processing' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                    ][$payout->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                    {{ ucfirst($payout->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('facility.wallet.payouts.show', $payout) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if($payout->status === 'failed' || $payout->status === 'rejected')
                                    <button class="text-yellow-600 hover:text-yellow-900" @click="retryPayout({{ $payout->id }})">
                                        <i class="fas fa-redo"></i> Retry
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                <p class="text-lg">No payout history</p>
                                <p class="text-sm mt-2">Your payout requests will appear here</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($payouts->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $payouts->appends(request()->except('page'))->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Payout Request Modal -->
<div x-data="{
    showPayoutModal: false,
    amount: {{ min(1000, $wallet->balance) }},
    maxAmount: {{ $wallet->balance }},
    paymentMethod: 'mpesa',
    accountDetails: '',
    isLoading: false,
    error: null,
    
    init() {
        // Set default account details based on user's saved payment methods
        this.$watch('paymentMethod', value => {
            if (value === 'mpesa') {
                this.accountDetails = '{{ auth()->user()->phone_number ?? '' }}';
            } else {
                this.accountDetails = '';
            }
        });
    },
    
    async submitPayoutRequest() {
        if (this.amount <= 0 || this.amount > this.maxAmount) {
            this.error = 'Please enter a valid amount';
            return;
        }
        
        if (!this.accountDetails.trim()) {
            this.error = 'Please enter your account details';
            return;
        }
        
        this.isLoading = true;
        this.error = null;
        
        try {
            const response = await fetch('{{ route("facility.wallet.payouts.request") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    amount: this.amount,
                    payment_method: this.paymentMethod,
                    account_details: this.accountDetails
                })
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to process payout request');
            }
            
            // Show success message and reload the page
            window.location.reload();
            
        } catch (error) {
            this.error = error.message || 'An error occurred while processing your request';
            console.error('Payout request error:', error);
        } finally {
            this.isLoading = false;
        }
    }
}" 
x-show="showPayoutModal" 
class="fixed z-10 inset-0 overflow-y-auto" 
aria-labelledby="modal-title" 
role="dialog" 
aria-modal="true"
x-cloak>
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showPayoutModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <i class="fas fa-money-bill-wave text-green-600"></i>
                </div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Request Payout
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Enter the amount you'd like to withdraw from your wallet balance.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="mt-5 sm:mt-6 space-y-4">
                <!-- Amount Input -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount (KES)</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">KES</span>
                        </div>
                        <input 
                            type="number" 
                            id="amount" 
                            x-model="amount"
                            :max="maxAmount"
                            min="100" 
                            step="1"
                            class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-16 pr-12 sm:text-sm border-gray-300 rounded-md" 
                            placeholder="0.00">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm" id="price-currency">
                                / <span x-text="'KES ' + maxAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span> available
                            </span>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Minimum withdrawal amount: KES 100.00
                    </p>
                </div>
                
                <!-- Payment Method -->
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                    <select 
                        id="payment_method" 
                        x-model="paymentMethod"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="mpesa">M-Pesa</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>
                
                <!-- Account Details -->
                <div x-show="paymentMethod === 'mpes'">
                    <label for="account_details" class="block text-sm font-medium text-gray-700">M-Pesa Phone Number</label>
                    <div class="mt-1">
                        <input 
                            type="tel" 
                            id="account_details" 
                            x-model="accountDetails"
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                            placeholder="e.g. 2547XXXXXXXX">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Enter the M-Pesa phone number to receive the funds (format: 2547XXXXXXXX)
                    </p>
                </div>
                
                <div x-show="paymentMethod === 'bank_transfer'">
                    <label for="bank_details" class="block text-sm font-medium text-gray-700">Bank Account Details</label>
                    <div class="mt-1">
                        <textarea 
                            id="bank_details" 
                            x-model="accountDetails"
                            rows="3" 
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                            placeholder="Bank Name, Account Name, Account Number, Branch"></textarea>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Please provide your full bank account details for the transfer
                    </p>
                </div>
                
                <!-- Error Message -->
                <div x-show="error" class="text-red-600 text-sm">
                    <i class="fas fa-exclamation-circle mr-1"></i> <span x-text="error"></span>
                </div>
            </div>
            
            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                <button 
                    type="button" 
                    @click="submitPayoutRequest()"
                    :disabled="isLoading"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm"
                    :class="{'opacity-75 cursor-not-allowed': isLoading}">
                    <span x-show="!isLoading">Request Payout</span>
                    <span x-show="isLoading">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Processing...
                    </span>
                </button>
                <button 
                    type="button" 
                    @click="showPayoutModal = false"
                    :disabled="isLoading"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function filterPayouts() {
        // Implement client-side filtering if needed
        // Or trigger a server-side filter via AJAX
    }
    
    function retryPayout(payoutId) {
        if (confirm('Are you sure you want to retry this payout request?')) {
            // Implement retry logic via AJAX
            fetch(`/facility/wallet/payouts/${payoutId}/retry`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to retry payout');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your request');
            });
        }
    }
</script>
@endpush

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
