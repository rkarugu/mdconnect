package com.mediconnect.app.ui.jobs

import android.app.Activity
import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.provider.OpenableColumns
import android.view.MenuItem
import android.view.View
import android.widget.Button
import android.widget.CheckBox
import android.widget.ProgressBar
import android.widget.TextView
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AppCompatActivity
import androidx.appcompat.widget.Toolbar
import androidx.lifecycle.lifecycleScope
import com.google.android.material.button.MaterialButton
import com.google.android.material.textfield.TextInputEditText
import com.mediconnect.app.R
import com.mediconnect.app.api.ApiClient
import com.mediconnect.app.api.models.Job
import com.mediconnect.app.util.SessionManager
import kotlinx.coroutines.launch
import java.io.File
import java.io.FileOutputStream

class JobApplicationActivity : AppCompatActivity() {

    private lateinit var tvJobTitle: TextView
    private lateinit var tvEmployer: TextView
    private lateinit var etCoverLetter: TextInputEditText
    private lateinit var btnUploadResume: MaterialButton
    private lateinit var tvSelectedFile: TextView
    private lateinit var cbUseProfile: CheckBox
    private lateinit var btnSubmitApplication: Button
    private lateinit var progressBar: ProgressBar

    private lateinit var apiClient: ApiClient
    private lateinit var sessionManager: SessionManager

    private var jobId: Int = -1
    private var job: Job? = null
    private var selectedResumeUri: Uri? = null

