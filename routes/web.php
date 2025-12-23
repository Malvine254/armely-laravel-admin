<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\TablesController;
use App\Http\Controllers\Admin\AdminsController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\CareerController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CaseStudiesController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HtmlPageController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');

Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/service-details/{name}', [HomeController::class, 'serviceDetails'])->name('service-details');
Route::post('/submit-consultation', [HomeController::class, 'submitConsultation'])->name('submit-consultation');

Route::get('/case-studies', [CaseStudiesController::class, 'index'])->name('case-studies.index');

Route::get('/blog/{blogId?}', [BlogController::class, 'index'])->name('blog.index');
Route::post('/blog/{blogId}/increment-clicks', [BlogController::class, 'incrementClicks'])->name('blog.increment-clicks');
Route::get('/all-partners', [HomeController::class, 'allPartners'])->name('partners.index');
Route::get('/all-partners/{slug}', [HtmlPageController::class, 'show'])
    ->where('slug', '^(aws|snowflake|microsoft|redhat|cisco|guardz|td-synnex|td)$')
    ->name('partners.show');

// Backward-compatible redirect for old path
Route::get('/partners/{slug}', function ($slug) {
    return redirect()->route('partners.show', ['slug' => $slug]);
});

Route::get('/events', [HomeController::class, 'events'])->name('events.index');
Route::get('/company', [HomeController::class, 'company'])->name('company.index');
Route::get('/career', [HomeController::class, 'career'])->name('career.index');
Route::get('/team', [HomeController::class, 'team'])->name('team.index');
Route::get('/customer-stories', [HomeController::class, 'customerStories'])->name('customer-stories.index');
Route::get('/social-impact', [HomeController::class, 'socialImpact'])->name('social-impact.index');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/social-impact-details/{secure_id}', [HomeController::class, 'socialImpactDetails'])->name('social-impact-details');
Route::get('/industries', [HomeController::class, 'industries'])->name('industries.index');
Route::get('/job-board', [HomeController::class, 'jobBoard'])->name('job-board.index');
Route::get('/applications', [HomeController::class, 'applications'])->name('applications.index');
Route::post('/applications', [HomeController::class, 'submitApplication'])->name('applications.submit');

// Search routes
Route::get('/api/search', [SearchController::class, 'search'])->name('search.api');
Route::get('/api/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

// Admin Authentication Routes (guest only)
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
    Route::get('/forgot-password', [AuthController::class, 'showReset'])->name('admin.reset');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('admin.reset.post');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('admin.password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('admin.password.update');
});

// Lightweight public ping for deployment health checks (no auth)
Route::get('/admin/tables/ping', [TablesController::class, 'ping'])->name('admin.tables.ping');

