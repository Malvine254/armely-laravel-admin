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
            // Consultations this week (using contacts, which has sent_date not created_at)
            $weekAgo = now()->subDays(7)->toDateString();
            $stats['consultations_this_week'] = DB::table('contacts')
                ->whereDate('sent_date', '>=', $weekAgo)
                ->count();
            
            // Total consultations (using contacts instead)
            $stats['total_consultations'] = DB::table('contacts')->count();
            
            // Total contacts
            $stats['total_contacts'] = DB::table('contacts')->count();
            
            // Total job applications
            $stats['total_job_apps'] = DB::table('job_applications')->count();
            
            // Total campaigns (remove this if campaigns table doesn't exist)
            $stats['total_campaigns'] = 0; // Placeholder - no campaigns table

            // Conversions (estimate: consultations that became contacts)
            $stats['conversions'] = intval($stats['total_contacts'] * 0.4);
            
        } catch (\Exception $e) {
            \Log::error('Stats load failed: ' . $e->getMessage());
        }

        // Load recent activity (public interactions + admin changes)
        $recentActivity = [];
        try {
            // Recent contacts - use COALESCE to handle null sent_date
            $contacts = DB::table('contacts')
                ->select(DB::raw("'Contact' as type"), 'name', 'email', 'subject as detail', 
                    DB::raw("COALESCE(sent_date, DATE(NOW())) as created_at"))
                ->orderByRaw("COALESCE(sent_date, DATE(NOW())) DESC")
                ->limit(5)
                ->get()
                ->map(function($item) { return (array) $item; })
                ->toArray();
            $recentActivity = array_merge($recentActivity, $contacts);
            
            // Recent job applications
            $applications = DB::table('job_applications')
                ->select(DB::raw("'Job Application' as type"), 'name', 'email', 'position as detail', 'application_date as created_at')
                ->orderBy('application_date', 'desc')
                ->limit(5)
                ->get()
                ->map(function($item) { return (array) $item; })
                ->toArray();
            $recentActivity = array_merge($recentActivity, $applications);
            
            // Admin activities (who did what) - NOW INCLUDES PAGE VISITS
            $adminActivities = DB::table('admin_activities')
                ->leftJoin('admin', 'admin_activities.admin_id', '=', 'admin.id')
                ->select(
                    'admin_activities.created_at', 
                    DB::raw("CASE 
                        WHEN admin_activities.action = 'login' THEN 'Login'
                        WHEN admin_activities.action = 'logout' THEN 'Logout'
                        WHEN admin_activities.action = 'page_visit' THEN 'Page Visit'
                        ELSE 'Admin Action'
                    END as type"),
                    DB::raw("COALESCE(admin.name, 'Unknown User') as name"),
                    DB::raw("COALESCE(admin.email, 'N/A') as email"),
                    DB::raw("CASE 
                        WHEN admin_activities.action = 'page_visit' THEN admin_activities.description
                        ELSE CONCAT(admin_activities.action, ' ', admin_activities.entity_type, 
                             CASE WHEN admin_activities.entity_id IS NOT NULL THEN CONCAT(' #', admin_activities.entity_id) ELSE '' END,
                             CASE WHEN admin_activities.description IS NOT NULL THEN CONCAT(' - ', admin_activities.description) ELSE '' END)
                    END as detail")
                )
                ->orderBy('admin_activities.created_at', 'desc')
                ->limit(50) // Get more activity records now
                ->get()
                ->map(function($item) { return (array) $item; })
                ->toArray();
            $recentActivity = array_merge($recentActivity, $adminActivities);

            \Log::info('Total activities before sorting: ' . count($recentActivity));

            // Sort by date
            usort($recentActivity, function($a, $b) {
                $dateA = strtotime($a['created_at'] ?? '0000-00-00 00:00:00');
                $dateB = strtotime($b['created_at'] ?? '0000-00-00 00:00:00');
                return $dateB - $dateA;
            });
            
            // Keep only last 10
            $recentActivity = array_slice($recentActivity, 0, 10);
            
            \Log::info('Activities after slicing to 10: ' . count($recentActivity));
            \Log::info('Final activities count: ' . count($recentActivity));
            
        } catch (\Exception $e) {
            \Log::error('Activity load failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }

        // NO SAMPLE DATA - Show real data only
        // If no activity found, the view will display "No recent activity found."
        \Log::info('Passing to view - activities count: ' . count($recentActivity));

        // Get chart data from database (last 30 days)
        $chartData = $this->getChartData(30);

        return view('admin.reports', compact('stats', 'recentActivity', 'chartData'));
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

    public function exportActivityPdf()
    {
        // Get the same data shown in the reports page
        $recentActivity = [];
        try {
            // Recent consultations
            $consultations = DB::table('consultation')
                ->select(DB::raw("'Consultation' as type"), 'name', 'email', 'service_name as detail', 'date_now as created_at')
                ->orderBy('date_now', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($consultations as $item) {
                $recentActivity[] = [
                    'type' => $item->type,
                    'name' => $item->name,
                    'email' => $item->email,
                    'detail' => $item->detail,
                    'created_at' => $item->created_at
                ];
            }
            
            // Recent contacts
            $contacts = DB::table('contacts')
                ->select(DB::raw("'Contact' as type"), 'name', 'email', 'subject as detail', 'sent_date as created_at')
                ->orderBy('sent_date', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($contacts as $item) {
                $recentActivity[] = [
                    'type' => $item->type,
                    'name' => $item->name,
                    'email' => $item->email,
                    'detail' => $item->detail,
                    'created_at' => $item->created_at
                ];
            }
            
            // Recent job applications
            $applications = DB::table('job_applications')
                ->select(DB::raw("'Job Application' as type"), 'name', 'email', 'position as detail', 'application_date as created_at')
                ->orderBy('application_date', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($applications as $item) {
                $recentActivity[] = [
                    'type' => $item->type,
                    'name' => $item->name,
                    'email' => $item->email,
                    'detail' => $item->detail,
                    'created_at' => $item->created_at
                ];
            }
            
            // Admin activities
            $adminActivities = DB::table('admin_activities')
                ->leftJoin('admin', 'admin_activities.admin_id', '=', 'admin.id')
                ->select(
                    'admin_activities.created_at',
                    DB::raw("CASE 
                        WHEN admin_activities.action = 'login' THEN 'Login'
                        WHEN admin_activities.action = 'logout' THEN 'Logout'
                        WHEN admin_activities.action = 'page_visit' THEN 'Page Visit'
                        ELSE 'Admin Action'
                    END as type"),
                    DB::raw("COALESCE(admin.name, 'Unknown User') as name"),
                    DB::raw("COALESCE(admin.email, 'N/A') as email"),
                    DB::raw("CASE 
                        WHEN admin_activities.action = 'page_visit' THEN admin_activities.description
                        ELSE CONCAT(admin_activities.action, ' ', admin_activities.entity_type, 
                             CASE WHEN admin_activities.entity_id IS NOT NULL THEN CONCAT(' #', admin_activities.entity_id) ELSE '' END,
                             CASE WHEN admin_activities.description IS NOT NULL THEN CONCAT(' - ', admin_activities.description) ELSE '' END)
                    END as detail")
                )
                ->orderBy('admin_activities.created_at', 'desc')
                ->limit(50)
                ->get();
            
            foreach ($adminActivities as $item) {
                $recentActivity[] = [
                    'type' => $item->type,
                    'name' => $item->name,
                    'email' => $item->email,
                    'detail' => $item->detail,
                    'created_at' => $item->created_at
                ];
            }

            // Sort by date
            usort($recentActivity, function($a, $b) {
                return strtotime($b->created_at ?? 0) - strtotime($a->created_at ?? 0);
            });
            
            // Keep only last 50
            $recentActivity = array_slice($recentActivity, 0, 50);
        } catch (\Exception $e) {
            \Log::error('Activity export PDF failed: ' . $e->getMessage());
            // Return empty data on error
            $recentActivity = [];
        }

        // NO SAMPLE DATA - Export real data only

        $filename = 'activity_report_' . date('Y-m-d_His') . '.html';
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Activity Report - ' . date('Y-m-d') . '</title>
    <style>
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px;
            color: #333;
        }
        h1 { 
            color: #2f5597; 
            border-bottom: 3px solid #2f5597;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .meta {
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .no-print {
            margin: 20px 0;
            padding: 15px;
            background: #f0f0f0;
            border-radius: 5px;
        }
        .no-print button {
            background: #2f5597;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
        }
        .no-print button:hover {
            background: #1e3a6b;
        }
        table { 
            width: 100%; 
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left;
        }
        th { 
            background-color: #2f5597; 
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f0f0f0;
        }
    </style>
    <script>
        function printReport() {
            window.print();
        }
        function saveAsPDF() {
            // Trigger browser print dialog with save as PDF option
            window.print();
        }
    </script>
</head>
<body>
    <h1>Recent Activity Report</h1>
    <div class="meta">
        <strong>Generated:</strong> ' . date('F d, Y g:i A') . '<br>
        <strong>Total Records:</strong> ' . count($recentActivity) . '
    </div>
    <div class="no-print">
        <button onclick="printReport()">🖨️ Print Report</button>
        <button onclick="saveAsPDF()">📄 Save as PDF</button>
        <p style="margin: 10px 0 0 0; font-size: 13px; color: #666;">Click "Save as PDF" and choose "Save as PDF" in your browser\'s print dialog.</p>
    </div>';
        
        if (empty($recentActivity)) {
            $html .= '<p style="color: #999; font-style: italic;">No activity data available</p>';
        } else {
            $html .= '<table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Time</th>
                        <th style="width: 15%;">Type</th>
                        <th style="width: 20%;">Name</th>
                        <th style="width: 20%;">Email</th>
                        <th style="width: 30%;">Detail</th>
                    </tr>
                </thead>
                <tbody>';
            
            foreach ($recentActivity as $activity) {
                $time = isset($activity['created_at']) ? date('M d, g:i A', strtotime($activity['created_at'])) : 'N/A';
                $type = $activity['type'] ?? 'Unknown';
                $name = htmlspecialchars($activity['name'] ?? 'N/A');
                $email = htmlspecialchars($activity['email'] ?? 'N/A');
                $detail = htmlspecialchars($activity['detail'] ?? 'N/A');
                
                $html .= '<tr>
                    <td>' . $time . '</td>
                    <td>' . $type . '</td>
                    <td>' . $name . '</td>
                    <td>' . $email . '</td>
                    <td>' . $detail . '</td>
                </tr>';
            }
            
            $html .= '</tbody>
            </table>';
        }
        
        $html .= '</body>
</html>';
        
        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($html),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    public function exportActivityExcel()
    {
        // Get the same data
        $recentActivity = [];
        try {
            $contacts = DB::table('contacts')
                ->select(DB::raw("'Contact' as type"), 'name', 'email', 'subject as detail', 'sent_date as created_at')
                ->orderBy('sent_date', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($contacts as $item) {
                $recentActivity[] = [
                    'type' => $item->type,
                    'name' => $item->name,
                    'email' => $item->email,
                    'detail' => $item->detail,
                    'created_at' => $item->created_at
                ];
            }
            
            $applications = DB::table('job_applications')
                ->select(DB::raw("'Job Application' as type"), 'name', 'email', 'position as detail', 'application_date as created_at')
                ->orderBy('application_date', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($applications as $item) {
                $recentActivity[] = [
                    'type' => $item->type,
                    'name' => $item->name,
                    'email' => $item->email,
                    'detail' => $item->detail,
                    'created_at' => $item->created_at
                ];
            }
            
            $adminActivities = DB::table('admin_activities')
                ->leftJoin('admin', 'admin_activities.admin_id', '=', 'admin.id')
                ->select(
                    'admin_activities.created_at',
                    DB::raw("CASE 
                        WHEN admin_activities.action = 'login' THEN 'Login'
                        WHEN admin_activities.action = 'logout' THEN 'Logout'
                        WHEN admin_activities.action = 'page_visit' THEN 'Page Visit'
                        ELSE 'Admin Action'
                    END as type"),
                    DB::raw("COALESCE(admin.name, 'Unknown User') as name"),
                    DB::raw("COALESCE(admin.email, 'N/A') as email"),
                    DB::raw("CASE 
                        WHEN admin_activities.action = 'page_visit' THEN admin_activities.description
                        ELSE CONCAT(admin_activities.action, ' ', admin_activities.entity_type, 
                             CASE WHEN admin_activities.entity_id IS NOT NULL THEN CONCAT(' #', admin_activities.entity_id) ELSE '' END,
                             CASE WHEN admin_activities.description IS NOT NULL THEN CONCAT(' - ', admin_activities.description) ELSE '' END)
                    END as detail")
                )
                ->orderBy('admin_activities.created_at', 'desc')
                ->limit(50)
                ->get();
            
            foreach ($adminActivities as $item) {
                $recentActivity[] = [
                    'type' => $item->type,
                    'name' => $item->name,
                    'email' => $item->email,
                    'detail' => $item->detail,
                    'created_at' => $item->created_at
                ];
            }

            usort($recentActivity, function($a, $b) {
                $timeA = isset($a['created_at']) ? strtotime($a['created_at']) : 0;
                $timeB = isset($b['created_at']) ? strtotime($b['created_at']) : 0;
                return $timeB - $timeA;
            });
            
            $recentActivity = array_slice($recentActivity, 0, 50);
        } catch (\Exception $e) {
            \Log::error('Activity export Excel failed: ' . $e->getMessage());
            // Return empty data on error
            $recentActivity = [];
        }

        // NO SAMPLE DATA - Export real data only

        $filename = 'activity_report_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($recentActivity) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write headers
            fputcsv($file, ['Time', 'Type', 'Name', 'Email', 'Detail']);
            
            // Write data
            foreach ($recentActivity as $activity) {
                fputcsv($file, [
                    isset($activity['created_at']) ? date('m/d/Y H:i', strtotime($activity['created_at'])) : 'N/A',
                    $activity['type'] ?? 'Unknown',
                    $activity['name'] ?? 'N/A',
                    $activity['email'] ?? 'N/A',
                    $activity['detail'] ?? 'N/A'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get chart data for engagement timeline
     */
    private function getChartData($days = 30)
    {
        try {
            $chartData = [
                'labels' => [],
                'consultations' => [],
                'contacts' => [],
                'applications' => []
            ];

            // Get data based on time range
            if ($days == 7) {
                // Daily data for last 7 days
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i)->toDateString();
                    $chartData['labels'][] = now()->subDays($i)->format('D');
                    
                    $chartData['consultations'][] = DB::table('contacts')
                        ->whereDate('sent_date', $date)
                        ->count();
                    
                    $chartData['contacts'][] = DB::table('contacts')
                        ->whereDate('sent_date', $date)
                        ->count();
                    
                    $chartData['applications'][] = DB::table('job_applications')
                        ->whereDate('application_date', $date)
                        ->count();
                }
            } elseif ($days == 30) {
                // Weekly data for last 4 weeks + current week
                for ($i = 4; $i >= 0; $i--) {
                    $endDate = now()->subWeeks($i);
                    $startDate = now()->subWeeks($i + 1);
                    
                    $chartData['labels'][] = $i == 0 ? 'This Week' : 'Week ' . (5 - $i);
                    
                    $chartData['consultations'][] = DB::table('contacts')
                        ->whereBetween('sent_date', [$startDate, $endDate])
                        ->count();
                    
                    $chartData['contacts'][] = DB::table('contacts')
                        ->whereBetween('sent_date', [$startDate, $endDate])
                        ->count();
                    
                    $chartData['applications'][] = DB::table('job_applications')
                        ->whereBetween('application_date', [$startDate, $endDate])
                        ->count();
                }
            } else { // 90 days
                // Bi-weekly data for last 90 days
                for ($i = 6; $i >= 0; $i--) {
                    $endDate = now()->subWeeks($i * 2);
                    $startDate = now()->subWeeks(($i + 1) * 2);
                    
                    $chartData['labels'][] = $i == 0 ? 'Week 13' : 'Week ' . ((7 - $i) * 2 - 1) . '-' . ((7 - $i) * 2);
                    
                    $chartData['consultations'][] = DB::table('contacts')
                        ->whereBetween('sent_date', [$startDate, $endDate])
                        ->count();
                    
                    $chartData['contacts'][] = DB::table('contacts')
                        ->whereBetween('sent_date', [$startDate, $endDate])
                        ->count();
                    
                    $chartData['applications'][] = DB::table('job_applications')
                        ->whereBetween('application_date', [$startDate, $endDate])
                        ->count();
                }
            }

            return $chartData;
        } catch (\Exception $e) {
            \Log::error('Chart data load failed: ' . $e->getMessage());
            // Return empty data structure on error
            return [
                'labels' => [],
                'consultations' => [],
                'contacts' => [],
                'applications' => []
            ];
        }
    }

    /**
     * AJAX endpoint to get chart data
     */
    public function getChartDataAjax(Request $request)
    {
        $days = $request->input('days', 30);
        $chartData = $this->getChartData($days);
        
        return response()->json($chartData);
    }
}

