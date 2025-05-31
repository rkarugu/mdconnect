<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalWorker;
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

        // Check if medical worker exists
        $medicalWorker = MedicalWorker::where('email', $request->email)->first();

        if (!$medicalWorker || !Hash::check($request->password, $medicalWorker->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if the medical worker is approved
        if ($medicalWorker->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is not approved yet. Status: ' . $medicalWorker->status,
            ], 403);
        }

        // Delete previous tokens
        $medicalWorker->tokens()->delete();

        // Create new token
        $token = $medicalWorker->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Medical worker logged in successfully',
            'data' => [
                'medical_worker' => $medicalWorker,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
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
