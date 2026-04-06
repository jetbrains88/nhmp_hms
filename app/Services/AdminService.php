<?php

namespace App\Services;

use App\Repositories\AdminRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminService
{
    protected $adminRepository;

    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function getDashboardData()
    {
        // Get basic stats
        $stats = $this->adminRepository->getDashboardStats();
        
        // Get trend data
        $visitTrendData = $this->adminRepository->getVisitTrendData();
        
        // Get system metrics
        $systemMetrics = $this->adminRepository->getSystemMetrics();
        
        // Merge all data
        $stats = array_merge($stats, $visitTrendData, $systemMetrics);
        
        // Ensure all required keys exist with defaults
        $stats = $this->ensureStatsKeys($stats);
        
        // Get recent data
        $recentPatients = $this->adminRepository->getRecentPatients();
        $recentVisits = $this->adminRepository->getRecentVisits();

        // Get system health
        $systemHealth = $this->getSystemHealth();

        return [
            'stats' => $stats,
            'recentPatients' => $recentPatients,
            'recentVisits' => $recentVisits,
            'systemHealth' => $systemHealth,
        ];
    }

    private function ensureStatsKeys($stats)
    {
        $requiredKeys = [
            'totalPatients' => 0,
            'todayVisits' => 0,
            'pendingPrescriptions' => 0,
            'lowStockMedicines' => 0,
            'totalUsers' => 0,
            'totalRoles' => 0,
            'activeUsers' => 0,
            'todayRevenue' => 0,
            'formattedRevenue' => '0.00',
            'visitTrend' => 0, // This is the key that was missing
            'today' => 0,
            'yesterday' => 0,
            'totalDepartments' => 0,
            'totalDesignations' => 0,
            'totalMedicines' => 0,
            'expiredMedicines' => 0,
        ];

        foreach ($requiredKeys as $key => $default) {
            if (!isset($stats[$key])) {
                $stats[$key] = $default;
            }
        }
        
        // Format revenue if not already formatted
        if (isset($stats['todayRevenue']) && !isset($stats['formattedRevenue'])) {
            $stats['formattedRevenue'] = number_format($stats['todayRevenue'], 2);
        }
        
        return $stats;
    }

    public function getSystemHealth()
    {
        return [
            'database' => $this->checkDatabaseConnection(),
            'storage' => $this->checkStorageSpace(),
            'lastBackup' => $this->getLastBackupTime(),
            'queue' => $this->checkQueueStatus(),
        ];
    }

    private function checkDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            $start = microtime(true);
            DB::select('SELECT 1');
            $responseTime = round((microtime(true) - $start) * 1000, 2);
            
            return [
                'status' => 'healthy', 
                'message' => 'Connected',
                'response_time' => $responseTime . 'ms'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error', 
                'message' => $e->getMessage(),
                'response_time' => 'N/A'
            ];
        }
    }

    private function checkStorageSpace()
    {
        try {
            $total = disk_total_space('/');
            $free = disk_free_space('/');
            $usedPercent = $total > 0 ? (($total - $free) / $total) * 100 : 0;
            
            return [
                'total' => $this->formatBytes($total),
                'free' => $this->formatBytes($free),
                'used_percent' => round($usedPercent, 1),
                'status' => $usedPercent > 90 ? 'critical' : ($usedPercent > 80 ? 'warning' : 'healthy'),
            ];
        } catch (\Exception $e) {
            return [
                'total' => 'N/A',
                'free' => 'N/A',
                'used_percent' => 0,
                'status' => 'error',
            ];
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes <= 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    private function getLastBackupTime()
    {
        try {
            $backupPath = storage_path('app/backups');
            if (file_exists($backupPath)) {
                $files = glob($backupPath . '/*.zip');
                if (!empty($files)) {
                    $latestFile = max($files);
                    $fileTime = filemtime($latestFile);
                    $timeDiff = time() - $fileTime;
                    
                    if ($timeDiff < 3600) { // Less than 1 hour
                        return 'Just now';
                    } elseif ($timeDiff < 86400) { // Less than 1 day
                        return round($timeDiff / 3600) . ' hours ago';
                    } else {
                        return date('Y-m-d H:i:s', $fileTime);
                    }
                }
            }
            return 'Never';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    private function checkQueueStatus()
    {
        try {
            $pendingJobs = DB::table('jobs')->count();
            
            return [
                'status' => $pendingJobs > 50 ? 'warning' : 'healthy',
                'pending_jobs' => $pendingJobs,
                'message' => $pendingJobs . ' pending jobs'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'pending_jobs' => 0,
                'message' => 'Queue not configured'
            ];
        }
    }
}