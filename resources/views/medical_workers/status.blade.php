@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-blue-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">Application Status</h2>
        </div>
        
        <div class="px-6 py-6">
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900">Hello, {{ $medicalWorker->name }}</h3>
                <p class="mt-2 text-sm text-gray-600">
                    Application submitted on {{ $medicalWorker->created_at->format('F j, Y') }}
                </p>
            </div>
            
            <div class="border-t border-gray-200 py-6">
                <h4 class="text-base font-medium text-gray-900 mb-4">Current Status</h4>
                
                <div class="flex items-center">
                    @if($medicalWorker->status == 'pending')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Pending Review
                        </span>
                    @elseif($medicalWorker->status == 'approved')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Approved
                        </span>
                    @elseif($medicalWorker->status == 'rejected')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Not Approved
                        </span>
                    @elseif($medicalWorker->status == 'suspended')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Suspended
                        </span>
                    @endif
                    
                    <span class="ml-2 text-sm text-gray-500">
                        Last updated: {{ $medicalWorker->last_status_change ? $medicalWorker->last_status_change->format('F j, Y') : $medicalWorker->updated_at->format('F j, Y') }}
                    </span>
                </div>
                
                @if($medicalWorker->status_reason)
                    <div class="mt-4 bg-gray-50 p-4 rounded-md">
                        <p class="text-sm text-gray-700">
                            <span class="font-medium">Status details:</span> {{ $medicalWorker->status_reason }}
                        </p>
                    </div>
                @endif
            </div>
            
            @if($medicalWorker->status == 'pending')
                <div class="border-t border-gray-200 py-6">
                    <h4 class="text-base font-medium text-gray-900 mb-4">What to Expect</h4>
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600">Your application is currently under review by our team. The verification process typically includes:</p>
                        <ol class="list-decimal pl-5 text-sm text-gray-600 space-y-2">
                            <li>Document verification - We review all submitted documents for authenticity</li>
                            <li>Credential verification - We verify your professional license and qualifications</li>
                            <li>Background check - A standard procedure for all healthcare professionals</li>
                        </ol>
                        <p class="text-sm text-gray-600">This process typically takes 1-3 business days to complete.</p>
                    </div>
                </div>
            @endif
            
            @if($medicalWorker->status == 'approved')
                <div class="border-t border-gray-200 py-6">
                    <h4 class="text-base font-medium text-gray-900 mb-4">Next Steps</h4>
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600">Congratulations! Your application has been approved. You should have received an email with your login credentials and instructions to download the MediConnect medical worker app.</p>
                        <p class="text-sm text-gray-600">If you haven't received this email, please check your spam folder or contact our support team.</p>
                        <div class="mt-4">
                            <a href="{{ config('app.medical_worker_app_download_url', 'https://mediconnect.com/download/medical-worker-app') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Download MediConnect App
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="border-t border-gray-200 py-6">
                <h4 class="text-base font-medium text-gray-900 mb-4">Need Help?</h4>
                <p class="text-sm text-gray-600">If you have any questions about your application or need assistance, please contact our support team at <a href="mailto:support@mediconnect.com" class="text-blue-600 hover:text-blue-500">support@mediconnect.com</a> or call us at (123) 456-7890.</p>
            </div>
        </div>
    </div>
</div>
@endsection
