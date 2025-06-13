<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalSpecialty;
use Illuminate\Http\Request;

class MedicalSpecialtyController extends Controller
{
    public function index()
    {
        $specialties = MedicalSpecialty::withCount('medicalWorkers')
            ->latest()
            ->paginate(10);

        return view('admin.medical_specialties.index', compact('specialties'));
    }

    public function create()
    {
        return view('admin.medical_specialties.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:medical_specialties',
            'description' => 'required|string',
            'qualification_requirements' => 'nullable|string',
            'minimum_experience_years' => 'nullable|integer|min:0|max:50',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean'
        ]);

        // Set default values
        $validatedData['is_active'] = $request->has('is_active');
        $validatedData['icon'] = $request->filled('icon') ? 'fa-' . $request->icon : null;

        MedicalSpecialty::create($validatedData);

        return redirect()
            ->route('medical_specialties.index')
            ->with('success', 'Medical specialty created successfully.');
    }

    public function show(MedicalSpecialty $medicalSpecialty)
    {
        $medicalSpecialty->load(['medicalWorkers.user']);
        return view('admin.medical_specialties.show', compact('medicalSpecialty'));
    }

    public function edit(MedicalSpecialty $medicalSpecialty)
    {
        return view('admin.medical_specialties.edit', compact('medicalSpecialty'));
    }

    public function update(Request $request, MedicalSpecialty $medicalSpecialty)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:medical_specialties,name,' . $medicalSpecialty->id,
            'description' => 'required|string',
            'qualification_requirements' => 'nullable|string',
            'minimum_experience_years' => 'nullable|integer|min:0|max:50',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean'
        ]);

        // Set default values
        $validatedData['is_active'] = $request->has('is_active');
        $validatedData['icon'] = $request->filled('icon') ? 'fa-' . $request->icon : null;

        $medicalSpecialty->update($validatedData);

        return redirect()
            ->route('medical_specialties.index')
            ->with('success', 'Medical specialty updated successfully.');
    }

    public function destroy(MedicalSpecialty $medicalSpecialty)
    {
        if ($medicalSpecialty->medicalWorkers()->count() > 0) {
            return back()->with('error', 'Cannot delete specialty that has medical workers assigned.');
        }

        $medicalSpecialty->delete();

        return redirect()
            ->route('medical_specialties.index')
            ->with('success', 'Medical specialty deleted successfully.');
    }
}
