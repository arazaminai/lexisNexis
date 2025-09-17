<?php 
require_once 'db.php';

class DocumentDB extends Db {
    private $pdo;

    public function __construct() {
        parent::__construct();
        $this->pdo = $this->getConnection();
    }


    public function listDocuments(): array {
        try {
            return $this->pdo
                ->query("SELECT id, filename, filepath, filetype, uploaded_at FROM documents")
                ->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to fetch documents: " . $e->getMessage());
        }
    }

    public function getDocument($id, $headings=["*"]){
        $validHeadings = ["*", "id", "filename", "filepath", "filetype", "filesize", "uploaded_at"];
        foreach ($headings as $heading) {
            if (!in_array($heading, $validHeadings)) {
                throw new Exception("Invalid heading: " . $heading);
            }
        }
        $columns = implode(", ", $headings);
        try {
            return $this->pdo
                ->query("SELECT " . $columns . " FROM documents WHERE id = " . intval($id))
                ->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to fetch document: " . $e->getMessage());
        }
    }

    public function deleteDocument($id): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM documents WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            throw new Exception("Failed to delete document: " . $e->getMessage());
        }
    }

    public function insertDocumentMeta($filename, $filepath, $filetype, $filesize): int {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO documents (filename, filepath, filetype, filesize) 
                VALUES (:filename, :filepath, :filetype, :filesize)
            ");
            $stmt->execute([
                ':filename' => $filename,
                ':filepath' => $filepath,
                ':filetype' => $filetype,
                ':filesize' => $filesize
            ]);
            return (int)$this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Failed to insert document: " . $e->getMessage());
        }
    }

    public function insertDocumentIndex($docId, $content): bool {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO document_index (doc_id, content) 
                VALUES (:doc_id, :content)
            ");
            return $stmt->execute([
                ':doc_id' => $docId,
                ':content' => $content
            ]);
        } catch (PDOException $e) {
            throw new Exception("Failed to insert document index: " . $e->getMessage());
        }
    }

    public function fullTextSearch($query): array {
        try {
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
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Search failed: " . $e->getMessage());
        }
    }

    public function getDocumentContent($docId): ?string {
        try {
            return $this->pdo
                ->query("SELECT content FROM document_index WHERE doc_id = " . intval($docId))
                ->fetchColumn() ?: null;
        } catch (PDOException $e) {
            throw new Exception("Failed to fetch document context: " . $e->getMessage());
        }
    }
}