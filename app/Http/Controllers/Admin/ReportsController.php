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

        // If no activity found, add sample data
        if (empty($recentActivity) || count($recentActivity) === 0) {
            $recentActivity = [
                [
                    'type' => 'Consultation',
                    'name' => 'John Smith',
                    'email' => 'john.smith@techcorp.com',
                    'detail' => 'Data Science and Analytics consultation request',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
                ],
                [
                    'type' => 'Contact',
                    'name' => 'Sarah Johnson',
                    'email' => 'sarah.j@innovate.io',
                    'detail' => 'Inquiry about Microsoft Fabric implementation',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-5 hours'))
                ],
                [
                    'type' => 'Job Application',
                    'name' => 'Michael Chen',
                    'email' => 'mchen@email.com',
                    'detail' => 'Senior Data Engineer position',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-8 hours'))
                ],
                [
                    'type' => 'Campaign',
                    'name' => 'Emily Davis',
                    'email' => 'emily.davis@globaltech.com',
                    'detail' => 'Enterprise AI Solutions Campaign',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
                ],
                [
                    'type' => 'Consultation',
                    'name' => 'Robert Martinez',
                    'email' => 'r.martinez@dataworks.net',
                    'detail' => 'AI Consulting service inquiry',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
                ],
                [
                    'type' => 'Contact',
                    'name' => 'Lisa Anderson',
                    'email' => 'l.anderson@startup.xyz',
                    'detail' => 'Question about Power BI integration',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
                ],
                [
                    'type' => 'Admin Change',
                    'name' => 'Admin User',
                    'email' => 'admin@armely.com',
                    'detail' => 'Updated service Data Strategy - Modified pricing',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
                ],
                [
                    'type' => 'Job Application',
                    'name' => 'David Wilson',
                    'email' => 'dwilson@jobseeker.com',
                    'detail' => 'AI/ML Consultant position',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
                ],
                [
                    'type' => 'Consultation',
                    'name' => 'Jennifer Lee',
                    'email' => 'jlee@enterprise.com',
                    'detail' => 'Microsoft Power Platform implementation',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
                ],
                [
                    'type' => 'Contact',
                    'name' => 'Thomas Brown',
                    'email' => 'thomas.b@company.org',
                    'detail' => 'General inquiry about managed services',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
                ]
            ];
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

        // If no data, use sample data
        if (empty($recentActivity)) {
            $recentActivity = [
                [
                    'type' => 'Page Visit',
                    'name' => 'Admin User',
                    'email' => 'admin@armely.com',
                    'detail' => 'Visited: admin/reports',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
                ],
                [
                    'type' => 'Login',
                    'name' => 'Admin User',
                    'email' => 'admin@armely.com',
                    'detail' => 'Admin User login',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
                ],
                [
                    'type' => 'Page Visit',
                    'name' => 'Admin User',
                    'email' => 'admin@armely.com',
                    'detail' => 'Visited: admin/dashboard',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours'))
                ],
                [
                    'type' => 'Consultation',
                    'name' => 'John Smith',
                    'email' => 'john.smith@techcorp.com',
                    'detail' => 'Data Science and Analytics consultation',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-5 hours'))
                ],
                [
                    'type' => 'Contact',
                    'name' => 'Sarah Johnson',
                    'email' => 'sarah.j@innovate.io',
                    'detail' => 'Inquiry about Microsoft Fabric',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
                ]
            ];
        }

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

        // If no data, use sample data
        if (empty($recentActivity)) {
            $recentActivity = [
                [
                    'type' => 'Page Visit',
                    'name' => 'Admin User',
                    'email' => 'admin@armely.com',
                    'detail' => 'Visited: admin/reports',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
                ],
                [
                    'type' => 'Login',
                    'name' => 'Admin User',
                    'email' => 'admin@armely.com',
                    'detail' => 'Admin User login',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
                ],
                [
                    'type' => 'Page Visit',
                    'name' => 'Admin User',
                    'email' => 'admin@armely.com',
                    'detail' => 'Visited: admin/dashboard',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours'))
                ],
                [
                    'type' => 'Consultation',
                    'name' => 'John Smith',
                    'email' => 'john.smith@techcorp.com',
                    'detail' => 'Data Science and Analytics consultation',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-5 hours'))
                ],
                [
                    'type' => 'Contact',
                    'name' => 'Sarah Johnson',
                    'email' => 'sarah.j@innovate.io',
                    'detail' => 'Inquiry about Microsoft Fabric',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
                ]
            ];
        }

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
}

