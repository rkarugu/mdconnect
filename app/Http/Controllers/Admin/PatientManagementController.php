<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PatientManagementController extends Controller
{
    /**
     * Display the patient management dashboard
     */
    public function index()
    {
        // Get dashboard statistics
        $stats = [
            'total_patients' => Patient::count(),
            'active_patients' => Patient::whereNotNull('last_login_at')
                ->where('last_login_at', '>=', Carbon::now()->subDays(30))
                ->count(),
            'new_patients_today' => Patient::whereDate('created_at', Carbon::today())->count(),
            'verified_patients' => Patient::whereNotNull('email_verified_at')->count(),
            'unverified_patients' => Patient::whereNull('email_verified_at')->count(),
        ];

        // Get recent patients
        $recent_patients = Patient::latest()
            ->take(10)
            ->get();

        // Get patients by gender for chart
        $gender_stats = Patient::selectRaw('gender, COUNT(*) as count')
            ->whereNotNull('gender')
            ->groupBy('gender')
            ->get();

        // Get patients by blood type for chart
        $blood_type_stats = Patient::selectRaw('blood_type, COUNT(*) as count')
            ->whereNotNull('blood_type')
            ->groupBy('blood_type')
            ->get();

        // Get registration trends (last 30 days)
        $registration_trends = Patient::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.patients.dashboard', compact(
            'stats', 
            'recent_patients', 
            'gender_stats', 
            'blood_type_stats',
            'registration_trends'
        ));
    }

    /**
     * Display a listing of all patients
     */
    public function list(Request $request)
    {
        $query = Patient::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Filter by verification status
        if ($request->has('verified') && $request->verified !== '') {
            if ($request->verified == '1') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Filter by gender
        if ($request->has('gender') && !empty($request->gender)) {
            $query->where('gender', $request->gender);
        }

        // Filter by blood type
        if ($request->has('blood_type') && !empty($request->blood_type)) {
            $query->where('blood_type', $request->blood_type);
        }

        // Sort options
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $patients = $query->paginate(20);

        return view('admin.patients.list', compact('patients'));
    }

    /**
     * Show the form for creating a new patient
     */
    public function create()
    {
        return view('admin.patients.create');
    }

    /**
     * Store a newly created patient
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:patients',
            'phone' => 'required|string|max:20|unique:patients',
            'password' => 'required|string|min:8|confirmed',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female,Other,Prefer not to say',
            'blood_type' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'medical_conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $patient = Patient::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'blood_type' => $request->blood_type,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'medical_conditions' => $request->medical_conditions,
                'allergies' => $request->allergies,
                'email_verified_at' => $request->has('verified') ? now() : null,
            ]);

            return redirect()->route('admin.patients.show', $patient->id)
                ->with('success', 'Patient created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create patient: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified patient
     */
    public function show(Patient $patient)
    {
        // Load additional relationships if needed
        $patient->load([
            // Add relationships here when available
            // 'appointments', 'medical_records', 'payments'
        ]);

        return view('admin.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified patient
     */
    public function edit(Patient $patient)
    {
        return view('admin.patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient
     */
    public function update(Request $request, Patient $patient)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:patients,email,' . $patient->id,
            'phone' => 'required|string|max:20|unique:patients,phone,' . $patient->id,
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female,Other,Prefer not to say',
            'blood_type' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'medical_conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $patient->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'blood_type' => $request->blood_type,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'medical_conditions' => $request->medical_conditions,
                'allergies' => $request->allergies,
            ]);

            // Handle email verification status
            if ($request->has('verified')) {
                if ($request->verified && !$patient->email_verified_at) {
                    $patient->update(['email_verified_at' => now()]);
                } elseif (!$request->verified && $patient->email_verified_at) {
                    $patient->update(['email_verified_at' => null]);
                }
            }

            return redirect()->route('admin.patients.show', $patient->id)
                ->with('success', 'Patient updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update patient: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified patient
     */
    public function destroy(Patient $patient)
    {
        try {
            $patient->delete();

            return redirect()->route('admin.patients.list')
                ->with('success', 'Patient deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete patient: ' . $e->getMessage());
        }
    }

    /**
     * Toggle patient verification status
     */
    public function toggleVerification(Patient $patient)
    {
        try {
            if ($patient->email_verified_at) {
                $patient->update(['email_verified_at' => null]);
                $message = 'Patient verification removed successfully!';
            } else {
                $patient->update(['email_verified_at' => now()]);
                $message = 'Patient verified successfully!';
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update verification status: ' . $e->getMessage());
        }
    }

    /**
     * Export patients data
     */
    public function export(Request $request)
    {
        // This would implement CSV/Excel export functionality
        // For now, return JSON data
        $patients = Patient::all();
        
        return response()->json($patients);
    }

    /**
     * Get patient analytics data
     */
    public function analytics()
    {
        $analytics = [
            'total_patients' => Patient::count(),
            'verified_patients' => Patient::whereNotNull('email_verified_at')->count(),
            'new_this_month' => Patient::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            'active_last_30_days' => Patient::whereNotNull('last_login_at')
                ->where('last_login_at', '>=', Carbon::now()->subDays(30))
                ->count(),
            'patients_with_pictures' => Patient::whereNotNull('profile_picture')->count(),
            'patients_with_conditions' => Patient::whereNotNull('medical_conditions')->count(),
            'patients_with_allergies' => Patient::whereNotNull('allergies')->count(),
            'patients_with_emergency_contacts' => Patient::whereNotNull('emergency_contact_name')->count(),
            'gender_distribution' => Patient::selectRaw('gender, COUNT(*) as count')
                ->whereNotNull('gender')
                ->groupBy('gender')
                ->get(),
            'blood_type_distribution' => Patient::selectRaw('blood_type, COUNT(*) as count')
                ->whereNotNull('blood_type')
                ->groupBy('blood_type')
                ->get(),
            'age_groups' => Patient::selectRaw('
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18 THEN "Under 18"
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 18 AND 30 THEN "18-30"
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 31 AND 50 THEN "31-50"
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 51 AND 70 THEN "51-70"
                    ELSE "Over 70"
                END as age_group,
                COUNT(*) as count
            ')
                ->whereNotNull('date_of_birth')
                ->groupBy('age_group')
                ->get(),
            'registration_trends' => Patient::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        return view('admin.patients.analytics', compact('analytics'));
    }
}
