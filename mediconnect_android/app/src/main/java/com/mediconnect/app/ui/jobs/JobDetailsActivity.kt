package com.mediconnect.app.ui.jobs

import android.content.Intent
import android.os.Bundle
import android.view.MenuItem
import android.view.View
import android.widget.Button
import android.widget.ProgressBar
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.appcompat.widget.Toolbar
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.lifecycleScope
import com.mediconnect.app.R
import com.mediconnect.app.api.ApiClient
import com.mediconnect.app.api.models.Job
import com.mediconnect.app.util.SessionManager
import kotlinx.coroutines.launch

class JobDetailsActivity : AppCompatActivity() {

    private lateinit var viewModel: JobsViewModel
    private lateinit var tvJobTitle: TextView
    private lateinit var tvEmployer: TextView
    private lateinit var tvLocation: TextView
    private lateinit var tvSalary: TextView
    private lateinit var tvJobType: TextView
    private lateinit var tvDescription: TextView
    private lateinit var tvRequirements: TextView
    private lateinit var btnApply: Button
    private lateinit var progressBar: ProgressBar
    
    private lateinit var apiClient: ApiClient
    private lateinit var sessionManager: SessionManager
    
    private var jobId: Int = -1
    private var job: Job? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_job_details)
        
        // Set up toolbar
        val toolbar = findViewById<Toolbar>(R.id.toolbar)
        setSupportActionBar(toolbar)
        supportActionBar?.setDisplayHomeAsUpEnabled(true)
        supportActionBar?.setDisplayShowHomeEnabled(true)
        
        // Initialize views
        tvJobTitle = findViewById(R.id.tvJobTitle)
        tvEmployer = findViewById(R.id.tvEmployer)
        tvLocation = findViewById(R.id.tvLocation)
        tvSalary = findViewById(R.id.tvSalary)
        tvJobType = findViewById(R.id.tvJobType)
        tvDescription = findViewById(R.id.tvDescription)
        tvRequirements = findViewById(R.id.tvRequirements)
        btnApply = findViewById(R.id.btnApply)
        progressBar = findViewById(R.id.progressBar)
        
        // Get job ID from intent
        jobId = intent.getIntExtra("JOB_ID", -1)
        if (jobId == -1) {
            Toast.makeText(this, "Error: Job not found", Toast.LENGTH_SHORT).show()
            finish()
            return
        }
        
        // Initialize API client and session manager
        apiClient = ApiClient.getInstance(applicationContext)
        sessionManager = SessionManager(applicationContext)
        
        // Set up ViewModel
        val factory = JobsViewModelFactory(application)
        viewModel = ViewModelProvider(this, factory).get(JobsViewModel::class.java)
        
        // Load job details
        loadJobDetails()
        
        // Set up apply button
        btnApply.setOnClickListener {
            if (job != null) {
                val intent = Intent(this, JobApplicationActivity::class.java).apply {
                    putExtra("JOB_ID", jobId)
                    putExtra("JOB", job)
                }
                startActivity(intent)
            }
        }
    }

    private fun loadJobDetails() {
        progressBar.visibility = View.VISIBLE
        btnApply.isEnabled = false
        
        lifecycleScope.launch {
            try {
                // Use the ViewModel to get job details
                val jobDetails = viewModel.getJobById(jobId)
                
                if (jobDetails != null) {
                    job = jobDetails
                    displayJobDetails(jobDetails)
                } else {
                    // If job couldn't be found
                    Toast.makeText(
                        this@JobDetailsActivity,
                        "Job not found",
                        Toast.LENGTH_SHORT
                    ).show()
                    finish()
                }
            } catch (e: Exception) {
                Toast.makeText(
                    this@JobDetailsActivity,
                    "Error loading job details: ${e.message}",
                    Toast.LENGTH_LONG
                ).show()
                finish()
            } finally {
                progressBar.visibility = View.GONE
                btnApply.isEnabled = true
            }
        }
    }
    

    
    private fun displayJobDetails(job: Job) {
        tvJobTitle.text = job.title
        tvEmployer.text = job.employer
        tvLocation.text = job.location
        tvSalary.text = job.salary
        tvJobType.text = job.type
        tvDescription.text = job.description
        tvRequirements.text = job.requirements
    }
    
    override fun onOptionsItemSelected(item: MenuItem): Boolean {
        if (item.itemId == android.R.id.home) {
            onBackPressed()
            return true
        }
        return super.onOptionsItemSelected(item)
    }
}
