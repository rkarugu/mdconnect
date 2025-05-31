package com.mediconnect.app.util

import android.content.Context
import android.content.SharedPreferences
import com.google.gson.Gson
import com.mediconnect.app.api.models.User

/**
 * Utility class to manage user session data including authentication tokens
 * and user information using SharedPreferences for secure storage
 */
class SessionManager(context: Context) {
    
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
     * Save user information
     */
    fun saveUser(user: User) {
        val userJson = gson.toJson(user)
        editor.putString(KEY_USER, userJson)
        editor.apply()
    }
    
    /**
     * Get stored user information
     */
    fun getUser(): User? {
        val userJson = sharedPreferences.getString(KEY_USER, null)
        return if (userJson != null) {
            gson.fromJson(userJson, User::class.java)
        } else {
            null
        }
    }
    
    /**
     * Check if user is logged in
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
        private const val PREF_NAME = "MediConnectPrefs"
        private const val KEY_AUTH_TOKEN = "auth_token"
        private const val KEY_USER = "user"
    }
}
