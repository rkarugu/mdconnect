<?php
// app/Http/Controllers/Admin/DashboardController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MedicalWorker;
use App\Models\MedicalSpecialty;
use App\Models\MedicalDocument;
use App\Models\MedicalFacility;
use App\Models\FacilityDocument;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Calculate facilities with pending documents
        $facilitiesWithPendingDocs = MedicalFacility::whereHas('documents', function($query) {
            $query->where('status', 'pending');
        })->count();
        
        // Get pending document counts
        $pendingWorkerDocs = MedicalDocument::where('status', 'pending')->count();
        $pendingFacilityDocs = FacilityDocument::where('status', 'pending')->count();
        $totalPendingDocs = $pendingWorkerDocs + $pendingFacilityDocs;
        
        $data = [
            'totalUsers' => User::count(),
            'totalMedicalWorkers' => MedicalWorker::count(),
            'totalSpecialties' => MedicalSpecialty::count(),
            'pendingDocuments' => $totalPendingDocs,
            'pendingFacilityDocs' => $pendingFacilityDocs,
            'pendingWorkerDocs' => $pendingWorkerDocs,
            'pendingFacilitiesCount' => $facilitiesWithPendingDocs,
            'recentUsers' => User::latest()->take(5)->get(),
            'recentMedicalWorkers' => MedicalWorker::with(['user', 'specialty'])
                ->latest()
                ->take(5)
                ->get(),
            'recentFacilities' => MedicalFacility::latest()->take(5)->get(),
        ];

        return view('admin.dashboard', $data);
    }
}