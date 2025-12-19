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
            $consultations[] = $this->safeCountBetween('consultation', 'date_now', $start, $end);
            $contacts[] = $this->safeCountBetween('contacts', 'sent_date', $start, $end);
            $jobApplications[] = $this->safeCountBetween('job_applications', 'application_date', $start, $end);
        }

        return [$labels, $consultations, $contacts, $jobApplications];
    }

    private function recentActivity(): Collection
    {
        $maxResults = 8;
        $activities = collect();

        // Safely fetch consultations
        if (Schema::hasTable('consultation')) {
            $detailCol = Schema::hasColumn('consultation', 'service_name')
                ? 'service_name'
                : (Schema::hasColumn('consultation', 'service') ? 'service' : 'id');
            $dateCol = Schema::hasColumn('consultation', 'date_now') ? 'date_now' : 'created_at';
            
            try {
                $consultations = DB::table('consultation')
                    ->selectRaw("'Consultation' as type, name, email, {$detailCol} as detail, {$this->dateExpression($dateCol)} as created_at")
                    ->orderByDesc($dateCol)
                    ->limit(5)
                    ->get();
                $activities = $activities->merge($consultations);
            } catch (\Exception $e) {
                // Skip if query fails
            }
        }

        // Safely fetch contacts
        if (Schema::hasTable('contacts')) {
            $detailCol = Schema::hasColumn('contacts', 'subject') ? 'subject' : 'id';
            $dateCol = Schema::hasColumn('contacts', 'sent_date') ? 'sent_date' : 'created_at';
            
            try {
                $contacts = DB::table('contacts')
                    ->selectRaw("'Contact' as type, name, email, {$detailCol} as detail, {$this->dateExpression($dateCol)} as created_at")
                    ->orderByDesc($dateCol)
                    ->limit(5)
                    ->get();
                $activities = $activities->merge($contacts);
            } catch (\Exception $e) {
                // Skip if query fails
            }
        }

        return $activities
            ->map(function ($item) {
                $created = $item->created_at ? Carbon::parse($item->created_at) : null;

                return [
                    'type' => $item->type,
                    'name' => $item->name ?? '',
                    'email' => $item->email ?? '',
                    'detail' => $item->detail ?? '',
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
