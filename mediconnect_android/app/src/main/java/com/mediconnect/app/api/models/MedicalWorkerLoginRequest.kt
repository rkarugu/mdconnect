package com.mediconnect.app.api.models

/**
 * Data class representing a medical worker login request
 */
data class MedicalWorkerLoginRequest(
    val email: String,
    val password: String
)
