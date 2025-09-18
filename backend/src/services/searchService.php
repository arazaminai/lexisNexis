<?php 
require_once __DIR__ . '/../db/documents.php';
require_once "cache.php";

class SearchService {
    private $documentDB;
    private $cache;

    public function __construct() {
        $this->documentDB = new DocumentDB();
        $this->cache = new Cache();
    }

    public function searchDocuments($query): array {
        $query = trim($query);
        if (empty($query)) {
            throw new InvalidArgumentException("Query parameter ?q= cannot be empty", 400);
        }

        // --- 1. Check cache (in-memory basic cache) ---
        $cached = $this->cache->get($query);
        if ($cached !== false) {
            return [
                "cached" => true,
                "results" => $cached
            ];
        }

        // --- 2. Run full-text query ---
        $results = $this->documentDB->fullTextSearch($query);

        
        // --- 3. Highlight keywords ---
        foreach ($results as &$row) {
            $content = $this->documentDB->getDocumentContent($row['id']);
            $row['highlight'] = $this->highlightSnippet($content, $query);
        }

        // --- 4. Store in cache ---
        $this->cache->set($query, $results, 300); 

        return [
            "cached" => false,
            "results" => $results
        ];
    }

    public function highlightSnippet($content, $query) {

        if (!$content) return null;

        // Simple keyword highlighting (case-insensitive)
        $escaped = preg_quote($query, '/');
        $snippet = preg_replace("/($escaped)/i", '<mark>$1</mark>', $content);

        // Return first 200 chars of highlighted snippet 
        if (preg_match('/<mark>/', $snippet, $matches, PREG_OFFSET_CAPTURE)) {
            $start = max(0, $matches[0][1] - 100);
            $end = min(strlen($snippet), $start + 200);
            return substr($snippet, $start, $end - $start) . "...";
        }
    }
}
