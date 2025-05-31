package com.mediconnect.app.ui

import android.content.Intent
import android.os.Bundle
import android.view.Menu
import android.view.MenuItem
import android.view.View
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import androidx.drawerlayout.widget.DrawerLayout
import androidx.navigation.findNavController
import androidx.navigation.ui.AppBarConfiguration
import androidx.navigation.ui.navigateUp
import androidx.navigation.ui.setupActionBarWithNavController
import androidx.navigation.ui.setupWithNavController
import com.google.android.material.navigation.NavigationView
import com.mediconnect.app.R
import com.mediconnect.app.ui.auth.LoginActivity
import com.mediconnect.app.ui.auth.MedicalWorkerLoginActivity
import com.mediconnect.app.util.MedicalWorkerSessionManager
import com.mediconnect.app.util.SessionManager

class MainActivity : AppCompatActivity() {

    private lateinit var appBarConfiguration: AppBarConfiguration
    private lateinit var sessionManager: SessionManager
    private lateinit var medicalWorkerSessionManager: MedicalWorkerSessionManager
    
    private var isMedicalWorker = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)
        
        // Initialize session managers
        sessionManager = SessionManager(this)
        medicalWorkerSessionManager = MedicalWorkerSessionManager(this)
        
        // Determine which type of user is logged in
        isMedicalWorker = !medicalWorkerSessionManager.getAuthToken().isNullOrEmpty()
        
        val toolbar = findViewById<androidx.appcompat.widget.Toolbar>(R.id.toolbar)
        setSupportActionBar(toolbar)

        val drawerLayout: DrawerLayout = findViewById(R.id.drawer_layout)
        val navView: NavigationView = findViewById(R.id.nav_view)
        val navController = findNavController(R.id.nav_host_fragment_content_main)
        
        // Configure top level destinations based on user type
        val topLevelDestinations = if (isMedicalWorker) {
            // Medical worker destinations
            setOf(
                R.id.nav_home, R.id.nav_profile, R.id.nav_settings
                // Add medical worker specific destinations here
            )
        } else {
            // Regular user destinations
            setOf(
                R.id.nav_home, R.id.nav_jobs, R.id.nav_applications, R.id.nav_profile, R.id.nav_settings
            )
        }
        
        appBarConfiguration = AppBarConfiguration(topLevelDestinations, drawerLayout)
        setupActionBarWithNavController(navController, appBarConfiguration)
        navView.setupWithNavController(navController)
        
        // Set up header with user info from shared preferences
        updateNavigationUserInfo()
        
        // Configure menu items based on user type
        configureNavigationMenu(navView)
        
        // Handle logout click
        navView.menu.findItem(R.id.nav_logout).setOnMenuItemClickListener {
            logout()
            true
        }
    }

    private fun updateNavigationUserInfo() {
        val navView: NavigationView = findViewById(R.id.nav_view)
        val headerView = navView.getHeaderView(0)
        val tvUserName = headerView.findViewById<TextView>(R.id.tvUserName)
        val tvUserEmail = headerView.findViewById<TextView>(R.id.tvUserEmail)
        
        if (isMedicalWorker) {
            // Get medical worker info
            val medicalWorker = medicalWorkerSessionManager.getMedicalWorker()
            tvUserName.text = medicalWorker?.name ?: "Medical Worker"
            tvUserEmail.text = medicalWorker?.email ?: ""
        } else {
            // Get regular user info
            val user = sessionManager.getUser()
            tvUserName.text = user?.name ?: "User"
            tvUserEmail.text = user?.email ?: ""
        }
    }
    
    private fun configureNavigationMenu(navView: NavigationView) {
        // Show/hide menu items based on user type
        val menu = navView.menu
        
        // Items that are only visible to regular users
        menu.findItem(R.id.nav_jobs)?.isVisible = !isMedicalWorker
        menu.findItem(R.id.nav_applications)?.isVisible = !isMedicalWorker
        
        // Add any medical worker specific items if needed
        // menu.findItem(R.id.nav_medical_worker_specific)?.isVisible = isMedicalWorker
    }
    
    private fun logout() {
        if (isMedicalWorker) {
            // Clear medical worker session
            medicalWorkerSessionManager.clearSession()
            
            // Navigate to medical worker login activity
            val intent = Intent(this, MedicalWorkerLoginActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
            startActivity(intent)
        } else {
            // Clear regular user session
            sessionManager.clearSession()
            
            // Navigate to login activity
            val intent = Intent(this, LoginActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
            startActivity(intent)
        }
        finish()
    }

    override fun onCreateOptionsMenu(menu: Menu): Boolean {
        // Inflate the menu; this adds items to the action bar if it is present.
        menuInflater.inflate(R.menu.main, menu)
        return true
    }

    override fun onOptionsItemSelected(item: MenuItem): Boolean {
        return when (item.itemId) {
            R.id.action_settings -> {
                findNavController(R.id.nav_host_fragment_content_main).navigate(R.id.nav_settings)
                true
            }
            else -> super.onOptionsItemSelected(item)
        }
    }

    override fun onSupportNavigateUp(): Boolean {
        val navController = findNavController(R.id.nav_host_fragment_content_main)
        return navController.navigateUp(appBarConfiguration) || super.onSupportNavigateUp()
    }
}
