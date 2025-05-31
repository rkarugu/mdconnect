package com.mediconnect.app.ui.auth

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.ArrayAdapter
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import com.mediconnect.app.R
import com.mediconnect.app.databinding.ActivityMedicalWorkerRegistrationBinding
import com.mediconnect.app.ui.MainActivity
import com.mediconnect.app.util.validateEmail
import com.mediconnect.app.util.validatePassword

/**
 * Activity for medical worker registration
 */
class MedicalWorkerRegistrationActivity : AppCompatActivity() {

    private lateinit var binding: ActivityMedicalWorkerRegistrationBinding
    private lateinit var viewModel: MedicalWorkerAuthViewModel

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMedicalWorkerRegistrationBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Initialize ViewModel
        viewModel = ViewModelProvider(this, MedicalWorkerAuthViewModelFactory(this))
            .get(MedicalWorkerAuthViewModel::class.java)

        // Set up click listeners
        binding.registerButton.setOnClickListener {
            attemptRegistration()
        }

        binding.loginLink.setOnClickListener {
            finish() // Go back to login activity
        }

        // Set up specialty spinner
        setupSpecialtySpinner()

        // Observe ViewModel state
        observeViewModel()
    }

    private fun setupSpecialtySpinner() {
        // This would ideally be loaded from an API, but for now we'll use static data
        val specialties = listOf(
            "General Medicine" to 1,
            "Cardiology" to 2,
            "Neurology" to 3,
            "Pediatrics" to 4,
            "Obstetrics & Gynecology" to 5,
            "Orthopedics" to 6,
            "Dermatology" to 7,
            "Psychiatry" to 8,
            "Ophthalmology" to 9,
            "ENT" to 10
        )
        
        val adapter = ArrayAdapter(
            this,
            android.R.layout.simple_spinner_item,
            specialties.map { it.first }
        )
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        binding.specialtySpinner.adapter = adapter
    }

    private fun observeViewModel() {
        // Observe loading state
        viewModel.isLoading.observe(this) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.registerButton.isEnabled = !isLoading
        }

        // Observe error messages
        viewModel.error.observe(this) { errorMessage ->
            errorMessage?.let {
                Toast.makeText(this, it, Toast.LENGTH_LONG).show()
                viewModel.clearError()
            }
        }

        // Observe registration success
        viewModel.registerSuccess.observe(this) { success ->
            if (success) {
                Toast.makeText(
                    this,
                    "Registration successful! Your account is pending approval.",
                    Toast.LENGTH_LONG
                ).show()
                
                // Navigate to main activity if registration successful
                val intent = Intent(this, MainActivity::class.java)
                intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
                startActivity(intent)
                finish()
            }
        }
    }

    private fun attemptRegistration() {
        // Get input values
        val name = binding.nameInput.text.toString().trim()
        val email = binding.emailInput.text.toString().trim()
        val password = binding.passwordInput.text.toString()
        val passwordConfirmation = binding.confirmPasswordInput.text.toString()
        val phone = binding.phoneInput.text.toString().trim()
        val licenseNumber = binding.licenseInput.text.toString().trim()
        val yearsOfExperience = binding.experienceInput.text.toString().trim()
        val bio = binding.bioInput.text.toString().trim()
        val education = binding.educationInput.text.toString().trim()
        val certifications = binding.certificationsInput.text.toString().trim()
        
        // Get selected specialty ID
        val specialtyPosition = binding.specialtySpinner.selectedItemPosition
        // This would use the real specialty IDs from the API response in a production app
        val specialtyId = specialtyPosition + 1 // Simple mapping for demo
        
        // Validate input
        var isValid = true

        if (name.isEmpty()) {
            binding.nameLayout.error = "Name is required"
            isValid = false
        } else {
            binding.nameLayout.error = null
        }

        if (!validateEmail(email)) {
            binding.emailLayout.error = getString(R.string.error_invalid_email)
            isValid = false
        } else {
            binding.emailLayout.error = null
        }

        if (!validatePassword(password)) {
            binding.passwordLayout.error = getString(R.string.error_invalid_password)
            isValid = false
        } else {
            binding.passwordLayout.error = null
        }

        if (password != passwordConfirmation) {
            binding.confirmPasswordLayout.error = "Passwords do not match"
            isValid = false
        } else {
            binding.confirmPasswordLayout.error = null
        }

        if (phone.isEmpty()) {
            binding.phoneLayout.error = "Phone is required"
            isValid = false
        } else {
            binding.phoneLayout.error = null
        }

        if (licenseNumber.isEmpty()) {
            binding.licenseLayout.error = "License number is required"
            isValid = false
        } else {
            binding.licenseLayout.error = null
        }

        if (yearsOfExperience.isEmpty()) {
            binding.experienceLayout.error = "Years of experience is required"
            isValid = false
        } else {
            binding.experienceLayout.error = null
        }

        // Attempt registration if input is valid
        if (isValid) {
            viewModel.register(
                name = name,
                email = email,
                password = password,
                passwordConfirmation = passwordConfirmation,
                phone = phone,
                specialtyId = specialtyId,
                licenseNumber = licenseNumber,
                yearsOfExperience = yearsOfExperience,
                bio = if (bio.isNotEmpty()) bio else null,
                education = if (education.isNotEmpty()) education else null,
                certifications = if (certifications.isNotEmpty()) certifications else null
            )
        }
    }
}
