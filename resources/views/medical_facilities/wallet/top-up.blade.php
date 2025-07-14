@extends('layouts.app')

@section('title', 'Top Up Wallet - ' . $facility->facility_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Top Up Wallet</h1>
        <div class="flex space-x-2">
            <a href="{{ route('facility.wallet.show') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Wallet
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top Up Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h2 class="text-lg font-medium text-gray-800">Add Funds to Your Wallet</h2>
                </div>
                <div class="p-6">
                    <form id="topUpForm" action="{{ route('facility.wallet.top-up.process') }}" method="POST" class="space-y-6">
                        @csrf
                        
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
                                    min="100" 
                                    step="1" 
                                    value="1000"
                                    required
                                    class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-16 pr-12 sm:text-sm border-gray-300 rounded-md" 
                                    placeholder="0.00">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm" id="price-currency">.00</span>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Minimum top-up amount: KES 100.00
                            </p>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Method
                            </label>
                            <div class="space-y-2">
                                <!-- M-Pesa Option -->
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input 
                                            id="mpesa" 
                                            name="payment_method" 
                                            type="radio" 
                                            value="mpesa" 
                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                            checked>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="mpesa" class="font-medium text-gray-700 flex items-center">
                                            <img src="{{ asset('images/mpesa-logo.png') }}" alt="M-Pesa" class="h-6 mr-2">
                                            M-Pesa
                                        </label>
                                        <p class="text-gray-500">Pay instantly via M-Pesa</p>
                                    </div>
                                </div>

                                <!-- Card Option -->
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input 
                                            id="card" 
                                            name="payment_method" 
                                            type="radio" 
                                            value="card" 
                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="card" class="font-medium text-gray-700 flex items-center">
                                            <i class="fab fa-cc-visa text-blue-600 text-xl mr-2"></i>
                                            <i class="fab fa-cc-mastercard text-red-500 text-xl mr-2"></i>
                                            Credit/Debit Card
                                        </label>
                                        <p class="text-gray-500">Pay with Visa, Mastercard, or other cards</p>
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
                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="bank_transfer" class="font-medium text-gray-700 flex items-center">
                                            <i class="fas fa-university text-gray-700 text-xl mr-2"></i>
                                            Bank Transfer
                                        </label>
                                        <p class="text-gray-500">Make a bank transfer to our account</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- M-Pesa Phone Number (Conditional) -->
                        <div id="mpesaPhoneField" class="hidden">
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">
                                M-Pesa Phone Number
                            </label>
                            <div class="mt-1">
                                <input 
                                    type="tel" 
                                    name="phone_number" 
                                    id="phone_number" 
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                    placeholder="e.g. 2547XXXXXXXX">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Enter the M-Pesa phone number to charge (format: 2547XXXXXXXX)
                            </p>
                        </div>

                        <!-- Card Details (Conditional) -->
                        <div id="cardDetails" class="hidden">
                            <div class="space-y-4">
                                <div>
                                    <label for="card_number" class="block text-sm font-medium text-gray-700 mb-1">
                                        Card Number
                                    </label>
                                    <input 
                                        type="text" 
                                        name="card_number" 
                                        id="card_number" 
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                        placeholder="1234 5678 9012 3456">
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="expiry" class="block text-sm font-medium text-gray-700 mb-1">
                                            Expiry Date
                                        </label>
                                        <input 
                                            type="text" 
                                            name="expiry" 
                                            id="expiry" 
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                            placeholder="MM/YY">
                                    </div>
                                    <div>
                                        <label for="cvv" class="block text-sm font-medium text-gray-700 mb-1">
                                            CVV
                                        </label>
                                        <input 
                                            type="text" 
                                            name="cvv" 
                                            id="cvv" 
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                            placeholder="123">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="card_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Name on Card
                                    </label>
                                    <input 
                                        type="text" 
                                        name="card_name" 
                                        id="card_name" 
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                        placeholder="John Doe">
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transfer Instructions (Conditional) -->
                        <div id="bankTransferInstructions" class="hidden bg-blue-50 p-4 rounded-md">
                            <h3 class="text-sm font-medium text-blue-800">Bank Transfer Instructions</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Please make a transfer to the following account:</p>
                                <div class="mt-2 space-y-1">
                                    <div class="grid grid-cols-3">
                                        <span class="text-gray-600">Bank:</span>
                                        <span class="col-span-2 font-medium">Equity Bank Kenya</span>
                                    </div>
                                    <div class="grid grid-cols-3">
                                        <span class="text-gray-600">Account Name:</span>
                                        <span class="col-span-2 font-medium">MediConnect Ltd</span>
                                    </div>
                                    <div class="grid grid-cols-3">
                                        <span class="text-gray-600">Account Number:</span>
                                        <span class="col-span-2 font-mono">1234567890</span>
                                    </div>
                                    <div class="grid grid-cols-3">
                                        <span class="text-gray-600">Branch:</span>
                                        <span class="col-span-2">Westlands</span>
                                    </div>
                                    <div class="grid grid-cols-3">
                                        <span class="text-gray-600">Swift Code:</span>
                                        <span class="col-span-2 font-mono">EQBLKENA</span>
                                    </div>
                                </div>
                                <p class="mt-2">
                                    <strong>Reference:</strong> FAC-{{ strtoupper(Str::random(8)) }}
                                </p>
                                <div class="mt-3 p-3 bg-white rounded border border-blue-200">
                                    <p class="text-xs text-gray-600">
                                        After making the transfer, please upload the payment receipt below and we'll credit your wallet within 1-2 business days.
                                    </p>
                                    <div class="mt-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Upload Receipt (Optional)
                                        </label>
                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="receipt" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                                        <span>Upload a file</span>
                                                        <input id="receipt" name="receipt" type="file" class="sr-only">
                                                    </label>
                                                    <p class="pl-1">or drag and drop</p>
                                                </div>
                                                <p class="text-xs text-gray-500">
                                                    PNG, JPG, PDF up to 5MB
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button 
                                type="submit" 
                                id="submitButton"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <span id="buttonText">Pay KES <span id="amountDisplay">1,000.00</span></span>
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
                    <h2 class="text-lg font-medium text-gray-800">Order Summary</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Amount to Add</span>
                            <span id="summaryAmount" class="font-medium">KES 1,000.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Processing Fee</span>
                            <span id="processingFee" class="font-medium">KES 0.00</span>
                        </div>
                        <div class="border-t border-gray-200 pt-4 mt-4">
                            <div class="flex justify-between font-medium text-gray-900">
                                <span>Total</span>
                                <span id="totalAmount">KES 1,000.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Payment Methods</h3>
                        <div class="flex space-x-4">
                            <img src="{{ asset('images/mpesa-logo.png') }}" alt="M-Pesa" class="h-8">
                            <i class="fab fa-cc-visa text-blue-600 text-3xl"></i>
                            <i class="fab fa-cc-mastercard text-red-500 text-3xl"></i>
                            <i class="fab fa-cc-paypal text-blue-500 text-3xl"></i>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Need Help?</h3>
                        <p class="text-sm text-gray-500">
                            If you encounter any issues, please contact our support team for assistance.
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
        const amountInput = document.getElementById('amount');
        const amountDisplay = document.getElementById('amountDisplay');
        const summaryAmount = document.getElementById('summaryAmount');
        const processingFee = document.getElementById('processingFee');
        const totalAmount = document.getElementById('totalAmount');
        const paymentMethodInputs = document.querySelectorAll('input[name="payment_method"]');
        const mpesaPhoneField = document.getElementById('mpesaPhoneField');
        const cardDetails = document.getElementById('cardDetails');
        const bankTransferInstructions = document.getElementById('bankTransferInstructions');
        const form = document.getElementById('topUpForm');
        const submitButton = document.getElementById('submitButton');
        const buttonText = document.getElementById('buttonText');
        const buttonLoading = document.getElementById('buttonLoading');

        // Format amount with commas
        function formatAmount(amount) {
            return parseFloat(amount).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Calculate fees and update summary
        function updateSummary() {
            const amount = parseFloat(amountInput.value) || 0;
            let fee = 0;
            
            // Calculate processing fee (example: 2.5% for card payments)
            const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            if (selectedPaymentMethod === 'card') {
                fee = amount * 0.025; // 2.5% fee for card payments
                if (fee < 10) fee = 10; // Minimum fee
            } else if (selectedPaymentMethod === 'mpesa') {
                fee = 0; // No fee for M-Pesa
            } else if (selectedPaymentMethod === 'bank_transfer') {
                fee = 0; // No fee for bank transfer
            }
            
            const total = amount + fee;
            
            // Update displays
            amountDisplay.textContent = formatAmount(amount);
            summaryAmount.textContent = `KES ${formatAmount(amount)}`;
            processingFee.textContent = `KES ${formatAmount(fee)}`;
            totalAmount.textContent = `KES ${formatAmount(total)}`;
            
            // Update button text
            const buttonTextEl = document.querySelector('#buttonText');
            if (buttonTextEl) {
                buttonTextEl.innerHTML = `Pay KES <span id="amountDisplay">${formatAmount(total)}</span>`;
            }
        }

        // Toggle payment method fields
        function togglePaymentMethodFields() {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
            
            // Hide all fields first
            mpesaPhoneField.classList.add('hidden');
            cardDetails.classList.add('hidden');
            bankTransferInstructions.classList.add('hidden');
            
            // Show relevant fields based on selection
            if (selectedMethod === 'mpesa') {
                mpesaPhoneField.classList.remove('hidden');
            } else if (selectedMethod === 'card') {
                cardDetails.classList.remove('hidden');
            } else if (selectedMethod === 'bank_transfer') {
                bankTransferInstructions.classList.remove('hidden');
            }
            
            // Update summary with new fees
            updateSummary();
        }

        // Event listeners
        amountInput.addEventListener('input', updateSummary);
        paymentMethodInputs.forEach(input => {
            input.addEventListener('change', togglePaymentMethodFields);
        });
        
        // Form submission
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Show loading state
                submitButton.disabled = true;
                buttonText.classList.add('hidden');
                buttonLoading.classList.remove('hidden');
                
                // Simulate form submission (replace with actual form submission)
                setTimeout(() => {
                    // In a real app, this would be an AJAX call to your server
                    console.log('Form submitted', {
                        amount: amountInput.value,
                        payment_method: document.querySelector('input[name="payment_method"]:checked').value,
                        // Add other form data as needed
                    });
                    
                    // For demo purposes, show success message after a delay
                    alert('Payment processed successfully! Your wallet will be updated shortly.');
                    window.location.href = '{{ route("facility.wallet.show") }}';
                }, 2000);
            });
        }
        
        // Initialize
        updateSummary();
        togglePaymentMethodFields();
    });
</script>
@endpush

<style>
    /* Custom styles for payment method selection */
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
    
    /* Style for file input */
    .file-upload {
        position: relative;
        overflow: hidden;
        display: inline-block;
    }
    
    .file-upload-input {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    
    /* Animation for loading state */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>
@endsection
