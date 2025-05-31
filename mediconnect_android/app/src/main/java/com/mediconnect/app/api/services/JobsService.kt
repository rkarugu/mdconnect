package com.mediconnect.app.api.services

import com.mediconnect.app.api.ApiConfig
import com.mediconnect.app.api.models.ApiResponse
import com.mediconnect.app.api.models.Application
import com.mediconnect.app.api.models.Job
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.Multipart
import retrofit2.http.POST
import retrofit2.http.Part
import retrofit2.http.Path
import retrofit2.http.Query
import java.io.File

/**
 * Retrofit service interface for job-related API endpoints
 */
interface JobsService {
    
    @GET(ApiConfig.Endpoints.JOBS)
    suspend fun getJobs(
        @Query("page") page: Int = 1,
        @Query("search") search: String? = null,
        @Query("location") location: String? = null,
        @Query("type") type: String? = null
    ): ApiResponse<List<Job>>
    
    @GET(ApiConfig.Endpoints.JOB_DETAILS)
    suspend fun getJobDetails(@Path("id") jobId: Int): ApiResponse<Job>
    
    @POST(ApiConfig.Endpoints.APPLY_JOB)
    suspend fun applyForJob(
        @Path("id") jobId: Int,
        @Body application: Map<String, String>
    ): ApiResponse<Application>
    
    /**
     * Apply for a job with a resume file upload
     */
    @Multipart
    @POST(ApiConfig.Endpoints.APPLY_JOB)
    suspend fun applyForJobWithResume(
        @Path("id") jobId: Int,
        @Part("message") message: RequestBody,
        @Part resumeFile: MultipartBody.Part
    ): ApiResponse<Application>
    
    /**
     * Overloaded method for convenience to apply with a file
     */
    suspend fun applyForJobWithResume(
        jobId: Int,
        message: String,
        resumeFile: File
    ): ApiResponse<Application> {
        val messageRequestBody = RequestBody.create("text/plain".toMediaTypeOrNull(), message)
        
        val fileRequestBody = RequestBody.create(
            "application/octet-stream".toMediaTypeOrNull(),
            resumeFile
        )
        
        val filePart = MultipartBody.Part.createFormData(
            "resume", 
            resumeFile.name, 
            fileRequestBody
        )
        
        return applyForJobWithResume(jobId, messageRequestBody, filePart)
    }
    
    /**
     * Apply for a job using the resume from profile
     */
    suspend fun applyForJob(
        jobId: Int,
        message: String,
        useProfileResume: Boolean
    ): ApiResponse<Application> {
        val application = mapOf(
            "message" to message,
            "use_profile_resume" to useProfileResume.toString()
        )
        return applyForJob(jobId, application)
    }
    
    @GET(ApiConfig.Endpoints.APPLICATIONS)
    suspend fun getMyApplications(
        @Query("page") page: Int = 1,
        @Query("status") status: String? = null
    ): ApiResponse<List<Application>>
    
    @GET(ApiConfig.Endpoints.APPLICATION_DETAILS)
    suspend fun getApplicationDetails(@Path("id") applicationId: Int): ApiResponse<Application>
    
    /**
     * Withdraw a job application
     */
    @POST(ApiConfig.Endpoints.WITHDRAW_APPLICATION)
    suspend fun withdrawApplication(@Path("id") applicationId: Int): ApiResponse<Any>
}
