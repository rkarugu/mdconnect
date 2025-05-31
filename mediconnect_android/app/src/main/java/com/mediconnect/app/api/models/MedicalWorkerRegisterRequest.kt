package com.mediconnect.app.api.models

/**
 * Data class representing a medical worker registration request
 */
data class MedicalWorkerRegisterRequest(
    val name: String,
    val email: String,
    val password: String,
    val password_confirmation: String,
    val phone: String,
    val specialty_id: Int,
    val license_number: String,
    val years_of_experience: String,
    val bio: String? = null,
    val education: String? = null,
    val certifications: String? = null
)
