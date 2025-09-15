<?php
class SearchHandler {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function searchDocuments($query) {
        $query = trim($query);
        if (strlen($query) < 3) {
            return $this->jsonResponse(["error" => "Search term too short"], 400);
        }

        $stmt = $this->pdo->prepare("
            SELECT d.id, d.filename, d.filepath, d.filetype, d.uploaded_at,
                   MATCH(i.content) AGAINST (:q IN NATURAL LANGUAGE MODE) AS relevance
            FROM documents d
            JOIN document_index i ON d.id = i.doc_id
            WHERE MATCH(i.content) AGAINST (:q IN NATURAL LANGUAGE MODE)
            ORDER BY relevance DESC, d.uploaded_at DESC
            LIMIT 20
        ");
        $stmt->execute([':q' => $query]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as &$row) {
            $row['highlight'] = $this->highlightSnippet($row['id'], $query);
        }

        return $this->jsonResponse([
            "results" => $results
        ]);
    }

    private function highlightSnippet($docId, $query) {
        $stmt = $this->pdo->prepare("SELECT content FROM document_index WHERE doc_id = :id");
        $stmt->execute([':id' => $docId]);
        $content = $stmt->fetchColumn();

        if (!$content) return null;

        // Simple keyword highlighting (case-insensitive)
        $escaped = preg_quote($query, '/');
        $snippet = preg_replace("/($escaped)/i", '<mark>$1</mark>', $content);

        // Return first 200 chars of highlighted snippet
        return substr($snippet, 0, 200) . "...";
    }

    private function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header("Content-Type: application/json");
        echo json_encode($data);
        exit;
    }
}
