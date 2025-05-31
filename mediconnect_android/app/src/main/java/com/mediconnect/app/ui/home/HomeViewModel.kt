package com.mediconnect.app.ui.home

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import com.mediconnect.app.api.ApiClient
import com.mediconnect.app.api.models.Job
import kotlinx.coroutines.launch

class HomeViewModel(application: Application) : AndroidViewModel(application) {
    
    private val apiClient = ApiClient.getInstance(application)

    private val _recentJobs = MutableLiveData<List<Job>>()
    val recentJobs: LiveData<List<Job>> = _recentJobs

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String>()
    val error: LiveData<String> = _error

    fun loadRecentJobs() {
        _isLoading.value = true
        _error.value = ""
        
        viewModelScope.launch {
            try {
                // Use the jobs service to get a list of recent jobs
                val response = apiClient.jobsService.getJobs(page = 1)
                
                if (response.success) {
                    _recentJobs.value = response.data ?: emptyList()
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
}


