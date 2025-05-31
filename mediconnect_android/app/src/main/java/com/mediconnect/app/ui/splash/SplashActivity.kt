package com.mediconnect.app.ui.splash

import android.content.Intent
import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import com.mediconnect.app.R
import com.mediconnect.app.databinding.ActivitySplashBinding
import com.mediconnect.app.ui.MainActivity
import com.mediconnect.app.ui.auth.LoginActivity
import com.mediconnect.app.ui.auth.MedicalWorkerLoginActivity
import com.mediconnect.app.util.MedicalWorkerSessionManager
import com.mediconnect.app.util.SessionManager

/**
 * Splash activity to show app logo and decide which screen to navigate to:
 * - Main Activity if user is already logged in
 * - User/Medical Worker choice screen if not logged in
 */
class SplashActivity : AppCompatActivity() {

    private lateinit var binding: ActivitySplashBinding
    private lateinit var sessionManager: SessionManager
    private lateinit var medicalWorkerSessionManager: MedicalWorkerSessionManager
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivitySplashBinding.inflate(layoutInflater)
        setContentView(binding.root)
        
        sessionManager = SessionManager(this)
        medicalWorkerSessionManager = MedicalWorkerSessionManager(this)
        
        // Check login status after a short delay
        Handler(Looper.getMainLooper()).postDelayed({
            checkLoginStatus()
        }, SPLASH_DELAY)
        
        // Set up click listeners
        binding.patientButton.setOnClickListener {
            startActivity(Intent(this, LoginActivity::class.java))
            finish()
        }
        
        binding.medicalWorkerButton.setOnClickListener {
            startActivity(Intent(this, MedicalWorkerLoginActivity::class.java))
            finish()
        }
    }
    
    private fun checkLoginStatus() {
        // First show the app logo for a moment
        binding.appLogo.visibility = View.VISIBLE
        binding.appName.visibility = View.VISIBLE
        
        // Check if user is already logged in
        if (sessionManager.isLoggedIn()) {
            // Regular user is logged in, go to main activity
            startActivity(Intent(this, MainActivity::class.java))
            finish()
        } else if (medicalWorkerSessionManager.isLoggedIn()) {
            // Medical worker is logged in, go to main activity
            // We'll handle different UI/functionality in MainActivity based on user type
            startActivity(Intent(this, MainActivity::class.java))
            finish()
        } else {
            // No one is logged in, show login options
            Handler(Looper.getMainLooper()).postDelayed({
                binding.loginOptionsLayout.visibility = View.VISIBLE
                binding.appLogo.animate().scaleX(0.7f).scaleY(0.7f).translationY(-200f).setDuration(500).start()
                binding.appName.animate().alpha(0.7f).translationY(-100f).setDuration(500).start()
            }, LOGO_DISPLAY_TIME)
        }
    }
    
    companion object {
        private const val SPLASH_DELAY = 1000L
        private const val LOGO_DISPLAY_TIME = 1500L
    }
}
