package com.mediconnect.app.ui.jobs

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import com.mediconnect.app.api.ApiClient
import com.mediconnect.app.api.models.Job
import kotlinx.coroutines.launch

class JobsViewModel(application: Application) : AndroidViewModel(application) {
    
    private val apiClient = ApiClient.getInstance(application)

    private val _jobs = MutableLiveData<List<Job>>()
    val jobs: LiveData<List<Job>> = _jobs

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String>()
    val error: LiveData<String> = _error

    // Function to load all jobs
    fun loadJobs(forceRefresh: Boolean = false) {
        _isLoading.value = true
        _error.value = ""
        
        viewModelScope.launch {
            try {
                // Use the jobs service to get all jobs
                val response = apiClient.jobsService.getJobs()
                
                if (response.success) {
                    _jobs.value = response.data ?: emptyList()
                } else {
                    _error.value = response.message
                }
            } catch (e: Exception) {
                _error.value = "Error loading jobs: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    // Function to search jobs by query
    fun searchJobs(query: String) {
        _isLoading.value = true
        _error.value = ""
        
        viewModelScope.launch {
            try {
                // Call the API to search for jobs using the search parameter
                val response = apiClient.jobsService.getJobs(search = query)
                
                if (response.success) {
                    _jobs.value = response.data ?: emptyList()
                } else {
                    _error.value = response.message
                }
            } catch (e: Exception) {
                _error.value = "Error searching jobs: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }
    
    // Function to get job by ID
    suspend fun getJobById(jobId: Int): Job? {
        // First check if we already have the job in our current list
        _jobs.value?.find { it.id == jobId }?.let {
            return it
        }
        
        // If not found locally, try to get it from the API
        try {
            val response = apiClient.jobsService.getJobDetails(jobId)
            if (response.success) {
                return response.data
            }
        } catch (e: Exception) {
            _error.value = "Error fetching job details: ${e.message}"
        }
        
        return null
    }
}
