<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseStudyCategory;
use App\Models\CaseStudyTechnology;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use App\Services\AzureMailService;
use App\Services\ActivityLogger;
use App\Services\NewsletterNotificationService;

class TablesController extends Controller
{
    /** @var array<string,bool> In-process cache for $this->tableExists() calls */
    private static array $tableExists = [];
    /** @var array<string,bool> In-process cache for $this->columnExists() calls */
    private static array $columnExists = [];

    private function tableExists(string $table): bool
    {
        return self::$tableExists[$table] ??= Schema::hasTable($table);
    }

    private function columnExists(string $table, string $column): bool
    {
        $key = "$table.$column";
        return self::$columnExists[$key] ??= Schema::hasColumn($table, $column);
    }

    private function firstExistingColumn(string $table, array $columns): ?string
    {
        foreach ($columns as $column) {
            if ($this->columnExists($table, $column)) {
                return $column;
            }
        }

        return null;
    }

    private function storeBlogImage(Request $request, string $blogTable): ?array
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        $request->validate([
            'image' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:5120', 'dimensions:min_width=900,min_height=500'],
        ], [
            'image.dimensions' => 'Please upload a blog image that is at least 900px wide and 500px tall so it displays consistently on the site.',
            'image.mimes' => 'Please upload a JPG, PNG, or WEBP blog image.',
            'image.max' => 'Blog images must be 5MB or smaller.',
        ]);

        $image = $request->file('image');
        $filename = time() . '_' . Str::slug($request->title ?? 'blog') . '.' . strtolower($image->getClientOriginalExtension());
        $image->move(public_path('images/blog'), $filename);

        $imageColumn = $this->columnExists($blogTable, 'image_path') ? 'image_path' : 'image';

