package com.mediconnect.app.ui.jobs

import android.app.Application
import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider

/**
 * Factory for creating a JobsViewModel with a constructor that takes an application.
 */
class JobsViewModelFactory(private val application: Application) : ViewModelProvider.Factory {
    @Suppress("UNCHECKED_CAST")
    override fun <T : ViewModel> create(modelClass: Class<T>): T {
        if (modelClass.isAssignableFrom(JobsViewModel::class.java)) {
            return JobsViewModel(application) as T
        }
        throw IllegalArgumentException("Unknown ViewModel class")
    }
}
