<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

        $stats = [
            'blogs' => $this->safeCountAny(['blog', 'blogs']),
            'videos' => $this->safeCountAny(['videos', 'video']),
            'careers' => $this->safeCountAny(['careers', 'career']),
            'admins' => $this->countActiveAdmins(),
            'unique_authors' => $this->countUniqueBlogAuthors($blogTable),
            'total_consultations' => $this->safeCount('contacts'), // Changed from consultation to contacts
            'consultations_today' => $this->safeCountOnDate('contacts', 'sent_date', $today), // Changed column to sent_date
            'consultations_this_week' => $this->safeCountSince('contacts', 'sent_date', $weekAgo), // Changed column to sent_date
            'total_contacts' => $this->safeCount('contacts'),
            'contacts_today' => $this->safeCountOnDate('contacts', 'sent_date', $today),
            'total_job_apps' => $this->safeCount('job_applications'),
            'job_apps_this_month' => $this->safeCountSince('job_applications', 'application_date', $monthAgo),
            'total_campaigns' => $this->safeCount('campaigns'),
            'total_admins' => $this->safeCount('admin'),
            'active_admins' => $this->countActiveAdmins(),
        ];

        [$labels, $consultations, $contacts, $jobApplications] = $this->buildMonthlyTrend();

        $recentActivity = $this->recentActivity();

        $topAuthors = $this->topBlogAuthors($blogTable, 5);
        $topAuthorHighlight = $topAuthors->first();
        
        // Fetch recent blogs safely with column fallbacks
        $recentBlogs = collect();
        if ($blogTable) {
            $query = DB::table($blogTable);
            $orderCol = $this->resolveBlogDateColumn($blogTable);
            if ($orderCol) {
                $query->orderBy($orderCol, 'desc');
            }
            $recentBlogs = $query->limit(5)->get();
        }

        // Fetch active careers safely with column fallbacks
        $activeCareers = collect();
        if (Schema::hasTable('careers')) {
            $query = DB::table('careers');
            if (Schema::hasColumn('careers', 'status')) {
                $query->where('status', 'active');
            }
            $orderCol = Schema::hasColumn('careers', 'id') ? 'id' : (Schema::hasColumn('careers', 'created_at') ? 'created_at' : null);
            if ($orderCol) {
                $query->orderBy($orderCol, 'desc');
            }
            $activeCareers = $query->limit(5)->get();
        }

        $adminName = $this->resolveAdminName();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'recentBlogs' => $recentBlogs,
            'activeCareers' => $activeCareers,
            'topAuthors' => $topAuthors,
            'topAuthorHighlight' => $topAuthorHighlight,
            'monthlyData' => [
                'labels' => $labels,
                'consultations' => $consultations,
                'contacts' => $contacts,
                'job_applications' => $jobApplications,
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

        if (in_array($authorCol, ['admin_id', 'user_id']) && Schema::hasTable('admin') && Schema::hasColumn('admin', 'id')) {
            $ids = $results->pluck('author_key')->filter()->unique();
            $adminNames = DB::table('admin')->whereIn('id', $ids)->pluck('name', 'id');

            return $results->map(function ($row) use ($adminNames) {
                $name = $row->author_key !== null
                    ? ($adminNames[$row->author_key] ?? 'Admin #' . $row->author_key)
                    : 'Unknown';
                return [
                    'name' => $name,
                    'total' => (int) $row->total,
                ];
            });
        }

        return $results->map(function ($row) {
            return [
                'name' => $row->author_key ?? 'Unknown',
                'total' => (int) $row->total,
            ];
        });
    }

    private function buildMonthlyTrend(): array
    {
        $labels = [];
        $consultations = [];
        $contacts = [];
        $jobApplications = [];

        for ($i = 11; $i >= 0; $i--) {
            $start = Carbon::now()->startOfMonth()->subMonths($i);
            $end = Carbon::now()->endOfMonth()->subMonths($i);

            $labels[] = $start->format('M');
            $consultations[] = $this->safeCountBetween('contacts', 'sent_date', $start, $end); // Changed from consultation to contacts
            $contacts[] = $this->safeCountBetween('contacts', 'sent_date', $start, $end);
            $jobApplications[] = $this->safeCountBetween('job_applications', 'application_date', $start, $end);
        }

        return [$labels, $consultations, $contacts, $jobApplications];
    }

    private function recentActivity(): Collection
    {
        $maxResults = 8;
        $activities = [];

        // Fetch contacts
        try {
            $contacts = DB::table('contacts')
                ->select(DB::raw("'Contact' as type"), 'name', 'email', 'subject as detail', 
                    DB::raw("COALESCE(sent_date, DATE(NOW())) as created_at"))
                ->orderByRaw("COALESCE(sent_date, DATE(NOW())) DESC")
                ->limit(5)
                ->get()
                ->map(function($item) { return (array) $item; })
                ->toArray();
            $activities = array_merge($activities, $contacts);
        } catch (\Exception $e) {
            // Skip if query fails
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
            // Skip if query fails
        }

        // Fetch admin activities
        try {
            $adminActivities = DB::table('admin_activities')
                ->leftJoin('admin', 'admin_activities.admin_id', '=', 'admin.id')
                ->whereNotNull('admin_activities.admin_id') // Only activities from authenticated admins
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
                ->limit(20)
                ->get()
                ->map(function($item) { return (array) $item; })
                ->toArray();
            $activities = array_merge($activities, $adminActivities);
        } catch (\Exception $e) {
            // Skip if query fails
        }

        // Sort and limit results
        usort($activities, function($a, $b) {
            $dateA = strtotime($a['created_at'] ?? '0000-00-00 00:00:00');
            $dateB = strtotime($b['created_at'] ?? '0000-00-00 00:00:00');
            return $dateB - $dateA;
        });

        return collect($activities)
            ->map(function ($item) {
                $created = isset($item['created_at']) ? Carbon::parse($item['created_at']) : null;

                return [
                    'type' => $item['type'] ?? '',
                    'name' => $item['name'] ?? '',
                    'email' => $item['email'] ?? '',
                    'detail' => $item['detail'] ?? '',
                    'created_at' => $created,
                ];
            })
            ->sortByDesc('created_at')
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
        $safeColumn = str_replace('`', '', $column);

        return "DATE(COALESCE(STR_TO_DATE(`{$safeColumn}`, '%d %b %Y'), `{$safeColumn}`))";
    }
}
