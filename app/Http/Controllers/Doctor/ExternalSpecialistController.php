<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\ExternalSpecialist;
use App\Models\MedicalSpecialty;
use Illuminate\Http\Request;

class ExternalSpecialistController extends Controller
{
    public function index()
    {
        $branchId = session('current_branch_id');

        $stats = [
            'total'    => ExternalSpecialist::where('branch_id', $branchId)->count(),
            'active'   => ExternalSpecialist::where('branch_id', $branchId)->where('is_active', true)->count(),
            'inactive' => ExternalSpecialist::where('branch_id', $branchId)->where('is_active', false)->count(),
        ];

        $specialtiesList = MedicalSpecialty::where('is_active', true)->orderBy('name')->get();

        return view('doctor.setup.physicians.index', compact('stats', 'branchId', 'specialtiesList'));
    }

    public function data(Request $request)
    {
        $branchId = session('current_branch_id');

        $query = ExternalSpecialist::with('medicalSpecialty')->where('branch_id', $branchId);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('clinic_hospital', 'like', "%{$request->search}%")
                  ->orWhereHas('medicalSpecialty', function($sq) use ($request) {
                      $sq->where('name', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('medical_specialty_id')) {
            $query->where('medical_specialty_id', $request->medical_specialty_id);
        }

        $sortField = in_array($request->sort_by, ['name', 'is_active', 'created_at'])
            ? $request->sort_by : 'name';
        $sortOrder = $request->sort_dir === 'desc' ? 'desc' : 'asc';

        $perPage = in_array((int) $request->per_page, [10, 15, 25, 50, 100])
            ? (int) $request->per_page : 15;

        $physicians = $query->orderBy($sortField, $sortOrder)->paginate($perPage);

        return response()->json($physicians);
    }

    public function stats()
    {
        $branchId = session('current_branch_id');

        $specialtyCount = ExternalSpecialist::where('branch_id', $branchId)
            ->whereNotNull('medical_specialty_id')
            ->distinct('medical_specialty_id')->count('medical_specialty_id');

        return response()->json([
            'total'       => ExternalSpecialist::where('branch_id', $branchId)->count(),
            'active'      => ExternalSpecialist::where('branch_id', $branchId)->where('is_active', true)->count(),
            'inactive'    => ExternalSpecialist::where('branch_id', $branchId)->where('is_active', false)->count(),
            'specialties' => $specialtyCount,
        ]);
    }

    public function store(Request $request)
    {
        $branchId = session('current_branch_id');

        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'medical_specialty_id' => 'required|exists:medical_specialties,id',
            'is_active'            => 'boolean',
        ]);

        $specialist = ExternalSpecialist::create(array_merge($validated, [
            'branch_id'  => $branchId,
            'created_by' => auth()->id(),
        ]));

        return response()->json([
            'success'    => true,
            'message'    => 'Physician added successfully.',
            'specialist' => $specialist->load('medicalSpecialty'),
        ]);
    }

    public function update(Request $request, ExternalSpecialist $externalSpecialist)
    {
        if ($externalSpecialist->branch_id !== (int) session('current_branch_id')) {
            abort(403, 'Unauthorized branch access.');
        }

        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'medical_specialty_id' => 'required|exists:medical_specialties,id',
            'is_active'            => 'boolean',
        ]);

        $externalSpecialist->update(array_merge($validated, [
            'updated_by' => auth()->id(),
        ]));

        return response()->json([
            'success'    => true,
            'message'    => 'Physician updated successfully.',
            'specialist' => $externalSpecialist->fresh('medicalSpecialty'),
        ]);
    }

    public function toggleStatus(ExternalSpecialist $externalSpecialist)
    {
        $externalSpecialist->update(['is_active' => !$externalSpecialist->is_active]);

        return response()->json([
            'success'    => true,
            'message'    => 'Status updated.',
            'specialist' => $externalSpecialist->fresh('medicalSpecialty'),
        ]);
    }

    public function destroy(ExternalSpecialist $externalSpecialist)
    {
        if ($externalSpecialist->branch_id !== (int) session('current_branch_id')) {
            abort(403, 'Unauthorized branch access.');
        }

        $externalSpecialist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Physician deleted successfully.',
        ]);
    }
}
