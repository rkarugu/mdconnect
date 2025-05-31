package com.mediconnect.app.ui.auth

import android.content.Context
import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import com.mediconnect.app.api.ApiClient
import com.mediconnect.app.util.MedicalWorkerSessionManager

/**
 * Factory for creating MedicalWorkerAuthViewModel with the appropriate dependencies
 */
class MedicalWorkerAuthViewModelFactory(private val context: Context) : ViewModelProvider.Factory {
    
    @Suppress("UNCHECKED_CAST")
    override fun <T : ViewModel> create(modelClass: Class<T>): T {
        if (modelClass.isAssignableFrom(MedicalWorkerAuthViewModel::class.java)) {
            val apiClient = ApiClient.getInstance(context)
            val sessionManager = MedicalWorkerSessionManager(context)
            return MedicalWorkerAuthViewModel(apiClient, sessionManager) as T
        }
        throw IllegalArgumentException("Unknown ViewModel class")
    }
}
