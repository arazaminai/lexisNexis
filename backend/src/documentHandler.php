<?php 
class DocumentHandler {
    protected $pdo;
    protected $uploadpath = "/static/uploads/";

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listDocuments() {
        try {
            $stmt = $this->pdo->query("SELECT id, filename, filepath, filetype, uploaded_at FROM documents");
            $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->jsonResponse($docs);
        } catch (PDOException $e) {
            $this->handleError("Failed to fetch documents", 500);
        }
    }
    
    // Get document metadata by ID
    public function getDocument($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, filename, filepath, filetype, uploaded_at FROM documents WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $doc = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$doc) {
                $this->handleError("Document not found", 404);
            } else {
                $this->jsonResponse($doc);
            }
        } catch (PDOException $e) {
            $this->handleError("Failed to fetch document", 500);
        }
    }

    public function deleteDocument($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM documents WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doc) {
            $this->handleError("Document not found", 404);
        }

        $filePath = __DIR__ . '/../../static/' . $doc['filepath'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $stmt = $this->pdo->prepare("DELETE FROM documents WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $this->jsonResponse(["message" => "Document deleted successfully"]);
    }


    // Upload a new document
    public function uploadDocument($file) {
        if (!isset($file)) {
            return $this->handleError("No file uploaded", 400);
        }

        $targetFile = $this->storeFile($file);

        $docId = $this->insertMetadata($file, $targetFile);

        $content = $this->extractText($file['type'], $targetFile);

        if ($content) {
            $this->insertIndex($docId, $content);
        }

        // 6. Response
        return $this->jsonResponse([
            "message" => "File uploaded and indexed successfully",
            "id" => $docId
        ], 201);
    }

    private function storeFile($file) {
        $allowed = ['text/plain', 'application/pdf'];
        if (!in_array($file['type'], $allowed)) {
            $this->handleError("Only TXT or PDF files allowed", 400);
        }

        $uploadDir = __DIR__ . '/..' . $this->uploadpath;
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filename = time() . "_" . basename($file['name']); // avoid collisions
        $targetFile = $uploadDir . $filename;

        // Open input and output streams
        $input = fopen($file['tmp_name'], 'rb');
        if (!$input) {
            $this->handleError("Failed to open uploaded file", 500);
        }

        $output = fopen($targetFile, 'wb');
        if (!$output) {
            fclose($input);
            $this->handleError("Failed to open target file for writing", 500);
        }

        // Stream copy in 8KB chunks
        while (!feof($input)) {
            $chunk = fread($input, 8192);
            fwrite($output, $chunk);
        }

        fclose($input);
        fclose($output);

        return $targetFile;
    }

    private function insertMetadata($file, $targetFile) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO documents (filename, filepath, filetype, filesize)
                VALUES (:filename, :filepath, :filetype, :filesize)
            ");
            $stmt->execute([
                ':filename' => basename($targetFile),
                ':filepath' => $this->uploadpath . basename($targetFile),
                ':filetype' => $file['type'],
                ':filesize' => filesize($targetFile)
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->handleError("Failed to save metadata: " . $e->getMessage(), 500);
        }
    }

    private function extractText($fileType, $targetFile) {
        $content = "";
        if ($fileType === 'text/plain') {
            $content = file_get_contents($targetFile);
        } elseif ($fileType === 'application/pdf') {
            $tmpTxt = $targetFile . ".txt";
            exec("pdftotext -layout " . escapeshellarg($targetFile) . " " . escapeshellarg($tmpTxt));
            if (file_exists($tmpTxt)) {
                $content = file_get_contents($tmpTxt);
                $content = trim($content); // remove extra spaces/newlines
                unlink($tmpTxt);
            }
        }
        return $content;
    }

    private function insertIndex($docId, $content) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO document_index (doc_id, content)
                VALUES (:doc_id, :content)
            ");
            $stmt->execute([
                ':doc_id' => $docId,
                ':content' => $content
            ]);
        } catch (PDOException $e) {
            $this->handleError("Failed to index document: " . $e->getMessage(), 500);
        }
    }


    public function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function handleError($message, $status = 500) {
        $this->jsonResponse(['error' => $message], $status);
        exit;
    }

    
}