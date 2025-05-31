package com.mediconnect.app.api.models

import com.google.gson.annotations.SerializedName

/**
 * Model class representing a job application in the system
 */
data class Application(
    val id: Int,
    @SerializedName("job_id")
    val jobId: Int,
    @SerializedName("user_id")
    val userId: Int,
    val status: String, // "pending", "accepted", "rejected"
    val message: String? = null,
    val resume: String? = null,
    @SerializedName("created_at")
    val createdAt: String,
    @SerializedName("updated_at")
    val updatedAt: String,
    // Related entities
    val job: Job? = null,
    val user: User? = null
)
