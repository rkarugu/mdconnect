package com.mediconnect.app.api.models

/**
 * Models for authentication requests
 */

/**
 * Login request model
 */
data class LoginRequest(
    val email: String,
    val password: String
)

/**
 * Registration request model
 */
data class RegisterRequest(
    val name: String,
    val email: String,
    val password: String,
    val password_confirmation: String,
    val phone: String? = null,
    val qualifications: String? = null,
    val experience: String? = null,
    val specialization: String? = null
)

/**
 * Authentication response containing the token
 */
data class AuthResponse(
    val token: String,
    val user: User
)
