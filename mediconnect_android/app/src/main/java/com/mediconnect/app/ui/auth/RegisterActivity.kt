package com.mediconnect.app.ui.auth

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Button
import android.widget.EditText
import android.widget.ProgressBar
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.mediconnect.app.R
import com.mediconnect.app.api.ApiClient
import com.mediconnect.app.api.models.RegisterRequest
import com.mediconnect.app.ui.MainActivity
import com.mediconnect.app.util.SessionManager
import kotlinx.coroutines.launch

class RegisterActivity : AppCompatActivity() {

    private lateinit var etFullName: EditText
    private lateinit var etEmail: EditText
    private lateinit var etPassword: EditText
    private lateinit var etConfirmPassword: EditText
    private lateinit var btnRegister: Button
    private lateinit var tvLogin: TextView
    private lateinit var progressBar: ProgressBar
    
    private lateinit var apiClient: ApiClient
    private lateinit var sessionManager: SessionManager

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_register)
        
        // Initialize API client and session manager
        apiClient = ApiClient.getInstance(applicationContext)
        sessionManager = SessionManager(applicationContext)
        
        // Check if user is already logged in
        if (sessionManager.isLoggedIn()) {
            navigateToMain()
            return
        }

        // Initialize views
        etFullName = findViewById(R.id.etFullName)
        etEmail = findViewById(R.id.etEmail)
        etPassword = findViewById(R.id.etPassword)
        etConfirmPassword = findViewById(R.id.etConfirmPassword)
        btnRegister = findViewById(R.id.btnRegister)
        tvLogin = findViewById(R.id.tvLogin)
        progressBar = findViewById(R.id.progressBar)

        // Set up click listeners
        btnRegister.setOnClickListener {
            performRegistration()
        }

        tvLogin.setOnClickListener {
            // Navigate back to login screen
            finish()
        }
    }

    private fun performRegistration() {
        val fullName = etFullName.text.toString().trim()
        val email = etEmail.text.toString().trim()
        val password = etPassword.text.toString().trim()
        val confirmPassword = etConfirmPassword.text.toString().trim()

        // Validate inputs
        if (fullName.isEmpty()) {
            etFullName.error = "Name is required"
            etFullName.requestFocus()
            return
        }
        
        if (email.isEmpty()) {
            etEmail.error = "Email is required"
            etEmail.requestFocus()
            return
        }
        
        if (password.isEmpty()) {
            etPassword.error = "Password is required"
            etPassword.requestFocus()
            return
        }
        
        if (confirmPassword.isEmpty()) {
            etConfirmPassword.error = "Please confirm your password"
            etConfirmPassword.requestFocus()
            return
        }

        if (password != confirmPassword) {
            etConfirmPassword.error = "Passwords do not match"
            etConfirmPassword.requestFocus()
            return
        }
        
        // Show progress
        progressBar.visibility = View.VISIBLE
        btnRegister.isEnabled = false

        // Create registration request
        val registerRequest = RegisterRequest(
            name = fullName,
            email = email,
            password = password,
            password_confirmation = confirmPassword
        )

        // Perform API call
        lifecycleScope.launch {
            try {
                val response = apiClient.authService.register(registerRequest)
                
                if (response.success) {
                    // Save auth token and user
                    response.data?.let { authResponse ->
                        sessionManager.saveAuthToken(authResponse.token)
                        sessionManager.saveUser(authResponse.user)
                        
                        // Navigate to main screen
                        Toast.makeText(
                            this@RegisterActivity, 
                            "Welcome, ${authResponse.user.name}",
                            Toast.LENGTH_SHORT
                        ).show()
                        
                        navigateToMain()
                    }
                } else {
                    // Show error message
                    Toast.makeText(
                        this@RegisterActivity,
                        response.message,
                        Toast.LENGTH_LONG
                    ).show()
                }
            } catch (e: Exception) {
                // Handle exceptions
                Toast.makeText(
                    this@RegisterActivity,
                    "Registration failed: ${e.message}",
                    Toast.LENGTH_LONG
                ).show()
            } finally {
                // Hide progress
                progressBar.visibility = View.GONE
                btnRegister.isEnabled = true
            }
        }
    }
    
    private fun navigateToMain() {
        val intent = Intent(this, MainActivity::class.java)
        intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
        startActivity(intent)
        finish()
    }
}
