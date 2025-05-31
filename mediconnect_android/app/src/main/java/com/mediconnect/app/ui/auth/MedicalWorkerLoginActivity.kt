package com.mediconnect.app.ui.auth

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import com.mediconnect.app.R
import com.mediconnect.app.databinding.ActivityMedicalWorkerLoginBinding
import com.mediconnect.app.ui.MainActivity
import com.mediconnect.app.util.validateEmail
import com.mediconnect.app.util.validatePassword

/**
 * Activity for medical worker login
 */
class MedicalWorkerLoginActivity : AppCompatActivity() {

    private lateinit var binding: ActivityMedicalWorkerLoginBinding
    private lateinit var viewModel: MedicalWorkerAuthViewModel

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMedicalWorkerLoginBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Initialize ViewModel
        viewModel = ViewModelProvider(this, MedicalWorkerAuthViewModelFactory(this))
            .get(MedicalWorkerAuthViewModel::class.java)

        // Check if already logged in
        viewModel.checkLoginStatus()

        // Set up click listeners
        binding.loginButton.setOnClickListener {
            attemptLogin()
        }

        binding.registerLink.setOnClickListener {
            navigateToRegistration()
        }

        // Observe ViewModel state
        observeViewModel()
    }

    private fun observeViewModel() {
        // Observe loading state
        viewModel.isLoading.observe(this) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.loginButton.isEnabled = !isLoading
        }

        // Observe error messages
        viewModel.error.observe(this) { errorMessage ->
            errorMessage?.let {
                Toast.makeText(this, it, Toast.LENGTH_LONG).show()
                viewModel.clearError()
            }
        }

        // Observe login success
        viewModel.loginSuccess.observe(this) { success ->
            if (success) {
                // Navigate to main activity if login successful
                val intent = Intent(this, MainActivity::class.java)
                intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
                startActivity(intent)
                finish()
            }
        }
    }

    private fun attemptLogin() {
        val email = binding.emailInput.text.toString().trim()
        val password = binding.passwordInput.text.toString()

        // Validate input
        var isValid = true

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

        // Attempt login if input is valid
        if (isValid) {
            viewModel.login(email, password)
        }
    }

    private fun navigateToRegistration() {
        val intent = Intent(this, MedicalWorkerRegistrationActivity::class.java)
        startActivity(intent)
    }
}
