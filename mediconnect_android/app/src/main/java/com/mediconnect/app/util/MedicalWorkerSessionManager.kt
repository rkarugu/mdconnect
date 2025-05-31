package com.mediconnect.app.util

import android.content.Context
import android.content.SharedPreferences
import com.google.gson.Gson
import com.mediconnect.app.api.models.MedicalWorker

/**
 * Utility class to manage medical worker session data including authentication tokens
 * and medical worker information using SharedPreferences for secure storage
 */
class MedicalWorkerSessionManager(context: Context) {
    
    private val sharedPreferences: SharedPreferences = context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE)
    private val editor: SharedPreferences.Editor = sharedPreferences.edit()
    private val gson = Gson()
    
    /**
     * Save authentication token
     */
    fun saveAuthToken(token: String) {
        editor.putString(KEY_AUTH_TOKEN, token)
        editor.apply()
    }
    
    /**
     * Get stored authentication token
     */
    fun getAuthToken(): String? {
        return sharedPreferences.getString(KEY_AUTH_TOKEN, null)
    }
    
    /**
     * Save medical worker information
     */
    fun saveMedicalWorker(medicalWorker: MedicalWorker) {
        val medicalWorkerJson = gson.toJson(medicalWorker)
        editor.putString(KEY_MEDICAL_WORKER, medicalWorkerJson)
        editor.apply()
    }
    
    /**
     * Get stored medical worker information
     */
    fun getMedicalWorker(): MedicalWorker? {
        val medicalWorkerJson = sharedPreferences.getString(KEY_MEDICAL_WORKER, null)
        return if (medicalWorkerJson != null) {
            gson.fromJson(medicalWorkerJson, MedicalWorker::class.java)
        } else {
            null
        }
    }
    
    /**
     * Check if medical worker is logged in
     */
    fun isLoggedIn(): Boolean {
        return getAuthToken() != null
    }
    
    /**
     * Clear session data (logout)
     */
    fun clearSession() {
        editor.clear()
        editor.apply()
    }
    
    companion object {
        private const val PREF_NAME = "MediConnectMedicalWorkerPrefs"
        private const val KEY_AUTH_TOKEN = "auth_token"
        private const val KEY_MEDICAL_WORKER = "medical_worker"
    }
}
