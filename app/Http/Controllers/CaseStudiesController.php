<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CaseStudiesController extends Controller
{
    public function index()
    {
        // Paginate case studies (6 per page)
        $caseStudies = DB::table('industry_listings')
            ->select('id', 'category', 'listing_image', 'body', 'pdf_url')
            ->orderByDesc('id')
            ->paginate(6, ['*'], 'case_page');

        $caseStudies->getCollection()->transform(function ($caseStudy) {
            $caseStudy->preview = $this->makePreviewText((string) ($caseStudy->body ?? ''), 120);

            return $caseStudy;
        });

        // Paginate white papers (6 per page)
        $whitePapers = DB::table('white_paper')
            ->select('id', 'title', 'body', 'images', 'pdf')
            ->orderByDesc('id')
            ->paginate(6, ['*'], 'paper_page');

        $whitePapers->getCollection()->transform(function ($paper) {
            $paper->preview = $this->makePreviewText((string) ($paper->body ?? ''), 120);

            return $paper;
        });

        return view('case-studies.index', [
            'caseStudies' => $caseStudies,
            'whitePapers' => $whitePapers,
        ]);
    }

    private function makePreviewText(string $value, int $limit): string
    {
        $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $cleaned = Str::of(strip_tags($decoded))
            ->replace(["\u{2010}", "\u{2011}", "\u{2012}", "\u{2013}", "\u{2014}", "\u{2015}", "\u{00AD}", "\u{FFFD}", 'â€‘', 'â€“', 'â€”'], '-')
            ->replace("\u{00A0}", ' ')
            ->replaceMatches('/\s+/u', ' ')
            ->trim();

        return Str::limit((string) $cleaned, $limit);
    }
}