    // Activity result launcher for file picking
    private val getContent = registerForActivityResult(ActivityResultContracts.StartActivityForResult()) { result ->
        if (result.resultCode == Activity.RESULT_OK) {
            result.data?.data?.let { uri ->
                selectedResumeUri = uri
                displaySelectedFileName(uri)
                cbUseProfile.isChecked = false
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_job_application)

        // Set up toolbar
        val toolbar = findViewById<Toolbar>(R.id.toolbar)
        setSupportActionBar(toolbar)
        supportActionBar?.setDisplayHomeAsUpEnabled(true)
        supportActionBar?.setDisplayShowHomeEnabled(true)

        // Initialize views
        tvJobTitle = findViewById(R.id.tvJobTitle)
        tvEmployer = findViewById(R.id.tvEmployer)
        etCoverLetter = findViewById(R.id.etCoverLetter)
        btnUploadResume = findViewById(R.id.btnUploadResume)
        tvSelectedFile = findViewById(R.id.tvSelectedFile)
        cbUseProfile = findViewById(R.id.cbUseProfile)
        btnSubmitApplication = findViewById(R.id.btnSubmitApplication)
        progressBar = findViewById(R.id.progressBar)

        // Initialize API client and session manager
        apiClient = ApiClient.getInstance(applicationContext)
        sessionManager = SessionManager(applicationContext)

        // Get job ID from intent
        jobId = intent.getIntExtra("JOB_ID", -1)
        job = intent.getParcelableExtra("JOB")

        if (jobId == -1 && job == null) {
            Toast.makeText(this, "Error: Job not found", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        // Display job information
        job?.let {
            displayJobInfo(it)
        } ?: loadJobDetails()

        // Set up click listeners
        btnUploadResume.setOnClickListener {
            openFilePicker()
        }

        cbUseProfile.setOnClickListener {
            if (cbUseProfile.isChecked) {
                selectedResumeUri = null
                tvSelectedFile.text = "Using resume from profile"
            } else {
                tvSelectedFile.text = "No file selected"
            }
        }

        btnSubmitApplication.setOnClickListener {
            submitApplication()
        }
    }

    private fun loadJobDetails() {
        progressBar.visibility = View.VISIBLE

        lifecycleScope.launch {
            try {
                val response = apiClient.jobsService.getJobDetails(jobId)
                if (response.success && response.data != null) {
                    job = response.data
                    displayJobInfo(response.data)
                } else {
                    Toast.makeText(
                        this@JobApplicationActivity,
                        "Error loading job details: ${response.message}",
                        Toast.LENGTH_LONG
                    ).show()
                    finish()
                }
            } catch (e: Exception) {
                Toast.makeText(
                    this@JobApplicationActivity,
                    "Error loading job details: ${e.message}",
                    Toast.LENGTH_LONG
                ).show()
                finish()
            } finally {
                progressBar.visibility = View.GONE
            }
        }
    }

    private fun displayJobInfo(job: Job) {
        tvJobTitle.text = job.title
        tvEmployer.text = job.employer
    }

    private fun openFilePicker() {
        val intent = Intent(Intent.ACTION_OPEN_DOCUMENT).apply {
            addCategory(Intent.CATEGORY_OPENABLE)
            type = "*/*"
            putExtra(Intent.EXTRA_MIME_TYPES, arrayOf(
                "application/pdf",
                "application/msword",
                "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
            ))
        }
        getContent.launch(intent)
    }

    private fun displaySelectedFileName(uri: Uri) {
        val cursor = contentResolver.query(uri, null, null, null, null)
        cursor?.use {
            if (it.moveToFirst()) {
                val displayNameIndex = it.getColumnIndex(OpenableColumns.DISPLAY_NAME)
                if (displayNameIndex != -1) {
                    val displayName = it.getString(displayNameIndex)
                    tvSelectedFile.text = displayName
                }
            }
        }
    }

    private fun getResumeFile(): File? {
        selectedResumeUri?.let { uri ->
            try {
                val inputStream = contentResolver.openInputStream(uri)
                val tempFile = File(cacheDir, "resume_temp")
                inputStream?.use { input ->
                    FileOutputStream(tempFile).use { output ->
                        input.copyTo(output)
                    }
                }
                return tempFile
            } catch (e: Exception) {
                Toast.makeText(
                    this,
                    "Error processing file: ${e.message}",
                    Toast.LENGTH_LONG
                ).show()
            }
        }
        return null
    }

    private fun submitApplication() {
        val coverLetter = etCoverLetter.text.toString().trim()
        
        // Validate inputs
        if (coverLetter.isEmpty()) {
            etCoverLetter.error = "Please provide a cover letter"
            etCoverLetter.requestFocus()
            return
        }

        if (!cbUseProfile.isChecked && selectedResumeUri == null) {
            Toast.makeText(
                this,
                "Please upload a resume or use your profile resume",
                Toast.LENGTH_LONG
            ).show()
            return
        }

        // Show progress
        progressBar.visibility = View.VISIBLE
        btnSubmitApplication.isEnabled = false

        lifecycleScope.launch {
            try {
                val resumeFile = if (cbUseProfile.isChecked) null else getResumeFile()
                
                // Call API to submit application
                val response = if (resumeFile != null) {
                    apiClient.jobsService.applyForJobWithResume(
                        jobId = jobId,
                        message = coverLetter,
                        resumeFile = resumeFile
                    )
                } else {
                    apiClient.jobsService.applyForJob(
                        jobId = jobId,
                        message = coverLetter,
                        useProfileResume = true
                    )
                }

                if (response.success) {
                    Toast.makeText(
                        this@JobApplicationActivity,
                        "Application submitted successfully!",
                        Toast.LENGTH_LONG
                    ).show()
                    setResult(RESULT_OK)
                    finish()
                } else {
                    Toast.makeText(
                        this@JobApplicationActivity,
                        "Failed to submit application: ${response.message}",
                        Toast.LENGTH_LONG
                    ).show()
                }
            } catch (e: Exception) {
                Toast.makeText(
                    this@JobApplicationActivity,
                    "Error submitting application: ${e.message}",
                    Toast.LENGTH_LONG
                ).show()
            } finally {
                progressBar.visibility = View.GONE
                btnSubmitApplication.isEnabled = true
            }
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
