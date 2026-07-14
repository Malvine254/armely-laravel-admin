<?php

namespace App\Http\Controllers;

use App\Models\SearchProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchProfileController extends Controller
{
    private const COOKIE_NAME = 'armely_search_visitor';
    private const MAX_TERMS = 20;

    public function show(Request $request): JsonResponse
    {
        [$identityKey, $userId, $visitorToken] = $this->identity($request);
        $profile = SearchProfile::firstOrCreate(
            ['identity_key' => $identityKey],
            ['user_id' => $userId, 'terms' => []]
        );

        if ($userId && !$profile->user_id) {
            $profile->update(['user_id' => $userId]);
        }

        return $this->respond($profile, $visitorToken);
    }

    public function track(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'term' => ['required', 'string', 'min:2', 'max:255'],
        ]);
        $term = preg_replace('/\s+/', ' ', trim($validated['term']));
        $termKey = mb_strtolower($term);
        $now = now();
        $hour = (string) $now->hour;
        $day = (string) $now->dayOfWeek;

        [$identityKey, $userId, $visitorToken] = $this->identity($request);
        $profile = SearchProfile::firstOrCreate(
            ['identity_key' => $identityKey],
            ['user_id' => $userId, 'terms' => []]
        );
        $terms = collect($profile->terms ?: []);
        $index = $terms->search(fn ($entry) => ($entry['termKey'] ?? '') === $termKey);

        if ($index !== false) {
            $entry = $terms->get($index);
            $entry['term'] = $term;
            $entry['count'] = ((int) ($entry['count'] ?? 0)) + 1;
            $entry['lastSearched'] = $now->toISOString();
            $entry['hourWeights'][$hour] = ((int) ($entry['hourWeights'][$hour] ?? 0)) + 1;
            $entry['dayWeights'][$day] = ((int) ($entry['dayWeights'][$day] ?? 0)) + 1;
            $terms->put($index, $entry);
        } else {
            $terms->push([
                'term' => $term,
                'termKey' => $termKey,
                'count' => 1,
                'lastSearched' => $now->toISOString(),
                'hourWeights' => [$hour => 1],
                'dayWeights' => [$day => 1],
            ]);
        }

        $profile->update([
            'user_id' => $userId,
            'terms' => $terms->sort(function ($a, $b) {
                $count = ((int) ($b['count'] ?? 0)) <=> ((int) ($a['count'] ?? 0));
                return $count !== 0 ? $count : strcmp($b['lastSearched'] ?? '', $a['lastSearched'] ?? '');
            })->take(self::MAX_TERMS)->values()->all(),
        ]);

        return $this->respond($profile->fresh(), $visitorToken);
    }

    private function identity(Request $request): array
    {
        $user = $request->user('sanctum');
        if ($user) {
            return ['user:'.$user->getAuthIdentifier(), $user->getAuthIdentifier(), null];
        }

        $token = (string) $request->cookie(self::COOKIE_NAME);
        $newToken = null;
        if (!preg_match('/^[a-f0-9-]{36}$/i', $token)) {
            $token = (string) Str::uuid();
            $newToken = $token;
        }

        return ['guest:'.hash('sha256', $token), null, $newToken];
    }

    private function respond(SearchProfile $profile, ?string $newToken): JsonResponse
    {
        $response = response()->json([
            'data' => [
                'terms' => array_slice($profile->terms ?: [], 0, self::MAX_TERMS),
                'updatedAt' => optional($profile->updated_at)->toISOString(),
            ],
        ]);

        if ($newToken) {
            $response->cookie(self::COOKIE_NAME, $newToken, 60 * 24 * 365, '/', null, request()->isSecure(), true, false, 'Lax');
        }

        return $response;
    }
}
