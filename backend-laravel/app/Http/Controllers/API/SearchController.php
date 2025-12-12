<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Perform semantic search
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
            'topik' => 'nullable|string',
            'tahun' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        $keyword = $request->input('q');
        $filters = [
            'topik' => $request->input('topik'),
            'tahun' => $request->input('tahun'),
        ];

        $results = $this->searchService->semanticSearch($keyword, $filters);

        return response()->json($results);
    }

    /**
     * Get search suggestions
     */
    public function suggestions(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1',
        ]);

        $partial = $request->input('q');
        $suggestions = $this->searchService->getSearchSuggestions($partial);

        return response()->json([
            'suggestions' => $suggestions,
        ]);
    }
}
