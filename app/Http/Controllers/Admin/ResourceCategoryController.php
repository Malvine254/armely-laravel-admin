<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResourceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ResourceCategoryController extends Controller
{
    public function index()
    {
        if (!Schema::hasTable('resource_categories')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Resource categories table is not available yet. Run migrations first.');
        }

        ResourceCategory::syncDefaults();

        $categories = ResourceCategory::query()
            ->withCount('resources')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $stats = [
            'total' => (int) $categories->count(),
            'active' => (int) $categories->where('is_active', true)->count(),
            'inactive' => (int) $categories->where('is_active', false)->count(),
            'service' => (int) $categories->where('source', 'service')->count(),
        ];

        return view('admin.resources.categories', [
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('resource_categories', 'name')],
            'source' => ['nullable', 'string', 'max:50'],
        ]);

        ResourceCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'source' => $validated['source'] ?? 'manual',
            'is_active' => true,
            'sort_order' => ResourceCategory::query()->count(),
        ]);

        return redirect()->route('admin.resource-categories.index')->with('success', 'Category created successfully.');
    }

    public function destroy(ResourceCategory $resourceCategory): RedirectResponse
    {
        $resourceCategory->resources()->update([
            'category_id' => null,
            'category' => null,
        ]);

        $resourceCategory->delete();

        return redirect()->route('admin.resource-categories.index')->with('success', 'Category deleted successfully.');
    }
}
