@extends('layouts.app')

@section('title', 'Request Payout - ' . $facility->facility_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Request Payout</h1>
        <div class="flex space-x-2">
            <a href="{{ route('facility.wallet.show') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Wallet
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Payout Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h2 class="text-lg font-medium text-gray-800">Payout Request Details</h2>
                </div>
                <div class="p-6">
                    <form id="payoutForm" action="{{ route('facility.wallet.payouts.process') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <!-- Available Balance -->
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h.01a1 1 0 100-2H10V9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Available for payout: <span class="font-semibold">KES {{ number_format($wallet->balance, 2) }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                                Amount (KES)
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">KES</span>
                                </div>
                                <input 
                                    type="number" 
                                    name="amount" 
                                    id="amount" 
                                    min="1000" 
                                    step="1" 
                                    value="{{ min(1000, $wallet->balance) }}"
                                    max="{{ $wallet->balance }}"
                                    required
                                    class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-16 pr-12 sm:text-sm border-gray-300 rounded-md" 
                                    placeholder="0.00">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm" id="max-amount">
                                        / {{ number_format($wallet->balance, 2) }} max
                                    </span>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Minimum payout amount: KES 1,000.00
                            </p>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Method
                            </label>
                            <div class="space-y-3">
                                <!-- M-Pesa Option -->
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input 
                                            id="mpesa" 
                                            name="payment_method" 
                                            type="radio" 
                                            value="mpesa" 
                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                            {{ old('payment_method', 'mpesa') === 'mpesa' ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="mpesa" class="font-medium text-gray-700 flex items-center">
                                            <img src="{{ asset('images/mpesa-logo.png') }}" alt="M-Pesa" class="h-6 mr-2">
                                            M-Pesa
                                        </label>
                                        <p class="text-gray-500">Receive funds directly to your M-Pesa number</p>
                                    </div>
                                </div>

                                <!-- Bank Transfer Option -->
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input 
                                            id="bank_transfer" 
                                            name="payment_method" 
                                            type="radio" 
                                            value="bank_transfer" 
                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                            {{ old('payment_method') === 'bank_transfer' ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="bank_transfer" class="font-medium text-gray-700 flex items-center">
                                            <i class="fas fa-university text-gray-700 text-xl mr-2"></i>
                                            Bank Transfer
                                        </label>
                                        <p class="text-gray-500">Transfer funds to your bank account (2-3 business days)</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Details (Conditional) -->
                        <div id="mpesaDetails" class="hidden">
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">
                                    M-Pesa Phone Number
                                </label>
                                <div class="mt-1">
                                    <input 
                                        type="tel" 
                                        name="phone_number" 
                                        id="phone_number" 
                                        value="{{ old('phone_number', auth()->user()->phone) }}"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                        placeholder="e.g. 2547XXXXXXXX">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Enter the M-Pesa phone number to receive funds (format: 2547XXXXXXXX)
                                </p>
                            </div>
                            
                            <div class="mt-4">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input 
                                            id="save_mpesa" 
                                            name="save_payment_method" 
                                            type="checkbox" 
                                            value="1"
                                            {{ old('save_payment_method') ? 'checked' : '' }}
                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="save_mpesa" class="font-medium text-gray-700">Save this M-Pesa number for future payouts</label>
                                        <p class="text-gray-500">Your payment details are securely encrypted</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="mpesaNickname" class="mt-4 hidden">
                                <label for="payment_method_nickname" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nickname (optional)
                                </label>
                                <input 
                                    type="text" 
                                    name="payment_method_nickname" 
                                    id="payment_method_nickname" 
                                    maxlength="50"
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                    placeholder="e.g. My M-Pesa">
                            </div>
                        </div>
                        
                        <div id="bankDetails" class="hidden">
                            <div class="space-y-4">
                                <div>
                                    <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Bank Name
                                    </label>
                                    <input 
                                        type="text" 
                                        name="bank_name" 
                                        id="bank_name" 
                                        value="{{ old('bank_name') }}"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                        placeholder="e.g. Equity Bank">
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="account_name" class="block text-sm font-medium text-gray-700 mb-1">
                                            Account Name
                                        </label>
                                        <input 
                                            type="text" 
                                            name="account_name" 
                                            id="account_name" 
                                            value="{{ old('account_name', $facility->facility_name) }}"
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                            placeholder="Account holder's name">
                                    </div>
                                    
                                    <div>
                                        <label for="account_number" class="block text-sm font-medium text-gray-700 mb-1">
                                            Account Number
                                        </label>
                                        <input 
                                            type="text" 
                                            name="account_number" 
                                            id="account_number" 
                                            value="{{ old('account_number') }}"
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                            placeholder="e.g. 1234567890">
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="swift_code" class="block text-sm font-medium text-gray-700 mb-1">
                                            SWIFT/BIC Code (if international)
                                        </label>
                                        <input 
                                            type="text" 
                                            name="swift_code" 
                                            id="swift_code" 
                                            value="{{ old('swift_code') }}"
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                            placeholder="e.g. EQBLKENA">
                                    </div>
                                    
                                    <div>
                                        <label for="branch_code" class="block text-sm font-medium text-gray-700 mb-1">
                                            Branch Code (if applicable)
                                        </label>
                                        <input 
                                            type="text" 
                                            name="branch_code" 
                                            id="branch_code" 
                                            value="{{ old('branch_code') }}"
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                            placeholder="e.g. 068">
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input 
                                                id="save_bank" 
                                                name="save_payment_method" 
                                                type="checkbox" 
                                                value="1"
                                                {{ old('save_payment_method') ? 'checked' : '' }}
                                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="save_bank" class="font-medium text-gray-700">Save these bank details for future payouts</label>
                                            <p class="text-gray-500">Your payment details are securely encrypted</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="bankNickname" class="hidden">
                                    <label for="payment_method_nickname_bank" class="block text-sm font-medium text-gray-700 mb-1">
                                        Nickname (optional)
                                    </label>
                                    <input 
                                        type="text" 
                                        name="payment_method_nickname" 
                                        id="payment_method_nickname_bank" 
                                        maxlength="50"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                        placeholder="e.g. Equity Main Account">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Terms and Conditions -->
                        <div class="mt-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input 
                                        id="terms" 
                                        name="terms" 
                                        type="checkbox" 
                                        required
                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="terms" class="font-medium text-gray-700">
                                        I agree to the <a href="#" class="text-blue-600 hover:text-blue-500">Terms of Service</a> and 
                                        <a href="#" class="text-blue-600 hover:text-blue-500">Payout Policy</a>
                                    </label>
                                    <p class="text-gray-500">
                                        By requesting a payout, you agree to our terms and conditions.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button 
                                type="submit" 
                                id="submitButton"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                :disabled="isSubmitting">
                                <span id="buttonText">Request Payout</span>
                                <span id="buttonLoading" class="hidden ml-2">
                                    <i class="fas fa-spinner fa-spin"></i> Processing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h2 class="text-lg font-medium text-gray-800">Payout Summary</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Available Balance</span>
                            <span class="font-medium">KES {{ number_format($wallet->balance, 2) }}</span>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between font-medium text-gray-900">
                                <span>Payout Amount</span>
                                <span id="summaryAmount">KES 0.00</span>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between font-medium text-lg">
                                <span>New Balance</span>
                                <span id="newBalance" class="text-blue-600">KES {{ number_format($wallet->balance, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Processing Time</h3>
                        <ul class="text-sm text-gray-500 space-y-2">
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>M-Pesa: Within 24 hours</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Bank Transfer: 2-3 business days</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Need Help?</h3>
                        <p class="text-sm text-gray-500">
                            If you have any questions about payouts, please contact our support team.
                        </p>
                        <div class="mt-2">
                            <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                                Contact Support <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('payoutForm');
        const amountInput = document.getElementById('amount');
        const paymentMethodInputs = document.querySelectorAll('input[name="payment_method"]');
        const mpesaDetails = document.getElementById('mpesaDetails');
        const bankDetails = document.getElementById('bankDetails');
        const mpesaNickname = document.getElementById('mpesaNickname');
        const bankNickname = document.getElementById('bankNickname');
        const saveMpesaCheckbox = document.getElementById('save_mpesa');
        const saveBankCheckbox = document.getElementById('save_bank');
        const summaryAmount = document.getElementById('summaryAmount');
        const newBalance = document.getElementById('newBalance');
        const maxAmount = parseFloat('{{ $wallet->balance }}');
        
        // Format number with commas
        function formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        // Update summary
        function updateSummary() {
            const amount = parseFloat(amountInput.value) || 0;
            const newBal = maxAmount - amount;
            
            summaryAmount.textContent = `KES ${formatNumber(amount.toFixed(2))}`;
            newBalance.textContent = `KES ${formatNumber(newBal.toFixed(2))}`;
            
            // Disable/enable submit button based on amount
            const submitButton = document.getElementById('submitButton');
            if (amount < 1000 || amount > maxAmount) {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
        
        // Toggle payment method details
        function togglePaymentMethodDetails() {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
            
            // Hide all details first
            mpesaDetails.classList.add('hidden');
            bankDetails.classList.add('hidden');
            
            // Show selected method details
            if (selectedMethod === 'mpesa') {
                mpesaDetails.classList.remove('hidden');
                // Set account details field name for form submission
                document.getElementById('phone_number').setAttribute('name', 'account_details');
            } else if (selectedMethod === 'bank_transfer') {
                bankDetails.classList.remove('hidden');
                // Set account details as JSON string for form submission
                document.getElementById('bank_name').addEventListener('input', updateBankDetails);
                document.getElementById('account_name').addEventListener('input', updateBankDetails);
                document.getElementById('account_number').addEventListener('input', updateBankDetails);
                document.getElementById('swift_code').addEventListener('input', updateBankDetails);
                document.getElementById('branch_code').addEventListener('input', updateBankDetails);
                updateBankDetails();
            }
        }
        
        // Update bank details as JSON string for form submission
        function updateBankDetails() {
            const bankData = {
                bank_name: document.getElementById('bank_name').value,
                account_name: document.getElementById('account_name').value,
                account_number: document.getElementById('account_number').value,
                swift_code: document.getElementById('swift_code').value,
                branch_code: document.getElementById('branch_code').value
            };
            
            // Create a hidden input for account_details if it doesn't exist
            let accountDetailsInput = document.getElementById('account_details_input');
            if (!accountDetailsInput) {
                accountDetailsInput = document.createElement('input');
                accountDetailsInput.type = 'hidden';
                accountDetailsInput.name = 'account_details';
                accountDetailsInput.id = 'account_details_input';
                form.appendChild(accountDetailsInput);
            }
            
            // Set the value as JSON string
            accountDetailsInput.value = JSON.stringify(bankData);
        }
        
        // Toggle nickname field based on save checkbox
        function toggleNicknameField(checkbox, nicknameField) {
            if (checkbox.checked) {
                nicknameField.classList.remove('hidden');
            } else {
                nicknameField.classList.add('hidden');
            }
        }
        
        // Event listeners
        amountInput.addEventListener('input', updateSummary);
        
        paymentMethodInputs.forEach(input => {
            input.addEventListener('change', togglePaymentMethodDetails);
        });
        
        if (saveMpesaCheckbox) {
            saveMpesaCheckbox.addEventListener('change', function() {
                toggleNicknameField(this, mpesaNickname);
            });
        }
        
        if (saveBankCheckbox) {
            saveBankCheckbox.addEventListener('change', function() {
                toggleNicknameField(this, bankNickname);
            });
        }
        
        // Form submission
        form.addEventListener('submit', function(e) {
            const amount = parseFloat(amountInput.value) || 0;
            
            // Validate amount
            if (amount < 1000) {
                e.preventDefault();
                alert('Minimum payout amount is KES 1,000.00');
                return false;
            }
            
            if (amount > maxAmount) {
                e.preventDefault();
                alert('Amount cannot exceed available balance');
                return false;
            }
            
            // Validate payment method details
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
            
            if (selectedMethod === 'mpesa') {
                const phoneNumber = document.getElementById('phone_number').value;
                if (!phoneNumber || !/^254\d{9}$/.test(phoneNumber)) {
                    e.preventDefault();
                    alert('Please enter a valid M-Pesa phone number in the format 2547XXXXXXXX');
                    return false;
                }
            } else if (selectedMethod === 'bank_transfer') {
                const bankName = document.getElementById('bank_name').value;
                const accountName = document.getElementById('account_name').value;
                const accountNumber = document.getElementById('account_number').value;
                
                if (!bankName || !accountName || !accountNumber) {
                    e.preventDefault();
                    alert('Please fill in all required bank details');
                    return false;
                }
            }
            
            // Show loading state
            const buttonText = document.getElementById('buttonText');
            const buttonLoading = document.getElementById('buttonLoading');
            buttonText.classList.add('hidden');
            buttonLoading.classList.remove('hidden');
        });
        
        // Initialize
        updateSummary();
        togglePaymentMethodDetails();
        
        // If there's a previously selected payment method, show it
        @if(old('payment_method') === 'mpesa')
            document.getElementById('mpesa').checked = true;
            mpesaDetails.classList.remove('hidden');
            bankDetails.classList.add('hidden');
            
            @if(old('save_payment_method'))
                saveMpesaCheckbox.checked = true;
                mpesaNickname.classList.remove('hidden');
                document.getElementById('payment_method_nickname').value = '{{ old("payment_method_nickname") }}';
            @endif
        @elseif(old('payment_method') === 'bank_transfer')
            document.getElementById('bank_transfer').checked = true;
            bankDetails.classList.remove('hidden');
            mpesaDetails.classList.add('hidden');
            
            @if(old('save_payment_method'))
                saveBankCheckbox.checked = true;
                bankNickname.classList.remove('hidden');
                document.getElementById('payment_method_nickname_bank').value = '{{ old("payment_method_nickname") }}';
            @endif
            
            // Parse and fill bank details if they exist
            @if(old('account_details'))
                try {
                    const bankData = JSON.parse('{{ old("account_details") }}'.replace(/&quot;/g, '"'));
                    document.getElementById('bank_name').value = bankData.bank_name || '';
                    document.getElementById('account_name').value = bankData.account_name || '';
                    document.getElementById('account_number').value = bankData.account_number || '';
                    document.getElementById('swift_code').value = bankData.swift_code || '';
                    document.getElementById('branch_code').value = bankData.branch_code || '';
                } catch (e) {
                    console.error('Error parsing bank details', e);
                }
            @endif
        @endif
    });
</script>
@endpush

@push('styles')
<style>
    /* Custom radio button styling */
    [type='radio']:checked {
        background-image: none;
    }
    
    [type='radio']:checked:after {
        content: '';
        display: block;
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        background-color: currentColor;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    
    /* Custom checkbox styling */
    [type='checkbox'] {
        border-radius: 0.25rem;
    }
    
    [type='checkbox']:checked {
        background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
        background-size: 100% 100%;
        background-position: center;
        background-repeat: no-repeat;
    }
    
    /* Form field focus styles */
    .focus\:ring-blue-500:focus {
        --tw-ring-color: rgba(59, 130, 246, 0.5);
    }
    
    .focus\:border-blue-500:focus {
        border-color: rgba(59, 130, 246, 0.5);
    }
    
    /* Animation for loading state */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .fa-spinner {
        animation: spin 1s linear infinite;
    }
    
    /* Disabled button state */
    button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endpush
@endsection
