package com.mediconnect.app.api

/**
 * Configuration class for API endpoints and settings
 */
object ApiConfig {
    // Base URL for the API
    const val BASE_URL = "http://192.168.1.100/mediconnect/api/" // Replace with your actual API URL
    
    // API Endpoints
    object Endpoints {
        // Auth endpoints
        const val LOGIN = "auth/login"
        const val REGISTER = "auth/register"
        const val LOGOUT = "auth/logout"
        const val PROFILE = "auth/profile"
        
        // Medical Worker Auth endpoints
        const val MEDICAL_WORKER_LOGIN = "medical-worker/login"
        const val MEDICAL_WORKER_REGISTER = "medical-worker/register"
        const val MEDICAL_WORKER_LOGOUT = "medical-worker/logout"
        const val MEDICAL_WORKER_PROFILE = "medical-worker/me"
        const val MEDICAL_WORKER_CHANGE_PASSWORD = "medical-worker/change-password"
        
        // Jobs endpoints
        const val JOBS = "jobs"
        const val JOB_DETAILS = "jobs/{id}"
        const val APPLY_JOB = "jobs/{id}/apply"
        
        // Application endpoints
        const val APPLICATIONS = "applications"
        const val APPLICATION_DETAILS = "applications/{id}"
        const val WITHDRAW_APPLICATION = "applications/{id}/withdraw"
    }
    
    // Request timeouts
    const val CONNECT_TIMEOUT = 30L // 30 seconds
    const val READ_TIMEOUT = 30L // 30 seconds
    const val WRITE_TIMEOUT = 30L // 30 seconds
    
    // Other config
    const val AUTHORIZATION_HEADER = "Authorization"
    const val BEARER_PREFIX = "Bearer "
}
