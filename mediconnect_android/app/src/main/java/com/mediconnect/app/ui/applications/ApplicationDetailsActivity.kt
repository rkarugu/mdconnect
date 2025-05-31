package com.mediconnect.app.ui.applications

import android.content.Intent
import android.os.Bundle
import android.view.MenuItem
import android.view.View
import android.widget.Button
import android.widget.ProgressBar
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.appcompat.widget.Toolbar
import androidx.lifecycle.lifecycleScope
import com.mediconnect.app.R
import com.mediconnect.app.api.ApiClient
import com.mediconnect.app.api.models.Application
import com.mediconnect.app.ui.jobs.JobDetailsActivity
import com.mediconnect.app.util.SessionManager
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

class ApplicationDetailsActivity : AppCompatActivity() {

    private lateinit var tvJobTitle: TextView
    private lateinit var tvEmployer: TextView
    private lateinit var tvLocation: TextView
    private lateinit var tvSalary: TextView
    private lateinit var tvStatus: TextView
    private lateinit var tvAppliedDate: TextView
    private lateinit var tvUpdatedDate: TextView
    private lateinit var tvFeedback: TextView
    private lateinit var tvFeedbackContent: TextView
    private lateinit var tvCoverLetter: TextView
    private lateinit var tvResumeInfo: TextView
    private lateinit var btnViewJob: Button
    private lateinit var btnWithdraw: Button
    private lateinit var progressBar: ProgressBar

    private lateinit var apiClient: ApiClient
    private lateinit var sessionManager: SessionManager

