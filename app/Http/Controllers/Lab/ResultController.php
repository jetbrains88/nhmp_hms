<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lab\SubmitResultsRequest;
use App\Models\LabOrderItem;
use App\Services\LabService;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    protected $labService;

    public function __construct(LabService $labService)
    {
        $this->labService = $labService;
    }

    /**
     * Show result entry form for an order item
     */
    public function create(LabOrderItem $orderItem)
    {
        $orderItem->load([
            'labOrder.patient',
            'labTestType.parameters' => function ($q) {
                $q->orderBy('order');
            }
        ]);
        
        // Load existing results if any
        $existingResults = $orderItem->labResults()
            ->get()
            ->keyBy('lab_test_parameter_id');
        
        return view('lab.orders.results.create', compact('orderItem', 'existingResults'));
    }

    /**
     * Submit results for an order item
     */
    public function store(SubmitResultsRequest $request, LabOrderItem $orderItem)
    {
        $orderItem = $this->labService->submitResults(
            $orderItem,
            $request->results,
            auth()->id()
        );
        
        return redirect()
            ->route('lab.orders.show', $orderItem->labOrder_id)
            ->with('success', 'Results submitted successfully');
    }

    /**
     * Show result edit form
     */
    public function edit(LabOrderItem $orderItem)
    {
        $orderItem->load([
            'labOrder.patient',
            'labTestType.parameters' => function ($q) {
                $q->orderBy('order');
            },
            'labResults'
        ]);
        
        $existingResults = $orderItem->labResults
            ->keyBy('lab_test_parameter_id');
        
        return view('lab.orders.results.create', compact('orderItem', 'existingResults'));
    }

    /**
     * Update results
     */
    public function update(SubmitResultsRequest $request, LabOrderItem $orderItem)
    {
        $orderItem = $this->labService->submitResults(
            $orderItem,
            $request->results,
            auth()->id()
        );
        
        return redirect()
            ->route('lab.orders.show', $orderItem->labOrder_id)
            ->with('success', 'Results updated successfully');
    }
}