// Admin Protected Routes
Route::prefix('admin')->middleware(['admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    // Graceful fallback: allow GET logout to handle cases where JS/form submission fails
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout.get');
    
    // Tables Management (CRUD for content)
    Route::get('/tables', [TablesController::class, 'index'])->name('admin.tables');
    
    // List endpoints for AJAX table reload
    Route::get('/tables/blogs/list', [TablesController::class, 'listBlogs'])->name('admin.tables.blogs.list');
    Route::get('/tables/videos/list', [TablesController::class, 'listVideos'])->name('admin.tables.videos.list');
    Route::get('/tables/careers/list', [TablesController::class, 'listCareers'])->name('admin.tables.careers.list');
    Route::get('/tables/social-impact/list', [TablesController::class, 'listSocialImpact'])->name('admin.tables.social-impact.list');
    Route::get('/tables/customer-stories/list', [TablesController::class, 'listCustomerStories'])->name('admin.tables.customer-stories.list');
    Route::get('/tables/events/list', [TablesController::class, 'listEvents'])->name('admin.tables.events.list');
    Route::get('/tables/team/list', [TablesController::class, 'listTeam'])->name('admin.tables.team.list');
    
    // Blogs
    Route::get('/tables/blogs/{id}', [TablesController::class, 'showBlog'])->name('admin.tables.blogs.show');
    Route::post('/tables/blogs', [TablesController::class, 'storeOrUpdateBlog'])->name('admin.tables.blogs.store');
    Route::put('/tables/blogs/{id}', [TablesController::class, 'updateBlog'])->name('admin.tables.blogs.update');
    Route::delete('/tables/blogs/{id}', [TablesController::class, 'deleteBlog'])->name('admin.tables.blogs.delete');
    
    // Videos
    Route::get('/tables/videos/{id}', [TablesController::class, 'showVideo'])->name('admin.tables.videos.show');
    Route::post('/tables/videos', [TablesController::class, 'storeOrUpdateVideo'])->name('admin.tables.videos.store');
    Route::put('/tables/videos/{id}', [TablesController::class, 'updateVideo'])->name('admin.tables.videos.update');
    Route::delete('/tables/videos/{id}', [TablesController::class, 'deleteVideo'])->name('admin.tables.videos.delete');
    
    // Careers
    Route::get('/tables/careers/{id}', [TablesController::class, 'showCareer'])->name('admin.tables.careers.show');
    Route::post('/tables/careers', [TablesController::class, 'storeOrUpdateCareer'])->name('admin.tables.careers.store');
    Route::put('/tables/careers/{id}', [TablesController::class, 'updateCareer'])->name('admin.tables.careers.update');
    Route::delete('/tables/careers/{id}', [TablesController::class, 'deleteCareer'])->name('admin.tables.careers.delete');
    
    // Social Impact
    Route::get('/tables/social-impact/{id}', [TablesController::class, 'showSocialImpact'])->name('admin.tables.social-impact.show');
    Route::post('/tables/social-impact', [TablesController::class, 'storeOrUpdateSocialImpact'])->name('admin.tables.social-impact.store');
    Route::put('/tables/social-impact/{id}', [TablesController::class, 'updateSocialImpact'])->name('admin.tables.social-impact.update');
    Route::delete('/tables/social-impact/{id}', [TablesController::class, 'deleteSocialImpact'])->name('admin.tables.social-impact.delete');
    
    // Customer Stories
    Route::get('/tables/customer-stories/{id}', [TablesController::class, 'showCustomerStory'])->name('admin.tables.customer-stories.show');
    Route::post('/tables/customer-stories', [TablesController::class, 'storeOrUpdateCustomerStory'])->name('admin.tables.customer-stories.store');
    Route::put('/tables/customer-stories/{id}', [TablesController::class, 'updateCustomerStory'])->name('admin.tables.customer-stories.update');
    Route::delete('/tables/customer-stories/{id}', [TablesController::class, 'deleteCustomerStory'])->name('admin.tables.customer-stories.delete');
    
    // Events
    Route::post('/tables/events', [TablesController::class, 'storeOrUpdateEvent'])->name('admin.tables.events.store');
    Route::delete('/tables/events/{id}', [TablesController::class, 'deleteEvent'])->name('admin.tables.events.delete');
    
    // Team
    Route::post('/tables/team', [TablesController::class, 'storeOrUpdateTeam'])->name('admin.tables.team.store');
    Route::delete('/tables/team/{id}', [TablesController::class, 'deleteTeam'])->name('admin.tables.team.delete');
    
    // Admin User Management
    Route::get('/profile', [ProfileController::class, 'show'])->name('admin.profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');

    Route::get('/admins', [AdminsController::class, 'index'])->name('admin.admins');
    Route::post('/admins', [AdminsController::class, 'store'])->name('admin.admins.store');
    Route::put('/admins/{id}', [AdminsController::class, 'update'])->name('admin.admins.update');
    Route::delete('/admins/{id}', [AdminsController::class, 'destroy'])->name('admin.admins.delete');
    
    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('admin.reports');
    Route::post('/reports/export', [ReportsController::class, 'export'])->name('admin.reports.export');
    Route::post('/reports/export-pdf', [ReportsController::class, 'exportActivityPdf'])->name('admin.reports.export.pdf');
    Route::post('/reports/export-excel', [ReportsController::class, 'exportActivityExcel'])->name('admin.reports.export.excel');
    
    // File Upload Handlers
    Route::post('/upload/image', [TablesController::class, 'uploadImage'])->name('admin.upload.image');
    Route::post('/upload/pdf', [TablesController::class, 'uploadPdf'])->name('admin.upload.pdf');
    
    // Career Management - Job Applications
    Route::get('/career/list-applications', [CareerController::class, 'listApplications'])->name('admin.career.list-applications');
    Route::get('/career/list-shortlisted', [CareerController::class, 'listShortlisted'])->name('admin.career.list-shortlisted');
    Route::get('/career/list-employees', [CareerController::class, 'listEmployees'])->name('admin.career.list-employees');
    Route::get('/career/locations', [CareerController::class, 'getLocations'])->name('admin.career.locations');
    Route::post('/career/shortlist/{id}', [CareerController::class, 'shortlist'])->name('admin.career.shortlist');
    Route::post('/career/hire/{id}', [CareerController::class, 'hire'])->name('admin.career.hire');
    Route::post('/career/reject/{id}', [CareerController::class, 'reject'])->name('admin.career.reject');
    Route::post('/career/delete-application/{id}', [CareerController::class, 'deleteApplication'])->name('admin.career.delete-application');
});
