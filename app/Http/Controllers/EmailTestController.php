<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\TestEmail;
use App\Mail\WelcomeEmail;
use App\Models\User;

class EmailTestController extends Controller
{
    /**
     * Show the email test form
     */
    public function showTestForm()
    {
        return view('admin.email-test');
    }

    /**
     * Send a test email
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:test,welcome'
        ]);

        $recipientEmail = $request->input('email');
        $emailType = $request->input('type', 'test');
        
        try {
            if ($emailType === 'welcome') {
                // For welcome email testing, we'll use the authenticated user
                // or find the first user in the system if needed
                $user = Auth::user() ?? User::first();
                
                if (!$user) {
                    return back()->with('error', 'No user found to send welcome email');
                }
                
                Mail::to($recipientEmail)->send(new WelcomeEmail($user));
                return back()->with('success', 'Welcome email sent successfully to ' . $recipientEmail);
            } else {
                // Standard test email
                Mail::to($recipientEmail)->send(new TestEmail());
                return back()->with('success', 'Test email sent successfully to ' . $recipientEmail);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
}
