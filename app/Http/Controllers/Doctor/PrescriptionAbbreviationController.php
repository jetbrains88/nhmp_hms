<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\PrescriptionAbbreviation;
use Illuminate\Http\Request;

class PrescriptionAbbreviationController extends Controller
{
    /**
     * Show the manage page (Doctor Medical menu → Rx Abbreviations).
     */
    public function index()
    {
        $stats = [
            'total'     => PrescriptionAbbreviation::count(),
            'active'    => PrescriptionAbbreviation::where('is_active', true)->count(),
            'inactive'  => PrescriptionAbbreviation::where('is_active', false)->count(),
            'frequency' => PrescriptionAbbreviation::where('category', 'frequency')->count(),
            'route'     => PrescriptionAbbreviation::where('category', 'route')->count(),
            'timing'    => PrescriptionAbbreviation::where('category', 'timing')->count(),
            'dosage'    => PrescriptionAbbreviation::where('category', 'dosage')->count(),
        ];

        return view('doctor.setup.prescription-abbreviations.index', compact('stats'));
    }

    /**
     * JSON data endpoint for the Alpine.js table.
     */
    public function data(Request $request)
    {
        $query = PrescriptionAbbreviation::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('abbreviation', 'like', "%{$request->search}%")
                  ->orWhere('full_meaning', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $sortField = in_array($request->sort_by, ['abbreviation', 'category', 'doses_per_day', 'is_active', 'created_at'])
            ? $request->sort_by : 'category';
        $sortDir = $request->sort_dir === 'desc' ? 'desc' : 'asc';

        $perPage = in_array((int) $request->per_page, [10, 15, 25, 50, 100])
            ? (int) $request->per_page : 25;

        $abbreviations = $query->orderBy($sortField, $sortDir)
            ->orderBy('abbreviation', 'asc')
            ->paginate($perPage);

        return response()->json($abbreviations);
    }

    /**
     * API endpoint for the prescription form: returns all active abbreviations grouped by category.
     * Used by Alpine.js on the consultation/prescription form to populate the selector and
     * auto-fill the "days" field based on doses_per_day.
     */
    public function forForm()
    {
        $abbreviations = PrescriptionAbbreviation::active()
            ->orderBy('category')
            ->orderBy('abbreviation')
            ->get(['id', 'abbreviation', 'full_meaning', 'category', 'doses_per_day']);

        return response()->json($abbreviations);
    }

    /**
     * Stats for stat cards.
     */
    public function stats()
    {
        return response()->json([
            'total'     => PrescriptionAbbreviation::count(),
            'active'    => PrescriptionAbbreviation::where('is_active', true)->count(),
            'inactive'  => PrescriptionAbbreviation::where('is_active', false)->count(),
            'frequency' => PrescriptionAbbreviation::where('category', 'frequency')->count(),
            'route'     => PrescriptionAbbreviation::where('category', 'route')->count(),
            'timing'    => PrescriptionAbbreviation::where('category', 'timing')->count(),
            'dosage'    => PrescriptionAbbreviation::where('category', 'dosage')->count(),
        ]);
    }

    /**
     * Store a new abbreviation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'abbreviation' => 'required|string|max:20|unique:prescription_abbreviations,abbreviation',
            'full_meaning' => 'required|string|max:500',
            'category'     => 'required|in:frequency,route,timing,dosage,general',
            'doses_per_day'=> 'nullable|integer|min:1|max:24',
            'is_active'    => 'boolean',
        ]);

        $abbr = PrescriptionAbbreviation::create($validated);

        return response()->json([
            'success'      => true,
            'message'      => 'Abbreviation added successfully.',
            'abbreviation' => $abbr,
        ]);
    }

    /**
     * Update an abbreviation.
     */
    public function update(Request $request, PrescriptionAbbreviation $prescriptionAbbreviation)
    {
        $validated = $request->validate([
            'abbreviation' => 'required|string|max:20|unique:prescription_abbreviations,abbreviation,' . $prescriptionAbbreviation->id,
            'full_meaning' => 'required|string|max:500',
            'category'     => 'required|in:frequency,route,timing,dosage,general',
            'doses_per_day'=> 'nullable|integer|min:1|max:24',
            'is_active'    => 'boolean',
        ]);

        $prescriptionAbbreviation->update($validated);

        return response()->json([
            'success'      => true,
            'message'      => 'Abbreviation updated.',
            'abbreviation' => $prescriptionAbbreviation->fresh(),
        ]);
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(PrescriptionAbbreviation $prescriptionAbbreviation)
    {
        $prescriptionAbbreviation->update(['is_active' => !$prescriptionAbbreviation->is_active]);

        return response()->json([
            'success'      => true,
            'message'      => 'Status updated.',
            'abbreviation' => $prescriptionAbbreviation->fresh(),
        ]);
    }

    /**
     * Delete an abbreviation.
     */
    public function destroy(PrescriptionAbbreviation $prescriptionAbbreviation)
    {
        $prescriptionAbbreviation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Abbreviation deleted.',
        ]);
    }
}
