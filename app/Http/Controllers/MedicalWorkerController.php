<?php

namespace App\Http\Controllers;

use App\Models\MedicalWorker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalWorkerController extends Controller
{
    /**
     * Display the application status page for medical workers.
     */
    public function showStatus(Request $request)
    {
        // Check if user is authenticated
        if (Auth::check()) {
            // Try to find a medical worker profile for the authenticated user
            $medicalWorker = MedicalWorker::where('user_id', Auth::id())
                ->orWhere('email', Auth::user()->email)
                ->first();
                
            if ($medicalWorker) {
                return view('medical_workers.status', [
                    'medicalWorker' => $medicalWorker
                ]);
            }
        }
        
        // If not authenticated or no medical worker profile found,
        // show the status lookup form
        return view('medical_workers.status_lookup');
    }
    
    /**
     * Look up an application status by email.
     */
    public function lookupStatus(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        
        $medicalWorker = MedicalWorker::where('email', $request->email)->first();
        
        if (!$medicalWorker) {
            return back()->with('error', 'No application found with that email address.');
        }
        
        return view('medical_workers.status', [
            'medicalWorker' => $medicalWorker
        ]);
    }
}
