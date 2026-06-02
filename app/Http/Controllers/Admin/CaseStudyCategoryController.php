<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseStudyCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CaseStudyCategoryController extends Controller
{
    public function index()
    {
        if (!Schema::hasTable('case_study_categories')) {
            return redirect()->route('admin.tables')
                ->with('error', 'Case study categories table is not available yet. Run migrations first.');
        }

        CaseStudyCategory::syncDefaults();

        $categories = CaseStudyCategory::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $stats = [
            'total' => (int) $categories->count(),
            'active' => (int) $categories->where('is_active', true)->count(),
            'inactive' => (int) $categories->where('is_active', false)->count(),
            'default' => (int) $categories->where('source', 'default')->count(),
        ];

        return view('admin.case-studies.categories', [
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('case_study_categories', 'name')],
            'source' => ['nullable', 'string', 'max:50'],
        ]);

        CaseStudyCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'source' => $validated['source'] ?? 'manual',
            'is_active' => true,
            'sort_order' => CaseStudyCategory::query()->count(),
        ]);

        return redirect()->route('admin.case-study-categories.index')->with('success', 'Category created successfully.');
    }

    public function destroy(CaseStudyCategory $caseStudyCategory): RedirectResponse
    {
        $caseStudyCategory->delete();

        return redirect()->route('admin.case-study-categories.index')->with('success', 'Category deleted successfully.');
    }
}
