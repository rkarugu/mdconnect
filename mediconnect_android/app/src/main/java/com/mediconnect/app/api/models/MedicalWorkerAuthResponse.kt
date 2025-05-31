package com.mediconnect.app.api.models

/**
 * Data class representing a medical worker authentication response
 */
data class MedicalWorkerAuthResponse(
    val medical_worker: MedicalWorker,
    val token: String,
    val token_type: String
)
