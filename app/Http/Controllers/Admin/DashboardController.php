<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $weekAgo = Carbon::today()->subDays(7);
        $monthAgo = Carbon::today()->subDays(30);

        $blogTable = $this->resolveBlogTable();

        $stats = Cache::remember('admin_dashboard_stats', 300, function () use ($today, $weekAgo, $monthAgo, $blogTable) {
        return [
            'blogs' => $this->safeCountAny(['blog', 'blogs']),
            'videos' => $this->safeCountAny(['videos', 'video']),
            'careers' => $this->safeCountAny(['careers', 'career']),
            'admins' => $this->countActiveAdmins(),
            'unique_authors' => $this->countUniqueBlogAuthors($blogTable),
            'total_consultations' => $this->safeCount('consultation'),
            'consultations_today' => $this->safeCountOnDate('consultation', 'date_now', $today),
            'consultations_this_week' => $this->safeCountSince('consultation', 'date_now', $weekAgo),
            'total_contacts' => $this->safeCount('contacts'),
            'contacts_today' => $this->safeCountOnDate('contacts', 'sent_date', $today),
            'total_job_apps' => $this->safeCount('job_applications'),
            'job_apps_this_month' => $this->safeCountSince('job_applications', 'application_date', $monthAgo),
            'total_campaigns' => $this->safeCount('campaigns'),
            'total_admins' => $this->safeCount('admin'),
            'active_admins' => $this->countActiveAdmins(),
        ];
        });

        [$labels, $consultations, $contacts, $jobApplications, $campaigns] = $this->buildMonthlyTrend();

        $recentActivity = Cache::remember('admin_dashboard_activity', 120, fn() => $this->recentActivity());

        $topAuthors = $this->topBlogAuthors($blogTable, 10);
        $topAuthorHighlight = $topAuthors->first();
        
        // Fetch recent blogs safely with column fallbacks and author profiles
        $recentBlogs = collect();
        if ($blogTable) {
            $query = DB::table($blogTable);
            
            // Try to join with team table for profile pictures
            if (Schema::hasTable('team')) {
                $authorCol = $this->resolveBlogAuthorColumn($blogTable);
                if ($authorCol && Schema::hasColumn('team', 'team_name')) {
                    $query->leftJoin('team', $blogTable . '.' . $authorCol, '=', 'team.team_name')
                          ->select($blogTable . '.*', 'team.team_image as author_image');
                }
            }

            $orderCol = $this->resolveBlogDateColumn($blogTable);
            if ($orderCol) {
                // Use dateExpression for reliable sorting of string dates
                $query->orderByRaw($this->dateExpression($blogTable . '.' . $orderCol) . " DESC");
            }
            $recentBlogs = $query->limit(10)->get();
        }

        // Fetch all videos safely
        $allVideos = collect();
        if (Schema::hasTable('videos')) {
            $allVideos = DB::table('videos')->orderBy('id', 'desc')->get();
        }

        // Fetch active careers safely with column fallbacks
        $activeCareers = collect();
        if (Schema::hasTable('career')) {
            $query = DB::table('career');
            // If status exists, use it, otherwise show all
            if (Schema::hasColumn('career', 'status')) {
                $query->where('status', 'active');
            }
            $orderCol = Schema::hasColumn('career', 'id') ? 'id' : (Schema::hasColumn('career', 'created_at') ? 'created_at' : 'job_id');
            if ($orderCol) {
                $query->orderBy($orderCol, 'desc');
            }
            $activeCareers = $query->get(); // Pull all active careers
        }

        $adminName = $this->resolveAdminName();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'recentBlogs' => $recentBlogs,
            'activeCareers' => $activeCareers,
            'allVideos' => $allVideos,
            'topAuthors' => $topAuthors,
            'topAuthorHighlight' => $topAuthorHighlight,
            'monthlyData' => [
                'labels' => $labels,
                'consultations' => $consultations,
                'contacts' => $contacts,
                'job_applications' => $jobApplications,
                'campaigns' => $campaigns,
            ],
            'adminName' => $adminName,
        ]);
    }

    private function countAll(string $table): int
    {
        return (int) DB::table($table)->count();
    }

    private function safeCount(string $table): int
    {
        return Schema::hasTable($table) ? (int) DB::table($table)->count() : 0;
    }

    private function safeCountAny(array $tables): int
    {
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                return (int) DB::table($table)->count();
            }
        }
        return 0;
    }

    private function countOnDate(string $table, string $column, Carbon $date): int
    {
        return (int) DB::table($table)
            ->whereRaw($this->dateExpression($column) . ' = ?', [$date->toDateString()])
            ->count();
    }

    private function safeCountOnDate(string $table, string $column, Carbon $date): int
    {
        if (!Schema::hasTable($table)) return 0;
        return (int) DB::table($table)
            ->whereRaw($this->dateExpression($column) . ' = ?', [$date->toDateString()])
            ->count();
    }

    private function countSince(string $table, string $column, Carbon $date): int
    {
        return (int) DB::table($table)
            ->whereRaw($this->dateExpression($column) . ' >= ?', [$date->toDateString()])
            ->count();
    }

    private function safeCountSince(string $table, string $column, Carbon $date): int
    {
        if (!Schema::hasTable($table)) return 0;
        return (int) DB::table($table)
            ->whereRaw($this->dateExpression($column) . ' >= ?', [$date->toDateString()])
            ->count();
    }

    private function countBetween(string $table, string $column, Carbon $start, Carbon $end): int
    {
        return (int) DB::table($table)
            ->whereBetween(DB::raw($this->dateExpression($column)), [$start->toDateString(), $end->toDateString()])
            ->count();
    }

    private function safeCountBetween(string $table, string $column, Carbon $start, Carbon $end): int
    {
        if (!Schema::hasTable($table)) return 0;
        return (int) DB::table($table)
            ->whereBetween(DB::raw($this->dateExpression($column)), [$start->toDateString(), $end->toDateString()])
            ->count();
    }

    private function countActiveAdmins(): int
    {
        return (int) DB::table('admin')->where('status', 'active')->count();
    }

    private function countUniqueBlogAuthors(?string $blogTable): int
    {
        if (!$blogTable) return 0;
        $authorCol = $this->resolveBlogAuthorColumn($blogTable);
        if (!$authorCol) return 0;

        return (int) DB::table($blogTable)
            ->distinct($authorCol)
            ->count($authorCol);
    }

    private function resolveBlogTable(): ?string
    {
        if (Schema::hasTable('blog')) return 'blog';
        if (Schema::hasTable('blogs')) return 'blogs';
        return null;
    }

    private function resolveBlogDateColumn(string $table): ?string
    {
        $candidates = ['date', 'blog_date', 'published_at', 'created_at', 'id'];
        foreach ($candidates as $column) {
            if (Schema::hasColumn($table, $column)) {
                return $column;
            }
        }
        return null;
    }

    private function resolveBlogAuthorColumn(string $table): ?string
    {
        $candidates = ['author', 'blog_author', 'author_name', 'written_by', 'writer', 'created_by', 'admin_id', 'user_id'];
        foreach ($candidates as $column) {
            if (Schema::hasColumn($table, $column)) {
                return $column;
            }
        }
        return null;
    }

    private function topBlogAuthors(?string $blogTable, int $limit = 5): Collection
    {
        if (!$blogTable) return collect();
        $authorCol = $this->resolveBlogAuthorColumn($blogTable);
        if (!$authorCol) return collect();

        $results = DB::table($blogTable)
            ->select($authorCol . ' as author_key', DB::raw('COUNT(*) as total'))
            ->groupBy($authorCol)
            ->orderByDesc('total')
            ->limit($limit)
            ->get();

        $authorImages = collect();
        if (Schema::hasTable('team') && Schema::hasColumn('team', 'team_name') && Schema::hasColumn('team', 'team_image')) {
            $names = $results->pluck('author_key')->filter()->unique();
            $authorImages = DB::table('team')->whereIn('team_name', $names)->pluck('team_image', 'team_name');
        }

        if (in_array($authorCol, ['admin_id', 'user_id']) && Schema::hasTable('admin') && Schema::hasColumn('admin', 'id')) {
            $ids = $results->pluck('author_key')->filter()->unique();
            $adminNames = DB::table('admin')->whereIn('id', $ids)->pluck('name', 'id');

            return $results->map(function ($row) use ($adminNames, $authorImages) {
                $name = $row->author_key !== null
                    ? ($adminNames[$row->author_key] ?? 'Admin #' . $row->author_key)
                    : 'Unknown';
                return [
                    'name' => $name,
                    'total' => (int) $row->total,
                    'image' => $authorImages[$name] ?? null,
                ];
            });
        }

        return $results->map(function ($row) use ($authorImages) {
            $name = $row->author_key ?? 'Unknown';
            return [
                'name' => $name,
                'total' => (int) $row->total,
                'image' => $authorImages[$name] ?? null,
            ];
        });
    }

    private function buildMonthlyTrend(): array
    {
        return Cache::remember('admin_monthly_trend', 1800, function () {
        $labels = [];
        $consultations = [];
        $contacts = [];
        $jobApplications = [];
        $campaigns = [];

        for ($i = 11; $i >= 0; $i--) {
            $start = Carbon::now()->startOfMonth()->subMonths($i);
            $end = Carbon::now()->endOfMonth()->subMonths($i);

            $labels[] = $start->format('M');
            $consultations[] = $this->safeCountBetween('consultation', 'date_now', $start, $end);
            $contacts[] = $this->safeCountBetween('contacts', 'sent_date', $start, $end);
            $jobApplications[] = $this->safeCountBetween('job_applications', 'application_date', $start, $end);
            $campaigns[] = $this->safeCountBetween('campaigns', 'sent_date', $start, $end);
        }

        return [$labels, $consultations, $contacts, $jobApplications, $campaigns];
        });
    }

    private function recentActivity(): Collection
    {
        $maxResults = 10;
        $activities = [];

        // Fetch consultations
        try {
            $consultations = DB::table('consultation')
                ->select(DB::raw("'Consultation' as type"), 'name', 'email', 'service_type as detail', 
                    DB::raw("COALESCE(STR_TO_DATE(date_now, '%d %b %Y'), date_now) as created_at"))
                ->orderByRaw("COALESCE(STR_TO_DATE(date_now, '%d %b %Y'), date_now) DESC")
                ->limit(5)
                ->get()
                ->map(function($item) { return (array) $item; })
                ->toArray();
            $activities = array_merge($activities, $consultations);
        } catch (\Exception $e) {
            // Skip
        }

        // Fetch contacts
        try {
            $contacts = DB::table('contacts')
                ->select(DB::raw("'Contact' as type"), 'name', 'email', 'subject as detail', 
                    DB::raw("sent_date as created_at"))
                ->orderBy('sent_date', 'desc')
                ->limit(5)
                ->get()
                ->map(function($item) { return (array) $item; })
                ->toArray();
            $activities = array_merge($activities, $contacts);
        } catch (\Exception $e) {
            // Skip
        }

        // Fetch job applications
        try {
            $applications = DB::table('job_applications')
                ->select(DB::raw("'Job Application' as type"), 'name', 'email', 'position as detail', 'application_date as created_at')
                ->orderBy('application_date', 'desc')
                ->limit(5)
                ->get()
                ->map(function($item) { return (array) $item; })
                ->toArray();
            $activities = array_merge($activities, $applications);
        } catch (\Exception $e) {
            // Skip
        }

        // Fetch campaigns
        try {
            if (Schema::hasTable('campaigns')) {
                $campaigns = DB::table('campaigns')
                    ->select(DB::raw("'Campaign' as type"), 'full_name as name', 'business_email as email', 'company_name as detail', 'sent_date as created_at')
                    ->orderBy('sent_date', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function($item) { return (array) $item; })
                    ->toArray();
                $activities = array_merge($activities, $campaigns);
            }
        } catch (\Exception $e) {
            // Skip
        }

        // Fetch admin activities
        try {
            $adminActivities = DB::table('admin_activities')
                ->leftJoin('admin', 'admin_activities.admin_id', '=', 'admin.id')
                ->select(
                    'admin_activities.created_at', 
                    DB::raw("'Admin Activity' as type"),
                    DB::raw("COALESCE(admin.name, 'Admin') as name"),
                    DB::raw("COALESCE(admin.email, 'N/A') as email"),
                    DB::raw("CONCAT(admin_activities.action, ' ', COALESCE(admin_activities.description, '')) as detail")
                )
                ->orderBy('admin_activities.created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($item) { return (array) $item; })
                ->toArray();
            $activities = array_merge($activities, $adminActivities);
        } catch (\Exception $e) {
            // Skip
        }

        // Sort and limit results
        usort($activities, function($a, $b) {
            $dateA = $a['created_at'] ? Carbon::parse($a['created_at']) : Carbon::minValue();
            $dateB = $b['created_at'] ? Carbon::parse($b['created_at']) : Carbon::minValue();
            return $dateB->timestamp - $dateA->timestamp;
        });

        return collect($activities)
            ->map(function ($item) {
                try {
                   $created = $item['created_at'] ? Carbon::parse($item['created_at']) : null;
                } catch (\Exception $e) {
                   $created = null;
                }

                return [
                    'type' => $item['type'] ?? '',
                    'name' => $item['name'] ?? '',
                    'email' => $item['email'] ?? '',
                    'detail' => $item['detail'] ?? '',
                    'created_at' => $created,
                ];
            })
            ->take($maxResults)
            ->values();
    }

    private function resolveAdminName(): string
    {
        if ($name = session('admin_name')) {
            return $name;
        }

        return DB::table('admin')->value('name') ?? 'Admin';
    }

    private function dateExpression(string $column): string
    {
        $parts = explode('.', str_replace('`', '', $column));
        $quotedParts = array_map(function($part) {
            return "`$part`";
        }, $parts);
        $quoted = implode('.', $quotedParts);

        return "DATE(COALESCE(STR_TO_DATE($quoted, '%d %b %Y'), $quoted))";
    }
}
