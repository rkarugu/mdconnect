package com.mediconnect.app.api.services

import com.mediconnect.app.api.ApiConfig
import com.mediconnect.app.api.models.ApiResponse
import com.mediconnect.app.api.models.MedicalWorker
import com.mediconnect.app.api.models.MedicalWorkerAuthResponse
import com.mediconnect.app.api.models.MedicalWorkerLoginRequest
import com.mediconnect.app.api.models.MedicalWorkerRegisterRequest
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.PUT

/**
 * Retrofit service interface for medical worker authentication-related API endpoints
 */
interface MedicalWorkerAuthService {
    
    @POST(ApiConfig.Endpoints.MEDICAL_WORKER_LOGIN)
    suspend fun login(@Body loginRequest: MedicalWorkerLoginRequest): ApiResponse<MedicalWorkerAuthResponse>
    
    @POST(ApiConfig.Endpoints.MEDICAL_WORKER_REGISTER)
    suspend fun register(@Body registerRequest: MedicalWorkerRegisterRequest): ApiResponse<MedicalWorkerAuthResponse>
    
    @POST(ApiConfig.Endpoints.MEDICAL_WORKER_LOGOUT)
    suspend fun logout(): ApiResponse<Any>
    
    @GET(ApiConfig.Endpoints.MEDICAL_WORKER_PROFILE)
    suspend fun getProfile(): ApiResponse<MedicalWorker>
    
    @PUT(ApiConfig.Endpoints.MEDICAL_WORKER_PROFILE)
    suspend fun updateProfile(@Body medicalWorker: MedicalWorker): ApiResponse<MedicalWorker>
    
    @POST(ApiConfig.Endpoints.MEDICAL_WORKER_CHANGE_PASSWORD)
    suspend fun changePassword(@Body passwordData: Map<String, String>): ApiResponse<Any>
}
