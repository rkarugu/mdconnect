package com.mediconnect.app.ui.applications

import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ProgressBar
import android.widget.TextView
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.chip.Chip
import com.google.android.material.chip.ChipGroup
import com.mediconnect.app.R
import com.mediconnect.app.api.models.Application

class ApplicationsFragment : Fragment() {

    private lateinit var applicationsViewModel: ApplicationsViewModel
    private lateinit var recyclerView: RecyclerView
    private lateinit var progressBar: ProgressBar
    private lateinit var loadMoreProgressBar: ProgressBar
    private lateinit var tvNoApplications: TextView
    private lateinit var chipGroup: ChipGroup
    private lateinit var chipAll: Chip
    private lateinit var chipPending: Chip
    private lateinit var chipAccepted: Chip
    private lateinit var chipRejected: Chip

    private var adapter: ApplicationsAdapter? = null

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val factory = ApplicationsViewModelFactory(requireActivity().application)
        applicationsViewModel = ViewModelProvider(this, factory).get(ApplicationsViewModel::class.java)
        val root = inflater.inflate(R.layout.fragment_applications, container, false)

        // Initialize views
        recyclerView = root.findViewById(R.id.recyclerView)
        progressBar = root.findViewById(R.id.progressBar)
        loadMoreProgressBar = root.findViewById(R.id.loadMoreProgressBar)
        tvNoApplications = root.findViewById(R.id.tvNoApplications)
        chipGroup = root.findViewById(R.id.chipGroup)
        chipAll = root.findViewById(R.id.chipAll)
        chipPending = root.findViewById(R.id.chipPending)
        chipAccepted = root.findViewById(R.id.chipAccepted)
        chipRejected = root.findViewById(R.id.chipRejected)

        // Set up RecyclerView
        recyclerView.layoutManager = LinearLayoutManager(context)

        // Set up filter chips
        setupFilterChips()

        // Observe ViewModel
        observeViewModel()

        return root
    }

    private fun setupFilterChips() {
        chipAll.setOnClickListener { applicationsViewModel.loadApplications(null, true) }
        chipPending.setOnClickListener { applicationsViewModel.loadApplications("pending", true) }
        chipAccepted.setOnClickListener { applicationsViewModel.loadApplications("accepted", true) }
        chipRejected.setOnClickListener { applicationsViewModel.loadApplications("rejected", true) }
    }

    private fun observeViewModel() {
        // Observe applications
        applicationsViewModel.applications.observe(viewLifecycleOwner) { applications ->
            if (applications.isEmpty()) {
                recyclerView.visibility = View.GONE
                tvNoApplications.visibility = View.VISIBLE
            } else {
                recyclerView.visibility = View.VISIBLE
                tvNoApplications.visibility = View.GONE
                setupAdapter(applications)
            }
        }

        // Observe loading state
        applicationsViewModel.isLoading.observe(viewLifecycleOwner) { isLoading ->
            // Only show the main loading indicator on initial or refresh loads
            // Use the smaller indicator at the bottom for pagination loads
            if (applicationsViewModel.isInitialLoad) {
                progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
                loadMoreProgressBar.visibility = View.GONE
            } else {
                progressBar.visibility = View.GONE
                loadMoreProgressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
            }
        }

        // Observe errors
        applicationsViewModel.error.observe(viewLifecycleOwner) { errorMessage ->
            errorMessage?.let {
                Toast.makeText(context, it, Toast.LENGTH_LONG).show()
            }
        }
    }

    private fun setupAdapter(applications: List<Application>) {
        if (adapter == null) {
            adapter = ApplicationsAdapter(applications) { application ->
                navigateToApplicationDetails(application)
            }
            recyclerView.adapter = adapter
            
            // Add scroll listener for pagination
            recyclerView.addOnScrollListener(object : RecyclerView.OnScrollListener() {
                override fun onScrolled(recyclerView: RecyclerView, dx: Int, dy: Int) {
                    super.onScrolled(recyclerView, dx, dy)
                    
                    val layoutManager = recyclerView.layoutManager as LinearLayoutManager
                    val visibleItemCount = layoutManager.childCount
                    val totalItemCount = layoutManager.itemCount
                    val firstVisibleItemPosition = layoutManager.findFirstVisibleItemPosition()
                    
                    // Load more when reaching near the end
                    if ((visibleItemCount + firstVisibleItemPosition) >= totalItemCount - 5
                        && firstVisibleItemPosition >= 0
                        && totalItemCount >= 10 // Only load more if we have a decent amount already
                        && applicationsViewModel.isLoading.value != true) {
                        // Load next page with pagination
                        applicationsViewModel.loadApplications(isPagination = true)
                    }
                }
            })
        } else {
            adapter = ApplicationsAdapter(applications) { application ->
                navigateToApplicationDetails(application)
            }
            recyclerView.adapter = adapter
        }
    }

    private fun navigateToApplicationDetails(application: Application) {
        val intent = Intent(requireContext(), ApplicationDetailsActivity::class.java).apply {
            putExtra("APPLICATION_ID", application.id)
        }
        startActivityForResult(intent, REQUEST_APPLICATION_DETAILS)
    }

    override fun onResume() {
        super.onResume()
        // Refresh data when returning to this fragment
        applicationsViewModel.refreshApplications()
    }
    
    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == REQUEST_APPLICATION_DETAILS && resultCode == RESULT_OK) {
            // Handle the result from ApplicationDetailsActivity
            data?.let { intent ->
                val applicationId = intent.getIntExtra("APPLICATION_ID", -1)
                val action = intent.getStringExtra("ACTION")
                
                if (applicationId != -1 && action == "WITHDRAWN") {
                    // Show a confirmation message
                    Toast.makeText(
                        context,
                        getString(R.string.application_withdrawn_success),
                        Toast.LENGTH_SHORT
                    ).show()
                }
            }
            // Refresh the applications list
            applicationsViewModel.refreshApplications()
        }
    }
    
    companion object {
        private const val REQUEST_APPLICATION_DETAILS = 1001
        private const val RESULT_OK = -1
    }
}
