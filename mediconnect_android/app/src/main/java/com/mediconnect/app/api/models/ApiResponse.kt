package com.mediconnect.app.api.models

/**
 * Generic API response wrapper
 */
data class ApiResponse<T>(
    val success: Boolean,
    val message: String,
    val data: T?
)
