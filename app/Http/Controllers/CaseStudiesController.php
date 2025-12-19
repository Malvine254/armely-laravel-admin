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

        // Paginate white papers (6 per page)
        $whitePapers = DB::table('white_paper')
            ->select('id', 'title', 'body', 'images', 'pdf')
            ->orderByDesc('id')
            ->paginate(6, ['*'], 'paper_page');

        return view('case-studies.index', [
            'caseStudies' => $caseStudies,
            'whitePapers' => $whitePapers,
        ]);
    }
}
