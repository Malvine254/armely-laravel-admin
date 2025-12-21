<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    /**
     * Search through all pages and return results
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->input('query', '');
        $maxResults = $request->input('max_results', 20);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Search query must be at least 2 characters',
                'results' => []
            ]);
        }

        $results = [];
        $searchablePages = $this->getSearchablePages();

        foreach ($searchablePages as $page) {
            try {
                // Get the content of each page
                $content = $this->getPageContent($page);
                
                if ($content) {
                    // Search for the query in the content
                    $matches = $this->searchInContent($content, $query, $page);
                    
                    if (!empty($matches)) {
                        $results = array_merge($results, $matches);
                    }
                }

                // Limit results
                if (count($results) >= $maxResults) {
                    break;
                }
            } catch (\Exception $e) {
                // Log error but continue searching other pages
                Log::error("Search error for page {$page['name']}: " . $e->getMessage());
            }
        }

        // Sort results by relevance
        usort($results, function ($a, $b) {
            return $b['relevance'] <=> $a['relevance'];
        });

        // Limit to max results
        $results = array_slice($results, 0, $maxResults);

        return response()->json([
            'success' => true,
            'query' => $query,
            'total_results' => count($results),
            'results' => $results
        ]);
    }

    /**
     * Get list of searchable pages
     *
     * @return array
     */
    private function getSearchablePages()
    {
        return [
            ['name' => 'Home', 'route' => 'home', 'view' => 'home'],
            ['name' => 'Company', 'route' => 'company.index', 'view' => 'company'],
            ['name' => 'Services', 'route' => 'services', 'view' => 'services'],
            ['name' => 'Industries', 'route' => 'industries.index', 'view' => 'industries'],
            ['name' => 'Blog', 'route' => 'blog.index', 'view' => 'blog.index'],
            ['name' => 'Career', 'route' => 'career.index', 'view' => 'career'],
            ['name' => 'Contact', 'route' => 'contact', 'view' => 'contact'],
            ['name' => 'Team', 'route' => 'team.index', 'view' => 'team'],
            ['name' => 'Events', 'route' => 'events.index', 'view' => 'events'],
            ['name' => 'Customer Stories', 'route' => 'customer-stories.index', 'view' => 'customer-stories'],
            ['name' => 'Case Studies', 'route' => 'case-studies.index', 'view' => 'case-studies.index'],
            ['name' => 'Social Impact', 'route' => 'social-impact.index', 'view' => 'social-impact'],
            ['name' => 'Partners', 'route' => 'partners.index', 'view' => 'partners'],
            ['name' => 'Job Board', 'route' => 'job-board.index', 'view' => 'job-board'],
        ];
    }

    /**
     * Get page content by rendering the view
     *
     * @param array $page
     * @return string|null
     */
    private function getPageContent($page)
    {
        try {
            // Check if the view exists
            if (View::exists($page['view'])) {
                // Render the view and get content
                $content = View::make($page['view'])->render();

                // Remove inline styles and scripts so search doesn't index CSS/JS
                // Strip <style>...</style> blocks
                $content = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $content);
                // Strip <script>...</script> blocks
                $content = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $content);
                // Also remove noscript blocks
                $content = preg_replace('/<noscript\b[^>]*>.*?<\/noscript>/is', '', $content);
                
                // Strip HTML tags but preserve spacing
                $content = strip_tags($content);
                
                // Remove extra whitespace
                $content = preg_replace('/\s+/', ' ', $content);
                
                return trim($content);
            }
        } catch (\Exception $e) {
            Log::error("Error getting content for {$page['name']}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Search for query in content and extract relevant snippets
     *
     * @param string $content
     * @param string $query
     * @param array $page
     * @return array
     */
    private function searchInContent($content, $query, $page)
    {
        $results = [];
        $query = strtolower($query);
        $contentLower = strtolower($content);

        // Find all occurrences
        $offset = 0;
        $maxSnippets = 3;
        $snippetCount = 0;
        $relevanceScore = 0;

        while (($pos = strpos($contentLower, $query, $offset)) !== false && $snippetCount < $maxSnippets) {
            // Extract snippet around the match
            $snippetLength = 200;
            $start = max(0, $pos - $snippetLength / 2);
            $snippet = substr($content, $start, $snippetLength * 2);

            // Clean up snippet
            $snippet = $this->cleanSnippet($snippet, $query);

            if (!empty($snippet)) {
                // Calculate position as percentage for exact location
                $position = round(($pos / strlen($content)) * 100, 2);

                $results[] = [
                    'page_name' => $page['name'],
                    'page_url' => route($page['route']),
                    'snippet' => $snippet,
                    'position' => $position . '%',
                    'relevance' => $this->calculateRelevance($query, $snippet, $page['name'])
                ];

                $snippetCount++;
                $relevanceScore++;
            }

            $offset = $pos + strlen($query);
        }

        return $results;
    }

    /**
     * Clean and format snippet
     *
     * @param string $snippet
     * @param string $query
     * @return string
     */
    private function cleanSnippet($snippet, $query)
    {
        // Trim snippet at word boundaries
        $snippet = trim($snippet);
        
        // Find first and last space for clean cut
        $firstSpace = strpos($snippet, ' ');
        $lastSpace = strrpos($snippet, ' ');
        
        if ($firstSpace !== false && $lastSpace !== false) {
            $snippet = substr($snippet, $firstSpace, $lastSpace - $firstSpace);
        }

        // Highlight the search term (case insensitive)
        $snippet = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark>$1</mark>', $snippet);

        // Add ellipsis
        $snippet = '...' . trim($snippet) . '...';

        return $snippet;
    }

    /**
     * Calculate relevance score
     *
     * @param string $query
     * @param string $snippet
     * @param string $pageName
     * @return int
     */
    private function calculateRelevance($query, $snippet, $pageName)
    {
        $score = 0;

        // Count occurrences in snippet
        $occurrences = substr_count(strtolower($snippet), strtolower($query));
        $score += $occurrences * 10;

        // Boost if query is in page name
        if (stripos($pageName, $query) !== false) {
            $score += 50;
        }

        // Boost for exact matches
        if (stripos($snippet, $query) !== false) {
            $score += 20;
        }

        return $score;
    }

    /**
     * Get search suggestions based on partial query
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestions(Request $request)
    {
        $query = $request->input('query', '');

        if (strlen($query) < 2) {
            return response()->json([
                'suggestions' => []
            ]);
        }

        // Common search terms and page names
        $suggestions = [
            'AI Services', 'AI Consulting', 'AI Advisory', 'Generative AI',
            'Data Services', 'Data Science', 'Data Analytics', 'Microsoft Fabric',
            'Digital Transformation', 'Cloud Solutions', 'Power Apps',
            'Microsoft Azure', 'Databricks', 'Snowflake',
            'Company Overview', 'Career Opportunities', 'Job Board',
            'Case Studies', 'Customer Stories', 'Blog Articles',
            'Contact Us', 'Social Impact', 'Partners',
            'Industries', 'Services', 'Team', 'Events'
        ];

        // Filter suggestions based on query
        $filtered = array_filter($suggestions, function ($suggestion) use ($query) {
            return stripos($suggestion, $query) !== false;
        });

        // Limit to 5 suggestions
        $filtered = array_slice(array_values($filtered), 0, 5);

        return response()->json([
            'suggestions' => $filtered
        ]);
    }
}
