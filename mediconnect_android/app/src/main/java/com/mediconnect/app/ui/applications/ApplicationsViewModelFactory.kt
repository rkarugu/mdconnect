package com.mediconnect.app.ui.applications

import android.app.Application
import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider

/**
 * Factory for creating an ApplicationsViewModel with a constructor that takes an application.
 */
class ApplicationsViewModelFactory(private val application: Application) : ViewModelProvider.Factory {
    @Suppress("UNCHECKED_CAST")
    override fun <T : ViewModel> create(modelClass: Class<T>): T {
        if (modelClass.isAssignableFrom(ApplicationsViewModel::class.java)) {
            return ApplicationsViewModel(application) as T
        }
        throw IllegalArgumentException("Unknown ViewModel class")
    }
}
