<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    // List all pending applications (status = 1)
    public function listApplications()
    {
        return JobApplication::where('status', 1)
            ->orderBy('application_date', 'desc')
            ->get();
    }

    // List shortlisted applications (status = 2)
    public function listShortlisted()
    {
        return JobApplication::where('status', 2)
            ->orderBy('application_date', 'desc')
            ->get();
    }

    // List hired employees (status = 3)
    public function listEmployees()
    {
        return JobApplication::where('status', 3)
            ->orderBy('application_date', 'desc')
            ->get();
    }

    // Get unique locations for filter
    public function getLocations()
    {
        return JobApplication::distinct('city')
            ->whereNotNull('city')
            ->pluck('city');
    }

    // Shortlist a candidate (1 -> 2)
    public function shortlist($id)
    {
        $application = JobApplication::findOrFail($id);
        $application->update(['status' => 2]);
        return response()->json(['message' => 'Candidate shortlisted']);
    }

    // Hire a candidate (2 -> 3)
    public function hire($id)
    {
        $application = JobApplication::findOrFail($id);
        $application->update(['status' => 3]);
        return response()->json(['message' => 'Candidate hired']);
    }

    // Reject a candidate (move back to 1)
    public function reject($id)
    {
        $application = JobApplication::findOrFail($id);
        $application->update(['status' => 1]);
        return response()->json(['message' => 'Candidate rejected']);
    }

    // Delete an application
    public function deleteApplication($id)
    {
        JobApplication::findOrFail($id)->delete();
        return response()->json(['message' => 'Application deleted']);
    }

    // Show career management page (if needed separately)
    public function index()
    {
        return view('admin.career.index');
    }

    // List posted jobs
    public function listPostedJobs()
    {
        // This can be implemented if you have a separate Career table
        // For now, returning empty or all active positions
        return response()->json(['message' => 'Posted jobs list']);
    }
}
