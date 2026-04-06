<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\MedicalSpecialty;
use Illuminate\Http\Request;

class ExternalSpecialistController extends Controller
{
    /**
     * Display the index page for medical specialties.
     * Repurposed from Physician Registry.
     */
    public function index()
    {
        $stats = [
            'total'    => MedicalSpecialty::count(),
            'active'   => MedicalSpecialty::where('is_active', true)->count(),
            'inactive' => MedicalSpecialty::where('is_active', false)->count(),
        ];

        return view('doctor.setup.physicians.index', compact('stats'));
    }

    /**
     * Get medical specialties data for the table.
     */
    public function data(Request $request)
    {
        $query = MedicalSpecialty::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $sortField = in_array($request->sort_by, ['name', 'is_active', 'created_at'])
            ? $request->sort_by : 'name';
        $sortOrder = $request->sort_dir === 'desc' ? 'desc' : 'asc';

        $perPage = in_array((int) $request->per_page, [10, 15, 25, 50, 100])
            ? (int) $request->per_page : 15;

        $specialties = $query->orderBy($sortField, $sortOrder)->paginate($perPage);

        return response()->json($specialties);
    }

    /**
     * Get stats for the dashboard cards.
     */
    public function stats()
    {
        return response()->json([
            'total'       => MedicalSpecialty::count(),
            'active'      => MedicalSpecialty::where('is_active', true)->count(),
            'inactive'    => MedicalSpecialty::where('is_active', false)->count(),
            'specialties' => MedicalSpecialty::where('is_active', true)->count(), // Same as active for this context
        ]);
    }

    /**
     * Store a new medical specialty.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:medical_specialties,name',
            'is_active' => 'boolean',
        ]);

        $specialty = MedicalSpecialty::create($validated);

        return response()->json([
            'success'   => true,
            'message'   => 'Medical Specialty added successfully.',
            'specialty' => $specialty,
        ]);
    }

    /**
     * Update an existing medical specialty.
     */
    public function update(Request $request, MedicalSpecialty $externalSpecialist)
    {
        // Note: The parameter is still named $externalSpecialist due to route binding, 
        // but it now receives a MedicalSpecialty model instance.
        
        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:medical_specialties,name,' . $externalSpecialist->id,
            'is_active' => 'boolean',
        ]);

        $externalSpecialist->update($validated);

        return response()->json([
            'success'   => true,
            'message'   => 'Medical Specialty updated successfully.',
            'specialty' => $externalSpecialist,
        ]);
    }

    /**
     * Toggle the active status of a specialty.
     */
    public function toggleStatus(MedicalSpecialty $externalSpecialist)
    {
        $externalSpecialist->update(['is_active' => !$externalSpecialist->is_active]);

        return response()->json([
            'success'   => true,
            'message'   => 'Status updated.',
            'specialty' => $externalSpecialist,
        ]);
    }

    /**
     * Delete a medical specialty.
     */
    public function destroy(MedicalSpecialty $externalSpecialist)
    {
        $externalSpecialist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Medical Specialty deleted successfully.',
        ]);
    }
}
