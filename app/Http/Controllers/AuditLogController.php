<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    /**
     * Display audit logs with filters
     */
    public function index(Request $request)
    {
        $query = AuditLog::with(['user', 'subject'])
            ->orderBy('created_at', 'desc');
        
        // Apply filters
        $filters = $this->applyFilters($query, $request);
        
        // Paginate results
        $perPage = $request->get('per_page', 25);
        $logs = $query->paginate($perPage)->withQueryString();
        
        // Get filter data for dropdowns
        $filterData = $this->getFilterData();
        
        // Get statistics
        $stats = $this->getStatistics($request);
        
        return view('backend.audit.index', compact('logs', 'filterData', 'filters', 'stats'));
    }
    
    /**
     * Apply filters to query
     */
    private function applyFilters($query, Request $request)
    {
        $filters = [];
        
        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
            $filters['action'] = $request->action;
        }
        
        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
            $filters['user_id'] = $request->user_id;
        }
        
        // Filter by model
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
            $filters['subject_type'] = $request->subject_type;
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
            $filters['status'] = $request->status;
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $query->where('created_at', '>=', $dateFrom);
            $filters['date_from'] = $request->date_from;
        }
        
        if ($request->filled('date_to')) {
            $dateTo = Carbon::parse($request->date_to)->endOfDay();
            $query->where('created_at', '<=', $dateTo);
            $filters['date_to'] = $request->date_to;
        }
        
        // Filter by IP address
        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
            $filters['ip_address'] = $request->ip_address;
        }
        
        // Filter by description
        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
            $filters['description'] = $request->description;
        }
        
        return $filters;
    }
    
    /**
     * Get filter data for dropdowns
     */
    private function getFilterData()
    {
        return [
            'actions' => AuditLog::distinct('action')
                ->orderBy('action')
                ->pluck('action'),
            
            'users' => User::select('id', 'full_name')
                ->whereIn('id', function($query) {
                    $query->select('user_id')
                        ->from('audit_logs')
                        ->distinct();
                })
                ->orderBy('full_name')
                ->get(),
            
            'models' => AuditLog::distinct('subject_type')
                ->orderBy('subject_type')
                ->pluck('subject_type')
                ->map(function($model) {
                    return class_basename($model);
                }),
            
            'statuses' => ['success', 'failed', 'warning'],
            
            'time_periods' => [
                'today' => 'Today',
                'yesterday' => 'Yesterday',
                'last_7_days' => 'Last 7 Days',
                'last_30_days' => 'Last 30 Days',
                'this_month' => 'This Month',
                'last_month' => 'Last Month',
            ],
        ];
    }
    
    /**
     * Get audit log statistics
     */
    private function getStatistics(Request $request)
    {
        $statsQuery = AuditLog::query();
        
        // Apply same filters as main query
        $this->applyFilters($statsQuery, $request);
        
        return [
            'total_logs' => $statsQuery->count(),
            'success_logs' => $statsQuery->clone()->where('status', 'success')->count(),
            'failed_logs' => $statsQuery->clone()->where('status', 'failed')->count(),
            'warning_logs' => $statsQuery->clone()->where('status', 'warning')->count(),
            'top_users' => $this->getTopUsers($request),
            'recent_activity' => $this->getRecentActivity(),
            'action_distribution' => $this->getActionDistribution($request),
        ];
    }
    
    /**
     * Get top users by activity
     */
    private function getTopUsers(Request $request)
    {
        return AuditLog::select('user_id', DB::raw('COUNT(*) as activity_count'))
            ->with(['user:id,full_name'])
            ->when($request->filled('date_from'), function($query) use ($request) {
                $query->where('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
            })
            ->when($request->filled('date_to'), function($query) use ($request) {
                $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
            })
            ->groupBy('user_id')
            ->orderBy('activity_count', 'desc')
            ->limit(5)
            ->get();
    }
    
    /**
     * Get recent activity summary
     */
    private function getRecentActivity()
    {
        return [
            'last_hour' => AuditLog::where('created_at', '>=', now()->subHour())->count(),
            'last_24_hours' => AuditLog::where('created_at', '>=', now()->subDay())->count(),
            'last_7_days' => AuditLog::where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }
    
    /**
     * Get action distribution
     */
    private function getActionDistribution(Request $request)
    {
        return AuditLog::select('action', DB::raw('COUNT(*) as count'))
            ->when($request->filled('date_from'), function($query) use ($request) {
                $query->where('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
            })
            ->when($request->filled('date_to'), function($query) use ($request) {
                $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
            })
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->action => $item->count];
            })
            ->toArray();
    }
    
    /**
     * Show audit log details
     */
    public function show($id)
    {
        $log = AuditLog::with(['user', 'subject'])->findOrFail($id);
        
        // Parse old and new values if they exist
        $oldValues = $log->old_values ? json_decode($log->old_values, true) : null;
        $newValues = $log->new_values ? json_decode($log->new_values, true) : null;
        
        return view('backend.audit.show', compact('log', 'oldValues', 'newValues'));
    }
    
    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        $query = AuditLog::with(['user', 'subject'])
            ->orderBy('created_at', 'desc');
        
        // Apply filters
        $this->applyFilters($query, $request);
        
        $logs = $query->get();
        
        $fileName = 'audit-logs-' . now()->format('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];
        
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Timestamp',
                'User',
                'Action',
                'Model',
                'Model ID',
                'Description',
                'IP Address',
                'User Agent',
                'Status',
                'Old Values',
                'New Values',
            ]);
            
            // Add data rows
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user->full_name ?? 'System',
                    $log->action,
                    class_basename($log->subject_type),
                    $log->subject_id,
                    $log->description,
                    $log->ip_address,
                    substr($log->user_agent, 0, 100) . (strlen($log->user_agent) > 100 ? '...' : ''),
                    $log->status,
                    $log->old_values ? 'Yes' : 'No',
                    $log->new_values ? 'Yes' : 'No',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Clear old audit logs
     */
    public function clearOldLogs(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:3650',
            'confirm' => 'required|accepted',
        ]);
        
        $days = $request->days;
        $cutoffDate = now()->subDays($days);
        
        $deletedCount = AuditLog::where('created_at', '<', $cutoffDate)->delete();
        
        // Log this action
        activity('audit')
            ->performedOn(new AuditLog())
            ->causedBy(auth()->user())
            ->withProperties([
                'days' => $days,
                'cutoff_date' => $cutoffDate->format('Y-m-d H:i:s'),
                'deleted_count' => $deletedCount,
            ])
            ->log('Cleared old audit logs');
        
        return redirect()->route('backend.audit.index')
            ->with('success', "Cleared $deletedCount audit logs older than $days days.");
    }
    
    /**
     * Get audit log statistics for dashboard
     */
    public function getStats(Request $request)
    {
        $period = $request->get('period', 'today');
        
        $query = AuditLog::query();
        
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'yesterday':
                $query->whereDate('created_at', today()->subDay());
                break;
            case 'last_7_days':
                $query->where('created_at', '>=', now()->subDays(7));
                break;
            case 'last_30_days':
                $query->where('created_at', '>=', now()->subDays(30));
                break;
        }
        
        $stats = [
            'total' => $query->count(),
            'success' => $query->clone()->where('status', 'success')->count(),
            'failed' => $query->clone()->where('status', 'failed')->count(),
            'warning' => $query->clone()->where('status', 'warning')->count(),
            'created' => $query->clone()->where('action', 'like', '%create%')->count(),
            'updated' => $query->clone()->where('action', 'like', '%update%')->count(),
            'deleted' => $query->clone()->where('action', 'like', '%delete%')->count(),
        ];
        
        return response()->json($stats);
    }
    
    /**
     * Search audit logs
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
        ]);
        
        $query = $request->query;
        
        $logs = AuditLog::with(['user', 'subject'])
            ->where(function($q) use ($query) {
                $q->where('description', 'like', "%$query%")
                  ->orWhere('action', 'like', "%$query%")
                  ->orWhere('ip_address', 'like', "%$query%")
                  ->orWhere('user_agent', 'like', "%$query%")
                  ->orWhere('old_values', 'like', "%$query%")
                  ->orWhere('new_values', 'like', "%$query%")
                  ->orWhereHas('user', function($q) use ($query) {
                      $q->where('full_name', 'like', "%$query%")
                        ->orWhere('email', 'like', "%$query%");
                  });
            })
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        return response()->json([
            'success' => true,
            'results' => $logs->map(function($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'description' => $log->description,
                    'user' => $log->user->full_name ?? 'System',
                    'created_at' => $log->created_at->format('M d, Y H:i'),
                    'status' => $log->status,
                    'status_color' => $this->getStatusColor($log->status),
                    'show_url' => route('backend.audit.show', $log->id),
                ];
            }),
            'count' => $logs->count(),
        ]);
    }
    
    /**
     * Get status color
     */
    private function getStatusColor($status)
    {
        return match($status) {
            'success' => 'green',
            'failed' => 'red',
            'warning' => 'yellow',
            default => 'gray',
        };
    }
}