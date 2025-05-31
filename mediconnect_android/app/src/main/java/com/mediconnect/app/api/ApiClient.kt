package com.mediconnect.app.api

import android.content.Context
import com.mediconnect.app.api.services.AuthService
import com.mediconnect.app.api.services.JobsService
import com.mediconnect.app.api.services.MedicalWorkerAuthService
import com.mediconnect.app.util.MedicalWorkerSessionManager
import com.mediconnect.app.util.SessionManager
import okhttp3.Interceptor
import okhttp3.OkHttpClient
import okhttp3.Request
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit

/**
 * Singleton class for managing API client instance and services
 */
class ApiClient private constructor(context: Context) {
    
    private val sessionManager = SessionManager(context)
    private val medicalWorkerSessionManager = MedicalWorkerSessionManager(context)
    private val retrofit: Retrofit
    
    // Services
    val authService: AuthService
    val jobsService: JobsService
    val medicalWorkerAuthService: MedicalWorkerAuthService
    
    init {
        // Create HTTP client with interceptors
        val client = createHttpClient()
        
        // Create Retrofit instance
        retrofit = Retrofit.Builder()
            .baseUrl(ApiConfig.BASE_URL)
            .client(client)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
        
        // Initialize services
        authService = retrofit.create(AuthService::class.java)
        jobsService = retrofit.create(JobsService::class.java)
        medicalWorkerAuthService = retrofit.create(MedicalWorkerAuthService::class.java)
    }
    
    private fun createHttpClient(): OkHttpClient {
        // Create logging interceptor
        val loggingInterceptor = HttpLoggingInterceptor().apply {
            level = HttpLoggingInterceptor.Level.BODY
        }
        
        // Create auth interceptor to add token to requests
        val authInterceptor = Interceptor { chain ->
            // Determine which token to use based on the request URL
            val request = chain.request()
            val url = request.url.toString()
            
            // For medical worker endpoints, use medical worker token
            val token = if (url.contains("/medical-worker/")) {
                medicalWorkerSessionManager.getAuthToken()
            } else {
                sessionManager.getAuthToken()
            }
            
            val authenticatedRequest: Request = if (token != null) {
                request.newBuilder()
                    .addHeader(ApiConfig.AUTHORIZATION_HEADER, ApiConfig.BEARER_PREFIX + token)
                    .build()
            } else {
                request
            }
            chain.proceed(authenticatedRequest)
        }
        
        // Build the OkHttpClient
        return OkHttpClient.Builder()
            .addInterceptor(authInterceptor)
            .addInterceptor(loggingInterceptor)
            .connectTimeout(ApiConfig.CONNECT_TIMEOUT, TimeUnit.SECONDS)
            .readTimeout(ApiConfig.READ_TIMEOUT, TimeUnit.SECONDS)
            .writeTimeout(ApiConfig.WRITE_TIMEOUT, TimeUnit.SECONDS)
            .build()
    }
    
    companion object {
        @Volatile
        private var instance: ApiClient? = null
        
        fun getInstance(context: Context): ApiClient {
            return instance ?: synchronized(this) {
                instance ?: ApiClient(context).also { instance = it }
            }
        }
    }
}
