<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\ActivityLogger;

class TablesController extends Controller
{
    public function index()
    {
        // Use table name fallback logic like DashboardController
        $blogTable = Schema::hasTable('blog') ? 'blog' : (Schema::hasTable('blogs') ? 'blogs' : null);
        $blogs = $blogTable ? DB::table($blogTable)->orderBy(Schema::hasColumn($blogTable, 'blog_id') ? 'blog_id' : 'id', 'desc')->limit(50)->get() : collect();
        
        $videoTable = Schema::hasTable('videos') ? 'videos' : (Schema::hasTable('video') ? 'video' : null);
        $videos = $videoTable ? DB::table($videoTable)->orderBy(Schema::hasColumn($videoTable, 'video_id') ? 'video_id' : 'id', 'desc')->limit(50)->get() : collect();
        
        $careerTable = Schema::hasTable('careers') ? 'careers' : (Schema::hasTable('career') ? 'career' : null);
        $careers = $careerTable ? DB::table($careerTable)->orderBy('id', 'desc')->limit(50)->get() : collect();
        
        $socialImpactTable = Schema::hasTable('social_impact') ? 'social_impact' : (Schema::hasTable('social_impacts') ? 'social_impacts' : null);
        $socialImpact = $socialImpactTable ? DB::table($socialImpactTable)->orderBy('id', 'desc')->limit(50)->get() : collect();
        
        $customerStoriesTable = Schema::hasTable('customer_stories') ? 'customer_stories' : (Schema::hasTable('customer_story') ? 'customer_story' : null);
        $customerStories = $customerStoriesTable ? DB::table($customerStoriesTable)->orderBy('id', 'desc')->limit(50)->get() : collect();
        
        $eventsTable = Schema::hasTable('events') ? 'events' : (Schema::hasTable('event') ? 'event' : null);
        $events = $eventsTable ? DB::table($eventsTable)->orderBy('id', 'desc')->limit(50)->get() : collect();
        
        $teamTable = Schema::hasTable('team') ? 'team' : (Schema::hasTable('teams') ? 'teams' : null);
        $team = $teamTable ? DB::table($teamTable)->orderBy('id', 'desc')->limit(50)->get() : collect();
        
        return view('admin.tables', compact('blogs', 'videos', 'careers', 'socialImpact', 'customerStories', 'events', 'team'));
    }
    
    // ========== LIST ENDPOINTS FOR AJAX TABLE RELOAD ==========
    
