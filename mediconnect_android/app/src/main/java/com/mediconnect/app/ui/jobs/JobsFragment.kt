package com.mediconnect.app.ui.jobs

import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.EditText
import android.widget.ProgressBar
import android.widget.TextView
import androidx.fragment.app.Fragment
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.mediconnect.app.R
import com.mediconnect.app.api.models.Job

class JobsFragment : Fragment() {

    private lateinit var jobsViewModel: JobsViewModel
    private lateinit var rvJobs: RecyclerView
    private lateinit var progressBar: ProgressBar
    private lateinit var tvNoJobs: TextView
    private lateinit var swipeRefresh: SwipeRefreshLayout
    private lateinit var etSearchJobs: EditText
    private lateinit var btnSearch: Button
    private lateinit var btnFilter: Button

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        jobsViewModel = ViewModelProvider(this).get(JobsViewModel::class.java)
        val root = inflater.inflate(R.layout.fragment_jobs, container, false)
        
        // Initialize views
        rvJobs = root.findViewById(R.id.rvJobs)
        progressBar = root.findViewById(R.id.progressBar)
        tvNoJobs = root.findViewById(R.id.tvNoJobs)
        swipeRefresh = root.findViewById(R.id.swipeRefresh)
        etSearchJobs = root.findViewById(R.id.etSearchJobs)
        btnSearch = root.findViewById(R.id.btnSearch)
        btnFilter = root.findViewById(R.id.btnFilter)
        
        setupRecyclerView()
        setupClickListeners()
        observeViewModel()
        
        // Load data
        jobsViewModel.loadJobs()
        
        return root
    }
    
    private fun setupRecyclerView() {
        rvJobs.layoutManager = LinearLayoutManager(context)
        // Set up adapter for jobs
        val jobsAdapter = JobsAdapter(emptyList()) { jobId ->
            onJobItemClick(jobId)
        }
        rvJobs.adapter = jobsAdapter
    }
    
    private fun setupClickListeners() {
        swipeRefresh.setOnRefreshListener {
            jobsViewModel.loadJobs(true)
        }
        
        btnSearch.setOnClickListener {
            val query = etSearchJobs.text.toString().trim()
            if (query.isNotEmpty()) {
                jobsViewModel.searchJobs(query)
            } else {
                jobsViewModel.loadJobs()
            }
        }
        
        btnFilter.setOnClickListener {
            // TODO: Show filter dialog
        }
    }
    
    private fun observeViewModel() {
        jobsViewModel.jobs.observe(viewLifecycleOwner) { jobs ->
            swipeRefresh.isRefreshing = false
            
            if (jobs.isEmpty()) {
                tvNoJobs.visibility = View.VISIBLE
                rvJobs.visibility = View.GONE
            } else {
                tvNoJobs.visibility = View.GONE
                rvJobs.visibility = View.VISIBLE
                (rvJobs.adapter as JobsAdapter).updateJobs(jobs)
            }
        }
        
        jobsViewModel.isLoading.observe(viewLifecycleOwner) { isLoading ->
            progressBar.visibility = if (isLoading && jobsViewModel.jobs.value.isNullOrEmpty()) 
                View.VISIBLE else View.GONE
        }
        
        jobsViewModel.error.observe(viewLifecycleOwner) { errorMessage ->
            if (errorMessage.isNotEmpty()) {
                // TODO: Show error message
                swipeRefresh.isRefreshing = false
            }
        }
    }
    
    // Navigate to job details
    private fun onJobItemClick(jobId: Int) {
        val intent = Intent(context, JobDetailsActivity::class.java).apply {
            putExtra("JOB_ID", jobId)
        }
        startActivity(intent)
    }
}
