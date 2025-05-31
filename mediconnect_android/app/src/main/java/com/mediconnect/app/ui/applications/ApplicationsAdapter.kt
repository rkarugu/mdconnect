package com.mediconnect.app.ui.applications

import android.text.format.DateUtils
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.mediconnect.app.R
import com.mediconnect.app.api.models.Application
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

class ApplicationsAdapter(
    private val applications: List<Application>,
    private val onItemClickListener: (Application) -> Unit
) : RecyclerView.Adapter<ApplicationsAdapter.ApplicationViewHolder>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ApplicationViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_application, parent, false)
        return ApplicationViewHolder(view)
    }

    override fun onBindViewHolder(holder: ApplicationViewHolder, position: Int) {
        holder.bind(applications[position])
    }

    override fun getItemCount(): Int = applications.size

    inner class ApplicationViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val tvJobTitle: TextView = itemView.findViewById(R.id.tvJobTitle)
        private val tvEmployer: TextView = itemView.findViewById(R.id.tvEmployer)
        private val tvStatus: TextView = itemView.findViewById(R.id.tvStatus)
        private val tvAppliedDate: TextView = itemView.findViewById(R.id.tvAppliedDate)
        private val btnViewDetails: Button = itemView.findViewById(R.id.btnViewDetails)

        fun bind(application: Application) {
            application.job?.let { job ->
                tvJobTitle.text = job.title
                tvEmployer.text = job.employer
            } ?: run {
                tvJobTitle.text = "Job #${application.jobId}"
                tvEmployer.text = "Unknown Employer"
            }

            // Set status and background
            tvStatus.text = application.status.capitalize()
            val backgroundRes = when (application.status.lowercase()) {
                "pending" -> R.drawable.status_pending_bg
                "accepted" -> R.drawable.status_accepted_bg
                "rejected" -> R.drawable.status_rejected_bg
                else -> R.drawable.status_pending_bg
            }
            tvStatus.setBackgroundResource(backgroundRes)

            // Format date
            val date = parseDate(application.createdAt)
            tvAppliedDate.text = if (date != null) {
                val timeAgo = DateUtils.getRelativeTimeSpanString(
                    date.time,
                    System.currentTimeMillis(),
                    DateUtils.DAY_IN_MILLIS
                )
                "Applied: $timeAgo"
            } else {
                "Applied: ${application.createdAt}"
            }

            // Set click listener
            btnViewDetails.setOnClickListener {
                onItemClickListener(application)
            }
            
            // Make the whole item clickable
            itemView.setOnClickListener {
                onItemClickListener(application)
            }
        }

        private fun parseDate(dateString: String): Date? {
            return try {
                // Try to parse the date in standard ISO format
                SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSSXXX", Locale.getDefault())
                    .parse(dateString)
            } catch (e: Exception) {
                try {
                    // Fallback to basic format without timezone
                    SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
                        .parse(dateString)
                } catch (e: Exception) {
                    null
                }
            }
        }
    }

    // Extension function to capitalize the first letter
    private fun String.capitalize(): String {
        return this.replaceFirstChar { 
            if (it.isLowerCase()) it.titlecase(Locale.getDefault()) else it.toString() 
        }
    }
}
