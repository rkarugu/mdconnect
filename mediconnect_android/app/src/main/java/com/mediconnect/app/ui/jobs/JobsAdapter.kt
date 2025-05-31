package com.mediconnect.app.ui.jobs

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.mediconnect.app.R
import com.mediconnect.app.api.models.Job

class JobsAdapter(
    private var jobs: List<Job>,
    private val onItemClick: (Int) -> Unit
) : RecyclerView.Adapter<JobsAdapter.JobViewHolder>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): JobViewHolder {
        val view = LayoutInflater.from(parent.context).inflate(R.layout.item_job, parent, false)
        return JobViewHolder(view)
    }

    override fun onBindViewHolder(holder: JobViewHolder, position: Int) {
        val job = jobs[position]
        holder.bind(job)
    }

    override fun getItemCount(): Int = jobs.size

    fun updateJobs(newJobs: List<Job>) {
        this.jobs = newJobs
        notifyDataSetChanged()
    }

    inner class JobViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val tvJobTitle: TextView = itemView.findViewById(R.id.tvJobTitle)
        private val tvEmployer: TextView = itemView.findViewById(R.id.tvEmployer)
        private val tvLocation: TextView = itemView.findViewById(R.id.tvLocation)
        private val tvSalary: TextView = itemView.findViewById(R.id.tvSalary)
        private val tvJobType: TextView = itemView.findViewById(R.id.tvJobType)

        init {
            itemView.setOnClickListener {
                val position = adapterPosition
                if (position != RecyclerView.NO_POSITION) {
                    onItemClick(jobs[position].id)
                }
            }
        }

        fun bind(job: Job) {
            tvJobTitle.text = job.title
            tvEmployer.text = job.employer
            tvLocation.text = job.location
            tvSalary.text = job.salary
            tvJobType.text = job.type
        }
    }
}