    private var applicationId: Int = -1
    private var application: Application? = null
    private var jobId: Int = -1

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_application_details)

        // Set up toolbar
        val toolbar = findViewById<Toolbar>(R.id.toolbar)
        setSupportActionBar(toolbar)
        supportActionBar?.setDisplayHomeAsUpEnabled(true)
        supportActionBar?.setDisplayShowHomeEnabled(true)
        supportActionBar?.title = getString(R.string.application_details)

        // Initialize views
        tvJobTitle = findViewById(R.id.tvJobTitle)
        tvEmployer = findViewById(R.id.tvEmployer)
        tvLocation = findViewById(R.id.tvLocation)
        tvSalary = findViewById(R.id.tvSalary)
        tvStatus = findViewById(R.id.tvStatus)
        tvAppliedDate = findViewById(R.id.tvAppliedDate)
        tvUpdatedDate = findViewById(R.id.tvUpdatedDate)
        tvFeedback = findViewById(R.id.tvFeedback)
        tvFeedbackContent = findViewById(R.id.tvFeedbackContent)
        tvCoverLetter = findViewById(R.id.tvCoverLetter)
        tvResumeInfo = findViewById(R.id.tvResumeInfo)
        btnViewJob = findViewById(R.id.btnViewJob)
        btnWithdraw = findViewById(R.id.btnWithdraw)
        progressBar = findViewById(R.id.progressBar)

        // Initialize API client and session manager
        apiClient = ApiClient.getInstance(applicationContext)
        sessionManager = SessionManager(applicationContext)

        // Get application ID from intent
        applicationId = intent.getIntExtra("APPLICATION_ID", -1)
        if (applicationId == -1) {
            Toast.makeText(this, "Error: Application not found", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        // Load application details
        loadApplicationDetails()

        // Set up button click listeners
        btnViewJob.setOnClickListener {
            if (jobId != -1) {
                val intent = Intent(this, JobDetailsActivity::class.java).apply {
                    putExtra("JOB_ID", jobId)
                }
                startActivity(intent)
            }
        }
        btnViewJob.text = getString(R.string.view_job)

        btnWithdraw.setOnClickListener {
            showWithdrawConfirmationDialog()
        }
        btnWithdraw.text = getString(R.string.withdraw_application)
    }

    private fun loadApplicationDetails() {
        progressBar.visibility = View.VISIBLE

        lifecycleScope.launch {
            try {
                val response = apiClient.jobsService.getApplicationDetails(applicationId)
                
                if (response.success && response.data != null) {
                    application = response.data
                    displayApplicationDetails(response.data)
                } else {
                    Toast.makeText(
                        this@ApplicationDetailsActivity,
                        "Error loading application details: ${response.message}",
                        Toast.LENGTH_LONG
                    ).show()
                    finish()
                }
            } catch (e: Exception) {
                Toast.makeText(
                    this@ApplicationDetailsActivity,
                    "Error loading application details: ${e.message}",
                    Toast.LENGTH_LONG
                ).show()
                finish()
            } finally {
                progressBar.visibility = View.GONE
            }
        }
    }

    private fun displayApplicationDetails(application: Application) {
        // Store job ID for job details navigation
        jobId = application.jobId

        // Display job details
        application.job?.let { job ->
            tvJobTitle.text = job.title
            tvEmployer.text = job.employer
            tvLocation.text = job.location
            tvSalary.text = job.salary
        } ?: run {
            tvJobTitle.text = "Job #${application.jobId}"
            tvEmployer.text = "Unknown Employer"
            tvLocation.text = "Not available"
            tvSalary.text = "Not available"
        }

        // Set status with appropriate background
        tvStatus.text = application.status.capitalize()
        val backgroundRes = when (application.status.lowercase()) {
            "pending" -> R.drawable.status_pending_bg
            "accepted" -> R.drawable.status_accepted_bg
            "rejected" -> R.drawable.status_rejected_bg
            else -> R.drawable.status_pending_bg
        }
        tvStatus.setBackgroundResource(backgroundRes)

        // Format dates
        tvAppliedDate.text = "Applied on: ${formatDate(application.createdAt)}"
        tvUpdatedDate.text = "Last updated: ${formatDate(application.updatedAt)}"

        // Show feedback if available
        if (application.status.lowercase() == "rejected") {
            tvFeedback.visibility = View.VISIBLE
            tvFeedbackContent.visibility = View.VISIBLE
            tvFeedbackContent.text = application.message ?: "No feedback provided"
        } else {
            tvFeedback.visibility = View.GONE
            tvFeedbackContent.visibility = View.GONE
        }

        // Show application details
        tvCoverLetter.text = application.message ?: "No cover letter provided"
        tvResumeInfo.text = if (application.resume.isNullOrEmpty()) {
            "Used resume from profile"
        } else {
            "Resume submitted: ${application.resume}"
        }

        // Only allow withdrawal if status is pending
        btnWithdraw.isEnabled = application.status.lowercase() == "pending"
    }

    private fun showWithdrawConfirmationDialog() {
        AlertDialog.Builder(this)
            .setTitle(getString(R.string.withdraw_application))
            .setMessage(getString(R.string.withdraw_confirmation))
            .setPositiveButton(getString(R.string.withdraw_application)) { _, _ ->
                withdrawApplication()
            }
            .setNegativeButton(getString(R.string.cancel), null)
            .show()
    }

    private fun withdrawApplication() {
        progressBar.visibility = View.VISIBLE
        btnWithdraw.isEnabled = false

        lifecycleScope.launch {
            try {
                // Call API to withdraw application
                // Note: This would need to be implemented in the API
                val response = apiClient.jobsService.withdrawApplication(applicationId)
                
                if (response.success) {
                    Toast.makeText(
                        this@ApplicationDetailsActivity,
                        getString(R.string.application_withdrawn_success),
                        Toast.LENGTH_LONG
                    ).show()
                    
                    // Create result intent with data about the withdrawn application
                    val resultIntent = Intent()
                    resultIntent.putExtra("APPLICATION_ID", applicationId)
                    resultIntent.putExtra("ACTION", "WITHDRAWN")
                    setResult(RESULT_OK, resultIntent)
                    finish()
                } else {
                    Toast.makeText(
                        this@ApplicationDetailsActivity,
                        "Failed to withdraw application: ${response.message}",
                        Toast.LENGTH_LONG
                    ).show()
                    btnWithdraw.isEnabled = true
                }
            } catch (e: Exception) {
                Toast.makeText(
                    this@ApplicationDetailsActivity,
                    "Error withdrawing application: ${e.message}",
                    Toast.LENGTH_LONG
                ).show()
                btnWithdraw.isEnabled = true
            } finally {
                progressBar.visibility = View.GONE
            }
        }
    }

    private fun formatDate(dateString: String): String {
        try {
            val inputFormat = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSSXXX", Locale.getDefault())
            val outputFormat = SimpleDateFormat("MMM dd, yyyy", Locale.getDefault())
            val date = inputFormat.parse(dateString)
            return if (date != null) {
                outputFormat.format(date)
            } else {
                dateString
            }
        } catch (e: Exception) {
            try {
                val fallbackFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
                val outputFormat = SimpleDateFormat("MMM dd, yyyy", Locale.getDefault())
                val date = fallbackFormat.parse(dateString)
                return if (date != null) {
                    outputFormat.format(date)
                } else {
                    dateString
                }
            } catch (e: Exception) {
                return dateString
            }
        }
    }

    private fun String.capitalize(): String {
        return this.replaceFirstChar { 
            if (it.isLowerCase()) it.titlecase(Locale.getDefault()) else it.toString() 
        }
    }

    override fun onOptionsItemSelected(item: MenuItem): Boolean {
        if (item.itemId == android.R.id.home) {
            onBackPressed()
            return true
        }
        return super.onOptionsItemSelected(item)
    }
}
