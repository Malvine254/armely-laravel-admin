<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\ActivityLogger;

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

        $caseStudies = $this->tableExists('industry_listings')
            ? DB::table('industry_listings')->orderBy('id', 'desc')->limit(50)->get()
            : collect();

        $eventsTable = $this->tableExists('events') ? 'events' : ($this->tableExists('event') ? 'event' : null);
        $events = $eventsTable ? DB::table($eventsTable)->orderBy('id', 'desc')->limit(50)->get() : collect();

        $teamTable = $this->tableExists('team') ? 'team' : ($this->tableExists('teams') ? 'teams' : null);
        $team = $teamTable ? DB::table($teamTable)->orderBy('id', 'desc')->limit(50)->get() : collect();

        $contacts = $this->tableExists('contacts') ? DB::table('contacts')->orderBy('id', 'desc')->limit(50)->get() : collect();

        $adminAuthors = $this->tableExists('admin')
            ? DB::table('admin')
                ->whereNotNull('name')
                ->where('name', '!=', '')
                ->orderBy('name')
                ->pluck('name')
                ->unique()
                ->values()
            : collect();

        return view('admin.tables', compact('blogs', 'videos', 'careers', 'socialImpact', 'customerStories', 'caseStudies', 'events', 'team', 'contacts', 'adminAuthors'));
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
            $searchValue = $request->input('search.value');
            if (!empty($searchValue)) {
                $query->where(function($q) use ($searchValue, $blogTable) {
                    $q->where($this->columnExists($blogTable, 'title') ? 'title' : 'id', 'like', "%{$searchValue}%")
                      ->orWhere($this->columnExists($blogTable, 'author') ? 'author' : 'id', 'like', "%{$searchValue}%");
                    
                    if ($this->columnExists($blogTable, 'blog_title')) {
                        $q->orWhere('blog_title', 'like', "%{$searchValue}%");
                    }
                    if ($this->columnExists($blogTable, 'description')) {
                        $q->orWhere('description', 'like', "%{$searchValue}%");
                    }
                });
            }
            
            $totalFiltered = $query->count();
            
            // Ordering
            $orderColIndex = $request->input('order.0.column', 0);
            $orderDir = $request->input('order.0.dir', 'desc');
            
            // Map column indices to actual DB columns
            $columns = [
                0 => $this->columnExists($blogTable, 'blog_id') ? 'blog_id' : 'id',
                1 => $this->columnExists($blogTable, 'title') ? 'title' : ($this->columnExists($blogTable, 'blog_title') ? 'blog_title' : 'id'),
                2 => $this->columnExists($blogTable, 'author') ? 'author' : 'id',
                3 => $this->columnExists($blogTable, 'date') ? 'date' : ($this->columnExists($blogTable, 'blog_date') ? 'blog_date' : 'id'),
                4 => $this->columnExists($blogTable, 'id') ? 'id' : 'blog_id'
            ];
            
            $orderBy = $columns[$orderColIndex] ?? $columns[0];
            
            // Paging
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            
            // Select all columns explicitly to ensure body/content is included
            $blogs = $query->select('*')
                ->orderBy($orderBy, $orderDir)
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

        if (!$this->tableExists('industry_listings')) {
            return response()->json(['success' => true, 'data' => [], 'limit' => $limit]);
        }

        $caseStudies = DB::table('industry_listings')->orderBy('id', 'desc')->limit($limit)->get();
        return response()->json(['success' => true, 'data' => $caseStudies, 'limit' => $limit]);
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
                $data[$bodyColumn] = $request->body;
            }
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . Str::slug($request->title ?? 'blog') . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/blog'), $filename);
                $imageColumn = $this->columnExists($blogTable, 'image_path') ? 'image_path' : 'image';
                $data[$imageColumn] = 'images/blog/' . $filename;
            }
            
            DB::table($blogTable)->where($idColumn, $request->id)->update($data);
            $blog = DB::table($blogTable)->where($idColumn, $request->id)->first();
            ActivityLogger::log('update', 'Blog', $request->id, 'Updated blog ' . ($request->title ?? ($blog->title ?? $blog->blog_title ?? '')));
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
                $data[$bodyColumn] = $request->body;
            }
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . Str::slug($request->title ?? 'blog') . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/blog'), $filename);
                $imageColumn = $this->columnExists($blogTable, 'image_path') ? 'image_path' : 'image';
                $data[$imageColumn] = 'images/blog/' . $filename;
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
            
            DB::table($blogTable)->insert($data);
            $blog = DB::table($blogTable)->orderBy('id', 'desc')->first();
            ActivityLogger::log('create', 'Blog', $blog->{$idColumn} ?? ($blog->id ?? null), 'Created blog ' . ($request->title ?? ($blog->title ?? $blog->blog_title ?? '')));
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
            'url' => 'required|string',
        ]);
        
        $videoTable = $this->tableExists('videos') ? 'videos' : 'youtube_videos';
        $idColumn = $this->columnExists($videoTable, 'video_id') ? 'video_id' : 'id';
        
        $data = [];
        
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
        if (!$this->tableExists('industry_listings')) {
            return response()->json(['success' => false, 'message' => 'Case studies table is not available.'], 422);
        }

        $validated = $request->validate([
            'id' => ['nullable', 'integer'],
            'category' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'listing_image' => ['nullable', 'image', 'max:5120'],
            'pdf' => ['nullable', 'mimes:pdf', 'max:20480'],
            'pdf_url' => ['nullable', 'string', 'max:2048'],
        ]);

        $data = [
            'category' => $validated['category'],
            'body' => $validated['body'] ?? '',
        ];

        if ($request->hasFile('listing_image')) {
            $image = $request->file('listing_image');
            $filename = time() . '_' . Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/case-study'), $filename);
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

        if ($request->has('id') && $request->id) {
            DB::table('industry_listings')->where('id', $request->id)->update($data);
            $caseStudy = DB::table('industry_listings')->where('id', $request->id)->first();
            ActivityLogger::log('update', 'CaseStudy', $request->id, 'Updated case study ' . ($caseStudy->category ?? ''));
            return response()->json(['success' => true, 'message' => 'Case study updated successfully', 'data' => $caseStudy]);
        }

        $id = DB::table('industry_listings')->insertGetId($data);
        $caseStudy = DB::table('industry_listings')->where('id', $id)->first();
        ActivityLogger::log('create', 'CaseStudy', $id, 'Created case study ' . ($caseStudy->category ?? ''));
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
            $data[$bodyColumn] = $request->body;
        }
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . Str::slug($request->title ?? 'blog') . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/blog'), $filename);
            $imageColumn = $this->columnExists($blogTable, 'image_path') ? 'image_path' : 'image';
            $data[$imageColumn] = 'images/blog/' . $filename;
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
            
            if ($request->filled('url')) {
                $data['url'] = $request->url;
            }
            
            if ($request->filled('recorded_url')) {
                if ($this->columnExists($table, 'recorded_url')) {
                    $data['recorded_url'] = $request->recorded_url;
                }
            }
            
            if (!empty($data)) {
                DB::table($table)->where('id', $id)->update($data);
            }
            
            $event = DB::table($table)->where('id', $id)->first();
            ActivityLogger::log('update', 'Event', $id, 'Updated event ' . ($event->title ?? ''));
            return response()->json(['success' => true, 'message' => 'Event updated successfully', 'data' => $event]);
        } else {
            // Create new event
            $data = [
                'title' => $request->title,
                'body' => $request->body,
                'start_date' => $request->start_date,
            ];
            
            if ($request->filled('url')) {
                $data['url'] = $request->url;
            }
            
            if ($request->filled('recorded_url') && $this->columnExists($table, 'recorded_url')) {
                $data['recorded_url'] = $request->recorded_url;
            }
            
            $eventId = DB::table($table)->insertGetId($data);
            $event = DB::table($table)->where('id', $eventId)->first();
            ActivityLogger::log('create', 'Event', $eventId, 'Created event ' . ($event->title ?? ''));
            
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
}
