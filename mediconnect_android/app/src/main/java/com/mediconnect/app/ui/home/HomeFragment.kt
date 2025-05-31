package com.mediconnect.app.ui.home

import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.ProgressBar
import android.widget.TextView
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.lifecycle.ViewModelProvider
import androidx.navigation.fragment.findNavController
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.mediconnect.app.R
import com.mediconnect.app.api.models.Job
import com.mediconnect.app.ui.jobs.JobDetailsActivity

class HomeFragment : Fragment() {

    private lateinit var homeViewModel: HomeViewModel
    private lateinit var rvRecentJobs: RecyclerView
    private lateinit var progressBar: ProgressBar
    private lateinit var btnFindJobs: Button
    private lateinit var tvNoJobs: TextView
    private lateinit var adapter: HomeAdapter

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val factory = HomeViewModelFactory(requireActivity().application)
        homeViewModel = ViewModelProvider(this, factory).get(HomeViewModel::class.java)
        val root = inflater.inflate(R.layout.fragment_home, container, false)
        
        // Initialize views
        rvRecentJobs = root.findViewById(R.id.rvRecentJobs)
        progressBar = root.findViewById(R.id.progressBar)
        btnFindJobs = root.findViewById(R.id.btnFindJobs)
        tvNoJobs = root.findViewById(R.id.tvNoJobs)
        
        setupRecyclerView()
        setupClickListeners()
        observeViewModel()
        
        // Load data
        homeViewModel.loadRecentJobs()
        
        return root
    }
    
    private fun setupRecyclerView() {
        adapter = HomeAdapter(emptyList()) { jobId ->
            onJobItemClick(jobId)
        }
        rvRecentJobs.layoutManager = LinearLayoutManager(context)
        rvRecentJobs.adapter = adapter
    }
    
    private fun setupClickListeners() {
        btnFindJobs.setOnClickListener {
            // Navigate to jobs fragment
            findNavController().navigate(R.id.nav_jobs)
        }
    }
    
    private fun observeViewModel() {
        homeViewModel.recentJobs.observe(viewLifecycleOwner) { jobs ->
            adapter.updateJobs(jobs)
            tvNoJobs.visibility = if (jobs.isEmpty()) View.VISIBLE else View.GONE
            rvRecentJobs.visibility = if (jobs.isEmpty()) View.GONE else View.VISIBLE
        }
        
        homeViewModel.isLoading.observe(viewLifecycleOwner) { isLoading ->
            progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
            if (isLoading) {
                tvNoJobs.visibility = View.GONE
            }
        }
        
        homeViewModel.error.observe(viewLifecycleOwner) { errorMessage ->
            if (errorMessage.isNotEmpty()) {
                Toast.makeText(context, errorMessage, Toast.LENGTH_LONG).show()
            }
        }
    }
    
    // TODO: Create job item click listener to navigate to job details
    private fun onJobItemClick(jobId: Int) {
        val intent = Intent(context, JobDetailsActivity::class.java).apply {
            putExtra("JOB_ID", jobId)
        }
        startActivity(intent)
    }
}
