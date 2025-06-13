<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LocumJobRequest;
use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // We should only show open jobs to medical workers
        $query = LocumJobRequest::query()->with('facility')->where('status', 'open');

        // Filter by specialization
        if ($request->has('specialization') && $request->specialization) {
            $query->where('specialization', 'like', '%' . $request->specialization . '%');
        }

        // Filter by location
        if ($request->has('location') && $request->location) {
            $query->whereHas('facility', function($q) use ($request) {
                $q->where('city', 'like', '%' . $request->location . '%')
                  ->orWhere('state', 'like', '%' . $request->location . '%')
                  ->orWhere('country', 'like', '%' . $request->location . '%');
            });
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Sort by options
        $sortField = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        $jobs = $query->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Jobs retrieved successfully.',
            'data' => $jobs
        ]);
    }
}
