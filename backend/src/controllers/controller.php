<?php
require_once __DIR__ . '/../db/documents.php';

class Controller {
    protected $documentDB;
    public function __construct() {
        $this->documentDB = new DocumentDB();
        
    }
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function handleError($message, $statusCode = 400) {
        $this->jsonResponse(['error' => $message], $statusCode);
    }
}