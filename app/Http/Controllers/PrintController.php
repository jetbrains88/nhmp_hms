<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use App\Models\LabOrder;
use App\Models\Prescription;
use App\Models\Visit;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintController extends Controller
{
    /**
     * Print/download a prescription PDF.
     * Called by route: print.prescription
     */
    public function prescription(Prescription $prescription)
    {
        $diagnosisId = $prescription->diagnosis_id;
        $diagnosis = Diagnosis::with(['visit.patient', 'prescriptions.medicine'])->findOrFail($diagnosisId);

        $pdf = Pdf::loadView('prints.prescription', compact('diagnosis'))->setOption([
            'isRemoteEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'defaultFont' => 'dejavu sans'
        ]);

        return $pdf->stream('Prescription-' . $diagnosis->visit->patient->name . '.pdf');
    }

    /**
     * Print/download a lab report PDF.
     * Called by route: print.lab-report
     */
    public function labReport(LabOrder $labOrder)
    {
        $labOrder->load(['patient', 'doctor', 'items.labTestType.parameters', 'items.labResults']);

        if (view()->exists('prints.lab-report')) {
            $pdf = Pdf::loadView('prints.lab-report', compact('labOrder'))->setOption([
                'isRemoteEnabled' => true,
                'isFontSubsettingEnabled' => true,
                'defaultFont' => 'dejavu sans'
            ]);
            return $pdf->stream('Lab-Report-' . $labOrder->patient->name . '.pdf');
        }

        // Fallback: redirect back with message if view doesn't exist
        return redirect()->back()->with('error', 'Print view for lab reports is not yet available.');
    }

    /**
     * Print/download a visit/queue token.
     * Called by route: print.visit-token
     */
    public function visitToken(Visit $visit)
    {
        $visit->load(['patient', 'doctor']);

        if (view()->exists('prints.visit-token')) {
            $pdf = Pdf::loadView('prints.visit-token', compact('visit'))->setOption([
                'isRemoteEnabled' => true,
                'isFontSubsettingEnabled' => true,
                'defaultFont' => 'dejavu sans'
            ]);
            return $pdf->stream('Token-' . $visit->queue_token . '.pdf');
        }

        return redirect()->back()->with('error', 'Print view for visit tokens is not yet available.');
    }

    /**
     * Legacy method - kept for backward compatibility.
     */
    public function downloadPrescription($prescriptionsId)
    {
        $diagnosisId = Prescription::find($prescriptionsId)->diagnosis_id;
        $diagnosis = Diagnosis::with(['visit.patient', 'prescriptions.medicine'])->findOrFail($diagnosisId);

        $pdf = Pdf::loadView('prints.prescription', compact('diagnosis'))->setOption([
            'isRemoteEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'defaultFont' => 'dejavu sans'
        ]);

        return $pdf->stream('Prescription-' . $diagnosis->visit->patient->name . '.pdf');
    }
}
