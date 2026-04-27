<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class CareerController extends Controller
{
    // List rejected applications (status = 0)
    public function listRejected()
    {
        return JobApplication::where('status', 0)
            ->orderBy('application_date', 'desc')
            ->get();
    }
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

    // Stream a CV file for a given application
    public function downloadCv($id)
    {
        $application = JobApplication::findOrFail($id);

        $cvValue = $application->cv_path ?? $application->cv ?? $application->resume ?? null;
        if (!$cvValue) {
            abort(404);
        }

        // Normalize stored value to match disk layout
        $path = ltrim(str_replace(['storage/', 'public/'], '', $cvValue), '/');
        if (!str_starts_with($path, 'cv_uploads/')) {
            $path = 'cv_uploads/' . $path;
        }

        // Check common locations: storage disk, public/storage, public root
        if (Storage::disk('public')->exists($path)) {
            $fullPath = Storage::disk('public')->path($path);
            return response()->download($fullPath);
        }

        $publicStoragePath = public_path('storage/' . $path);
        if (file_exists($publicStoragePath)) {
            return response()->file($publicStoragePath);
        }

        $publicCvPath = public_path($path);
        if (file_exists($publicCvPath)) {
            return response()->file($publicCvPath);
        }

        abort(404);
    }

    // Delete a CV file and entire application
    public function deleteCv($id)
    {
        $application = JobApplication::findOrFail($id);

        $cvValue = $application->cv_path ?? $application->cv ?? $application->resume ?? null;
        
        // Delete CV file if it exists
        if ($cvValue) {
            // Normalize stored value
            $path = ltrim(str_replace(['storage/', 'public/'], '', $cvValue), '/');
            if (!str_starts_with($path, 'cv_uploads/')) {
                $path = 'cv_uploads/' . $path;
            }

            // Try to delete from storage disk
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            // Also try common locations
            $publicStoragePath = public_path('storage/' . $path);
            if (file_exists($publicStoragePath)) {
                @unlink($publicStoragePath);
            }

            $publicCvPath = public_path($path);
            if (file_exists($publicCvPath)) {
                @unlink($publicCvPath);
            }
        }

        // Delete entire application record
        $application->delete();

        return response()->json(['message' => 'Application and CV deleted successfully']);
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
        $application->update(['status' => 0]);
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
