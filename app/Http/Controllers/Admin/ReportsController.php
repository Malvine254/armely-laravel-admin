<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        // Initialize stats
        $stats = [
            'consultations_this_week' => 0,
            'total_consultations' => 0,
            'conversions' => 0,
            'avg_response_time' => '—',
            'open_tickets' => 0,
            'total_contacts' => 0,
            'total_job_apps' => 0,
            'total_campaigns' => 0
        ];

        // Load stats from database
        try {
            // Consultations this week
            $weekAgo = now()->subDays(7)->toDateString();
            $stats['consultations_this_week'] = DB::table('consultation')
                ->where('date_now', '>=', $weekAgo)
                ->count();
            
            // Total consultations
            $stats['total_consultations'] = DB::table('consultation')->count();
            
            // Total contacts
            $stats['total_contacts'] = DB::table('contacts')->count();
            
            // Total job applications
            $stats['total_job_apps'] = DB::table('job_applications')->count();
            
            // Total campaigns
            $stats['total_campaigns'] = DB::table('campaigns')->count();

            // Conversions (estimate: consultations that became contacts)
            $stats['conversions'] = intval($stats['total_contacts'] * 0.4);
            
        } catch (\Exception $e) {
            \Log::error('Stats load failed: ' . $e->getMessage());
        }

        // Load recent activity (public interactions + admin changes)
        $recentActivity = [];
        try {
            // Recent consultations
            $consultations = DB::table('consultation')
                ->select(DB::raw("'Consultation' as type"), 'name', 'email', 'service_name as detail', 'date_now as created_at')
                ->orderBy('date_now', 'desc')
                ->limit(5)
                ->get()
                ->toArray();
            $recentActivity = array_merge($recentActivity, $consultations);
            
            // Recent contacts
            $contacts = DB::table('contacts')
                ->select(DB::raw("'Contact' as type"), 'name', 'email', 'subject as detail', 'sent_date as created_at')
                ->orderBy('sent_date', 'desc')
                ->limit(5)
                ->get()
                ->toArray();
            $recentActivity = array_merge($recentActivity, $contacts);
            
            // Recent job applications
            $applications = DB::table('job_applications')
                ->select(DB::raw("'Job Application' as type"), 'name', 'email', 'position as detail', 'application_date as created_at')
                ->orderBy('application_date', 'desc')
                ->limit(5)
                ->get()
                ->toArray();
            $recentActivity = array_merge($recentActivity, $applications);
            
            // Recent campaigns
            $campaigns = DB::table('campaigns')
                ->select(DB::raw("'Campaign' as type"), 'full_name as name', 'business_email as email', 'company_name as detail', 'sent_date as created_at')
                ->orderBy('sent_date', 'desc')
                ->limit(5)
                ->get()
                ->toArray();
            $recentActivity = array_merge($recentActivity, $campaigns);
            
            // Admin activities (who did what)
            $adminActivities = DB::table('admin_activities')
                ->leftJoin('admin', 'admin_activities.admin_id', '=', 'admin.id')
                ->select('admin_activities.created_at', DB::raw("'Admin Change' as type"), 'admin.name as name', 'admin.email as email', DB::raw("CONCAT(admin_activities.action, ' ', admin_activities.entity_type, ' #', COALESCE(admin_activities.entity_id,''), CASE WHEN admin_activities.description IS NOT NULL THEN CONCAT(' - ', admin_activities.description) ELSE '' END) as detail"))
                ->orderBy('admin_activities.created_at', 'desc')
                ->limit(10)
                ->get()
                ->toArray();
            $recentActivity = array_merge($recentActivity, $adminActivities);

            // Sort by date
            usort($recentActivity, function($a, $b) {
                return strtotime($b->created_at ?? 0) - strtotime($a->created_at ?? 0);
            });
            
            // Keep only last 10
            $recentActivity = array_slice($recentActivity, 0, 10);
            
            // Convert objects to arrays for Blade
            $recentActivity = array_map(function($item) {
                return (array) $item;
            }, $recentActivity);
            
        } catch (\Exception $e) {
            \Log::error('Activity load failed: ' . $e->getMessage());
        }

        return view('admin.reports', compact('stats', 'recentActivity'));
    }
    
    public function export(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'format' => 'required|in:pdf,csv,excel',
        ]);
        
        $reportType = $validated['report_type'];
        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;
        $format = $validated['format'];
        
        $data = $this->getReportData($reportType, $startDate, $endDate);
        
        switch ($format) {
            case 'pdf':
                return $this->exportPdf($reportType, $data);
            case 'csv':
                return $this->exportCsv($reportType, $data);
            case 'excel':
                return $this->exportExcel($reportType, $data);
            default:
                return redirect()->back()->with('error', 'Invalid format');
        }
    }
    
    private function getReportData($reportType, $startDate, $endDate)
    {
        $query = DB::table($reportType);
        
        if ($startDate && $endDate) {
            $dateColumn = $this->getDateColumn($reportType);
            if ($dateColumn) {
                $query->whereBetween($dateColumn, [$startDate, $endDate]);
            }
        }
        
        return $query->get();
    }
    
    private function getDateColumn($reportType)
    {
        $dateColumns = [
            'blogs' => 'blog_date',
            'videos' => 'created_at',
            'careers' => 'created_at',
            'applications' => 'applied_at',
            'social_impact' => 'published_date',
            'customer_stories' => 'created_at',
            'admins' => 'joined_date',
        ];
        
        return $dateColumns[$reportType] ?? null;
    }
    
    private function exportPdf($reportType, $data)
    {
        $filename = $reportType . '_report_' . date('Y-m-d') . '.pdf';
        
        // Simple HTML table for PDF
        $html = '<html><head><style>
            body { font-family: Arial, sans-serif; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #4CAF50; color: white; }
        </style></head><body>';
        
        $html .= '<h1>' . ucfirst($reportType) . ' Report</h1>';
        $html .= '<p>Generated on: ' . date('Y-m-d H:i:s') . '</p>';
        
        if ($data->isEmpty()) {
            $html .= '<p>No data available</p>';
        } else {
            $html .= '<table><thead><tr>';
            
            // Table headers
            $firstRow = (array) $data->first();
            foreach (array_keys($firstRow) as $key) {
                $html .= '<th>' . ucfirst(str_replace('_', ' ', $key)) . '</th>';
            }
            $html .= '</tr></thead><tbody>';
            
            // Table rows
            foreach ($data as $row) {
                $html .= '<tr>';
                foreach ((array) $row as $value) {
                    $html .= '<td>' . htmlspecialchars($value ?? '') . '</td>';
                }
                $html .= '</tr>';
            }
            
            $html .= '</tbody></table>';
        }
        
        $html .= '</body></html>';
        
        // For now, return HTML. Later you can integrate DOMPDF
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    private function exportCsv($reportType, $data)
    {
        $filename = $reportType . '_report_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            if (!$data->isEmpty()) {
                // Write headers
                $firstRow = (array) $data->first();
                fputcsv($file, array_keys($firstRow));
                
                // Write data
                foreach ($data as $row) {
                    fputcsv($file, (array) $row);
                }
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    private function exportExcel($reportType, $data)
    {
        // For now, export as CSV (Excel compatible)
        // Later you can integrate PhpSpreadsheet for true Excel format
        return $this->exportCsv($reportType, $data);
    }
}

