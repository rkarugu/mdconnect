<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalWorker;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MedicalWorkerAuthController extends Controller
{
    /**
     * Register a new medical worker.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:medical_workers',
            'password' => 'required|string|min:8|confirmed',
            'license_number' => 'required|string|unique:medical_workers',
            'specialty_id' => 'required|exists:medical_specialties,id',
            'years_of_experience' => 'required|string',
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $medicalWorker = MedicalWorker::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'license_number' => $request->license_number,
            'specialty_id' => $request->specialty_id,
            'years_of_experience' => $request->years_of_experience,
            'phone' => $request->phone,
            'bio' => $request->bio,
            'education' => $request->education,
            'certifications' => $request->certifications,
            'status' => 'pending', // New medical workers are pending by default
        ]);

        // Create token for the new medical worker
        $token = $medicalWorker->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Medical worker registered successfully',
            'data' => [
                'medical_worker' => $medicalWorker,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    /**
     * Login a medical worker.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        Log::debug('MEDICAL_WORKER_LOGIN_ATTEMPT', ['request_data' => $request->all()]);
        Log::debug('Validator created');
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        Log::debug('Attempting to authenticate medical worker with guard.', ['credentials' => $credentials]);

        if (!auth()->guard('medical-worker')->attempt($credentials)) {
            Log::warning('MEDICAL_WORKER_LOGIN_FAILED', ['email' => $request->email, 'reason' => 'Invalid credentials from Auth facade']);
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $request->session()->regenerate();
        $medicalWorker = auth()->guard('medical-worker')->user();

        // Check if the medical worker is approved
        Log::debug('Checking approval status for worker: ' . $medicalWorker->id);
        if ($medicalWorker->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is not approved yet. Status: ' . $medicalWorker->status,
            ], 403);
        }

        // Delete previous tokens
        Log::debug('Deleting old tokens for worker: ' . $medicalWorker->id);
        $medicalWorker->tokens()->delete();

        // Create new token
        Log::debug('Creating new token for worker: ' . $medicalWorker->id);
        $token = $medicalWorker->createToken('auth_token')->plainTextToken;

        Log::debug('Token created. Checking if password change is required for worker: ' . $medicalWorker->id);
        Log::debug('Medical worker profile picture path: ' . $medicalWorker->profile_picture, ['profile_picture' => $medicalWorker->profile_picture]);
        $passwordChangeRequired = $medicalWorker->password_change_required ?? false;
        $message = $passwordChangeRequired
            ? 'Please change your password'
            : 'Medical worker logged in successfully';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'medical_worker' => $medicalWorker,
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ])->setStatusCode(200);
    }

    /**
     * Get the authenticated medical worker.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'medical_worker' => $request->user()
            ]
        ]);
    }

    /**
     * Logout a medical worker (revoke the token).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Medical worker logged out successfully'
        ]);
    }

    /**
     * Update medical worker profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $medicalWorker = $request->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string',
            'bio' => 'sometimes|string',
            'education' => 'sometimes|string',
            'certifications' => 'sometimes|string',
            'is_available' => 'sometimes|boolean',
            'working_hours' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $medicalWorker->update($request->only([
            'name', 'phone', 'bio', 'education', 'certifications', 'is_available', 'working_hours'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'medical_worker' => $medicalWorker
            ]
        ]);
    }

    /**
     * Change password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $medicalWorker = $request->user();

        if (!Hash::check($request->current_password, $medicalWorker->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 401);
        }

        $medicalWorker->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }
}