        return [$imageColumn => 'images/blog/' . $filename];
    }

    /**
     * Move images pasted into CKEditor as data URIs out of the database.
     * The saved HTML contains a normal public URL instead of a large base64 value.
     */
    private function persistEmbeddedBlogImages(string $html): string
    {
        if ($html === '' || stripos($html, 'data:image/') === false) {
            return $html;
        }

        $uploadDirectory = public_path('ckeditor_uploads');
        File::ensureDirectoryExists($uploadDirectory);

        return preg_replace_callback(
            '/(<img\b[^>]*\bsrc\s*=\s*)(["\'])data:(image\/(?:jpeg|jpg|png|webp|gif));base64,([^"\']+)\2/i',
            function (array $matches) use ($uploadDirectory): string {
                $encoded = preg_replace('/\s+/', '', html_entity_decode($matches[4], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                $binary = base64_decode($encoded ?? '', true);

                if ($binary === false) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'body' => 'One of the pasted blog images is not valid. Please remove it and paste it again.',
                    ]);
                }

                if (strlen($binary) > 5 * 1024 * 1024) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'body' => 'Each pasted blog image must be 5MB or smaller.',
                    ]);
                }

                $mime = strtolower($matches[3]);
                $extension = match ($mime) {
                    'image/jpeg', 'image/jpg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp',
                    'image/gif' => 'gif',
                };
                $filename = now()->format('YmdHis') . '_' . Str::uuid() . '.' . $extension;
                File::put($uploadDirectory . DIRECTORY_SEPARATOR . $filename, $binary);

                return $matches[1] . $matches[2] . asset('ckeditor_uploads/' . $filename) . $matches[2];
            },
            $html
        ) ?? $html;
    }

    public function index()
    {
        $blogTable = $this->tableExists('blog') ? 'blog' : ($this->tableExists('blogs') ? 'blogs' : null);
        $blogs = $blogTable ? DB::table($blogTable)->orderBy($this->columnExists($blogTable, 'blog_id') ? 'blog_id' : 'id', 'desc')->limit(50)->get() : collect();

        $videoTable = $this->tableExists('videos') ? 'videos' : ($this->tableExists('video') ? 'video' : null);
        $videos = $videoTable ? DB::table($videoTable)->orderBy($this->columnExists($videoTable, 'video_id') ? 'video_id' : 'id', 'desc')->limit(50)->get() : collect();

        $careerTable = $this->tableExists('careers') ? 'careers' : ($this->tableExists('career') ? 'career' : null);
        $careers = $careerTable ? DB::table($careerTable)->orderBy('id', 'desc')->limit(50)->get() : collect();

        $socialImpactTable = $this->tableExists('social_impact') ? 'social_impact' : ($this->tableExists('social_impacts') ? 'social_impacts' : null);
        $socialImpact = $socialImpactTable ? DB::table($socialImpactTable)->orderBy('id', 'desc')->limit(50)->get() : collect();

        $customerStoriesTable = $this->tableExists('customer_stories') ? 'customer_stories' : ($this->tableExists('customer_story') ? 'customer_story' : null);
        $customerStories = $customerStoriesTable ? DB::table($customerStoriesTable)->orderBy('id', 'desc')->limit(50)->get() : collect();

        $caseStudies = $this->listCaseStudyResources(50);

        $eventsTable = $this->tableExists('events') ? 'events' : ($this->tableExists('event') ? 'event' : null);
        $events = $eventsTable ? DB::table($eventsTable)->orderBy('id', 'desc')->limit(50)->get() : collect();
        $normalEvents = $events->filter(fn ($event) => ($event->event_type ?? 'normal') === 'normal');
        $privateEvents = $events->filter(fn ($event) => ($event->event_type ?? 'normal') === 'private');
        $normalEventCount = $normalEvents->count();
        $privateEventCount = $privateEvents->count();
        $privateEventIds = $privateEvents->pluck('id');
        $privateEventTitles = $privateEvents
            ->pluck('title')
            ->filter()
            ->push('Sovereign Data Clouds with Snowflake')
            ->unique()
            ->values();
        $eventRegistrations = $this->tableExists('event_registrations')
            ? DB::table('event_registrations')
                ->where(function ($query) use ($privateEventIds, $privateEventTitles) {
                    $query->whereIn('event_id', $privateEventIds)
                        ->orWhere(function ($legacyQuery) use ($privateEventTitles) {
                            $legacyQuery->whereNull('event_id')->whereIn('event_name', $privateEventTitles);
                        });
                })
                ->orderByDesc('id')
                ->limit(250)
                ->get()
            : collect();

        $teamTable = $this->tableExists('team') ? 'team' : ($this->tableExists('teams') ? 'teams' : null);
        $team = $teamTable ? DB::table($teamTable)->orderBy('id', 'desc')->limit(50)->get() : collect();

        $contacts = $this->tableExists('contacts') ? DB::table('contacts')->orderBy('id', 'desc')->limit(50)->get() : collect();
        $newsletterSubscribers = $this->tableExists('newsletter_subscribers') ? DB::table('newsletter_subscribers')->orderByDesc('id')->limit(250)->get() : collect();
        $siteBanners = $this->tableExists('website_ad_banners')
            ? DB::table('website_ad_banners')
                ->where('page', 'global')
                ->orderBy('display_order')
                ->orderByDesc('id')
                ->limit(50)
                ->get()
            : collect();
        $announcements = $this->tableExists('announcements')
            ? DB::table('announcements')
                ->orderBy('display_order')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit(100)
                ->get()
            : collect();

        $adminAuthors = $this->tableExists('admin')
            ? DB::table('admin')
                ->whereNotNull('name')
                ->where('name', '!=', '')
                ->orderBy('name')
                ->pluck('name')
                ->unique()
                ->values()
            : collect();

        $caseStudyCategories = $this->caseStudyCategoryOptions();
        $caseStudyTechnologies = $this->caseStudyTechnologyOptions();

        return view('admin.tables', compact('blogs', 'videos', 'careers', 'socialImpact', 'customerStories', 'caseStudies', 'events', 'normalEventCount', 'privateEventCount', 'eventRegistrations', 'team', 'contacts', 'newsletterSubscribers', 'siteBanners', 'announcements', 'adminAuthors', 'caseStudyCategories', 'caseStudyTechnologies'));
    }
    
    // ========== LIST ENDPOINTS FOR AJAX TABLE RELOAD ==========
    
    public function listBlogs(Request $request)
    {
        $blogTable = $this->tableExists('blogs') ? 'blogs' : 'blog';
        
        // Check if it's a DataTables AJAX request
        if ($request->has('draw')) {
            $query = DB::table($blogTable);
            $totalData = $query->count();
            
            // Searching
            $searchValue = trim((string) $request->input('search.value', ''));
            if ($searchValue !== '') {
                // Never scan body/content/description here. Legacy blog content
                // can contain megabytes of base64 image data.
                $searchColumns = array_values(array_filter([
                    $this->columnExists($blogTable, 'title') ? 'title' : null,
                    $this->columnExists($blogTable, 'blog_title') ? 'blog_title' : null,
                    $this->columnExists($blogTable, 'author') ? 'author' : null,
                    $this->columnExists($blogTable, 'date') ? 'date' : null,
                    $this->columnExists($blogTable, 'blog_date') ? 'blog_date' : null,
                ]));

                $query->where(function ($q) use ($searchValue, $searchColumns) {
                    foreach ($searchColumns as $index => $column) {
                        $method = $index === 0 ? 'where' : 'orWhere';
                        $q->{$method}($column, 'like', '%' . $searchValue . '%');
                    }
                });
            }
            
            $totalFiltered = $query->count();
            
            // Ordering
            $requestedOrderColumn = $request->input('order.0.column');
            $orderColIndex = $requestedOrderColumn !== null ? (int) $requestedOrderColumn : null;
            $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            
            // Map column indices to actual DB columns
            $columns = [
                0 => $this->columnExists($blogTable, 'title') ? 'title' : ($this->columnExists($blogTable, 'blog_title') ? 'blog_title' : 'id'),
                1 => $this->columnExists($blogTable, 'author') ? 'author' : 'id',
                2 => $this->columnExists($blogTable, 'date') ? 'date' : ($this->columnExists($blogTable, 'blog_date') ? 'blog_date' : 'id'),
                3 => $this->columnExists($blogTable, 'blog_id') ? 'blog_id' : 'id',
            ];
            
            $recentColumn = $this->columnExists($blogTable, 'blog_id') ? 'blog_id' : 'id';
            $orderBy = $orderColIndex !== null ? ($columns[$orderColIndex] ?? $recentColumn) : $recentColumn;
            
            // Paging
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            
            // Keep the DataTables response small. Blog bodies can contain legacy
            // base64 images and are loaded only when View/Edit is opened.
            $listColumns = array_values(array_filter([
                $this->columnExists($blogTable, 'id') ? 'id' : null,
                $this->columnExists($blogTable, 'blog_id') ? 'blog_id' : null,
                $this->columnExists($blogTable, 'title') ? 'title' : null,
                $this->columnExists($blogTable, 'blog_title') ? 'blog_title' : null,
                $this->columnExists($blogTable, 'author') ? 'author' : null,
                $this->columnExists($blogTable, 'date') ? 'date' : null,
                $this->columnExists($blogTable, 'blog_date') ? 'blog_date' : null,
                $this->columnExists($blogTable, 'image_path') ? 'image_path' : null,
                $this->columnExists($blogTable, 'image') ? 'image' : null,
                $this->columnExists($blogTable, 'status') ? 'status' : null,
            ]));

            $blogs = $query->select($listColumns)
                ->orderBy($orderBy, $orderDir)
                ->when($orderBy !== $recentColumn, fn ($orderedQuery) => $orderedQuery->orderByDesc($recentColumn))
                ->offset($start)
                ->limit($length)
                ->get();
                
            return response()->json([
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $blogs
            ]);
        }

        // Fallback for non-datatable requests
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 500));
        
        $start = microtime(true);
        try {
            $blogs = DB::table($blogTable)
                ->orderBy($this->columnExists($blogTable, 'blog_id') ? 'blog_id' : 'id', 'desc')
                ->limit($limit)
                ->get();
            
            return response()->json(['success' => true, 'data' => $blogs, 'limit' => $limit]);
        } catch (\Throwable $e) {
            Log::error('listBlogs fallback failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }
    
    public function listVideos(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 500));

        $videoTable = $this->tableExists('videos') ? 'videos' : 'video';
        $videos = DB::table($videoTable)->orderBy('id', 'desc')->limit($limit)->get();
        return response()->json(['success' => true, 'data' => $videos, 'limit' => $limit]);
    }
    
    public function listCareers(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 500));

        $careerTable = $this->tableExists('career') ? 'career' : 'careers';
        $careers = DB::table($careerTable)->orderBy('id', 'desc')->limit($limit)->get();
        return response()->json(['success' => true, 'data' => $careers, 'limit' => $limit]);
    }
    
    public function listSocialImpact(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 500));

        $table = $this->tableExists('social_impact') ? 'social_impact' : 'social_impacts';
        $items = DB::table($table)->orderBy('id', 'desc')->limit($limit)->get();
        return response()->json(['success' => true, 'data' => $items, 'limit' => $limit]);
    }
    
    public function listCustomerStories(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 500));

        $table = $this->tableExists('customer_stories') ? 'customer_stories' : 'customer_story';
        $stories = DB::table($table)->orderBy('id', 'desc')->limit($limit)->get();
        return response()->json(['success' => true, 'data' => $stories, 'limit' => $limit]);
    }

    public function listCaseStudies(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 500));

        $caseStudies = $this->listCaseStudyResources($limit);
        return response()->json(['success' => true, 'data' => $caseStudies, 'limit' => $limit]);
    }

    private function listCaseStudyResources(int $limit)
    {
        $items = collect();

        if ($this->tableExists('industry_listings')) {
            $caseStudyQuery = DB::table('industry_listings')
                ->select('id', 'category', 'body', 'listing_image', 'pdf_url')
                ->orderBy('id', 'desc')
                ->limit($limit);

            if ($this->columnExists('industry_listings', 'title')) {
                $caseStudyQuery->addSelect('title');
            }

            if ($this->columnExists('industry_listings', 'outcome_tag')) {
                $caseStudyQuery->addSelect('outcome_tag');
            }

            if ($this->columnExists('industry_listings', 'technology')) {
                $caseStudyQuery->addSelect('technology');
            }

            if ($this->columnExists('industry_listings', 'one_pager_content')) {
                $caseStudyQuery->addSelect('one_pager_content');
            }

            if ($this->columnExists('industry_listings', 'created_at')) {
                $caseStudyQuery->addSelect('created_at');
            }

            $caseStudies = $caseStudyQuery->get()->map(function ($item) {
                $item->resource_type = 'case_study';
                return $item;
            });

            $items = $items->concat($caseStudies);
        }

        if ($this->tableExists('white_paper')) {
            $titleColumn = $this->firstExistingColumn('white_paper', ['title']);
            $bodyColumn = $this->firstExistingColumn('white_paper', ['body', 'description', 'content']);
            $imageColumn = $this->firstExistingColumn('white_paper', ['images', 'image', 'image_path']);
            $pdfColumn = $this->firstExistingColumn('white_paper', ['pdf', 'pdf_url']);

            $whitePaperQuery = DB::table('white_paper')
                ->select('id')
                ->orderBy('id', 'desc')
                ->limit($limit);

            $whitePaperQuery->selectRaw(($titleColumn ? $titleColumn : 'NULL') . ' as title');
            $whitePaperQuery->selectRaw(($bodyColumn ? $bodyColumn : 'NULL') . ' as body');
            $whitePaperQuery->selectRaw(($imageColumn ? $imageColumn : 'NULL') . ' as listing_image');
            $whitePaperQuery->selectRaw(($pdfColumn ? $pdfColumn : 'NULL') . ' as pdf_url');
            $whitePaperQuery->selectRaw('NULL as category');

            if ($this->columnExists('white_paper', 'created_at')) {
                $whitePaperQuery->addSelect('created_at');
            } else {
                $whitePaperQuery->selectRaw('NULL as created_at');
            }

            $whitePapers = $whitePaperQuery->get()->map(function ($item) {
                $item->resource_type = 'white_paper';
                return $item;
            });

            $items = $items->concat($whitePapers);
        }

        return $items
            ->sortByDesc(function ($item) {
                $createdAt = isset($item->created_at) ? strtotime((string) $item->created_at) : 0;
                return $createdAt > 0 ? $createdAt : (int) ($item->id ?? 0);
            })
            ->take($limit)
            ->values();
    }

    private function caseStudyCategoryDefaults(): array
    {
        return [
            'Healthcare',
            'Energy (Oil & Gas)',
            'Government & Public Sector',
            'Legal (Social Services)',
            'Transportation & Logistics',
            'Agriculture/Cannabis',
        ];
    }

    /**
     * Active technologies as [slug => name] for the case-study form dropdown,
     * the public filters, and validation. Falls back to model defaults.
     */
    private function caseStudyTechnologyOptions(): \Illuminate\Support\Collection
    {
        if ($this->tableExists('case_study_technologies')) {
            try {
                CaseStudyTechnology::syncDefaults();

                $technologies = DB::table('case_study_technologies')
                    ->select('slug', 'name')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->pluck('name', 'slug');

                if ($technologies->isNotEmpty()) {
                    return $technologies;
                }
            } catch (\Throwable $e) {
                Log::warning('Unable to load case study technologies', ['error' => $e->getMessage()]);
            }
        }

        $defaults = collect();
        foreach (CaseStudyTechnology::defaults() as $name => $slug) {
            $defaults->put($slug, $name);
        }

        return $defaults;
    }

    private function caseStudyCategoryOptions(): \Illuminate\Support\Collection
    {
        if ($this->tableExists('case_study_categories')) {
            try {
                CaseStudyCategory::syncDefaults();

                $categories = DB::table('case_study_categories')
                    ->select('name')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->pluck('name');

                if ($categories->isNotEmpty()) {
                    return $categories;
                }
            } catch (\Throwable $e) {
                Log::warning('Unable to load case study categories', ['error' => $e->getMessage()]);
            }
        }

        return collect($this->caseStudyCategoryDefaults());
    }

    private function normalizeCaseStudyCategory(string $value): string
    {
        $normalized = Str::slug(Str::lower(trim($value)));

        $map = [
            'government-public-sector' => 'Government & Public Sector',
            'public-sector' => 'Government & Public Sector',
            'local-government' => 'Government & Public Sector',
            'state-local-government' => 'Government & Public Sector',
            'energy' => 'Energy (Oil & Gas)',
            'energy-utilities' => 'Energy (Oil & Gas)',
            'oil-gas' => 'Energy (Oil & Gas)',
            'legal' => 'Legal (Social Services)',
            'social-services' => 'Legal (Social Services)',
            'transportation' => 'Transportation & Logistics',
            'transportation-logistics' => 'Transportation & Logistics',
            'logistics' => 'Transportation & Logistics',
            'agriculture' => 'Agriculture/Cannabis',
            'cannabis' => 'Agriculture/Cannabis',
            'agriculture-cannabis' => 'Agriculture/Cannabis',
            'education' => 'Government & Public Sector',
            'high-tech' => 'Transportation & Logistics',
            'high-tech-consulting' => 'Transportation & Logistics',
            'power-platform' => 'Government & Public Sector',
        ];

        if (array_key_exists($normalized, $map)) {
            return $map[$normalized];
        }

        return trim($value);
    }
    
    public function listEvents(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 500));

        $table = $this->tableExists('events') ? 'events' : 'event';
        $events = DB::table($table)->orderBy('id', 'desc')->limit($limit)->get();
        return response()->json(['success' => true, 'data' => $events, 'limit' => $limit]);
    }
    
    public function listTeam(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 500));

        $table = $this->tableExists('team') ? 'team' : 'teams';
        $team = DB::table($table)->orderBy('id', 'desc')->limit($limit)->get();
        return response()->json(['success' => true, 'data' => $team, 'limit' => $limit]);
    }

    public function listContacts(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 500));

        $contacts = DB::table('contacts')->orderBy('id', 'desc')->limit($limit)->get();
        return response()->json($contacts);
    }

    public function storeAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:announcement,offer',
            'summary' => 'nullable|string',
            'body_html' => 'required|string',
            'cta_label' => 'nullable|string|max:120',
            'cta_url' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer|min:0|max:9999',
            'published_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        if (!$this->tableExists('announcements')) {
            return back()->with('error', 'Announcements table is missing. Please run migrations first.');
        }

        $id = DB::table('announcements')->insertGetId([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'summary' => $validated['summary'] ?? null,
            'body_html' => $validated['body_html'],
            'cta_label' => $validated['cta_label'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
            'display_order' => (int) ($validated['display_order'] ?? 0),
            'published_at' => $validated['published_at'] ?? now(),
            'is_active' => $request->boolean('is_active', true),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ActivityLogger::log('create', 'announcement', $id, 'Created announcement from admin tables');

        return back()->with('success', 'Announcement created successfully.');
    }

    public function updateAnnouncement(Request $request, int $id)
    {
        if (!$this->tableExists('announcements')) {
            return back()->with('error', 'Announcements table is missing. Please run migrations first.');
        }

        $record = DB::table('announcements')->where('id', $id)->first();
        if (!$record) {
            return back()->with('error', 'Announcement not found.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:announcement,offer',
            'summary' => 'nullable|string',
            'body_html' => 'required|string',
            'cta_label' => 'nullable|string|max:120',
            'cta_url' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer|min:0|max:9999',
            'published_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        DB::table('announcements')->where('id', $id)->update([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'summary' => $validated['summary'] ?? null,
            'body_html' => $validated['body_html'],
            'cta_label' => $validated['cta_label'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
            'display_order' => (int) ($validated['display_order'] ?? 0),
            'published_at' => $validated['published_at'] ?? $record->published_at ?? now(),
            'is_active' => $request->boolean('is_active'),
            'updated_at' => now(),
        ]);

        ActivityLogger::log('update', 'announcement', $id, 'Updated announcement from admin tables');

        return back()->with('success', 'Announcement updated successfully.');
    }

    public function toggleAnnouncementStatus(int $id)
    {
        if (!$this->tableExists('announcements')) {
            return back()->with('error', 'Announcements table is missing. Please run migrations first.');
        }

        $record = DB::table('announcements')->where('id', $id)->first();
        if (!$record) {
            return back()->with('error', 'Announcement not found.');
        }

        $newStatus = ! (bool) $record->is_active;
        DB::table('announcements')->where('id', $id)->update([
            'is_active' => $newStatus,
            'updated_at' => now(),
        ]);

        ActivityLogger::log(
            'update',
            'announcement',
            $id,
            $newStatus ? 'Activated announcement from admin tables' : 'Deactivated announcement from admin tables'
        );

        return back()->with('success', $newStatus ? 'Announcement activated successfully.' : 'Announcement deactivated successfully.');
    }

    public function deleteAnnouncement(int $id)
    {
        if (!$this->tableExists('announcements')) {
            return back()->with('error', 'Announcements table is missing. Please run migrations first.');
        }

        $record = DB::table('announcements')->where('id', $id)->first();
        if (!$record) {
            return back()->with('error', 'Announcement not found.');
        }

        DB::table('announcements')->where('id', $id)->delete();
        ActivityLogger::log('delete', 'announcement', $id, 'Deleted announcement from admin tables');

        return back()->with('success', 'Announcement deleted successfully.');
    }

    // Public ping endpoint for quick health / connectivity checks (no heavy DB work)
    public function ping()
    {
        // Small, unconditional file append to help deployed debugging when logging is broken
        try {
            @file_put_contents(storage_path('logs/debug_ping.txt'), "[".now()."] ping\n", FILE_APPEND | LOCK_EX);
        } catch (\Throwable $__e) {
        }

        return response()->json([
            'ok' => true,
            'time' => now()->toDateTimeString(),
            'env' => config('app.env') ?? 'unknown'
        ]);
    }
    
    // ========== END LIST ENDPOINTS ==========
    
    // Blog Management
    public function storeOrUpdateBlog(Request $request)
    {
        $blogTable = $this->tableExists('blogs') ? 'blogs' : 'blog';
        $idColumn = $this->columnExists($blogTable, 'blog_id') ? 'blog_id' : 'id';
        
        // Check if this is an update or create
        if ($request->has('id') && $request->id) {
            // UPDATE - Only update fields that are provided
            $data = [];
            
            if ($request->filled('title')) {
                $titleColumn = $this->columnExists($blogTable, 'title') ? 'title' : 'blog_title';
                $data[$titleColumn] = $request->title;
            }
            
            if ($request->filled('author')) {
                $data['author'] = $request->author;
            }
            
            if ($request->filled('date')) {
                $dateColumn = $this->columnExists($blogTable, 'date') ? 'date' : 'blog_date';
                $data[$dateColumn] = $request->date;
            }
            
            if ($request->filled('body')) {
                $bodyColumn = $this->columnExists($blogTable, 'body') ? 'body' : 'content';
                $data[$bodyColumn] = $this->persistEmbeddedBlogImages($request->body);
            }
            
            if ($imageData = $this->storeBlogImage($request, $blogTable)) {
                $data = array_merge($data, $imageData);
            }

            if (!empty($data)) {
                DB::table($blogTable)->where($idColumn, $request->id)->update($data);
            }

            $blog = DB::table($blogTable)->where($idColumn, $request->id)->first();
            if (!empty($data)) {
                ActivityLogger::log('update', 'Blog', $request->id, 'Updated blog ' . ($request->title ?? ($blog->title ?? $blog->blog_title ?? '')));
                app(NewsletterNotificationService::class)->sendBlogNotification($blog, $idColumn);
            }
            return response()->json(['success' => true, 'message' => 'Blog updated successfully', 'data' => $blog]);
        } else {
            // CREATE - Require all fields
            $data = [];
            
            if ($request->has('title')) {
                $titleColumn = $this->columnExists($blogTable, 'title') ? 'title' : 'blog_title';
                $data[$titleColumn] = $request->title;
            }
            
            if ($request->has('author')) {
                $data['author'] = $request->author;
            }
            
            if ($request->has('date')) {
                $dateColumn = $this->columnExists($blogTable, 'date') ? 'date' : 'blog_date';
                $data[$dateColumn] = $request->date;
            }
            
            if ($request->has('body')) {
                $bodyColumn = $this->columnExists($blogTable, 'body') ? 'body' : 'content';
                $data[$bodyColumn] = $this->persistEmbeddedBlogImages((string) $request->body);
            }
            
            if ($imageData = $this->storeBlogImage($request, $blogTable)) {
                $data = array_merge($data, $imageData);
            }
            
            // Generate IDs if needed
            if ($idColumn === 'blog_id') {
                $maxBlogId = DB::table($blogTable)->max('blog_id') ?? 0;
                $data['blog_id'] = $maxBlogId + 1;
            }
            
            if ($this->columnExists($blogTable, 'id') && $idColumn !== 'id') {
                $maxId = DB::table($blogTable)->max('id') ?? 0;
                $data['id'] = $maxId + 1;
            }
            
            // Add default values for fields that don't have defaults
            if ($this->columnExists($blogTable, 'clicks') && !isset($data['clicks'])) {
                $data['clicks'] = 0;
            }
            if ($this->columnExists($blogTable, 'views') && !isset($data['views'])) {
                $data['views'] = 0;
            }
            if ($this->columnExists($blogTable, 'status') && !isset($data['status'])) {
                $data['status'] = 'published';
            }
            
            $insertedId = null;
            if ($this->columnExists($blogTable, 'id')) {
                $insertedId = DB::table($blogTable)->insertGetId($data);
            } else {
                DB::table($blogTable)->insert($data);
            }

            $blogQuery = DB::table($blogTable);
            if ($insertedId !== null) {
                $blogQuery->where('id', $insertedId);
            } elseif ($idColumn === 'blog_id' && isset($data['blog_id'])) {
                $blogQuery->where('blog_id', $data['blog_id']);
            } elseif ($idColumn === 'id' && isset($data['id'])) {
                $blogQuery->where('id', $data['id']);
            } else {
                $blogQuery->orderBy($idColumn, 'desc');
            }

            $blog = $blogQuery->first();
            if (!$blog) {
                $blogIdValue = $insertedId ?? ($data[$idColumn] ?? null);
                $blog = (object) array_merge($data, [
                    'id' => $blogIdValue,
                    'blog_id' => $blogIdValue,
                    'title' => $data['title'] ?? $data['blog_title'] ?? ($request->title ?? ''),
                    'blog_title' => $data['blog_title'] ?? $data['title'] ?? ($request->title ?? ''),
                    'author' => $data['author'] ?? ($request->author ?? ''),
                    'date' => $data['date'] ?? ($request->date ?? ''),
                    'blog_date' => $data['blog_date'] ?? $data['date'] ?? ($request->date ?? ''),
                    'body' => $data['body'] ?? ($request->body ?? ''),
                    'content' => $data['content'] ?? $data['body'] ?? ($request->body ?? ''),
                    'image_path' => $data['image_path'] ?? $data['image'] ?? $data['image_url'] ?? null,
                ]);
            }
            if ($blog) {
                ActivityLogger::log('create', 'Blog', $blog->{$idColumn} ?? ($blog->id ?? null), 'Created blog ' . ($request->title ?? ($blog->title ?? $blog->blog_title ?? '')));
                app(NewsletterNotificationService::class)->sendBlogNotification($blog, $idColumn);
            } else {
                ActivityLogger::log('create', 'Blog', $insertedId ?? ($data[$idColumn] ?? null), 'Created blog ' . ($request->title ?? ''));
                Log::warning('Blog create saved, but the inserted blog row could not be reloaded for notification.', [
                    'table' => $blogTable,
                    'id_column' => $idColumn,
                    'inserted_id' => $insertedId,
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Blog created successfully', 'data' => $blog]);
        }
    }
    
    public function deleteBlog($id)
    {
        $blogTable = $this->tableExists('blogs') ? 'blogs' : 'blog';
        $idColumn = $this->columnExists($blogTable, 'blog_id') ? 'blog_id' : 'id';
        DB::table($blogTable)->where($idColumn, $id)->delete();
        ActivityLogger::log('delete', 'Blog', $id, 'Deleted blog #' . $id);
        return response()->json(['success' => true, 'message' => 'Blog deleted successfully']);
    }
    
    // Video Management
    public function storeOrUpdateVideo(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'url' => 'required|string',
        ]);
        
        $videoTable = $this->tableExists('videos') ? 'videos' : 'youtube_videos';
        $idColumn = $this->columnExists($videoTable, 'video_id') ? 'video_id' : 'id';
        
        $data = [];

        if ($this->columnExists($videoTable, 'title')) {
            $data['title'] = $validated['title'];
        } elseif ($this->columnExists($videoTable, 'video_title')) {
            $data['video_title'] = $validated['title'];
        } elseif ($this->columnExists($videoTable, 'video_name')) {
            $data['video_name'] = $validated['title'];
        }

        if (array_key_exists('description', $validated)) {
            if ($this->columnExists($videoTable, 'description')) {
                $data['description'] = $validated['description'];
            } elseif ($this->columnExists($videoTable, 'video_description')) {
                $data['video_description'] = $validated['description'];
            }
        }
        
        // Add url/iframe column with fallback
        if ($this->columnExists($videoTable, 'url')) {
            $data['url'] = $validated['url'];
        } elseif ($this->columnExists($videoTable, 'video_url')) {
            $data['video_url'] = $validated['url'];
        } elseif ($this->columnExists($videoTable, 'iframe')) {
            $data['iframe'] = $validated['url'];
        } elseif ($this->columnExists($videoTable, 'embed')) {
            $data['embed'] = $validated['url'];
        }
        
        if ($request->has('id') && $request->id) {
            DB::table($videoTable)->where($idColumn, $request->id)->update($data);
            ActivityLogger::log('update', 'Video', $request->id, 'Updated video');
            return response()->json(['success' => true, 'message' => 'Video updated successfully']);
        } else {
            $id = DB::table($videoTable)->insertGetId($data);
            ActivityLogger::log('create', 'Video', $id, 'Created video');
            return response()->json(['success' => true, 'message' => 'Video created successfully', 'id' => $id]);
        }
    }
    
    public function deleteVideo($id)
    {
        $videoTable = $this->tableExists('videos') ? 'videos' : 'video';
        $idColumn = $this->columnExists($videoTable, 'video_id') ? 'video_id' : 'id';
        DB::table($videoTable)->where($idColumn, $id)->delete();
        ActivityLogger::log('delete', 'Video', $id, 'Deleted video #' . $id);
        return response()->json(['success' => true, 'message' => 'Video deleted successfully']);
    }
    
    // Career Management
    public function storeOrUpdateCareer(Request $request)
    {
        $careerTable = $this->tableExists('career') ? 'career' : 'careers';
        
        if ($request->has('id') && $request->id) {
            // UPDATE
            $data = [];
            
            if ($request->filled('job_title')) {
                $col = $this->columnExists($careerTable, 'job_title') ? 'job_title' : 'title';
                $data[$col] = $request->job_title;
            }
            if ($request->filled('job_description')) {
                $col = $this->columnExists($careerTable, 'job_description') ? 'job_description' : 'description';
                $data[$col] = $request->job_description;
            }
            if ($request->filled('job_location')) {
                $col = $this->columnExists($careerTable, 'job_location') ? 'job_location' : 'location';
                $data[$col] = $request->job_location;
            }
            if ($request->filled('job_type')) {
                $col = $this->columnExists($careerTable, 'job_type') ? 'job_type' : 'type';
                $data[$col] = $request->job_type;
            }
            if ($request->filled('job_deadline')) {
                $col = $this->columnExists($careerTable, 'job_deadline') ? 'job_deadline' : 'deadline';
                $data[$col] = $request->job_deadline;
            }
            
            if (!empty($data)) {
                DB::table($careerTable)->where('id', $request->id)->update($data);
            }
            
            $career = DB::table($careerTable)->where('id', $request->id)->first();
            ActivityLogger::log('update', 'Career', $request->id, 'Updated career ' . ($career->job_title ?? $career->title ?? ''));
            return response()->json(['success' => true, 'message' => 'Career updated successfully', 'data' => $career]);
        } else {
            // CREATE
            $data = [];
            
            if ($request->filled('job_title')) {
                $col = $this->columnExists($careerTable, 'job_title') ? 'job_title' : 'title';
                $data[$col] = $request->job_title;
            }
            if ($request->filled('job_description')) {
                $col = $this->columnExists($careerTable, 'job_description') ? 'job_description' : 'description';
                $data[$col] = $request->job_description;
            }
            if ($request->filled('job_location')) {
                $col = $this->columnExists($careerTable, 'job_location') ? 'job_location' : 'location';
                $data[$col] = $request->job_location;
            }
            if ($request->filled('job_type')) {
                $col = $this->columnExists($careerTable, 'job_type') ? 'job_type' : 'type';
                $data[$col] = $request->job_type;
            }
            if ($request->filled('job_deadline')) {
                $col = $this->columnExists($careerTable, 'job_deadline') ? 'job_deadline' : 'deadline';
                $data[$col] = $request->job_deadline;
            }
            
            $id = DB::table($careerTable)->insertGetId($data);
            $career = DB::table($careerTable)->where('id', $id)->first();
            ActivityLogger::log('create', 'Career', $id, 'Created career ' . ($career->job_title ?? $career->title ?? ''));
            return response()->json(['success' => true, 'message' => 'Career created successfully', 'data' => $career]);
        }
    }
    
    public function deleteCareer($id)
    {
        $careerTable = $this->tableExists('career') ? 'career' : 'careers';
        DB::table($careerTable)->where('id', $id)->delete();
        ActivityLogger::log('delete', 'Career', $id, 'Deleted career #' . $id);
        return response()->json(['success' => true, 'message' => 'Career deleted successfully']);
    }
    
    // Social Impact Management
    public function storeOrUpdateSocialImpact(Request $request)
    {
        $table = $this->tableExists('social_impact') ? 'social_impact' : 'social_impacts';
        
        if ($request->has('id') && $request->id) {
            // UPDATE
            $data = [];
            
            if ($request->filled('title')) {
                $col = $this->columnExists($table, 'title') ? 'title' : 'impact_title';
                $data[$col] = $request->title;
            }
            if ($request->filled('body')) {
                $col = $this->columnExists($table, 'body') ? 'body' : 'content';
                $data[$col] = $request->body;
            }
            if ($request->filled('category')) {
                $col = $this->columnExists($table, 'category') ? 'category' : 'impact_area';
                $data[$col] = $request->category;
            }
            if ($request->filled('posted_date')) {
                $col = $this->columnExists($table, 'posted_date') ? 'posted_date' : 'published_date';
                $data[$col] = $request->posted_date;
            }
            if ($request->filled('author_name')) {
                $col = $this->columnExists($table, 'author_name') ? 'author_name' : 'author';
                $data[$col] = $request->author_name;
            }
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/social-impact'), $filename);
                
                if ($this->columnExists($table, 'image_url')) {
                    $data['image_url'] = 'images/social-impact/' . $filename;
                } elseif ($this->columnExists($table, 'image')) {
                    $data['image'] = 'images/social-impact/' . $filename;
                }
            }
            
            if (!empty($data)) {
                DB::table($table)->where('id', $request->id)->update($data);
            }
            
            $item = DB::table($table)->where('id', $request->id)->first();
            ActivityLogger::log('update', 'SocialImpact', $request->id, 'Updated social impact ' . ($item->title ?? $item->impact_title ?? ''));
            return response()->json(['success' => true, 'message' => 'Social impact updated successfully', 'data' => $item]);
        } else {
            // CREATE
            $data = [];
            
            if ($request->filled('title')) {
                $col = $this->columnExists($table, 'title') ? 'title' : 'impact_title';
                $data[$col] = $request->title;
            }
            if ($request->filled('body')) {
                $col = $this->columnExists($table, 'body') ? 'body' : 'content';
                $data[$col] = $request->body;
            }
            if ($request->filled('category')) {
                $col = $this->columnExists($table, 'category') ? 'category' : 'impact_area';
                $data[$col] = $request->category;
            }
            if ($request->filled('posted_date')) {
                $col = $this->columnExists($table, 'posted_date') ? 'posted_date' : 'published_date';
                $data[$col] = $request->posted_date;
            }
            if ($request->filled('author_name')) {
                $col = $this->columnExists($table, 'author_name') ? 'author_name' : 'author';
                $data[$col] = $request->author_name;
            }
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/social-impact'), $filename);
                
                if ($this->columnExists($table, 'image_url')) {
                    $data['image_url'] = 'images/social-impact/' . $filename;
                } elseif ($this->columnExists($table, 'image')) {
                    $data['image'] = 'images/social-impact/' . $filename;
                }
            }
            
            // Set defaults
            if (!isset($data['author_name']) && $this->columnExists($table, 'author_name')) {
                $data['author_name'] = 'Admin';
            }
            
            $id = DB::table($table)->insertGetId($data);
            $item = DB::table($table)->where('id', $id)->first();
            ActivityLogger::log('create', 'SocialImpact', $id, 'Created social impact ' . ($item->title ?? $item->impact_title ?? ''));
            return response()->json(['success' => true, 'message' => 'Social impact created successfully', 'data' => $item]);
        }
    }
    
    public function deleteSocialImpact($id)
    {
        $table = $this->tableExists('social_impact') ? 'social_impact' : 'social_impacts';
        DB::table($table)->where('id', $id)->delete();
        ActivityLogger::log('delete', 'SocialImpact', $id, 'Deleted social impact #' . $id);
        return response()->json(['success' => true, 'message' => 'Social Impact deleted successfully']);
    }
    
    // Customer Stories Management
    public function storeOrUpdateCustomerStory(Request $request)
    {
        $table = $this->tableExists('customer_stories') ? 'customer_stories' : 'customer_story';
        
        if ($request->has('id') && $request->id) {
            // UPDATE
            $data = [];
            
            if ($request->filled('name')) {
                $data['name'] = $request->name;
            }
            if ($request->filled('position')) {
                $data['position'] = $request->position;
            }
            if ($request->has('company') && $this->columnExists($table, 'company')) {
                $data['company'] = $request->input('company');
            }
            if ($request->has('pdf_url') && $this->columnExists($table, 'pdf_url')) {
                $data['pdf_url'] = $request->input('pdf_url');
            }
            if ($request->filled('body_content')) {
                $col = $this->columnExists($table, 'body_content') ? 'body_content' : 'content';
                $data[$col] = $request->body_content;
            }

            if ($request->hasFile('profile')) {
                $image = $request->file('profile');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/customers'), $filename);

                if ($this->columnExists($table, 'profile')) {
                    $data['profile'] = 'images/customers/' . $filename;
                } elseif ($this->columnExists($table, 'profile_image')) {
                    $data['profile_image'] = 'images/customers/' . $filename;
                } elseif ($this->columnExists($table, 'image')) {
                    $data['image'] = 'images/customers/' . $filename;
                }
            }

            if (!empty($data)) {
                DB::table($table)->where('id', $request->id)->update($data);
            }
            
            $story = DB::table($table)->where('id', $request->id)->first();
            ActivityLogger::log('update', 'CustomerStory', $request->id, 'Updated customer story ' . ($story->name ?? ''));
            return response()->json(['success' => true, 'message' => 'Customer story updated successfully', 'data' => $story]);
        } else {
            // CREATE
            $data = [];
            
            if ($request->filled('name')) {
                $data['name'] = $request->name;
            }
            if ($request->filled('position')) {
                $data['position'] = $request->position;
            }
            if ($request->has('company') && $this->columnExists($table, 'company')) {
                $data['company'] = $request->input('company');
            }
            if ($request->has('pdf_url') && $this->columnExists($table, 'pdf_url')) {
                $data['pdf_url'] = $request->input('pdf_url');
            }
            if ($request->filled('body_content')) {
                $col = $this->columnExists($table, 'body_content') ? 'body_content' : 'content';
                $data[$col] = $request->body_content;
            }

            if ($request->hasFile('profile')) {
                $image = $request->file('profile');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/customers'), $filename);

                if ($this->columnExists($table, 'profile')) {
                    $data['profile'] = 'images/customers/' . $filename;
                } elseif ($this->columnExists($table, 'profile_image')) {
                    $data['profile_image'] = 'images/customers/' . $filename;
                } elseif ($this->columnExists($table, 'image')) {
                    $data['image'] = 'images/customers/' . $filename;
                }
            }

            $id = DB::table($table)->insertGetId($data);
            $story = DB::table($table)->where('id', $id)->first();
            ActivityLogger::log('create', 'CustomerStory', $id, 'Created customer story ' . ($story->name ?? ''));
            return response()->json(['success' => true, 'message' => 'Customer story created successfully', 'data' => $story]);
        }
    }
    
    public function deleteCustomerStory($id)
    {
        $table = $this->tableExists('customer_stories') ? 'customer_stories' : 'customer_story';
        DB::table($table)->where('id', $id)->delete();
        ActivityLogger::log('delete', 'CustomerStory', $id, 'Deleted customer story #' . $id);
        return response()->json(['success' => true, 'message' => 'Customer Story deleted successfully']);
    }

    public function storeOrUpdateCaseStudy(Request $request)
    {
        if ($request->input('resource_type') === 'white_paper') {
            return $this->storeOrUpdateWhitePaper($request);
        }

        $sourceResourceType = (string) $request->input('source_resource_type', 'case_study');

        if (!$this->tableExists('industry_listings')) {
            return response()->json(['success' => false, 'message' => 'Case studies table is not available.'], 422);
        }

        $validated = $request->validate([
            'id' => ['nullable', 'integer'],
            'category' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'technology' => ['nullable', 'string', Rule::in($this->caseStudyTechnologyOptions()->keys()->all())],
            'outcome_tag' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'one_pager_content' => ['nullable', 'string'],
            'listing_image' => ['nullable', 'mimes:pdf', 'max:20480'],
            'pdf' => ['nullable', 'mimes:pdf', 'max:20480'],
            'pdf_url' => ['nullable', 'string', 'max:2048'],
        ]);

        $normalizedCategory = $this->normalizeCaseStudyCategory((string) $validated['category']);

        if ($this->tableExists('case_study_categories')) {
            CaseStudyCategory::syncDefaults();
            $allowed = DB::table('case_study_categories')
                ->where('is_active', true)
                ->pluck('name')
                ->map(fn ($name) => trim((string) $name))
                ->filter()
                ->all();

            if (!in_array($normalizedCategory, $allowed, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please choose a valid case study category from the managed list.',
                    'errors' => ['category' => ['Please choose a valid case study category from the managed list.']],
                ], 422);
            }
        }

        $data = [
            'category' => $normalizedCategory,
            'body' => $validated['body'] ?? '',
        ];

        if ($this->columnExists('industry_listings', 'one_pager_content')) {
            $data['one_pager_content'] = $this->sanitizeOnePagerContent((string) ($validated['one_pager_content'] ?? ''));
        }

        if ($this->columnExists('industry_listings', 'technology')) {
            $technology = trim((string) ($validated['technology'] ?? ''));
            $data['technology'] = $technology !== '' ? $technology : null;
        }

        if ($this->columnExists('industry_listings', 'outcome_tag')) {
            $outcomeTag = trim((string) ($validated['outcome_tag'] ?? ''));
            if ($outcomeTag !== '') {
                $parts = preg_split('/[\r\n,]+/', $outcomeTag) ?: [];
                $parts = array_values(array_filter(array_map('trim', $parts)));
                $data['outcome_tag'] = $parts[0] ?? '';
            } else {
                $data['outcome_tag'] = null;
            }
        }

        if ($this->columnExists('industry_listings', 'title')) {
            $title = trim((string) ($validated['title'] ?? ''));
            if ($title === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Case study title is required.',
                    'errors' => ['title' => ['Case study title is required.']],
                ], 422);
            }
            $data['title'] = $title;
        }

        if ($request->hasFile('listing_image')) {
            $previewPdf = $request->file('listing_image');
            $filename = time() . '_' . Str::slug(pathinfo($previewPdf->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf';
            $previewDirectory = public_path('case-study-previews');
            File::ensureDirectoryExists($previewDirectory);
            $previewPdf->move($previewDirectory, $filename);
            $data['listing_image'] = $filename;
        }

        if ($request->hasFile('pdf')) {
            $pdf = $request->file('pdf');
            $filename = time() . '_' . Str::slug(pathinfo($pdf->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf';
            $pdf->move(public_path('case_docs'), $filename);
            $data['pdf_url'] = $filename;
        } elseif ($request->filled('pdf_url')) {
            $data['pdf_url'] = trim((string) $validated['pdf_url']);
        }

        $isCrossTypeMove = $request->has('id') && $request->id && $sourceResourceType === 'white_paper';

        if ($request->has('id') && $request->id && !$isCrossTypeMove) {
            if (!empty($data)) {
                DB::table('industry_listings')->where('id', $request->id)->update($data);
            }
            $caseStudy = DB::table('industry_listings')->where('id', $request->id)->first();
            ActivityLogger::log('update', 'CaseStudy', $request->id, 'Updated case study ' . ($caseStudy->category ?? ''));
            if (!empty($data)) {
                app(NewsletterNotificationService::class)->sendCaseStudyNotification($caseStudy);
            }
            return response()->json(['success' => true, 'message' => 'Case study updated successfully', 'data' => $caseStudy]);
        }

        if ($this->columnExists('industry_listings', 'listing_id')) {
            $data['listing_id'] = time();
        }

        $id = DB::table('industry_listings')->insertGetId($data);
        $caseStudy = DB::table('industry_listings')->where('id', $id)->first();

        if ($isCrossTypeMove && $this->tableExists('white_paper')) {
            DB::table('white_paper')->where('id', $request->id)->delete();
        }

        ActivityLogger::log('create', 'CaseStudy', $id, 'Created case study ' . ($caseStudy->category ?? ''));
        app(NewsletterNotificationService::class)->sendCaseStudyNotification($caseStudy);
        return response()->json(['success' => true, 'message' => 'Case study created successfully', 'data' => $caseStudy]);
    }

    public function deleteCaseStudy($id)
    {
        if (!$this->tableExists('industry_listings')) {
            return response()->json(['success' => false, 'message' => 'Case studies table is not available.'], 422);
        }

        DB::table('industry_listings')->where('id', $id)->delete();
        ActivityLogger::log('delete', 'CaseStudy', $id, 'Deleted case study #' . $id);
        return response()->json(['success' => true, 'message' => 'Case study deleted successfully']);
    }

    public function storeOrUpdateWhitePaper(Request $request)
    {
        if ($request->input('resource_type') === 'case_study') {
            return $this->storeOrUpdateCaseStudy($request);
        }

        $sourceResourceType = (string) $request->input('source_resource_type', 'white_paper');

        if (!$this->tableExists('white_paper')) {
            return response()->json(['success' => false, 'message' => 'White papers table is not available.'], 422);
        }

        $validated = $request->validate([
            'id' => ['nullable', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'white_paper_image' => ['nullable', 'image', 'max:5120'],
            'existing_image' => ['nullable', 'string', 'max:2048'],
            'pdf' => ['nullable', 'mimes:pdf', 'max:20480'],
            'pdf_url' => ['nullable', 'string', 'max:2048'],
        ]);

        $titleColumn = $this->firstExistingColumn('white_paper', ['title']) ?? 'title';
        $bodyColumn = $this->firstExistingColumn('white_paper', ['body', 'description', 'content']) ?? 'body';
        $imageColumn = $this->firstExistingColumn('white_paper', ['images', 'image', 'image_path']);
        $pdfColumn = $this->firstExistingColumn('white_paper', ['pdf', 'pdf_url']);

        $data = [
            $titleColumn => $validated['title'],
            $bodyColumn => $validated['body'] ?? '',
        ];

        if ($request->hasFile('white_paper_image') && $imageColumn) {
            $image = $request->file('white_paper_image');
            $filename = time() . '_' . Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/white-papers'), $filename);
            $data[$imageColumn] = $filename;
        } elseif ($imageColumn && $request->filled('existing_image')) {
            $data[$imageColumn] = trim((string) $validated['existing_image']);
        }

        if ($request->hasFile('pdf') && $pdfColumn) {
            $pdf = $request->file('pdf');
            $filename = time() . '_' . Str::slug(pathinfo($pdf->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf';
            $pdf->move(public_path('white_paper_docs'), $filename);
            $data[$pdfColumn] = $filename;
        } elseif ($request->filled('pdf_url') && $pdfColumn) {
            $data[$pdfColumn] = trim((string) $validated['pdf_url']);
        }

        $isCrossTypeMove = $request->has('id') && $request->id && $sourceResourceType === 'case_study';

        if ($request->has('id') && $request->id && !$isCrossTypeMove) {
            if (!empty($data)) {
                DB::table('white_paper')->where('id', $request->id)->update($data);
            }
            $whitePaper = DB::table('white_paper')->where('id', $request->id)->first();
            ActivityLogger::log('update', 'WhitePaper', $request->id, 'Updated white paper ' . ((string) ($whitePaper->{$titleColumn} ?? '')));
            if (!empty($data)) {
                app(NewsletterNotificationService::class)->sendWhitePaperNotification($whitePaper);
            }
            return response()->json(['success' => true, 'message' => 'White paper updated successfully', 'data' => $whitePaper]);
        }

        // Some production schemas have NOT NULL `images`/`pdf` columns without defaults.
        // Ensure inserts always provide those fields even when upload is omitted.
        if ($imageColumn && !array_key_exists($imageColumn, $data)) {
            $data[$imageColumn] = '';
        }
        if ($this->columnExists('white_paper', 'images') && !array_key_exists('images', $data)) {
            $data['images'] = '';
        }

        if ($pdfColumn && !array_key_exists($pdfColumn, $data)) {
            $data[$pdfColumn] = '';
        }
        if ($this->columnExists('white_paper', 'pdf') && !array_key_exists('pdf', $data)) {
            $data['pdf'] = '';
        }

        $id = DB::table('white_paper')->insertGetId($data);
        $whitePaper = DB::table('white_paper')->where('id', $id)->first();

        if ($isCrossTypeMove && $this->tableExists('industry_listings')) {
            DB::table('industry_listings')->where('id', $request->id)->delete();
        }

        ActivityLogger::log('create', 'WhitePaper', $id, 'Created white paper ' . ((string) ($whitePaper->{$titleColumn} ?? '')));
        app(NewsletterNotificationService::class)->sendWhitePaperNotification($whitePaper);
        return response()->json(['success' => true, 'message' => 'White paper created successfully', 'data' => $whitePaper]);
    }

    public function deleteWhitePaper($id)
    {
        if (!$this->tableExists('white_paper')) {
            return response()->json(['success' => false, 'message' => 'White papers table is not available.'], 422);
        }

        DB::table('white_paper')->where('id', $id)->delete();
        ActivityLogger::log('delete', 'WhitePaper', $id, 'Deleted white paper #' . $id);
        return response()->json(['success' => true, 'message' => 'White paper deleted successfully']);
    }
    
    // Image Upload Handler (for CKEditor)
    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|max:5120',
        ]);
        
        if ($request->hasFile('upload')) {
            $image = $request->file('upload');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('ckeditor_uploads'), $filename);
            
            $url = asset('ckeditor_uploads/' . $filename);
            
            return response()->json([
                'uploaded' => true,
                'url' => $url
            ]);
        }
        
        return response()->json(['uploaded' => false]);
    }
    
    // PDF Upload Handler
    public function uploadPdf(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:10240',
        ]);
        
        if ($request->hasFile('pdf')) {
            $pdf = $request->file('pdf');
            $filename = time() . '_' . $pdf->getClientOriginalName();
            $pdf->move(public_path('pdf'), $filename);
            
            $url = asset('pdf/' . $filename);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'filename' => $filename
            ]);
        }
        
        return response()->json(['success' => false]);
    }
    
    // Show/Get individual items
    public function showBlog($id)
    {
        $blogTable = $this->tableExists('blogs') ? 'blogs' : 'blog';
        $idColumn = $this->columnExists($blogTable, 'blog_id') ? 'blog_id' : 'id';
        $blog = DB::table($blogTable)->where($idColumn, $id)->first();
        return response()->json($blog);
    }
    
    public function updateBlog(Request $request, $id)
    {
        Log::info('UpdateBlog called', [
            'id' => $id,
            'request_data' => $request->all(),
            'has_file' => $request->hasFile('image')
        ]);
        
        $blogTable = $this->tableExists('blogs') ? 'blogs' : 'blog';
        $idColumn = $this->columnExists($blogTable, 'blog_id') ? 'blog_id' : 'id';
        
        Log::info('Table info', [
            'table' => $blogTable,
            'id_column' => $idColumn
        ]);
        
        $data = [];
        
        // Only update fields that are provided and not empty
        if ($request->filled('title')) {
            $titleColumn = $this->columnExists($blogTable, 'title') ? 'title' : 'blog_title';
            $data[$titleColumn] = $request->title;
        }
        
        if ($request->filled('author')) {
            $data['author'] = $request->author;
        }
        
        if ($request->filled('date')) {
            $dateColumn = $this->columnExists($blogTable, 'date') ? 'date' : 'blog_date';
            $data[$dateColumn] = $request->date;
        }
        
        if ($request->filled('body')) {
            $bodyColumn = $this->columnExists($blogTable, 'body') ? 'body' : 'content';
            $data[$bodyColumn] = $this->persistEmbeddedBlogImages($request->body);
        }
        
        if ($imageData = $this->storeBlogImage($request, $blogTable)) {
            $data = array_merge($data, $imageData);
        }
        
        Log::info('Data to update', ['data' => $data]);
        
        if (!empty($data)) {
            // Check if the record exists first
            $exists = DB::table($blogTable)->where($idColumn, $id)->exists();
            Log::info('Record exists check', [
                'exists' => $exists,
                'where_column' => $idColumn,
                'where_value' => $id
            ]);
            
            if (!$exists) {
                Log::error('Record not found with given ID');
                return response()->json(['success' => false, 'message' => 'Blog not found'], 404);
            }
            
            $affected = DB::table($blogTable)->where($idColumn, $id)->update($data);
            Log::info('Update executed', ['rows_affected' => $affected]);

            $blog = DB::table($blogTable)->where($idColumn, $id)->first();
            if ($blog) {
                ActivityLogger::log('update', 'Blog', $id, 'Updated blog ' . ($blog->title ?? $blog->blog_title ?? ''));
                app(NewsletterNotificationService::class)->sendBlogNotification($blog, $idColumn);
            }
        } else {
            Log::warning('No data to update');
        }
        
        return response()->json(['success' => true, 'message' => 'Blog updated successfully']);
    }
    
    public function showVideo($id)
    {
        $videoTable = $this->tableExists('videos') ? 'videos' : 'video';
        $video = DB::table($videoTable)->where('id', $id)->first();
        return response()->json($video);
    }
    
    public function updateVideo(Request $request, $id)
    {
        $videoTable = $this->tableExists('videos') ? 'videos' : 'video';
        
        $data = [];
        if ($request->has('title')) $data['title'] = $request->title;
        if ($request->has('url')) $data['url'] = $request->url;
        if ($request->has('description')) $data['description'] = $request->description;
        
        DB::table($videoTable)->where('id', $id)->update($data);
        return response()->json(['success' => true, 'message' => 'Video updated successfully']);
    }
    
    public function showCareer($id)
    {
        $careerTable = $this->tableExists('career') ? 'career' : 'careers';
        $career = DB::table($careerTable)->where('id', $id)->first();
        return response()->json($career);
    }
    
    public function updateCareer(Request $request, $id)
    {
        $careerTable = $this->tableExists('career') ? 'career' : 'careers';
        
        $data = [];
        
        if ($request->filled('job_title')) {
            $col = $this->columnExists($careerTable, 'job_title') ? 'job_title' : 'title';
            $data[$col] = $request->job_title;
        }
        if ($request->filled('job_description')) {
            $col = $this->columnExists($careerTable, 'job_description') ? 'job_description' : 'description';
            $data[$col] = $request->job_description;
        }
        if ($request->filled('job_location')) {
            $col = $this->columnExists($careerTable, 'job_location') ? 'job_location' : 'location';
            $data[$col] = $request->job_location;
        }
        if ($request->filled('job_type')) {
            $col = $this->columnExists($careerTable, 'job_type') ? 'job_type' : 'type';
            $data[$col] = $request->job_type;
        }
        if ($request->filled('job_deadline')) {
            $col = $this->columnExists($careerTable, 'job_deadline') ? 'job_deadline' : 'deadline';
            $data[$col] = $request->job_deadline;
        }
        
        if (!empty($data)) {
            DB::table($careerTable)->where('id', $id)->update($data);
        }
        return response()->json(['success' => true, 'message' => 'Career updated successfully']);
    }
    
    public function showSocialImpact($id)
    {
        $table = $this->tableExists('social_impact') ? 'social_impact' : 'social_impacts';
        $item = DB::table($table)->where('id', $id)->first();
        return response()->json($item);
    }
    
    public function updateSocialImpact(Request $request, $id)
    {
        $table = $this->tableExists('social_impact') ? 'social_impact' : 'social_impacts';
        
        $data = [];
        
        if ($request->filled('title')) {
            $col = $this->columnExists($table, 'title') ? 'title' : 'impact_title';
            $data[$col] = $request->title;
        }
        if ($request->filled('body')) {
            $col = $this->columnExists($table, 'body') ? 'body' : 'content';
            $data[$col] = $request->body;
        }
        if ($request->filled('category')) {
            $col = $this->columnExists($table, 'category') ? 'category' : 'impact_area';
            $data[$col] = $request->category;
        }
        if ($request->filled('posted_date')) {
            $col = $this->columnExists($table, 'posted_date') ? 'posted_date' : 'published_date';
            $data[$col] = $request->posted_date;
        }
        if ($request->filled('author_name')) {
            $col = $this->columnExists($table, 'author_name') ? 'author_name' : 'author';
            $data[$col] = $request->author_name;
        }
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/social-impact'), $filename);
            
            if ($this->columnExists($table, 'image_url')) {
                $data['image_url'] = 'images/social-impact/' . $filename;
            } elseif ($this->columnExists($table, 'image')) {
                $data['image'] = 'images/social-impact/' . $filename;
            }
        }
        
        if (!empty($data)) {
            DB::table($table)->where('id', $id)->update($data);
        }
        return response()->json(['success' => true, 'message' => 'Social impact updated successfully']);
    }
    
    public function showCustomerStory($id)
    {
        $table = $this->tableExists('customer_stories') ? 'customer_stories' : 'customer_story';
        $item = DB::table($table)->where('id', $id)->first();
        return response()->json($item);
    }
    
    public function updateCustomerStory(Request $request, $id)
    {
        $table = $this->tableExists('customer_stories') ? 'customer_stories' : 'customer_story';
        
        $data = [];
        
        if ($request->filled('name')) {
            $col = $this->columnExists($table, 'name') ? 'name' : 'customer_name';
            $data[$col] = $request->name;
        }
        if ($request->filled('position')) {
            $col = $this->columnExists($table, 'position') ? 'position' : 'job_title';
            $data[$col] = $request->position;
        }
        if ($request->has('company') && $this->columnExists($table, 'company')) {
            $data['company'] = $request->input('company');
        }
        if ($request->has('pdf_url') && $this->columnExists($table, 'pdf_url')) {
            $data['pdf_url'] = $request->input('pdf_url');
        }
        if ($request->filled('body_content')) {
            $col = $this->columnExists($table, 'body_content') ? 'body_content' : 'content';
            $data[$col] = $request->body_content;
        }
        
        if ($request->hasFile('profile')) {
            $image = $request->file('profile');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/customers'), $filename);
            
            if ($this->columnExists($table, 'profile')) {
                $data['profile'] = 'images/customers/' . $filename;
            } elseif ($this->columnExists($table, 'profile_image')) {
                $data['profile_image'] = 'images/customers/' . $filename;
            } elseif ($this->columnExists($table, 'image')) {
                $data['image'] = 'images/customers/' . $filename;
            }
        }
        
        if (!empty($data)) {
            DB::table($table)->where('id', $id)->update($data);
        }
        return response()->json(['success' => true, 'message' => 'Customer story updated successfully']);
    }
    
    // ========== EVENT MANAGEMENT ==========
    
    public function storeOrUpdateEvent(Request $request)
    {
        $table = $this->tableExists('events') ? 'events' : 'event';
        $id = $request->id;
        $isUpdate = filled($id);
        $validatedType = $request->validate([
            'event_type' => ['nullable', Rule::in(['normal', 'private'])],
            'start_date' => [$isUpdate ? 'nullable' : 'required', 'date_format:Y-m-d'],
            'start_time' => [$isUpdate ? 'nullable' : 'required', 'date_format:H:i'],
            'timezone' => ['required', Rule::in(['CST', 'EST', 'MST', 'PST', 'UTC'])],
            'url' => [Rule::requiredIf($request->input('event_type') === 'private'), 'nullable', 'url', 'max:2048'],
        ])['event_type'] ?? 'normal';
        
        if ($id) {
            // Update existing event
            $data = [];
            
            if ($request->filled('title')) {
                $data['title'] = $request->title;
            }
            
            if ($request->filled('body')) {
                $data['body'] = $request->body;
            }
            
            if ($request->filled('start_date')) {
                $data['start_date'] = $request->start_date;
            }
            if ($request->filled('start_time')) {
                $data['start_time'] = $request->start_time;
            }
            $data['timezone'] = $request->timezone;
            
            if ($request->filled('url')) {
                $data['url'] = $request->url;
            }
            
            if ($request->filled('recorded_url')) {
                if ($this->columnExists($table, 'recorded_url')) {
                    $data['recorded_url'] = $request->recorded_url;
                }
            }

            if ($this->columnExists($table, 'event_type')) {
                $data['event_type'] = $validatedType;
                if ($validatedType === 'private' && empty(DB::table($table)->where('id', $id)->value('private_slug'))) {
                    $data['private_slug'] = Str::lower(Str::random(32));
                }
            }
            
            if (!empty($data)) {
                DB::table($table)->where('id', $id)->update($data);
            }
            
            $event = DB::table($table)->where('id', $id)->first();
            ActivityLogger::log('update', 'Event', $id, 'Updated event ' . ($event->title ?? ''));
            if (!empty($data) && ($event->event_type ?? 'normal') === 'normal') {
                app(NewsletterNotificationService::class)->sendEventNotification($event);
            }
            return response()->json(['success' => true, 'message' => 'Event updated successfully', 'data' => $event]);
        } else {
            // Create new event
            $data = [
                'title' => $request->title,
                'body' => $request->body,
                'start_date' => $request->start_date,
                'start_time' => $request->start_time,
                'timezone' => $request->timezone,
            ];

            if ($this->columnExists($table, 'event_type')) {
                $data['event_type'] = $validatedType;
                $data['private_slug'] = $validatedType === 'private' ? Str::lower(Str::random(32)) : null;
            }
            
            if ($request->filled('url')) {
                $data['url'] = $request->url;
            }
            
            if ($request->filled('recorded_url') && $this->columnExists($table, 'recorded_url')) {
                $data['recorded_url'] = $request->recorded_url;
            }
            
            $eventId = DB::table($table)->insertGetId($data);
            $event = DB::table($table)->where('id', $eventId)->first();
            ActivityLogger::log('create', 'Event', $eventId, 'Created event ' . ($event->title ?? ''));
            if (($event->event_type ?? 'normal') === 'normal') {
                app(NewsletterNotificationService::class)->sendEventNotification($event);
            }
            
            return response()->json(['success' => true, 'message' => 'Event created successfully', 'data' => $event]);
        }
    }
    
    public function deleteEvent($id)
    {
        $table = $this->tableExists('events') ? 'events' : 'event';
        DB::table($table)->where('id', $id)->delete();
        ActivityLogger::log('delete', 'Event', $id, 'Deleted event #' . $id);
        return response()->json(['success' => true, 'message' => 'Event deleted successfully']);
    }

    public function listEventRegistrations()
    {
        if (!$this->tableExists('event_registrations')) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $privateEvents = DB::table('events')->where('event_type', 'private')->get(['id', 'title']);
        $privateIds = $privateEvents->pluck('id');
        $privateTitles = $privateEvents->pluck('title')
            ->push('Sovereign Data Clouds with Snowflake')
            ->unique()
            ->values();

        return response()->json([
            'success' => true,
            'data' => DB::table('event_registrations')
                ->where(function ($query) use ($privateIds, $privateTitles) {
                    $query->whereIn('event_id', $privateIds)
                        ->orWhere(function ($legacyQuery) use ($privateTitles) {
                            $legacyQuery->whereNull('event_id')->whereIn('event_name', $privateTitles);
                        });
                })
                ->orderByDesc('id')
                ->limit(250)
                ->get(),
        ]);
    }

    public function updateEventRegistrationStatus(Request $request, AzureMailService $mailer, int $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'verified', 'attended', 'rejected'])],
        ]);

        $registration = DB::table('event_registrations')->where('id', $id)->first();
        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'Registration not found.'], 404);
        }

        DB::table('event_registrations')->where('id', $id)->update([
            'status' => $validated['status'],
            'verified_at' => $validated['status'] === 'verified' ? now() : null,
            'verified_by' => $validated['status'] === 'verified' ? auth('admin')->id() : null,
            'updated_at' => now(),
        ]);

        ActivityLogger::log('update', 'EventRegistration', $id, ucfirst($validated['status']).' event registration for '.$registration->work_email);

        $message = 'Registration marked '.$validated['status'].'.';
        $invitationSent = null;

        if ($validated['status'] === 'verified' && empty($registration->event_link_sent_at)) {
            $eventTable = $this->tableExists('events') ? 'events' : 'event';
            $event = !empty($registration->event_id)
                ? DB::table($eventTable)->where('id', $registration->event_id)->first()
                : DB::table($eventTable)->where('title', $registration->event_name)->where('event_type', 'private')->first();

            if (!$event || empty($event->url)) {
                $message .= ' Invitation not sent because this event does not have an access URL.';
                $invitationSent = false;
            } elseif (DB::table('event_email_unsubscribes')->where('email', $registration->work_email)->exists()) {
                $message .= ' Invitation not sent because this attendee unsubscribed from event emails.';
                $invitationSent = false;
            } else {
                $unsubscribeToken = trim((string) ($registration->unsubscribe_token ?? '')) ?: Str::random(64);
                if (empty($registration->unsubscribe_token)) {
                    DB::table('event_registrations')->where('id', $id)->update(['unsubscribe_token' => $unsubscribeToken]);
                }

                $email = AzureMailService::normalizeEmail((string) $registration->work_email);
                $html = view('emails.events.verified-event-link', [
                    'name' => (string) $registration->full_name,
                    'eventTitle' => (string) $event->title,
                    'eventDate' => trim(implode(' ', array_filter([
                        (string) ($event->start_date ?? ''),
                        (string) ($event->start_time ?? ''),
                        (string) ($event->timezone ?? ''),
                    ]))),
                    'eventUrl' => (string) $event->url,
                    'unsubscribeUrl' => URL::signedRoute('events.emails.unsubscribe', ['token' => $unsubscribeToken]),
                ])->render();

                $invitationSent = AzureMailService::isDeliverableEmail($email)
                    && $this->sendEventEmailWithRetry(
                        $mailer,
                        AzureMailService::outboundFromEmail(),
                        $email,
                        'Your event access link: '.$event->title,
                        $html
                    );

                if ($invitationSent) {
                    DB::table('event_registrations')->where('id', $id)->update([
                        'event_id' => $event->id,
                        'event_link_sent_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $message .= ' The invitation email was sent automatically.';
                } else {
                    $message .= ' Verification was saved, but email delivery failed. You can retry with “Send to Verified.”';
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'invitation_sent' => $invitationSent,
        ]);
    }

    public function sendEventLinkToVerified(Request $request, AzureMailService $mailer)
    {
        $validated = $request->validate([
            'event_id' => ['required', 'integer'],
            'registration_ids' => ['nullable', 'array'],
            'registration_ids.*' => ['integer', 'distinct'],
            'is_reminder' => ['nullable', 'boolean'],
        ]);
        $isReminder = (bool) ($validated['is_reminder'] ?? false);

        $eventTable = $this->tableExists('events') ? 'events' : 'event';
        $event = DB::table($eventTable)->where('id', $validated['event_id'])->first();
        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Event not found.'], 404);
        }

        $eventUrl = trim((string) ($event->url ?? ''));
        if (!$isReminder && ($eventUrl === '' || !filter_var($eventUrl, FILTER_VALIDATE_URL))) {
            return response()->json(['success' => false, 'message' => 'Add a valid event URL before sending invitations.'], 422);
        }

        $registrationsQuery = DB::table('event_registrations')
            ->whereIn('status', $isReminder ? ['verified', 'attended'] : ['verified'])
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('event_email_unsubscribes')
                    ->whereColumn('event_email_unsubscribes.email', 'event_registrations.work_email');
            })
            ->where(function ($query) use ($event) {
                $query->where('event_id', $event->id)
                    ->orWhere(function ($legacyQuery) use ($event) {
                        $legacyQuery->whereNull('event_id')
                            ->where('event_name', (string) $event->title);
                    });
            });

        if (!$isReminder) {
            $registrationsQuery->whereNull('event_link_sent_at');
        }
        if (!empty($validated['registration_ids'])) {
            $registrationsQuery->whereIn('id', $validated['registration_ids']);
        }

        $registrations = $registrationsQuery->orderBy('id')->get();

        if ($registrations->isEmpty()) {
            return response()->json(['success' => false, 'message' => $isReminder
                ? 'None of the selected recipients are eligible for this event reminder.'
                : 'There are no verified attendees waiting for an event link.'], 422);
        }

        $from = AzureMailService::outboundFromEmail();
        $sent = 0;
        $failed = 0;

        foreach ($registrations as $registration) {
            $email = AzureMailService::normalizeEmail((string) $registration->work_email);
            $unsubscribeToken = trim((string) ($registration->unsubscribe_token ?? ''));
            if ($unsubscribeToken === '') {
                $unsubscribeToken = Str::random(64);
                DB::table('event_registrations')->where('id', $registration->id)->update([
                    'unsubscribe_token' => $unsubscribeToken,
                    'updated_at' => now(),
                ]);
            }
            $emailView = $isReminder ? 'emails.events.reminder' : 'emails.events.verified-event-link';
            $html = view($emailView, [
                'name' => (string) $registration->full_name,
                'eventTitle' => (string) ($event->title ?? $registration->event_name),
                'eventDate' => trim(implode(' ', array_filter([
                    (string) ($event->start_date ?? ''),
                    (string) ($event->start_time ?? ''),
                    (string) ($event->timezone ?? ''),
                ]))),
                'eventUrl' => $eventUrl,
                'unsubscribeUrl' => URL::signedRoute('events.emails.unsubscribe', [
                    'token' => $unsubscribeToken,
                ]),
            ])->render();

            if (AzureMailService::isDeliverableEmail($email)
                && $this->sendEventEmailWithRetry($mailer, $from, $email, ($isReminder ? 'Reminder: ' : 'Your event access link: ').($event->title ?? 'Armely Event'), $html)) {
                DB::table('event_registrations')->where('id', $registration->id)->update([
                    'event_id' => $event->id,
                    'event_link_sent_at' => now(),
                    'updated_at' => now(),
                ]);
                $sent++;
            } else {
                $failed++;
            }
        }

        $emailType = $isReminder ? 'event reminder' : 'event link';
        ActivityLogger::log('email', 'Event', $event->id, "Sent {$emailType} to {$sent} attendee(s); {$failed} failed.");

        return response()->json([
            'success' => $sent > 0,
            'message' => ucfirst($emailType)." sent to {$sent} attendee(s).".($failed ? " {$failed} could not be delivered." : ''),
            'sent' => $sent,
            'failed' => $failed,
        ], $sent > 0 ? 200 : 502);
    }

    public function sendEventThankYou(Request $request, AzureMailService $mailer)
    {
        $validated = $request->validate([
            'event_id' => ['required', 'integer'],
            'registration_ids' => ['nullable', 'array'],
            'registration_ids.*' => ['integer', 'distinct'],
        ]);
        $eventTable = $this->tableExists('events') ? 'events' : 'event';
        $event = DB::table($eventTable)->where('id', $validated['event_id'])->first();
        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Event not found.'], 404);
        }

        $registrationsQuery = DB::table('event_registrations')
            ->whereIn('status', ['verified', 'attended'])
            ->whereNull('thank_you_sent_at')
            ->where(function ($query) use ($event) {
                $query->where('event_id', $event->id)
                    ->orWhere(function ($legacyQuery) use ($event) {
                        $legacyQuery->whereNull('event_id')->where('event_name', (string) $event->title);
                    });
            })
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('event_email_unsubscribes')
                    ->whereColumn('event_email_unsubscribes.email', 'event_registrations.work_email');
            });

        if (!empty($validated['registration_ids'])) {
            $registrationsQuery->whereIn('id', $validated['registration_ids']);
        }

        $registrations = $registrationsQuery->orderBy('id')->get();

        if ($registrations->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'None of the selected recipients are eligible for a thank-you email, or they have already received one.'], 422);
        }

        $from = AzureMailService::outboundFromEmail();
        $sent = 0;
        $failed = 0;
        foreach ($registrations as $registration) {
            $token = trim((string) ($registration->unsubscribe_token ?? '')) ?: Str::random(64);
            if (empty($registration->unsubscribe_token)) {
                DB::table('event_registrations')->where('id', $registration->id)->update(['unsubscribe_token' => $token]);
            }

            $html = view('emails.events.thank-you', [
                'name' => $registration->full_name,
                'eventTitle' => $event->title,
                'unsubscribeUrl' => URL::signedRoute('events.emails.unsubscribe', ['token' => $token]),
            ])->render();

            $email = AzureMailService::normalizeEmail((string) $registration->work_email);
            if (AzureMailService::isDeliverableEmail($email)
                && $this->sendEventEmailWithRetry($mailer, $from, $email, 'Thank you for joining us: '.$event->title, $html)) {
                DB::table('event_registrations')->where('id', $registration->id)->update([
                    'thank_you_sent_at' => now(),
                    'updated_at' => now(),
                ]);
                $sent++;
            } else {
                $failed++;
            }
        }

        ActivityLogger::log('email', 'Event', $event->id, "Sent {$sent} event thank-you email(s); {$failed} failed.");

        return response()->json([
            'success' => $sent > 0,
            'message' => "Thank-you email sent to {$sent} recipient(s).".($failed ? " {$failed} could not be delivered." : ''),
            'sent' => $sent,
            'failed' => $failed,
        ], $sent > 0 ? 200 : 502);
    }

    private function sendEventEmailWithRetry(
        AzureMailService $mailer,
        string $from,
        string $to,
        string $subject,
        string $html
    ): bool {
        $resolvedFrom = AzureMailService::normalizeEmail($from);
        if (!filter_var($resolvedFrom, FILTER_VALIDATE_EMAIL)) {
            $resolvedFrom = AzureMailService::normalizeEmail((string) config('mail.from.address', ''));
        }

        if (!filter_var($resolvedFrom, FILTER_VALIDATE_EMAIL)) {
            Log::error('Event email sender address is missing or invalid', [
                'to' => $to,
                'subject' => $subject,
                'azure_sender_configured' => AzureMailService::outboundFromEmail() !== '',
                'mail_sender_configured' => trim((string) config('mail.from.address', '')) !== '',
            ]);

            return false;
        }

        if ($mailer->sendEmail($resolvedFrom, $to, $subject, $html)) {
            return true;
        }

        Log::warning('Event email first attempt failed; retrying once', [
            'to' => $to,
            'subject' => $subject,
        ]);

        $sent = (new AzureMailService())->sendEmail($resolvedFrom, $to, $subject, $html);
        if ($sent) {
            return true;
        }

        $fallbackMailer = (string) config('mail.default', 'log');
        if (in_array($fallbackMailer, ['log', 'array'], true)) {
            Log::error('Event email retry failed and no delivery-capable fallback mailer is configured', [
                'to' => $to,
                'subject' => $subject,
                'fallback_mailer' => $fallbackMailer,
            ]);

            return false;
        }

        try {
            Mail::html($html, function ($message) use ($resolvedFrom, $to, $subject) {
                $message->from($resolvedFrom, (string) config('mail.from.name', 'Armely'))
                    ->to($to)
                    ->subject($subject);

                $replyTo = AzureMailService::replyToEmail();
                if ($replyTo) {
                    $message->replyTo($replyTo);
                }
            });

            Log::warning('Event email sent through fallback mailer after Graph failed', [
                'to' => $to,
                'subject' => $subject,
                'fallback_mailer' => $fallbackMailer,
            ]);

            return true;
        } catch (\Throwable $exception) {
            Log::error('Event email failed through Graph and fallback mailer', [
                'to' => $to,
                'subject' => $subject,
                'fallback_mailer' => $fallbackMailer,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
    
    // ========== TEAM MANAGEMENT ==========
    
    public function storeOrUpdateTeam(Request $request)
    {
        $table = $this->tableExists('team') ? 'team' : 'teams';
        $id = $request->id;
        
        if ($id) {
            // Update existing team member
            $data = [];
            
            if ($request->filled('team_name')) {
                if ($this->columnExists($table, 'team_name')) {
                    $data['team_name'] = $request->team_name;
                } elseif ($this->columnExists($table, 'name')) {
                    $data['name'] = $request->team_name;
                }
            }
            
            if ($request->filled('team_title')) {
                if ($this->columnExists($table, 'team_title')) {
                    $data['team_title'] = $request->team_title;
                } elseif ($this->columnExists($table, 'title')) {
                    $data['title'] = $request->team_title;
                }
            }
            
            if ($request->filled('team_body')) {
                if ($this->columnExists($table, 'team_body')) {
                    $data['team_body'] = $request->team_body;
                } elseif ($this->columnExists($table, 'body')) {
                    $data['body'] = $request->team_body;
                } elseif ($this->columnExists($table, 'bio')) {
                    $data['bio'] = $request->team_body;
                }
            }
            
            if ($request->filled('linkedin') && $this->columnExists($table, 'linkedin')) {
                $data['linkedin'] = $request->linkedin;
            }
            
            if ($request->filled('facebook') && $this->columnExists($table, 'facebook')) {
                $data['facebook'] = $request->facebook;
            }
            
            if ($request->filled('instagram') && $this->columnExists($table, 'instagram')) {
                $data['instagram'] = $request->instagram;
            }
            
            if ($request->filled('x') && $this->columnExists($table, 'x')) {
                $data['x'] = $request->x;
            }
            
            if ($request->hasFile('team_image')) {
                $image = $request->file('team_image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/team'), $filename);
                
                if ($this->columnExists($table, 'team_image')) {
                    $data['team_image'] = $filename;
                } elseif ($this->columnExists($table, 'image')) {
                    $data['image'] = $filename;
                }
            }
            
            if (!empty($data)) {
                DB::table($table)->where('id', $id)->update($data);
            }
            
            $member = DB::table($table)->where('id', $id)->first();
            ActivityLogger::log('update', 'Team', $id, 'Updated team member ' . ($member->team_name ?? $member->name ?? ''));
            return response()->json(['success' => true, 'message' => 'Team member updated successfully', 'data' => $member]);
        } else {
            // Create new team member
            $data = [];
            
            if ($this->columnExists($table, 'team_name')) {
                $data['team_name'] = $request->team_name;
            } elseif ($this->columnExists($table, 'name')) {
                $data['name'] = $request->team_name;
            }
            
            if ($this->columnExists($table, 'team_title')) {
                $data['team_title'] = $request->team_title;
            } elseif ($this->columnExists($table, 'title')) {
                $data['title'] = $request->team_title;
            }
            
            if ($this->columnExists($table, 'team_body')) {
                $data['team_body'] = $request->team_body;
            } elseif ($this->columnExists($table, 'body')) {
                $data['body'] = $request->team_body;
            } elseif ($this->columnExists($table, 'bio')) {
                $data['bio'] = $request->team_body;
            }
            
            if ($request->filled('linkedin') && $this->columnExists($table, 'linkedin')) {
                $data['linkedin'] = $request->linkedin;
            }
            
            if ($request->filled('facebook') && $this->columnExists($table, 'facebook')) {
                $data['facebook'] = $request->facebook;
            }
            
            if ($request->filled('instagram') && $this->columnExists($table, 'instagram')) {
                $data['instagram'] = $request->instagram;
            }
            
            if ($request->filled('x') && $this->columnExists($table, 'x')) {
                $data['x'] = $request->x;
            }
            
            if ($request->hasFile('team_image')) {
                $image = $request->file('team_image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/team'), $filename);
                
                if ($this->columnExists($table, 'team_image')) {
                    $data['team_image'] = $filename;
                } elseif ($this->columnExists($table, 'image')) {
                    $data['image'] = $filename;
                }
            }
            
            if ($this->columnExists($table, 'created_date')) {
                $data['created_date'] = now()->format('d-m-y h:i:sa');
            }
            
            $memberId = DB::table($table)->insertGetId($data);
            $member = DB::table($table)->where('id', $memberId)->first();
            ActivityLogger::log('create', 'Team', $memberId, 'Created team member ' . ($member->team_name ?? $member->name ?? ''));
            
            return response()->json(['success' => true, 'message' => 'Team member created successfully', 'data' => $member]);
        }
    }
    
    public function deleteTeam($id)
    {
        $table = $this->tableExists('team') ? 'team' : 'teams';
        
        // Get the team member to delete their image
        $member = DB::table($table)->where('id', $id)->first();
        if ($member) {
            $imageColumn = $this->columnExists($table, 'team_image') ? 'team_image' : 'image';
            if (isset($member->$imageColumn)) {
                $imagePath = public_path('images/team/' . $member->$imageColumn);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }
        
        DB::table($table)->where('id', $id)->delete();
        ActivityLogger::log('delete', 'Team', $id, 'Deleted team member #' . $id);
        return response()->json(['success' => true, 'message' => 'Team member deleted successfully']);
    }

    // ========== CONTACTS CRUD FUNCTIONS ==========
    
    public function storeOrUpdateContact(Request $request)
    {
        $id = $request->id;
        
        $data = [
            'name' => $request->name ?? '',
            'email' => $request->email ?? '',
            'phone' => $request->phone ?? '',
            'organization' => $request->organization ?? '',
            'subject' => $request->subject ?? '',
            'message' => $request->message ?? '',
            'sent_date' => $request->sent_date ?? now(),
        ];
        
        if ($id) {
            // Update existing contact
            DB::table('contacts')->where('id', $id)->update($data);
            ActivityLogger::log('update', 'Contact', $id, 'Updated contact: ' . ($request->name ?? ''));
            return response()->json(['success' => true, 'message' => 'Contact updated successfully']);
        } else {
            // Create new contact
            $insertId = DB::table('contacts')->insertGetId($data);
            ActivityLogger::log('create', 'Contact', $insertId, 'Created new contact: ' . ($request->name ?? ''));
            return response()->json(['success' => true, 'message' => 'Contact created successfully', 'id' => $insertId]);
        }
    }
    
    public function deleteContact($id)
    {
        DB::table('contacts')->where('id', $id)->delete();
        ActivityLogger::log('delete', 'Contact', $id, 'Deleted contact #' . $id);
        return response()->json(['success' => true, 'message' => 'Contact deleted successfully']);
    }

    public function unsubscribeNewsletterSubscriber($id)
    {
        if (!$this->tableExists('newsletter_subscribers')) {
            return response()->json(['success' => false, 'message' => 'Newsletter subscribers table is not available.'], 422);
        }

        $subscriber = DB::table('newsletter_subscribers')->where('id', $id)->first();

        DB::table('newsletter_subscribers')->where('id', $id)->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
            'updated_at' => now(),
        ]);

        if ($subscriber && $this->tableExists('newsletter_notification_unsubscribes')) {
            $email = AzureMailService::normalizeEmail((string) ($subscriber->email ?? ''));
            if ($email !== '') {
                DB::table('newsletter_notification_unsubscribes')->updateOrInsert(
                    ['email' => $email],
                    [
                        'source' => 'admin',
                        'unsubscribed_at' => now(),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }

        ActivityLogger::log('update', 'NewsletterSubscriber', $id, 'Unsubscribed newsletter subscriber #' . $id);
        return response()->json(['success' => true, 'message' => 'Subscriber unsubscribed successfully']);
    }

    public function resubscribeNewsletterSubscriber($id)
    {
        if (!$this->tableExists('newsletter_subscribers')) {
            return response()->json(['success' => false, 'message' => 'Newsletter subscribers table is not available.'], 422);
        }

        $subscriber = DB::table('newsletter_subscribers')->where('id', $id)->first();

        DB::table('newsletter_subscribers')->where('id', $id)->update([
            'status' => 'active',
            'unsubscribed_at' => null,
            'subscribed_at' => now(),
            'updated_at' => now(),
        ]);

        if ($subscriber && $this->tableExists('newsletter_notification_unsubscribes')) {
            $email = AzureMailService::normalizeEmail((string) ($subscriber->email ?? ''));
            if ($email !== '') {
                DB::table('newsletter_notification_unsubscribes')->where('email', $email)->delete();
            }
        }

        ActivityLogger::log('update', 'NewsletterSubscriber', $id, 'Reactivated newsletter subscriber #' . $id);
        return response()->json(['success' => true, 'message' => 'Subscriber reactivated successfully']);
    }

    public function deleteNewsletterSubscriber($id)
    {
        if (!$this->tableExists('newsletter_subscribers')) {
            return response()->json(['success' => false, 'message' => 'Newsletter subscribers table is not available.'], 422);
        }

        DB::table('newsletter_subscribers')->where('id', $id)->delete();
        ActivityLogger::log('delete', 'NewsletterSubscriber', $id, 'Deleted newsletter subscriber #' . $id);
        return response()->json(['success' => true, 'message' => 'Subscriber deleted successfully']);
    }

    private function sanitizeOnePagerContent(string $content): string
    {
        $allowedTags = '<h1><h2><h3><h4><p><br><hr><ul><ol><li><strong><b><em><i><blockquote><table><thead><tbody><tr><th><td>';
        $content = strip_tags($content, $allowedTags);

        // Preserve semantic structure while dropping pasted styles, scripts, and event attributes.
        $content = preg_replace_callback('/<([a-z][a-z0-9]*)(?:\s[^>]*)?>/i', static function (array $match): string {
            return '<' . strtolower($match[1]) . '>';
        }, $content) ?? '';

        return trim($content);
    }
}
