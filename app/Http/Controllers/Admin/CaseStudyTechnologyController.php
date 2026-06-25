<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseStudyTechnology;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CaseStudyTechnologyController extends Controller
{
    public function index()
    {
        if (!Schema::hasTable('case_study_technologies')) {
            return redirect()->route('admin.tables')
                ->with('error', 'Case study technologies table is not available yet. Run migrations first.');
        }

        CaseStudyTechnology::syncDefaults();

        $technologies = CaseStudyTechnology::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $stats = [
            'total' => (int) $technologies->count(),
            'active' => (int) $technologies->where('is_active', true)->count(),
            'inactive' => (int) $technologies->where('is_active', false)->count(),
            'default' => (int) $technologies->where('source', 'default')->count(),
        ];

        return view('admin.case-studies.technologies', [
            'technologies' => $technologies,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('case_study_technologies', 'name')],
            'source' => ['nullable', 'string', 'max:50'],
        ]);

        CaseStudyTechnology::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'source' => $validated['source'] ?? 'manual',
            'is_active' => true,
            'sort_order' => CaseStudyTechnology::query()->count(),
        ]);

        return redirect()->route('admin.case-study-technologies.index')->with('success', 'Technology created successfully.');
    }

    public function destroy(CaseStudyTechnology $caseStudyTechnology): RedirectResponse
    {
        $caseStudyTechnology->delete();

        return redirect()->route('admin.case-study-technologies.index')->with('success', 'Technology deleted successfully.');
    }
}
