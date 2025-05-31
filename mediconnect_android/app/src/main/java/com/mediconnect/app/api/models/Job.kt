package com.mediconnect.app.api.models

import android.os.Parcelable
import com.google.gson.annotations.SerializedName
import kotlinx.parcelize.Parcelize

/**
 * Model class representing a job listing in the system
 */
@Parcelize
data class Job(
    val id: Int,
    val title: String,
    val description: String,
    val requirements: String,
    val employer: String,
    val location: String,
    val salary: String,
    val type: String,
    @SerializedName("created_at")
    val createdAt: String,
    @SerializedName("updated_at")
    val updatedAt: String,
    val latitude: Double? = null,
    val longitude: Double? = null,
    @SerializedName("application_count")
    val applicationCount: Int = 0,
    val isApplied: Boolean = false
) : Parcelable
