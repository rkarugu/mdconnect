package com.mediconnect.app.ui.applications

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import com.mediconnect.app.api.ApiClient
import com.mediconnect.app.api.models.Application as JobApplication
import kotlinx.coroutines.launch

class ApplicationsViewModel(application: Application) : AndroidViewModel(application) {
    
    private val apiClient = ApiClient.getInstance(application.applicationContext)
    
    private val _applications = MutableLiveData<List<JobApplication>>()
    val applications: LiveData<List<JobApplication>> = _applications
    
    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading
    
    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error
    
    private var currentPage = 1
    private var currentStatus: String? = null
    
    // Flag to indicate if this is an initial load or pagination
    var isInitialLoad = true
        private set
    
    init {
        loadApplications()
    }
    
    fun loadApplications(status: String? = null, reset: Boolean = false, isPagination: Boolean = false) {
        // Set the loading type flag
        isInitialLoad = !isPagination
        if (reset) {
            currentPage = 1
            _applications.value = emptyList()
        }
        
        currentStatus = status
        
        _isLoading.value = true
        _error.value = null
        
        viewModelScope.launch {
            try {
                val response = apiClient.jobsService.getMyApplications(
                    page = currentPage,
                    status = currentStatus
                )
                
                if (response.success) {
                    val newApplications = response.data ?: emptyList<JobApplication>()
                    if (reset || currentPage == 1) {
                        _applications.value = newApplications
                    } else {
                        // Append to existing list for pagination
                        val currentList = _applications.value ?: emptyList<JobApplication>()
                        _applications.value = currentList + newApplications
                    }
                    currentPage++
                } else {
                    _error.value = response.message ?: "Failed to load applications"
                }
            } catch (e: Exception) {
                _error.value = e.message ?: "An error occurred"
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    fun refreshApplications() {
        loadApplications(currentStatus, true, false)
    }
    
    fun getApplicationDetails(applicationId: Int): JobApplication? {
        return applications.value?.find { it.id == applicationId }
    }
}