    public function listBlogs(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 500));

        $start = microtime(true);
        // Temporary fallback debug file (works even if Laravel logging is misconfigured)
        try {
            @file_put_contents(storage_path('logs/debug_blogs.txt'), "[".now()."] listBlogs called, limit={$limit}\n", FILE_APPEND | LOCK_EX);
        } catch (\Throwable $__e) {
            // suppress - this is only diagnostic
        }
        $blogTable = Schema::hasTable('blogs') ? 'blogs' : 'blog';

        try {
            $blogs = DB::table($blogTable)
                ->orderBy(Schema::hasColumn($blogTable, 'blog_id') ? 'blog_id' : 'id', 'desc')
                ->limit($limit)
                ->get();

            $duration = round((microtime(true) - $start) * 1000, 2);
            \Illuminate\Support\Facades\Log::info('listBlogs executed', [
                'table' => $blogTable,
                'limit' => $limit,
                'count' => is_countable($blogs) ? count($blogs) : (method_exists($blogs, 'count') ? $blogs->count() : null),
                'ms' => $duration,
            ]);
            try {
                @file_put_contents(storage_path('logs/debug_blogs.txt'), "[".now()."] listBlogs succeeded, count=".(is_countable($blogs)?count($blogs):(method_exists($blogs,'count')?$blogs->count():0)).", ms={$duration}\n", FILE_APPEND | LOCK_EX);
            } catch (\Throwable $__e) {}

            return response()->json(['success' => true, 'data' => $blogs, 'limit' => $limit]);
        } catch (\Throwable $e) {
            $duration = round((microtime(true) - $start) * 1000, 2);
            \Illuminate\Support\Facades\Log::error('listBlogs failed', [
                'table' => $blogTable,
                'limit' => $limit,
                'ms' => $duration,
                'error' => $e->getMessage(),
            ]);

            try {
                @file_put_contents(storage_path('logs/debug_blogs.txt'), "[".now()."] listBlogs FAILED, error=".$e->getMessage().", ms={$duration}\n", FILE_APPEND | LOCK_EX);
            } catch (\Throwable $__e) {}

            return response()->json(['success' => false, 'message' => 'Server error loading blogs'], 500);
        }
    }
    
    public function listVideos(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 500));

        $videoTable = Schema::hasTable('videos') ? 'videos' : 'video';
        $videos = DB::table($videoTable)->orderBy('id', 'desc')->limit($limit)->get();
        return response()->json(['success' => true, 'data' => $videos, 'limit' => $limit]);
    }
    
    public function listCareers(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 500));

        $careerTable = Schema::hasTable('career') ? 'career' : 'careers';
        $careers = DB::table($careerTable)->orderBy('id', 'desc')->limit($limit)->get();
        return response()->json(['success' => true, 'data' => $careers, 'limit' => $limit]);
    }
    
    public function listSocialImpact(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 500));

        $table = Schema::hasTable('social_impact') ? 'social_impact' : 'social_impacts';
        $items = DB::table($table)->orderBy('id', 'desc')->limit($limit)->get();
        return response()->json(['success' => true, 'data' => $items, 'limit' => $limit]);
    }
    
    public function listCustomerStories(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 500));

        $table = Schema::hasTable('customer_stories') ? 'customer_stories' : 'customer_story';
        $stories = DB::table($table)->orderBy('id', 'desc')->limit($limit)->get();
        return response()->json(['success' => true, 'data' => $stories, 'limit' => $limit]);
    }
    
    public function listEvents(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 500));

        $table = Schema::hasTable('events') ? 'events' : 'event';
        $events = DB::table($table)->orderBy('id', 'desc')->limit($limit)->get();
        return response()->json(['success' => true, 'data' => $events, 'limit' => $limit]);
    }
    
    public function listTeam(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 500));

        $table = Schema::hasTable('team') ? 'team' : 'teams';
        $team = DB::table($table)->orderBy('id', 'desc')->limit($limit)->get();
        return response()->json(['success' => true, 'data' => $team, 'limit' => $limit]);
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
        $blogTable = Schema::hasTable('blogs') ? 'blogs' : 'blog';
        $idColumn = Schema::hasColumn($blogTable, 'blog_id') ? 'blog_id' : 'id';
        
        // Check if this is an update or create
        if ($request->has('id') && $request->id) {
            // UPDATE - Only update fields that are provided
            $data = [];
            
            if ($request->filled('title')) {
                $titleColumn = Schema::hasColumn($blogTable, 'title') ? 'title' : 'blog_title';
                $data[$titleColumn] = $request->title;
            }
            
            if ($request->filled('author')) {
                $data['author'] = $request->author;
            }
            
            if ($request->filled('date')) {
                $dateColumn = Schema::hasColumn($blogTable, 'date') ? 'date' : 'blog_date';
                $data[$dateColumn] = $request->date;
            }
            
            if ($request->filled('body')) {
                $bodyColumn = Schema::hasColumn($blogTable, 'body') ? 'body' : 'content';
                $data[$bodyColumn] = $request->body;
            }
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . Str::slug($request->title ?? 'blog') . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/blog'), $filename);
                $imageColumn = Schema::hasColumn($blogTable, 'image_path') ? 'image_path' : 'image';
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
                $titleColumn = Schema::hasColumn($blogTable, 'title') ? 'title' : 'blog_title';
                $data[$titleColumn] = $request->title;
            }
            
            if ($request->has('author')) {
                $data['author'] = $request->author;
            }
            
            if ($request->has('date')) {
                $dateColumn = Schema::hasColumn($blogTable, 'date') ? 'date' : 'blog_date';
                $data[$dateColumn] = $request->date;
            }
            
            if ($request->has('body')) {
                $bodyColumn = Schema::hasColumn($blogTable, 'body') ? 'body' : 'content';
                $data[$bodyColumn] = $request->body;
            }
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . Str::slug($request->title ?? 'blog') . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/blog'), $filename);
                $imageColumn = Schema::hasColumn($blogTable, 'image_path') ? 'image_path' : 'image';
                $data[$imageColumn] = 'images/blog/' . $filename;
            }
            
            // Generate IDs if needed
            if ($idColumn === 'blog_id') {
                $maxBlogId = DB::table($blogTable)->max('blog_id') ?? 0;
                $data['blog_id'] = $maxBlogId + 1;
            }
            
            if (Schema::hasColumn($blogTable, 'id') && $idColumn !== 'id') {
                $maxId = DB::table($blogTable)->max('id') ?? 0;
                $data['id'] = $maxId + 1;
            }
            
            // Add default values for fields that don't have defaults
            if (Schema::hasColumn($blogTable, 'clicks') && !isset($data['clicks'])) {
                $data['clicks'] = 0;
            }
            if (Schema::hasColumn($blogTable, 'views') && !isset($data['views'])) {
                $data['views'] = 0;
            }
            if (Schema::hasColumn($blogTable, 'status') && !isset($data['status'])) {
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
        $blogTable = Schema::hasTable('blogs') ? 'blogs' : 'blog';
        $idColumn = Schema::hasColumn($blogTable, 'blog_id') ? 'blog_id' : 'id';
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
        
        $videoTable = Schema::hasTable('videos') ? 'videos' : 'youtube_videos';
        $idColumn = Schema::hasColumn($videoTable, 'video_id') ? 'video_id' : 'id';
        
        $data = [];
        
        // Add url/iframe column with fallback
        if (Schema::hasColumn($videoTable, 'url')) {
            $data['url'] = $validated['url'];
        } elseif (Schema::hasColumn($videoTable, 'video_url')) {
            $data['video_url'] = $validated['url'];
        } elseif (Schema::hasColumn($videoTable, 'iframe')) {
            $data['iframe'] = $validated['url'];
        } elseif (Schema::hasColumn($videoTable, 'embed')) {
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
        $videoTable = Schema::hasTable('videos') ? 'videos' : 'video';
        $idColumn = Schema::hasColumn($videoTable, 'video_id') ? 'video_id' : 'id';
        DB::table($videoTable)->where($idColumn, $id)->delete();
        ActivityLogger::log('delete', 'Video', $id, 'Deleted video #' . $id);
        return response()->json(['success' => true, 'message' => 'Video deleted successfully']);
    }
    
    // Career Management
    public function storeOrUpdateCareer(Request $request)
    {
        $careerTable = Schema::hasTable('career') ? 'career' : 'careers';
        
        if ($request->has('id') && $request->id) {
            // UPDATE
            $data = [];
            
            if ($request->filled('job_title')) {
                $col = Schema::hasColumn($careerTable, 'job_title') ? 'job_title' : 'title';
                $data[$col] = $request->job_title;
            }
            if ($request->filled('job_description')) {
                $col = Schema::hasColumn($careerTable, 'job_description') ? 'job_description' : 'description';
                $data[$col] = $request->job_description;
            }
            if ($request->filled('job_location')) {
                $col = Schema::hasColumn($careerTable, 'job_location') ? 'job_location' : 'location';
                $data[$col] = $request->job_location;
            }
            if ($request->filled('job_type')) {
                $col = Schema::hasColumn($careerTable, 'job_type') ? 'job_type' : 'type';
                $data[$col] = $request->job_type;
            }
            if ($request->filled('job_deadline')) {
                $col = Schema::hasColumn($careerTable, 'job_deadline') ? 'job_deadline' : 'deadline';
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
                $col = Schema::hasColumn($careerTable, 'job_title') ? 'job_title' : 'title';
                $data[$col] = $request->job_title;
            }
            if ($request->filled('job_description')) {
                $col = Schema::hasColumn($careerTable, 'job_description') ? 'job_description' : 'description';
                $data[$col] = $request->job_description;
            }
            if ($request->filled('job_location')) {
                $col = Schema::hasColumn($careerTable, 'job_location') ? 'job_location' : 'location';
                $data[$col] = $request->job_location;
            }
            if ($request->filled('job_type')) {
                $col = Schema::hasColumn($careerTable, 'job_type') ? 'job_type' : 'type';
                $data[$col] = $request->job_type;
            }
            if ($request->filled('job_deadline')) {
                $col = Schema::hasColumn($careerTable, 'job_deadline') ? 'job_deadline' : 'deadline';
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
        $careerTable = Schema::hasTable('career') ? 'career' : 'careers';
        DB::table($careerTable)->where('id', $id)->delete();
        ActivityLogger::log('delete', 'Career', $id, 'Deleted career #' . $id);
        return response()->json(['success' => true, 'message' => 'Career deleted successfully']);
    }
    
    // Social Impact Management
    public function storeOrUpdateSocialImpact(Request $request)
    {
        $table = Schema::hasTable('social_impact') ? 'social_impact' : 'social_impacts';
        
        if ($request->has('id') && $request->id) {
            // UPDATE
            $data = [];
            
            if ($request->filled('title')) {
                $col = Schema::hasColumn($table, 'title') ? 'title' : 'impact_title';
                $data[$col] = $request->title;
            }
            if ($request->filled('body')) {
                $col = Schema::hasColumn($table, 'body') ? 'body' : 'content';
                $data[$col] = $request->body;
            }
            if ($request->filled('category')) {
                $col = Schema::hasColumn($table, 'category') ? 'category' : 'impact_area';
                $data[$col] = $request->category;
            }
            if ($request->filled('posted_date')) {
                $col = Schema::hasColumn($table, 'posted_date') ? 'posted_date' : 'published_date';
                $data[$col] = $request->posted_date;
            }
            if ($request->filled('author_name')) {
                $col = Schema::hasColumn($table, 'author_name') ? 'author_name' : 'author';
                $data[$col] = $request->author_name;
            }
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/social-impact'), $filename);
                
                if (Schema::hasColumn($table, 'image_url')) {
                    $data['image_url'] = 'images/social-impact/' . $filename;
                } elseif (Schema::hasColumn($table, 'image')) {
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
                $col = Schema::hasColumn($table, 'title') ? 'title' : 'impact_title';
                $data[$col] = $request->title;
            }
            if ($request->filled('body')) {
                $col = Schema::hasColumn($table, 'body') ? 'body' : 'content';
                $data[$col] = $request->body;
            }
            if ($request->filled('category')) {
                $col = Schema::hasColumn($table, 'category') ? 'category' : 'impact_area';
                $data[$col] = $request->category;
            }
            if ($request->filled('posted_date')) {
                $col = Schema::hasColumn($table, 'posted_date') ? 'posted_date' : 'published_date';
                $data[$col] = $request->posted_date;
            }
            if ($request->filled('author_name')) {
                $col = Schema::hasColumn($table, 'author_name') ? 'author_name' : 'author';
                $data[$col] = $request->author_name;
            }
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/social-impact'), $filename);
                
                if (Schema::hasColumn($table, 'image_url')) {
                    $data['image_url'] = 'images/social-impact/' . $filename;
                } elseif (Schema::hasColumn($table, 'image')) {
                    $data['image'] = 'images/social-impact/' . $filename;
                }
            }
            
            // Set defaults
            if (!isset($data['author_name']) && Schema::hasColumn($table, 'author_name')) {
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
        $table = Schema::hasTable('social_impact') ? 'social_impact' : 'social_impacts';
        DB::table($table)->where('id', $id)->delete();
        ActivityLogger::log('delete', 'SocialImpact', $id, 'Deleted social impact #' . $id);
        return response()->json(['success' => true, 'message' => 'Social Impact deleted successfully']);
    }
    
    // Customer Stories Management
    public function storeOrUpdateCustomerStory(Request $request)
    {
        $table = Schema::hasTable('customer_stories') ? 'customer_stories' : 'customer_story';
        
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
                $col = Schema::hasColumn($table, 'body_content') ? 'body_content' : 'content';
                $data[$col] = $request->body_content;
            }
            
            if ($request->hasFile('profile')) {
                $image = $request->file('profile');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/customers'), $filename);
                
                if (Schema::hasColumn($table, 'profile')) {
                    $data['profile'] = 'images/customers/' . $filename;
                } elseif (Schema::hasColumn($table, 'profile_image')) {
                    $data['profile_image'] = 'images/customers/' . $filename;
                } elseif (Schema::hasColumn($table, 'image')) {
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
                $col = Schema::hasColumn($table, 'body_content') ? 'body_content' : 'content';
                $data[$col] = $request->body_content;
            }
            
            if ($request->hasFile('profile')) {
                $image = $request->file('profile');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/customers'), $filename);
                
                if (Schema::hasColumn($table, 'profile')) {
                    $data['profile'] = 'images/customers/' . $filename;
                } elseif (Schema::hasColumn($table, 'profile_image')) {
                    $data['profile_image'] = 'images/customers/' . $filename;
                } elseif (Schema::hasColumn($table, 'image')) {
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
        $table = Schema::hasTable('customer_stories') ? 'customer_stories' : 'customer_story';
        DB::table($table)->where('id', $id)->delete();
        ActivityLogger::log('delete', 'CustomerStory', $id, 'Deleted customer story #' . $id);
        return response()->json(['success' => true, 'message' => 'Customer Story deleted successfully']);
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
        $blogTable = Schema::hasTable('blogs') ? 'blogs' : 'blog';
        $idColumn = Schema::hasColumn($blogTable, 'blog_id') ? 'blog_id' : 'id';
        $blog = DB::table($blogTable)->where($idColumn, $id)->first();
        return response()->json($blog);
    }
    
    public function updateBlog(Request $request, $id)
    {
        \Log::info('UpdateBlog called', [
            'id' => $id,
            'request_data' => $request->all(),
            'has_file' => $request->hasFile('image')
        ]);
        
        $blogTable = Schema::hasTable('blogs') ? 'blogs' : 'blog';
        $idColumn = Schema::hasColumn($blogTable, 'blog_id') ? 'blog_id' : 'id';
        
        \Log::info('Table info', [
            'table' => $blogTable,
            'id_column' => $idColumn
        ]);
        
        $data = [];
        
        // Only update fields that are provided and not empty
        if ($request->filled('title')) {
            $titleColumn = Schema::hasColumn($blogTable, 'title') ? 'title' : 'blog_title';
            $data[$titleColumn] = $request->title;
        }
        
        if ($request->filled('author')) {
            $data['author'] = $request->author;
        }
        
        if ($request->filled('date')) {
            $dateColumn = Schema::hasColumn($blogTable, 'date') ? 'date' : 'blog_date';
            $data[$dateColumn] = $request->date;
        }
        
        if ($request->filled('body')) {
            $bodyColumn = Schema::hasColumn($blogTable, 'body') ? 'body' : 'content';
            $data[$bodyColumn] = $request->body;
        }
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . Str::slug($request->title ?? 'blog') . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/blog'), $filename);
            $imageColumn = Schema::hasColumn($blogTable, 'image_path') ? 'image_path' : 'image';
            $data[$imageColumn] = 'images/blog/' . $filename;
        }
        
        \Log::info('Data to update', ['data' => $data]);
        
        if (!empty($data)) {
            // Check if the record exists first
            $exists = DB::table($blogTable)->where($idColumn, $id)->exists();
            \Log::info('Record exists check', [
                'exists' => $exists,
                'where_column' => $idColumn,
                'where_value' => $id
            ]);
            
            if (!$exists) {
                \Log::error('Record not found with given ID');
                return response()->json(['success' => false, 'message' => 'Blog not found'], 404);
            }
            
            $affected = DB::table($blogTable)->where($idColumn, $id)->update($data);
            \Log::info('Update executed', ['rows_affected' => $affected]);
        } else {
            \Log::warning('No data to update');
        }
        
        return response()->json(['success' => true, 'message' => 'Blog updated successfully']);
    }
    
    public function showVideo($id)
    {
        $videoTable = Schema::hasTable('videos') ? 'videos' : 'video';
        $video = DB::table($videoTable)->where('id', $id)->first();
        return response()->json($video);
    }
    
    public function updateVideo(Request $request, $id)
    {
        $videoTable = Schema::hasTable('videos') ? 'videos' : 'video';
        
        $data = [];
        if ($request->has('title')) $data['title'] = $request->title;
        if ($request->has('url')) $data['url'] = $request->url;
        if ($request->has('description')) $data['description'] = $request->description;
        
        DB::table($videoTable)->where('id', $id)->update($data);
        return response()->json(['success' => true, 'message' => 'Video updated successfully']);
    }
    
    public function showCareer($id)
    {
        $careerTable = Schema::hasTable('career') ? 'career' : 'careers';
        $career = DB::table($careerTable)->where('id', $id)->first();
        return response()->json($career);
    }
    
    public function updateCareer(Request $request, $id)
    {
        $careerTable = Schema::hasTable('career') ? 'career' : 'careers';
        
        $data = [];
        
        if ($request->filled('job_title')) {
            $col = Schema::hasColumn($careerTable, 'job_title') ? 'job_title' : 'title';
            $data[$col] = $request->job_title;
        }
        if ($request->filled('job_description')) {
            $col = Schema::hasColumn($careerTable, 'job_description') ? 'job_description' : 'description';
            $data[$col] = $request->job_description;
        }
        if ($request->filled('job_location')) {
            $col = Schema::hasColumn($careerTable, 'job_location') ? 'job_location' : 'location';
            $data[$col] = $request->job_location;
        }
        if ($request->filled('job_type')) {
            $col = Schema::hasColumn($careerTable, 'job_type') ? 'job_type' : 'type';
            $data[$col] = $request->job_type;
        }
        if ($request->filled('job_deadline')) {
            $col = Schema::hasColumn($careerTable, 'job_deadline') ? 'job_deadline' : 'deadline';
            $data[$col] = $request->job_deadline;
        }
        
        if (!empty($data)) {
            DB::table($careerTable)->where('id', $id)->update($data);
        }
        return response()->json(['success' => true, 'message' => 'Career updated successfully']);
    }
    
    public function showSocialImpact($id)
    {
        $table = Schema::hasTable('social_impact') ? 'social_impact' : 'social_impacts';
        $item = DB::table($table)->where('id', $id)->first();
        return response()->json($item);
    }
    
    public function updateSocialImpact(Request $request, $id)
    {
        $table = Schema::hasTable('social_impact') ? 'social_impact' : 'social_impacts';
        
        $data = [];
        
        if ($request->filled('title')) {
            $col = Schema::hasColumn($table, 'title') ? 'title' : 'impact_title';
            $data[$col] = $request->title;
        }
        if ($request->filled('body')) {
            $col = Schema::hasColumn($table, 'body') ? 'body' : 'content';
            $data[$col] = $request->body;
        }
        if ($request->filled('category')) {
            $col = Schema::hasColumn($table, 'category') ? 'category' : 'impact_area';
            $data[$col] = $request->category;
        }
        if ($request->filled('posted_date')) {
            $col = Schema::hasColumn($table, 'posted_date') ? 'posted_date' : 'published_date';
            $data[$col] = $request->posted_date;
        }
        if ($request->filled('author_name')) {
            $col = Schema::hasColumn($table, 'author_name') ? 'author_name' : 'author';
            $data[$col] = $request->author_name;
        }
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/social-impact'), $filename);
            
            if (Schema::hasColumn($table, 'image_url')) {
                $data['image_url'] = 'images/social-impact/' . $filename;
            } elseif (Schema::hasColumn($table, 'image')) {
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
        $table = Schema::hasTable('customer_stories') ? 'customer_stories' : 'customer_story';
        $item = DB::table($table)->where('id', $id)->first();
        return response()->json($item);
    }
    
    public function updateCustomerStory(Request $request, $id)
    {
        $table = Schema::hasTable('customer_stories') ? 'customer_stories' : 'customer_story';
        
        $data = [];
        
        if ($request->filled('name')) {
            $col = Schema::hasColumn($table, 'name') ? 'name' : 'customer_name';
            $data[$col] = $request->name;
        }
        if ($request->filled('position')) {
            $col = Schema::hasColumn($table, 'position') ? 'position' : 'job_title';
            $data[$col] = $request->position;
        }
        if ($request->filled('body_content')) {
            $col = Schema::hasColumn($table, 'body_content') ? 'body_content' : 'content';
            $data[$col] = $request->body_content;
        }
        
        if ($request->hasFile('profile')) {
            $image = $request->file('profile');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/customers'), $filename);
            
            if (Schema::hasColumn($table, 'profile')) {
                $data['profile'] = 'images/customers/' . $filename;
            } elseif (Schema::hasColumn($table, 'profile_image')) {
                $data['profile_image'] = 'images/customers/' . $filename;
            } elseif (Schema::hasColumn($table, 'image')) {
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
        $table = Schema::hasTable('events') ? 'events' : 'event';
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
                if (Schema::hasColumn($table, 'recorded_url')) {
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
            
            if ($request->filled('recorded_url') && Schema::hasColumn($table, 'recorded_url')) {
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
        $table = Schema::hasTable('events') ? 'events' : 'event';
        DB::table($table)->where('id', $id)->delete();
        ActivityLogger::log('delete', 'Event', $id, 'Deleted event #' . $id);
        return response()->json(['success' => true, 'message' => 'Event deleted successfully']);
    }
    
    // ========== TEAM MANAGEMENT ==========
    
    public function storeOrUpdateTeam(Request $request)
    {
        $table = Schema::hasTable('team') ? 'team' : 'teams';
        $id = $request->id;
        
        if ($id) {
            // Update existing team member
            $data = [];
            
            if ($request->filled('team_name')) {
                if (Schema::hasColumn($table, 'team_name')) {
                    $data['team_name'] = $request->team_name;
                } elseif (Schema::hasColumn($table, 'name')) {
                    $data['name'] = $request->team_name;
                }
            }
            
            if ($request->filled('team_title')) {
                if (Schema::hasColumn($table, 'team_title')) {
                    $data['team_title'] = $request->team_title;
                } elseif (Schema::hasColumn($table, 'title')) {
                    $data['title'] = $request->team_title;
                }
            }
            
            if ($request->filled('team_body')) {
                if (Schema::hasColumn($table, 'team_body')) {
                    $data['team_body'] = $request->team_body;
                } elseif (Schema::hasColumn($table, 'body')) {
                    $data['body'] = $request->team_body;
                } elseif (Schema::hasColumn($table, 'bio')) {
                    $data['bio'] = $request->team_body;
                }
            }
            
            if ($request->filled('linkedin') && Schema::hasColumn($table, 'linkedin')) {
                $data['linkedin'] = $request->linkedin;
            }
            
            if ($request->filled('facebook') && Schema::hasColumn($table, 'facebook')) {
                $data['facebook'] = $request->facebook;
            }
            
            if ($request->filled('instagram') && Schema::hasColumn($table, 'instagram')) {
                $data['instagram'] = $request->instagram;
            }
            
            if ($request->filled('x') && Schema::hasColumn($table, 'x')) {
                $data['x'] = $request->x;
            }
            
            if ($request->hasFile('team_image')) {
                $image = $request->file('team_image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/team'), $filename);
                
                if (Schema::hasColumn($table, 'team_image')) {
                    $data['team_image'] = $filename;
                } elseif (Schema::hasColumn($table, 'image')) {
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
            
            if (Schema::hasColumn($table, 'team_name')) {
                $data['team_name'] = $request->team_name;
            } elseif (Schema::hasColumn($table, 'name')) {
                $data['name'] = $request->team_name;
            }
            
            if (Schema::hasColumn($table, 'team_title')) {
                $data['team_title'] = $request->team_title;
            } elseif (Schema::hasColumn($table, 'title')) {
                $data['title'] = $request->team_title;
            }
            
            if (Schema::hasColumn($table, 'team_body')) {
                $data['team_body'] = $request->team_body;
            } elseif (Schema::hasColumn($table, 'body')) {
                $data['body'] = $request->team_body;
            } elseif (Schema::hasColumn($table, 'bio')) {
                $data['bio'] = $request->team_body;
            }
            
            if ($request->filled('linkedin') && Schema::hasColumn($table, 'linkedin')) {
                $data['linkedin'] = $request->linkedin;
            }
            
            if ($request->filled('facebook') && Schema::hasColumn($table, 'facebook')) {
                $data['facebook'] = $request->facebook;
            }
            
            if ($request->filled('instagram') && Schema::hasColumn($table, 'instagram')) {
                $data['instagram'] = $request->instagram;
            }
            
            if ($request->filled('x') && Schema::hasColumn($table, 'x')) {
                $data['x'] = $request->x;
            }
            
            if ($request->hasFile('team_image')) {
                $image = $request->file('team_image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/team'), $filename);
                
                if (Schema::hasColumn($table, 'team_image')) {
                    $data['team_image'] = $filename;
                } elseif (Schema::hasColumn($table, 'image')) {
                    $data['image'] = $filename;
                }
            }
            
            if (Schema::hasColumn($table, 'created_date')) {
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
        $table = Schema::hasTable('team') ? 'team' : 'teams';
        
        // Get the team member to delete their image
        $member = DB::table($table)->where('id', $id)->first();
        if ($member) {
            $imageColumn = Schema::hasColumn($table, 'team_image') ? 'team_image' : 'image';
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
}
