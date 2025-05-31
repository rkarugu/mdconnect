package com.mediconnect.app.api.services

import com.mediconnect.app.api.ApiConfig
import com.mediconnect.app.api.models.ApiResponse
import com.mediconnect.app.api.models.AuthResponse
import com.mediconnect.app.api.models.LoginRequest
import com.mediconnect.app.api.models.RegisterRequest
import com.mediconnect.app.api.models.User
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.POST

/**
 * Retrofit service interface for authentication-related API endpoints
 */
interface AuthService {
    
    @POST(ApiConfig.Endpoints.LOGIN)
    suspend fun login(@Body loginRequest: LoginRequest): ApiResponse<AuthResponse>
    
    @POST(ApiConfig.Endpoints.REGISTER)
    suspend fun register(@Body registerRequest: RegisterRequest): ApiResponse<AuthResponse>
    
    @POST(ApiConfig.Endpoints.LOGOUT)
    suspend fun logout(): ApiResponse<Any>
    
    @GET(ApiConfig.Endpoints.PROFILE)
    suspend fun getProfile(): ApiResponse<User>
}
