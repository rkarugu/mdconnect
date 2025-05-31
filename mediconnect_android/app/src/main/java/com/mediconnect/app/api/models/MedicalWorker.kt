package com.mediconnect.app.api.models

import android.os.Parcelable
import kotlinx.parcelize.Parcelize

/**
 * Data class representing a medical worker
 */
@Parcelize
data class MedicalWorker(
    val id: Int,
    val name: String,
    val email: String,
    val phone: String? = null,
    val profilePicture: String? = null,
    val specialtyId: Int,
    val specialtyName: String? = null, // For UI display purposes
    val licenseNumber: String,
    val yearsOfExperience: String,
    val bio: String? = null,
    val education: String? = null,
    val certifications: String? = null,
    val status: String, // pending, approved, rejected, suspended
    val statusReason: String? = null,
    val isAvailable: Boolean = true,
    val workingHours: Map<String, List<String>>? = null,
    val approvedAt: String? = null,
    val createdAt: String? = null,
    val updatedAt: String? = null
) : Parcelable
