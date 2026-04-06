<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\Prescription;
use App\Models\LabOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        $stats = [
            'total_branches' => Branch::count(),
            'total_users' => User::count(),
            'total_patients' => Patient::count(),
            'visits_today' => Visit::whereDate('created_at', today())->count(),
            'prescriptions_today' => Prescription::whereDate('created_at', today())->count(),
            'lab_orders_today' => LabOrder::whereDate('created_at', today())->count(),
            'active_users' => User::where('is_active', true)->count(),
            'cmo_branches' => Branch::where('type', 'CMO')->count(),
            'rmo_branches' => Branch::where('type', 'RMO')->count(),
        ];

        // Recent activities
        $recentUsers = User::latest()->take(5)->get();
        $recentPatients = Patient::latest()->take(5)->get();

        // Chart data
        $userRegistrations = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $patientRegistrations = Patient::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentUsers',
            'recentPatients',
            'userRegistrations',
            'patientRegistrations'
        ));
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        return view('admin.settings');
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'timezone' => 'required|timezone',
        ]);

        // Update .env or settings table
        // This is a simplified version - in production, use a proper settings package

        return redirect()->route('admin.settings')
            ->with('success', 'Settings updated successfully');
    }
}
