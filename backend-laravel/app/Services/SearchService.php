<?php

namespace App\Services;

use App\Models\Skripsi;
use Illuminate\Support\Facades\Log;

class SearchService
{
    protected $ontologyService;

    public function __construct(OntologyService $ontologyService)
    {
        $this->ontologyService = $ontologyService;
    }

    /**
     * Perform semantic search on skripsi
     */
    public function semanticSearch($keyword, $filters = [])
    {
        try {
            // Step 1: Get related topics from ontology
            $relatedTopics = $this->ontologyService->getRelatedTopics($keyword);
            
            Log::info("Semantic search for '{$keyword}', found related topics: " . implode(', ', $relatedTopics));

            // Step 2: Build database query
            $query = Skripsi::query();

            // Apply keyword search with Boyer-Moore algorithm concept
            // (Laravel's LIKE uses similar pattern matching internally)
            $query->where(function ($q) use ($keyword, $relatedTopics) {
                // Exact and partial matches on main keyword
                $q->where('judul', 'like', "%{$keyword}%")
                  ->orWhere('kata_kunci', 'like', "%{$keyword}%")
                  ->orWhere('abstrak', 'like', "%{$keyword}%")
                  ->orWhere('topik', 'like', "%{$keyword}%");

                // Semantic expansion: search for related topics
                foreach ($relatedTopics as $relatedTopic) {
                    $q->orWhere('judul', 'like', "%{$relatedTopic}%")
                      ->orWhere('kata_kunci', 'like', "%{$relatedTopic}%")
                      ->orWhere('topik', 'like', "%{$relatedTopic}%");
                }
            });

            // Apply filters
            if (isset($filters['topik']) && $filters['topik']) {
                $query->byTopik($filters['topik']);
            }

            if (isset($filters['tahun']) && $filters['tahun']) {
                $query->byTahun($filters['tahun']);
            }

            // Order by relevance (exact matches first)
            $query->orderByRaw("
                CASE 
                    WHEN judul LIKE '%{$keyword}%' THEN 1
                    WHEN kata_kunci LIKE '%{$keyword}%' THEN 2
                    WHEN topik LIKE '%{$keyword}%' THEN 3
                    ELSE 4
                END
            ");

            $query->orderBy('tahun', 'desc');

            // Execute query
            $results = $query->with('creator')->get();

            // Add metadata about semantic matches
            $results = $results->map(function ($skripsi) use ($keyword, $relatedTopics) {
                $skripsi->is_exact_match = $this->isExactMatch($skripsi, $keyword);
                $skripsi->is_semantic_match = $this->isSemanticMatch($skripsi, $relatedTopics);
                $skripsi->matched_topics = $this->getMatchedTopics($skripsi, $keyword, $relatedTopics);
                return $skripsi;
            });

            return [
                'keyword' => $keyword,
                'related_topics' => $relatedTopics,
                'total_results' => $results->count(),
                'results' => $results,
            ];
        } catch (\Exception $e) {
            Log::error("Semantic search error: " . $e->getMessage());
            return [
                'keyword' => $keyword,
                'related_topics' => [],
                'total_results' => 0,
                'results' => collect([]),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Boyer-Moore string matching algorithm
     * Returns true if pattern is found in text
     */
    public function boyerMooreSearch($text, $pattern)
    {
        $text = strtolower($text);
        $pattern = strtolower($pattern);
        
        $n = strlen($text);
        $m = strlen($pattern);
        
        if ($m > $n) {
            return false;
        }

        // Build bad character table
        $badChar = [];
        for ($i = 0; $i < 256; $i++) {
            $badChar[$i] = -1;
        }
        
        for ($i = 0; $i < $m; $i++) {
            $badChar[ord($pattern[$i])] = $i;
        }

        // Search
        $shift = 0;
        while ($shift <= ($n - $m)) {
            $j = $m - 1;

            while ($j >= 0 && $pattern[$j] == $text[$shift + $j]) {
                $j--;
            }

            if ($j < 0) {
                return true; // Pattern found
            } else {
                $shift += max(1, $j - $badChar[ord($text[$shift + $j])]);
            }
        }

        return false;
    }

    /**
     * Check if skripsi is an exact match
     */
    protected function isExactMatch($skripsi, $keyword)
    {
        return $this->boyerMooreSearch($skripsi->judul, $keyword) ||
               $this->boyerMooreSearch($skripsi->kata_kunci ?? '', $keyword);
    }

    /**
     * Check if skripsi is a semantic match
     */
    protected function isSemanticMatch($skripsi, $relatedTopics)
    {
        foreach ($relatedTopics as $topic) {
            if ($this->boyerMooreSearch($skripsi->judul, $topic) ||
                $this->boyerMooreSearch($skripsi->kata_kunci ?? '', $topic) ||
                $this->boyerMooreSearch($skripsi->topik ?? '', $topic)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get matched topics for a skripsi
     */
    protected function getMatchedTopics($skripsi, $keyword, $relatedTopics)
    {
        $matched = [];

        if ($this->boyerMooreSearch($skripsi->judul, $keyword) ||
            $this->boyerMooreSearch($skripsi->kata_kunci ?? '', $keyword)) {
            $matched[] = $keyword;
        }

        foreach ($relatedTopics as $topic) {
            if ($this->boyerMooreSearch($skripsi->judul, $topic) ||
                $this->boyerMooreSearch($skripsi->kata_kunci ?? '', $topic) ||
                $this->boyerMooreSearch($skripsi->topik ?? '', $topic)) {
                $matched[] = $topic;
            }
        }

        return array_unique($matched);
    }

    /**
     * Get search suggestions based on ontology
     */
    public function getSearchSuggestions($partial)
    {
        try {
            // Get topics from database
            $dbSuggestions = Skripsi::select('topik')
                ->where('topik', 'like', "%{$partial}%")
                ->distinct()
                ->limit(5)
                ->pluck('topik')
                ->toArray();

            // Could also query ontology for suggestions
            // For now, return database suggestions
            return $dbSuggestions;
        } catch (\Exception $e) {
            Log::error("Error getting search suggestions: " . $e->getMessage());
            return [];
        }
    }
}
