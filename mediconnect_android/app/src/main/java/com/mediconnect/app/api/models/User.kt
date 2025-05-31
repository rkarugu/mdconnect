package com.mediconnect.app.api.models

import com.google.gson.annotations.SerializedName

/**
 * Model class representing a user in the system
 */
data class User(
    val id: Int,
    val name: String,
    val email: String,
    val phone: String? = null,
    @SerializedName("profile_image")
    val profileImage: String? = null,
    val qualifications: String? = null,
    val experience: String? = null,
    val specialization: String? = null,
    @SerializedName("created_at")
    val createdAt: String? = null,
    @SerializedName("updated_at")
    val updatedAt: String? = null
)
