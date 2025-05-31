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
import com.mediconnect.app.api.models.LoginRequest
import com.mediconnect.app.ui.MainActivity
import com.mediconnect.app.util.SessionManager
import kotlinx.coroutines.launch

class LoginActivity : AppCompatActivity() {

    private lateinit var etEmail: EditText
    private lateinit var etPassword: EditText
    private lateinit var btnLogin: Button
    private lateinit var tvRegister: TextView
    private lateinit var tvForgotPassword: TextView
    private lateinit var progressBar: ProgressBar
    
    private lateinit var apiClient: ApiClient
    private lateinit var sessionManager: SessionManager

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_login)
        
        // Initialize API client and session manager
        apiClient = ApiClient.getInstance(applicationContext)
        sessionManager = SessionManager(applicationContext)
        
        // Check if user is already logged in
        if (sessionManager.isLoggedIn()) {
            navigateToMain()
            return
        }

        // Initialize views
        etEmail = findViewById(R.id.etEmail)
        etPassword = findViewById(R.id.etPassword)
        btnLogin = findViewById(R.id.btnLogin)
        tvRegister = findViewById(R.id.tvRegister)
        tvForgotPassword = findViewById(R.id.tvForgotPassword)
        progressBar = findViewById(R.id.progressBar)

        // Set up click listeners
        btnLogin.setOnClickListener {
            performLogin()
        }

        tvRegister.setOnClickListener {
            // Navigate to register screen
            startActivity(Intent(this, RegisterActivity::class.java))
        }

        tvForgotPassword.setOnClickListener {
            // TODO: Implement forgot password functionality
            Toast.makeText(this, "Forgot password not implemented yet", Toast.LENGTH_SHORT).show()
        }
    }

    private fun performLogin() {
        val email = etEmail.text.toString().trim()
        val password = etPassword.text.toString().trim()

        // Validate inputs
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

        // Show progress
        progressBar.visibility = View.VISIBLE
        btnLogin.isEnabled = false

        // Create login request
        val loginRequest = LoginRequest(email, password)

        // Perform API call
        lifecycleScope.launch {
            try {
                val response = apiClient.authService.login(loginRequest)
                
                if (response.success) {
                    // Save auth token and user
                    response.data?.let { authResponse ->
                        sessionManager.saveAuthToken(authResponse.token)
                        sessionManager.saveUser(authResponse.user)
                        
                        // Navigate to main screen
                        Toast.makeText(
                            this@LoginActivity, 
                            "Welcome, ${authResponse.user.name}",
                            Toast.LENGTH_SHORT
                        ).show()
                        
                        navigateToMain()
                    }
                } else {
                    // Show error message
                    Toast.makeText(
                        this@LoginActivity,
                        response.message,
                        Toast.LENGTH_LONG
                    ).show()
                }
            } catch (e: Exception) {
                // Handle exceptions
                Toast.makeText(
                    this@LoginActivity,
                    "Login failed: ${e.message}",
                    Toast.LENGTH_LONG
                ).show()
            } finally {
                // Hide progress
                progressBar.visibility = View.GONE
                btnLogin.isEnabled = true
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
