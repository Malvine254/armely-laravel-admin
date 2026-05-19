<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class CompanyContentController extends Controller
{
    public function index()
    {
        $portfolios = collect();
        $banners = collect();

        if (Schema::hasTable('company_portfolios')) {
            $portfolios = DB::table('company_portfolios')
                ->orderBy('display_order')
                ->orderByDesc('id')
                ->get();
        }

        if (Schema::hasTable('website_ad_banners')) {
            $banners = DB::table('website_ad_banners')
                ->orderBy('display_order')
                ->orderByDesc('id')
                ->get();
        }

        return view('admin.company-content', compact('portfolios', 'banners'));
    }

    public function storePortfolio(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'short_description' => 'required|string',
            'long_description' => 'nullable|string',
            'features' => 'nullable|string',
            'logo' => 'nullable|image|max:4096',
            'cta_label' => 'nullable|string|max:120',
            'cta_url' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
        ]);

        $payload = [
            'title' => $validated['title'],
            'category' => $validated['category'] ?? null,
            'short_description' => $validated['short_description'],
            'long_description' => $validated['long_description'] ?? null,
            'features' => $this->normalizeFeatures($validated['features'] ?? null),
            'cta_label' => $validated['cta_label'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
            'display_order' => (int) ($validated['display_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($request->hasFile('logo')) {
            $payload['logo_path'] = $request->file('logo')->store('company/portfolio', 'public');
        }

        $id = DB::table('company_portfolios')->insertGetId($payload);
        ActivityLogger::log('create', 'company_portfolio', $id, 'Created company portfolio entry');

        return back()->with('success', 'Company portfolio item created successfully.');
    }

    public function updatePortfolio(Request $request, int $id): RedirectResponse
    {
        $record = DB::table('company_portfolios')->where('id', $id)->first();
        if (!$record) {
            return back()->with('error', 'Portfolio item not found.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'short_description' => 'required|string',
            'long_description' => 'nullable|string',
            'features' => 'nullable|string',
            'logo' => 'nullable|image|max:4096',
            'cta_label' => 'nullable|string|max:120',
            'cta_url' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
        ]);

        $payload = [
            'title' => $validated['title'],
            'category' => $validated['category'] ?? null,
            'short_description' => $validated['short_description'],
            'long_description' => $validated['long_description'] ?? null,
            'features' => $this->normalizeFeatures($validated['features'] ?? null),
            'cta_label' => $validated['cta_label'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
            'display_order' => (int) ($validated['display_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
            'updated_at' => now(),
        ];

        if ($request->hasFile('logo')) {
            if (!empty($record->logo_path)) {
                Storage::disk('public')->delete((string) $record->logo_path);
            }
            $payload['logo_path'] = $request->file('logo')->store('company/portfolio', 'public');
        }

        DB::table('company_portfolios')->where('id', $id)->update($payload);
        ActivityLogger::log('update', 'company_portfolio', $id, 'Updated company portfolio entry');

        return back()->with('success', 'Company portfolio item updated successfully.');
    }

    public function deletePortfolio(int $id): RedirectResponse
    {
        $record = DB::table('company_portfolios')->where('id', $id)->first();
        if (!$record) {
            return back()->with('error', 'Portfolio item not found.');
        }

        if (!empty($record->logo_path)) {
            Storage::disk('public')->delete((string) $record->logo_path);
        }

        DB::table('company_portfolios')->where('id', $id)->delete();
        ActivityLogger::log('delete', 'company_portfolio', $id, 'Deleted company portfolio entry');

        return back()->with('success', 'Company portfolio item deleted successfully.');
    }

    public function storeBanner(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'page' => 'required|in:company,home,global',
            'headline' => 'required|string|max:255',
            'message' => 'nullable|string',
            'button_label' => 'nullable|string|max:120',
            'button_url' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:4096',
            'background_style' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer|min:0|max:9999',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'nullable|boolean',
        ]);

        $payload = [
            'page' => $validated['page'],
            'headline' => $validated['headline'],
            'message' => $validated['message'] ?? null,
            'button_label' => $validated['button_label'] ?? null,
            'button_url' => $validated['button_url'] ?? null,
            'background_style' => $validated['background_style'] ?? null,
            'display_order' => (int) ($validated['display_order'] ?? 0),
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($request->hasFile('image')) {
            $payload['image_path'] = $request->file('image')->store('company/banners', 'public');
        }

        $id = DB::table('website_ad_banners')->insertGetId($payload);
        ActivityLogger::log('create', 'website_ad_banner', $id, 'Created website ad banner');

        return back()->with('success', 'Advert banner created successfully.');
    }

    public function updateBanner(Request $request, int $id): RedirectResponse
    {
        $record = DB::table('website_ad_banners')->where('id', $id)->first();
        if (!$record) {
            return back()->with('error', 'Banner not found.');
        }

        $validated = $request->validate([
            'page' => 'required|in:company,home,global',
            'headline' => 'required|string|max:255',
            'message' => 'nullable|string',
            'button_label' => 'nullable|string|max:120',
            'button_url' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:4096',
            'background_style' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer|min:0|max:9999',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'nullable|boolean',
        ]);

        $payload = [
            'page' => $validated['page'],
            'headline' => $validated['headline'],
            'message' => $validated['message'] ?? null,
            'button_label' => $validated['button_label'] ?? null,
            'button_url' => $validated['button_url'] ?? null,
            'background_style' => $validated['background_style'] ?? null,
            'display_order' => (int) ($validated['display_order'] ?? 0),
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'updated_at' => now(),
        ];

        if ($request->hasFile('image')) {
            if (!empty($record->image_path)) {
                Storage::disk('public')->delete((string) $record->image_path);
            }
            $payload['image_path'] = $request->file('image')->store('company/banners', 'public');
        }

        DB::table('website_ad_banners')->where('id', $id)->update($payload);
        ActivityLogger::log('update', 'website_ad_banner', $id, 'Updated website ad banner');

        return back()->with('success', 'Advert banner updated successfully.');
    }

    public function deleteBanner(int $id): RedirectResponse
    {
        $record = DB::table('website_ad_banners')->where('id', $id)->first();
        if (!$record) {
            return back()->with('error', 'Banner not found.');
        }

        if (!empty($record->image_path)) {
            Storage::disk('public')->delete((string) $record->image_path);
        }

        DB::table('website_ad_banners')->where('id', $id)->delete();
        ActivityLogger::log('delete', 'website_ad_banner', $id, 'Deleted website ad banner');

        return back()->with('success', 'Advert banner deleted successfully.');
    }

    private function normalizeFeatures(?string $rawFeatures): ?string
    {
        if ($rawFeatures === null) {
            return null;
        }

        $items = collect(preg_split('/\r\n|\r|\n/', $rawFeatures) ?: [])
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->values();

        return $items->isEmpty() ? null : $items->toJson();
    }
}
