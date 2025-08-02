<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Mail\PatientEmailVerification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PatientAuthController extends Controller
{
    /**
     * Register a new patient
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:patients',
            'phone' => 'required|string|max:20|unique:patients',
            'password' => 'required|string|min:8|confirmed',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'emergency_contact_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $patient = Patient::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'emergency_contact_name' => $request->emergency_contact_name,
                'email_verified_at' => null, // Require email verification
                'is_verified' => true,
            ]);

            // Generate verification token and send email
            $verificationToken = base64_encode($patient->email . '|' . now()->addHours(24)->timestamp);
            
            // Send verification email
            Mail::to($patient->email)->send(new PatientEmailVerification($patient, $verificationToken));
            
            $token = $patient->createToken('patient-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Patient registered successfully. Please check your email to verify your account.',
                'data' => [
                    'user' => [
                        'id' => $patient->id,
                        'first_name' => $patient->first_name,
                        'last_name' => $patient->last_name,
                        'full_name' => $patient->full_name,
                        'email' => $patient->email,
                        'phone' => $patient->phone,
                        'date_of_birth' => $patient->date_of_birth,
                        'gender' => $patient->gender,
                        'emergency_contact_name' => $patient->emergency_contact_name,
                        'profile_picture' => $patient->profile_picture,
                        'is_verified' => $patient->is_verified,
                        'created_at' => $patient->created_at,
                    ],
                    'token' => $token,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login patient
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required_without:phone|email',
            'phone' => 'required_without:email|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Determine login field (email or phone)
        $loginField = $request->has('email') ? 'email' : 'phone';
        $loginValue = $request->has('email') ? $request->email : $request->phone;

        // Find patient by email or phone
        $patient = Patient::where($loginField, $loginValue)
                          ->active()
                          ->first();

        if (!$patient || !Hash::check($request->password, $patient->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Check if email is verified
        if (is_null($patient->email_verified_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your email address before logging in. Check your inbox for the verification link.',
                'error_code' => 'EMAIL_NOT_VERIFIED'
            ], 403);
        }

        // Update last login timestamp
        $patient->updateLastLogin();

        // Revoke existing tokens
        $patient->tokens()->delete();

        // Create new token
        $token = $patient->createToken('patient-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $patient->id,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'full_name' => $patient->full_name,
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                    'date_of_birth' => $patient->date_of_birth,
                    'gender' => $patient->gender,
                    'emergency_contact_name' => $patient->emergency_contact_name,
                    'profile_picture' => $patient->profile_picture,
                    'is_verified' => $patient->is_verified,
                    'last_login_at' => $patient->last_login_at,
                    'created_at' => $patient->created_at,
                ],
                'token' => $token,
            ]
        ]);
    }

    /**
     * Get current patient profile
     */
    public function me(Request $request)
    {
        $patient = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $patient->id,
                'first_name' => $patient->first_name,
                'last_name' => $patient->last_name,
                'full_name' => $patient->full_name,
                'email' => $patient->email,
                'phone' => $patient->phone,
                'date_of_birth' => $patient->date_of_birth,
                'gender' => $patient->gender,
                'age' => $patient->age,
                'blood_type' => $patient->blood_type,
                'allergies' => $patient->allergies,
                'medical_conditions' => $patient->medical_conditions,
                'current_medications' => $patient->current_medications,
                'emergency_contact_name' => $patient->emergency_contact_name,
                'emergency_contact_phone' => $patient->emergency_contact_phone,
                'emergency_contact_relationship' => $patient->emergency_contact_relationship,
                'address' => $patient->address,
                'city' => $patient->city,
                'state' => $patient->state,
                'zip_code' => $patient->zip_code,
                'profile_picture' => $patient->profile_picture,
                'bio' => $patient->bio,
                'preferences' => $patient->preferences,
                'is_verified' => $patient->is_verified,
                'last_login_at' => $patient->last_login_at,
                'created_at' => $patient->created_at,
            ]
        ]);
    }

    /**
     * Update patient profile
     */
    public function updateProfile(Request $request)
    {
        $patient = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20|unique:patients,phone,' . $patient->id,
            'date_of_birth' => 'sometimes|date',
            'gender' => 'sometimes|in:Male,Female,Other,male,female,other',
            'blood_type' => 'sometimes|string|max:10',
            'allergies' => 'sometimes|string',
            'medical_conditions' => 'sometimes|string',
            'current_medications' => 'sometimes|string',
            'emergency_contact_name' => 'sometimes|string|max:255',
            'emergency_contact_phone' => 'sometimes|string|max:20',
            'emergency_contact_relationship' => 'sometimes|string|max:100',
            'address' => 'sometimes|string|max:500',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:100',
            'zip_code' => 'sometimes|string|max:20',
            'bio' => 'sometimes|string|max:1000',
            'preferences' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = $request->only([
                'first_name', 'last_name', 'phone', 'date_of_birth', 
                'gender', 'blood_type', 'allergies', 'medical_conditions', 'current_medications',
                'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',
                'address', 'city', 'state', 'zip_code', 'bio', 'preferences'
            ]);

            $patient->update($updateData);

            // Reload the patient to get fresh data
            $patient->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'id' => $patient->id,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'full_name' => $patient->first_name . ' ' . $patient->last_name,
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                    'date_of_birth' => $patient->date_of_birth,
                    'gender' => $patient->gender,
                    'blood_type' => $patient->blood_type,
                    'emergency_contact_name' => $patient->emergency_contact_name,
                    'emergency_contact_phone' => $patient->emergency_contact_phone,
                    'address' => $patient->address,
                    'city' => $patient->city,
                    'state' => $patient->state,
                    'zip_code' => $patient->zip_code,
                    'profile_picture' => $patient->profile_picture,
                    'is_verified' => !is_null($patient->email_verified_at),
                    'last_login_at' => $patient->last_login_at,
                    'created_at' => $patient->created_at,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profile update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload profile picture
     */
    public function uploadProfilePicture(Request $request)
    {
        try {
            $patient = auth()->user();
            
            if (!$patient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient not authenticated'
                ], 401);
            }

            // Validate the uploaded file
            $validator = Validator::make($request->all(), [
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle file upload
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                
                // Generate unique filename
                $filename = 'profile_' . $patient->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Store the file in public/storage/profile_pictures
                $path = $file->storeAs('profile_pictures', $filename, 'public');
                
                // Delete old profile picture if exists
                if ($patient->profile_picture) {
                    $oldPath = str_replace('/storage/', '', $patient->profile_picture);
                    if (\Storage::disk('public')->exists($oldPath)) {
                        \Storage::disk('public')->delete($oldPath);
                    }
                }
                
                // Update patient record with new profile picture URL
                $fullUrl = url('/storage/' . $path);
                $patient->update([
                    'profile_picture' => $fullUrl
                ]);
                
                // Reload the patient to get fresh data
                $patient->refresh();

                return response()->json([
                    'success' => true,
                    'message' => 'Profile picture uploaded successfully',
                    'data' => [
                        'id' => $patient->id,
                        'first_name' => $patient->first_name,
                        'last_name' => $patient->last_name,
                        'full_name' => $patient->first_name . ' ' . $patient->last_name,
                        'email' => $patient->email,
                        'phone' => $patient->phone,
                        'date_of_birth' => $patient->date_of_birth,
                        'gender' => $patient->gender,
                        'emergency_contact_name' => $patient->emergency_contact_name,
                        'emergency_contact_phone' => $patient->emergency_contact_phone,
                        'address' => $patient->address,
                        'city' => $patient->city,
                        'state' => $patient->state,
                        'zip_code' => $patient->zip_code,
                        'profile_picture' => $patient->profile_picture,
                        'is_verified' => !is_null($patient->email_verified_at),
                        'last_login_at' => $patient->last_login_at,
                        'created_at' => $patient->created_at,
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload profile picture',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        try {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Password change failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout patient
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Forgot password - send reset link
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // TODO: Implement password reset email sending
        // For now, return success message
        return response()->json([
            'success' => true,
            'message' => 'Password reset link sent to your email'
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // TODO: Implement password reset token validation
        // For now, return success message
        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully'
        ]);
    }

    /**
     * Send email verification
     */
    public function sendEmailVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:patients,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $patient = Patient::where('email', $request->email)->first();
            
            if (!$patient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient not found'
                ], 404);
            }

            if ($patient->email_verified_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email is already verified'
                ], 400);
            }

            // Generate verification token
            $verificationToken = base64_encode($patient->email . '|' . now()->addHours(24)->timestamp);
            
            // Send actual verification email
            Mail::to($patient->email)->send(new PatientEmailVerification($patient, $verificationToken));
            
            return response()->json([
                'success' => true,
                'message' => 'Verification email sent successfully. Please check your inbox.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify email address
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Decode verification token
            $decodedToken = base64_decode($request->token);
            $tokenParts = explode('|', $decodedToken);
            
            if (count($tokenParts) !== 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification token'
                ], 400);
            }

            $email = $tokenParts[0];
            $expiryTimestamp = $tokenParts[1];

            // Check if token is expired
            if (now()->timestamp > $expiryTimestamp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verification token has expired'
                ], 400);
            }

            // Find patient by email
            $patient = Patient::where('email', $email)->first();
            
            if (!$patient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient not found'
                ], 404);
            }

            if ($patient->email_verified_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email is already verified'
                ], 400);
            }

            // Mark email as verified
            $patient->update([
                'email_verified_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully! You can now log in to your account.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Email verification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify email address via GET request (for email links)
     */
    public function verifyEmailGet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->view('emails.verification-error', [
                'message' => 'Invalid verification link. Please check your email for a valid verification link.'
            ], 400);
        }

        try {
            // Decode verification token
            $decodedToken = base64_decode($request->token);
            $tokenParts = explode('|', $decodedToken);
            
            if (count($tokenParts) !== 2) {
                return response()->view('emails.verification-error', [
                    'message' => 'Invalid verification token. Please request a new verification email.'
                ], 400);
            }

            $email = $tokenParts[0];
            $expiryTimestamp = $tokenParts[1];

            // Check if token is expired
            if (now()->timestamp > $expiryTimestamp) {
                return response()->view('emails.verification-error', [
                    'message' => 'Verification link has expired. Please request a new verification email.'
                ], 400);
            }

            // Find patient by email
            $patient = Patient::where('email', $email)->first();
            
            if (!$patient) {
                return response()->view('emails.verification-error', [
                    'message' => 'Patient account not found. Please contact support.'
                ], 404);
            }

            if ($patient->email_verified_at) {
                return response()->view('emails.verification-success', [
                    'message' => 'Your email is already verified! You can now log in to your MediConnect account.',
                    'patient' => $patient
                ]);
            }

            // Mark email as verified
            $patient->update([
                'email_verified_at' => now()
            ]);

            return response()->view('emails.verification-success', [
                'message' => 'Email verified successfully! You can now log in to your MediConnect account.',
                'patient' => $patient
            ]);

        } catch (\Exception $e) {
            return response()->view('emails.verification-error', [
                'message' => 'Email verification failed. Please try again or contact support.'
            ], 500);
        }
    }

    /**
     * Resend email verification
     */
    public function resendEmailVerification(Request $request)
    {
        $patient = $request->user();
        
        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Patient not found'
            ], 404);
        }

        if ($patient->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email is already verified'
            ], 400);
        }

        try {
            // Generate new verification token
            $verificationToken = base64_encode($patient->email . '|' . now()->addHours(24)->timestamp);
            
            // Send actual verification email
            Mail::to($patient->email)->send(new PatientEmailVerification($patient, $verificationToken));
            
            return response()->json([
                'success' => true,
                'message' => 'Verification email resent successfully. Please check your inbox.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend verification email',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
