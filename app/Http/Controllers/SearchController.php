<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Medicine;
use App\Models\Visit;
use App\Models\LabOrder;
use App\Models\Prescription;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Global search across modules
     */
    public function global(Request $request)
    {
        $query = $request->get('q');
        $branchId = session('current_branch_id');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];

        // Search patients
        if (auth()->user()->can('view_patients')) {
            $patients = Patient::where('branch_id', $branchId)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('cnic', 'LIKE', "%{$query}%")
                        ->orWhere('emrn', 'LIKE', "%{$query}%")
                        ->orWhere('phone', 'LIKE', "%{$query}%");
                })
                ->limit(5)
                ->get()
                ->map(function ($patient) {
                    return [
                        'type' => 'patient',
                        'id' => $patient->id,
                        'title' => $patient->name,
                        'subtitle' => $patient->emrn . ' | ' . $patient->cnic,
                        'url' => auth()->user()->hasRole('doctor') 
                                    ? route('doctor.patients.history', $patient) 
                                    : route('reception.patients.show', $patient),
                        'icon' => 'user',
                    ];
                });

            $results = array_merge($results, $patients->toArray());
        }

        // Search medicines
        if (auth()->user()->can('view_medicines')) {
            $medicines = Medicine::where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                    ->orWhere('is_global', true);
            })
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('generic_name', 'LIKE', "%{$query}%")
                        ->orWhere('brand', 'LIKE', "%{$query}%");
                })
                ->limit(5)
                ->get()
                ->map(function ($medicine) {
                    return [
                        'type' => 'medicine',
                        'id' => $medicine->id,
                        'title' => $medicine->name,
                        'subtitle' => $medicine->generic_name . ' | ' . $medicine->manufacturer,
                        'url' => route('pharmacy.medicines.show', $medicine),
                        'icon' => 'pill',
                    ];
                });

            $results = array_merge($results, $medicines->toArray());
        }

        // Search visits
        if (auth()->user()->can('view_visits')) {
            $visits = Visit::with('patient')
                ->where('branch_id', $branchId)
                ->whereHas('patient', function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('emrn', 'LIKE', "%{$query}%");
                })
                ->orWhere('queue_token', 'LIKE', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function ($visit) {
                    return [
                        'type' => 'visit',
                        'id' => $visit->id,
                        'title' => 'Visit: ' . $visit->queue_token,
                        'subtitle' => $visit->patient->name . ' | ' . $visit->status,
                        'url' => auth()->user()->hasRole('doctor')
                                    ? route('doctor.consultancy.show', $visit)
                                    : route('reception.visits.show', $visit),
                        'icon' => 'clipboard',
                    ];
                });

            $results = array_merge($results, $visits->toArray());
        }

        // Search lab orders
        if (auth()->user()->can('view_lab_reports')) {
            $labOrders = LabOrder::with('patient')
                ->where('branch_id', $branchId)
                ->where(function ($q) use ($query) {
                    $q->where('lab_number', 'LIKE', "%{$query}%")
                        ->orWhereHas('patient', function ($pq) use ($query) {
                            $pq->where('name', 'LIKE', "%{$query}%");
                        });
                })
                ->limit(5)
                ->get()
                ->map(function ($labOrder) {
                    return [
                        'type' => 'lab',
                        'id' => $labOrder->id,
                        'title' => 'Lab: ' . $labOrder->lab_number,
                        'subtitle' => $labOrder->patient->name . ' | ' . $labOrder->status,
                        'url' => route('lab.orders.show', $labOrder),
                        'icon' => 'flask',
                    ];
                });

            $results = array_merge($results, $labOrders->toArray());
        }

        return response()->json($results);
    }

    /**
     * Quick patient search for forms
     */
    public function patients(Request $request)
    {
        $query = $request->get('q');
        $branchId = session('current_branch_id');

        $patients = Patient::where('branch_id', $branchId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('cnic', 'LIKE', "%{$query}%")
                    ->orWhere('emrn', 'LIKE', "%{$query}%")
                    ->orWhere('phone', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'cnic', 'emrn', 'phone', 'dob', 'gender']);

        return response()->json($patients);
    }

    /**
     * Quick medicine search for forms
     */
    public function medicines(Request $request)
    {
        $query = $request->get('q');
        $branchId = session('current_branch_id');

        $medicines = Medicine::where(function ($q) use ($branchId) {
            $q->where('branch_id', $branchId)
                ->orWhere('is_global', true);
        })
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('generic_name', 'LIKE', "%{$query}%")
                    ->orWhere('brand', 'LIKE', "%{$query}%");
            })
            ->with(['category', 'batches' => function ($b) use ($branchId) {
                $b->where('branch_id', $branchId)
                    ->where('remaining_quantity', '>', 0);
            }])
            ->limit(10)
            ->get();

        return response()->json($medicines);
    }
}
