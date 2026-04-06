<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Visit;
use App\Models\Prescription;
use App\Models\LabOrder;
use App\Models\Appointment;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Generate patient statistics report
     */
    public function patientStats(array $filters = [])
    {
        $query = Patient::query();

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return [
            'total_patients' => (clone $query)->count(),
            'new_patients' => (clone $query)->whereDate('created_at', today())->count(),
            'nhmp_patients' => (clone $query)->whereHas('employeeDetail')->count(),
            'dependents' => (clone $query)->whereNotNull('parent_id')->count(),
            'by_gender' => (clone $query)->select('gender', DB::raw('count(*) as total'))
                ->groupBy('gender')
                ->get(),
            'by_blood_group' => (clone $query)->select('blood_group', DB::raw('count(*) as total'))
                ->whereNotNull('blood_group')
                ->groupBy('blood_group')
                ->get(),
            'registrations_by_month' => (clone $query)->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as total')
            )
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get(),
        ];
    }

    /**
     * Generate visit statistics report
     */
    public function visitStats(array $filters = [])
    {
        $query = Visit::query();

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return [
            'total_visits' => (clone $query)->count(),
            'by_type' => (clone $query)->select('visit_type', DB::raw('count(*) as total'))
                ->groupBy('visit_type')
                ->get(),
            'by_status' => (clone $query)->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get(),
            'average_wait_time' => (clone $query)->where('status', 'completed')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as avg_wait'))
                ->value('avg_wait'),
            'visits_by_hour' => (clone $query)->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('count(*) as total')
            )
                ->groupBy(DB::raw('HOUR(created_at)'))
                ->orderBy('hour')
                ->get(),
            'top_doctors' => (clone $query)->select(
                'doctor_id',
                DB::raw('count(*) as total')
            )
                ->whereNotNull('doctor_id')
                ->groupBy('doctor_id')
                ->with('doctor:id,name')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * Generate pharmacy report
     */
    public function pharmacyStats(array $filters = [])
    {
        $branchId = $filters['branch_id'] ?? null;

        $inventoryQuery = InventoryLog::query();
        $prescriptionQuery = Prescription::query();

        if ($branchId) {
            $inventoryQuery->where('branch_id', $branchId);
            $prescriptionQuery->where('branch_id', $branchId);
        }

        if (!empty($filters['date_from'])) {
            $inventoryQuery->whereDate('created_at', '>=', $filters['date_from']);
            $prescriptionQuery->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $inventoryQuery->whereDate('created_at', '<=', $filters['date_to']);
            $prescriptionQuery->whereDate('created_at', '<=', $filters['date_to']);
        }

        return [
            'total_prescriptions' => $prescriptionQuery->count(),
            'prescriptions_by_status' => $prescriptionQuery->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get(),
            'total_dispensed' => $prescriptionQuery->where('status', 'completed')->count(),
            'top_medicines' => $prescriptionQuery->select(
                'medicine_id',
                DB::raw('count(*) as total'),
                DB::raw('SUM(quantity) as total_quantity')
            )
                ->groupBy('medicine_id')
                ->with('medicine:id,name')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),
            'inventory_movements' => $inventoryQuery->select(
                'type',
                DB::raw('count(*) as total'),
                DB::raw('SUM(quantity) as total_quantity')
            )
                ->groupBy('type')
                ->get(),
            'stock_value' => DB::table('medicine_batches')
                ->when($branchId, function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })
                ->select(DB::raw('SUM(remaining_quantity * unit_price) as total_value'))
                ->value('total_value'),
        ];
    }

    /**
     * Generate laboratory report
     */
    public function labStats(array $filters = [])
    {
        $branchId = $filters['branch_id'] ?? null;
        $query = LabOrder::query();

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return [
            'total_orders' => (clone $query)->count(),
            'by_status' => (clone $query)->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get(),
            'by_priority' => (clone $query)->select('priority', DB::raw('count(*) as total'))
                ->groupBy('priority')
                ->get(),
            'avg_processing_time' => (clone $query)->where('status', 'completed')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, reporting_date)) as avg_hours'))
                ->value('avg_hours'),
            'top_tests' => DB::table('lab_order_items')
                ->join('lab_test_types', 'lab_order_items.lab_test_type_id', '=', 'lab_test_types.id')
                ->when($branchId, function ($q) use ($branchId) {
                    $q->whereIn('lab_order_items.lab_order_id', function ($sub) use ($branchId) {
                        $sub->select('id')->from('lab_orders')->where('branch_id', $branchId);
                    });
                })
                ->select(
                    'lab_test_types.name',
                    DB::raw('count(*) as total')
                )
                ->groupBy('lab_test_types.id', 'lab_test_types.name')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),
            'abnormal_percentage' => DB::table('lab_results')
                ->where('is_abnormal', true)
                ->when($branchId, function ($q) use ($branchId) {
                    $q->whereIn('lab_order_item_id', function ($sub) use ($branchId) {
                        $sub->select('id')->from('lab_order_items')
                            ->whereIn('lab_order_id', function ($sub2) use ($branchId) {
                                $sub2->select('id')->from('lab_orders')->where('branch_id', $branchId);
                            });
                    });
                })
                ->select(DB::raw('(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM lab_results)) as percentage'))
                ->value('percentage'),
        ];
    }

    /**
     * Generate appointment report
     */
    public function appointmentStats(array $filters = [])
    {
        $query = Appointment::query();

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['doctor_id'])) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('scheduled_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('scheduled_at', '<=', $filters['date_to']);
        }

        return [
            'total_appointments' => (clone $query)->count(),
            'by_status' => (clone $query)->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get(),
            'by_type' => (clone $query)->select('type', DB::raw('count(*) as total'))
                ->groupBy('type')
                ->get(),
            'completion_rate' => (clone $query)->select(
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) * 100.0 / COUNT(*) as rate')
            )->value('rate'),
            'no_show_rate' => (clone $query)->select(
                DB::raw('SUM(CASE WHEN status = "no_show" THEN 1 ELSE 0 END) * 100.0 / COUNT(*) as rate')
            )->value('rate'),
            'popular_doctors' => (clone $query)->select(
                'doctor_id',
                DB::raw('count(*) as total')
            )
                ->groupBy('doctor_id')
                ->with('doctor:id,name')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),
        ];
    }


    /**
     * Generate audit log report
     */
    public function auditStats(array $filters = [])
    {
        $query = DB::table('audit_logs');

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return [
            'total_actions' => $query->count(),
            'by_action' => $query->select('action', DB::raw('count(*) as total'))
                ->groupBy('action')
                ->get(),
            'by_entity' => $query->select('entity_type', DB::raw('count(*) as total'))
                ->groupBy('entity_type')
                ->get(),
            'by_user' => $query->select('user_id', DB::raw('count(*) as total'))
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),
            'by_hour' => $query->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('count(*) as total')
            )
                ->groupBy(DB::raw('HOUR(created_at)'))
                ->orderBy('hour')
                ->get(),
        ];
    }

    /**
     * Export report data to CSV
     */
    public function toCsv(array $data, array $headers): string
    {
        $output = fopen('php://temp', 'r+');

        // Add headers
        fputcsv($output, $headers);

        // Add data rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
