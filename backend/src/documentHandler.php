<?php 
class DocumentHandler {
    protected $pdo;

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

        $this->jsonResponse(["message" => "Document deleted successfully"]);
    }

    public function uploadDocument($file) {
        if (!isset($_FILES['document'])) {
            return $this->handleError("No file uploaded", 400);
        }

        $file = $_FILES['document'];
        $allowed = ['text/plain', 'application/pdf'];
        if (!in_array($file['type'], $allowed)) {
            return $this->handleError("Unsupported file type", 415);
        }

        $filename = basename($file['name']);
        $uploadDir = __DIR__ . '/../static/uploads/';
        $targetFile = $uploadDir . time() . '_' . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $stmt = $this->pdo->prepare("INSERT INTO documents (filename, filepath, filetype) VALUES (:filename, :filepath, :filetype)");
            $stmt->execute([
                ':filename' => $filename,
                ':filepath' => $targetFile,
                ':filetype' => $file['type']
            ]);
            $docId = $this->pdo->lastInsertId();
        } else {
            return $this->handleError("Failed to move uploaded file", 500);
        }

        return $this->jsonResponse([
            "message" => "File uploaded and indexed successfully",
            "id" => $docId
        ], 201);
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