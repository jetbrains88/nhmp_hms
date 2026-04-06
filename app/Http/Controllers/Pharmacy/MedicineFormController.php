<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\MedicineForm;
use Illuminate\Http\Request;

class MedicineFormController extends Controller
{
    public function index()
    {
        return view('pharmacy.medicine_forms.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:medicine_forms,name',
        ]);

        $form = MedicineForm::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Form created successfully.',
                'data' => $form
            ]);
        }

        return redirect()->route('pharmacy.medicine-forms.index')->with('success', 'Form created successfully.');
    }

    public function update(Request $request, MedicineForm $medicineForm)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:medicine_forms,name,' . $medicineForm->id,
        ]);

        $medicineForm->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Form updated successfully.',
                'data' => $medicineForm
            ]);
        }

        return redirect()->route('pharmacy.medicine-forms.index')->with('success', 'Form updated successfully.');
    }

    public function destroy(MedicineForm $medicineForm)
    {
        $medicineForm->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Form deleted successfully.'
            ]);
        }

        return redirect()->route('pharmacy.medicine-forms.index')->with('success', 'Form deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:medicine_forms,id'
        ]);

        MedicineForm::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => count($validated['ids']) . ' forms purged successfully.'
        ]);
    }

    public function stats()
    {
        $stats = [
            'total' => MedicineForm::count()
        ];
        return response()->json($stats);
    }

    public function data(Request $request)
    {
        $query = MedicineForm::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);

        $perPage = $request->get('per_page', 10);
        $forms = $query->paginate($perPage);

        return response()->json($forms);
    }
}
