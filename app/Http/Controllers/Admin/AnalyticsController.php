<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard.
     */
    public function index(Request $request)
    {
        $dateRange = $request->input('date_range', '30'); // days
        $entityType = $request->input('entity_type', '') ?? ''; // '' for all, 'admin', 'user', 'guest', convert null to empty string
        $startDate = Carbon::now()->subDays((int)$dateRange);

        // Get analytics summary
        $analytics = [
            'total_visits' => $this->getTotalVisits($startDate, $entityType),
            'unique_visitors' => $this->getUniqueVisitors($startDate, $entityType),
            'unique_ips' => $this->getUniqueIPs($startDate, $entityType),
            'total_users' => $this->getTotalUsers($startDate, $entityType),
            'guest_visits' => $this->getGuestVisits($startDate),
            'admin_visits' => $this->getAdminVisits($startDate),
        ];

        // Get top visited pages
        $topPages = $this->getTopPages($startDate, $entityType);

        // Get top countries
        $topCountries = $this->getTopCountries($startDate, $entityType);

        // Get top IPs
        $topIPs = $this->getTopIPs($startDate, $entityType);

        // Get visitor timeline (last 30 days)
        $visitorTimeline = $this->getVisitorTimeline($startDate);

        // Get user activity breakdown
        $userActivity = $this->getUserActivity($startDate);

        // Get referrer sources
        $referrerSources = $this->getReferrerSources($startDate);

        // Get browser/user agent stats
        $browserStats = $this->getBrowserStats($startDate);

        return view('admin.analytics.index', compact(
            'analytics',
            'topPages',
            'topCountries',
            'topIPs',
            'visitorTimeline',
            'userActivity',
            'referrerSources',
            'browserStats',
            'dateRange',
            'startDate'
        ));
    }

    /**
     * API endpoint for analytics data (returns JSON).
     */
    public function apiSummary(Request $request)
    {
        $dateRange = $request->input('date_range', '30'); // days
        $entityType = $request->input('entity_type', '') ?? ''; // filter by entity type, convert null to empty string
        $startDate = Carbon::now()->subDays((int)$dateRange);

        // Get analytics summary
        $analytics = [
            'total_visits' => $this->getTotalVisits($startDate, $entityType),
            'unique_visitors' => $this->getUniqueVisitors($startDate, $entityType),
            'unique_ips' => $this->getUniqueIPs($startDate, $entityType),
            'total_users' => $this->getTotalUsers($startDate, $entityType),
            'guest_visits' => $this->getGuestVisits($startDate),
            'admin_visits' => $this->getAdminVisits($startDate),
            'top_pages' => $this->getTopPages($startDate, $entityType),
            'top_countries' => $this->getTopCountries($startDate, $entityType),
            'top_ips' => $this->getTopIPs($startDate, $entityType),
            'visitor_timeline' => $this->getVisitorTimeline($startDate),
            'user_activity' => $this->getUserActivity($startDate),
            'date_range' => $dateRange,
            'entity_type' => $entityType,
        ];

        return response()->json($analytics);
    }

    /**
     * Export analytics as CSV.
     */
    public function exportCsv(Request $request)
    {
        $dateRange = $request->input('date_range', '30');
        $startDate = Carbon::now()->subDays((int)$dateRange);

        $activities = DB::table('admin_activities')
            ->where('action', 'page_visit')
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->select('ip_address', 'page_url', 'country', 'user_agent', 'created_at')
            ->get();

        $fileName = 'analytics-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $handle = fopen('php://memory', 'w');
        fputcsv($handle, ['IP Address', 'Page URL', 'Country', 'User Agent', 'Date']);

        foreach ($activities as $activity) {
            fputcsv($handle, [
                $activity->ip_address,
                $activity->page_url,
                $activity->country,
                $activity->user_agent,
                Carbon::parse($activity->created_at)->format('Y-m-d H:i:s'),
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, $headers);
    }

    /**
     * Export analytics as PDF.
     */
    public function exportPdf(Request $request)
    {
        $dateRange = $request->input('date_range', '30');
        $startDate = Carbon::now()->subDays((int)$dateRange);

        $analytics = [
            'total_visits' => $this->getTotalVisits($startDate),
            'unique_visitors' => $this->getUniqueVisitors($startDate),
            'unique_ips' => $this->getUniqueIPs($startDate),
            'top_pages' => $this->getTopPages($startDate),
            'top_countries' => $this->getTopCountries($startDate),
        ];

        $html = view('admin.analytics.export-pdf', compact('analytics', 'dateRange', 'startDate'))->render();
        
        // Return as downloadable HTML (browsers will convert to PDF if requested)
        $fileName = 'analytics-' . now()->format('Y-m-d-H-i-s') . '.html';
        return response($html, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }


    // Helper methods
    private function getTotalVisits(Carbon $startDate, string $entityType = ''): int
    {
        $query = DB::table('admin_activities')
            ->where('action', 'page_visit')
            ->where('created_at', '>=', $startDate);
        
        if ($entityType) {
            $query->where('entity_type', $entityType);
        }
        
        return $query->count();
    }

    private function getUniqueVisitors(Carbon $startDate, string $entityType = ''): int
    {
        $query = DB::table('admin_activities')
            ->where('action', 'page_visit')
            ->where('created_at', '>=', $startDate);
        
        if ($entityType) {
            $query->where('entity_type', $entityType);
        }
        
        return $query->distinct('ip_address')->count('ip_address');
    }

    private function getUniqueIPs(Carbon $startDate, string $entityType = ''): int
    {
        $query = DB::table('admin_activities')
            ->where('action', 'page_visit')
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('ip_address');
        
        if ($entityType) {
            $query->where('entity_type', $entityType);
        }
        
        return $query->distinct('ip_address')->count();
    }

    private function getTotalUsers(Carbon $startDate, string $entityType = ''): int
    {
        return DB::table('admin_activities')
            ->where('action', 'page_visit')
            ->where('created_at', '>=', $startDate)
            ->where('entity_type', '!=', 'guest')
            ->distinct('admin_id')
            ->count('admin_id');
    }

    private function getGuestVisits(Carbon $startDate): int
    {
        return DB::table('admin_activities')
            ->where('action', 'page_visit')
            ->where('created_at', '>=', $startDate)
            ->where('entity_type', 'guest')
            ->count();
    }

    private function getAdminVisits(Carbon $startDate): int
    {
        return DB::table('admin_activities')
            ->where('action', 'page_visit')
            ->where('created_at', '>=', $startDate)
            ->where('entity_type', 'admin')
            ->count();
    }

    private function getTopPages(Carbon $startDate, string $entityType = '')
    {
        $query = DB::table('admin_activities')
            ->where('action', 'page_visit')
            ->where('created_at', '>=', $startDate);
        
        if ($entityType) {
            $query->where('entity_type', $entityType);
        }
        
        return $query->groupBy('page_url')
            ->select('page_url', DB::raw('COUNT(*) as visits'))
            ->orderByDesc('visits')
            ->limit(10)
            ->get();
    }

    private function getTopCountries(Carbon $startDate, string $entityType = '')
    {
        $query = DB::table('admin_activities')
            ->where('action', 'page_visit')
            ->where('created_at', '>=', $startDate);
        
        if ($entityType) {
            $query->where('entity_type', $entityType);
        }
        
        return $query->whereNotNull('country')
            ->groupBy('country')
            ->select('country', DB::raw('COUNT(*) as visits'))
            ->orderByDesc('visits')
            ->limit(10)
            ->get();
    }

    private function getTopIPs(Carbon $startDate, string $entityType = '')
    {
        $query = DB::table('admin_activities')
            ->where('action', 'page_visit')
            ->where('created_at', '>=', $startDate);
        
        if ($entityType) {
            $query->where('entity_type', $entityType);
        }
        
        return $query->whereNotNull('ip_address')
            ->groupBy('ip_address', 'country')
            ->select(
                'ip_address',
                'country',
                DB::raw('COUNT(*) as visits'),
                DB::raw('COUNT(DISTINCT admin_id) as unique_users'),
                DB::raw('COUNT(DISTINCT page_url) as pages_visited')
            )
            ->orderByDesc('visits')
            ->limit(10)
            ->get();
    }

    private function getVisitorTimeline(Carbon $startDate)
    {
        $days = DB::table('admin_activities')
            ->where('action', 'page_visit')
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as visits'),
                DB::raw('COUNT(DISTINCT ip_address) as unique_visitors')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $days->map(fn($day) => [
            'date' => $day->date,
            'visits' => $day->visits,
            'unique_visitors' => $day->unique_visitors
        ]);
    }

    private function getUserActivity(Carbon $startDate)
    {
        return DB::table('admin_activities')
            ->where('action', 'page_visit')
            ->where('created_at', '>=', $startDate)
            ->select('entity_type', DB::raw('COUNT(*) as count'))
            ->groupBy('entity_type')
            ->get()
            ->map(fn($item) => ['type' => $item->entity_type, 'count' => $item->count]);
    }

    private function getReferrerSources(Carbon $startDate)
    {
        return DB::table('admin_activities')
            ->where('action', 'page_visit')
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('referrer')
            ->groupBy('referrer')
            ->select('referrer', DB::raw('COUNT(*) as visits'))
            ->orderByDesc('visits')
            ->limit(10)
            ->get();
    }

    private function getBrowserStats(Carbon $startDate)
    {
        return DB::table('admin_activities')
            ->where('action', 'page_visit')
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('user_agent')
            ->select(
                DB::raw("SUBSTRING(user_agent, 1, 50) as browser"),
                DB::raw('COUNT(*) as visits')
            )
            ->groupBy('browser')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();
    }
}
