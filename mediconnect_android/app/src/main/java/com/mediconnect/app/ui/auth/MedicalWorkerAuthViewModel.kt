package com.mediconnect.app.ui.auth

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.mediconnect.app.api.ApiClient
import com.mediconnect.app.api.models.MedicalWorker
import com.mediconnect.app.api.models.MedicalWorkerLoginRequest
import com.mediconnect.app.api.models.MedicalWorkerRegisterRequest
import com.mediconnect.app.util.MedicalWorkerSessionManager
import kotlinx.coroutines.launch

/**
 * ViewModel to manage medical worker authentication state and operations
 */
class MedicalWorkerAuthViewModel(
    private val apiClient: ApiClient,
    private val sessionManager: MedicalWorkerSessionManager
) : ViewModel() {

    // LiveData for authentication state
    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _loginSuccess = MutableLiveData<Boolean>()
    val loginSuccess: LiveData<Boolean> = _loginSuccess

    private val _registerSuccess = MutableLiveData<Boolean>()
    val registerSuccess: LiveData<Boolean> = _registerSuccess

    private val _medicalWorker = MutableLiveData<MedicalWorker?>()
    val medicalWorker: LiveData<MedicalWorker?> = _medicalWorker

    /**
     * Check if the medical worker is already logged in
     */
    fun checkLoginStatus() {
        _medicalWorker.value = sessionManager.getMedicalWorker()
        _loginSuccess.value = sessionManager.isLoggedIn()
    }

    /**
     * Login a medical worker
     */
    fun login(email: String, password: String) {
        _isLoading.value = true
        _error.value = null

        viewModelScope.launch {
            try {
                val loginRequest = MedicalWorkerLoginRequest(email, password)
                val response = apiClient.medicalWorkerAuthService.login(loginRequest)

                if (response.success) {
                    // Save authentication token and medical worker info
                    response.data?.let { authResponse ->
                        sessionManager.saveAuthToken(authResponse.token)
                        sessionManager.saveMedicalWorker(authResponse.medical_worker)
                        _medicalWorker.value = authResponse.medical_worker
                        _loginSuccess.value = true
                    }
                } else {
                    _error.value = response.message ?: "Login failed"
                    _loginSuccess.value = false
                }
            } catch (e: Exception) {
                _error.value = e.message ?: "An error occurred during login"
                _loginSuccess.value = false
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Register a new medical worker
     */
    fun register(
        name: String,
        email: String,
        password: String,
        passwordConfirmation: String,
        phone: String,
        specialtyId: Int,
        licenseNumber: String,
        yearsOfExperience: String,
        bio: String? = null,
        education: String? = null,
        certifications: String? = null
    ) {
        _isLoading.value = true
        _error.value = null

        viewModelScope.launch {
            try {
                val registerRequest = MedicalWorkerRegisterRequest(
                    name = name,
                    email = email,
                    password = password,
                    password_confirmation = passwordConfirmation,
                    phone = phone,
                    specialty_id = specialtyId,
                    license_number = licenseNumber,
                    years_of_experience = yearsOfExperience,
                    bio = bio,
                    education = education,
                    certifications = certifications
                )

                val response = apiClient.medicalWorkerAuthService.register(registerRequest)

                if (response.success) {
                    // Save authentication token and medical worker info
                    response.data?.let { authResponse ->
                        sessionManager.saveAuthToken(authResponse.token)
                        sessionManager.saveMedicalWorker(authResponse.medical_worker)
                        _medicalWorker.value = authResponse.medical_worker
                        _registerSuccess.value = true
                    }
                } else {
                    _error.value = response.message ?: "Registration failed"
                    _registerSuccess.value = false
                }
            } catch (e: Exception) {
                _error.value = e.message ?: "An error occurred during registration"
                _registerSuccess.value = false
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Logout the medical worker
     */
    fun logout() {
        _isLoading.value = true

        viewModelScope.launch {
            try {
                // Call the logout API
                apiClient.medicalWorkerAuthService.logout()
            } catch (e: Exception) {
                // Even if API fails, clear local session
            } finally {
                // Clear session data
                sessionManager.clearSession()
                _medicalWorker.value = null
                _loginSuccess.value = false
                _isLoading.value = false
            }
        }
    }

    /**
     * Get the medical worker profile
     */
    fun getProfile() {
        if (!sessionManager.isLoggedIn()) {
            return
        }

        _isLoading.value = true
        _error.value = null

        viewModelScope.launch {
            try {
                val response = apiClient.medicalWorkerAuthService.getProfile()

                if (response.success) {
                    response.data?.let { medicalWorker ->
                        sessionManager.saveMedicalWorker(medicalWorker)
                        _medicalWorker.value = medicalWorker
                    }
                } else {
                    _error.value = response.message ?: "Failed to load profile"
                }
            } catch (e: Exception) {
                _error.value = e.message ?: "An error occurred while loading profile"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Update medical worker profile
     */
    fun updateProfile(medicalWorker: MedicalWorker) {
        _isLoading.value = true
        _error.value = null

        viewModelScope.launch {
            try {
                val response = apiClient.medicalWorkerAuthService.updateProfile(medicalWorker)

                if (response.success) {
                    response.data?.let { updatedMedicalWorker ->
                        sessionManager.saveMedicalWorker(updatedMedicalWorker)
                        _medicalWorker.value = updatedMedicalWorker
                    }
                } else {
                    _error.value = response.message ?: "Failed to update profile"
                }
            } catch (e: Exception) {
                _error.value = e.message ?: "An error occurred while updating profile"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Clear error messages
     */
    fun clearError() {
        _error.value = null
    }
}
