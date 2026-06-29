<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Testing Reports Data...\n\n";

try {
    // Test consultations
    $consultations = DB::table('consultation')
        ->select(DB::raw("'Consultation' as type"), 'name', 'email', 'service_name as detail', 'date_now as created_at')
        ->orderBy('date_now', 'desc')
        ->limit(3)
        ->get();
    
    echo "Consultations found: " . $consultations->count() . "\n";
    if ($consultations->count() > 0) {
        echo "Sample: " . json_encode($consultations->first(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // Test contacts
    $contacts = DB::table('contacts')
        ->select(DB::raw("'Contact' as type"), 'name', 'email', 'subject as detail', 'sent_date as created_at')
        ->orderBy('sent_date', 'desc')
        ->limit(3)
        ->get();
    
    echo "Contacts found: " . $contacts->count() . "\n";
    if ($contacts->count() > 0) {
        echo "Sample: " . json_encode($contacts->first(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // Test admin activities
    $adminActivities = DB::table('admin_activities')
        ->leftJoin('admin', 'admin_activities.admin_id', '=', 'admin.id')
        ->select('admin_activities.created_at', 'admin_activities.action', 'admin.name', 'admin.email')
        ->orderBy('admin_activities.created_at', 'desc')
        ->limit(3)
        ->get();
    
    echo "Admin Activities found: " . $adminActivities->count() . "\n";
    if ($adminActivities->count() > 0) {
        echo "Sample: " . json_encode($adminActivities->first(), JSON_PRETTY_PRINT) . "\n\n";
    }
    
    // Combined test
    $recentActivity = [];
    
    $consultations = DB::table('consultation')
        ->select(DB::raw("'Consultation' as type"), 'name', 'email', 'service_name as detail', 'date_now as created_at')
        ->orderBy('date_now', 'desc')
        ->limit(5)
        ->get()
        ->toArray();
    $recentActivity = array_merge($recentActivity, $consultations);
    
    $contacts = DB::table('contacts')
        ->select(DB::raw("'Contact' as type"), 'name', 'email', 'subject as detail', 'sent_date as created_at')
        ->orderBy('sent_date', 'desc')
        ->limit(5)
        ->get()
        ->toArray();
    $recentActivity = array_merge($recentActivity, $contacts);
    
    $applications = DB::table('job_applications')
        ->select(DB::raw("'Job Application' as type"), 'name', 'email', 'position as detail', 'application_date as created_at')
        ->orderBy('application_date', 'desc')
        ->limit(5)
        ->get()
        ->toArray();
    $recentActivity = array_merge($recentActivity, $applications);
    
    $campaigns = DB::table('campaigns')
        ->select(DB::raw("'Campaign' as type"), 'full_name as name', 'business_email as email', 'company_name as detail', 'sent_date as created_at')
        ->orderBy('sent_date', 'desc')
        ->limit(5)
        ->get()
        ->toArray();
    $recentActivity = array_merge($recentActivity, $campaigns);
    
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
        ->get()
        ->toArray();
    $recentActivity = array_merge($recentActivity, $adminActivities);
    
    echo "\nTotal combined activities: " . count($recentActivity) . "\n";
    
    // Sort by date
    usort($recentActivity, function($a, $b) {
        return strtotime($b->created_at ?? 0) - strtotime($a->created_at ?? 0);
    });
    
    $recentActivity = array_slice($recentActivity, 0, 10);
    
    echo "After sorting and limiting to 10: " . count($recentActivity) . "\n\n";
    
    foreach ($recentActivity as $activity) {
        $actObj = (array) $activity;
        echo sprintf("%-15s | %-30s | %s\n", 
            $actObj['type'] ?? 'N/A',
            substr($actObj['name'] ?? 'N/A', 0, 30),
            $actObj['created_at'] ?? 'N/A'
        );
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
