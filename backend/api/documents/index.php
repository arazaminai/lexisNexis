<?php
header("Content-Type: application/json");
include '../../src/db.php';
require '../../src/documentHandler.php';

// Allow CORS (for testing, adjust in production)
header("Access-Control-Allow-Origin: http://localhost"); // or "*" for all origins
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

$method = $_SERVER['REQUEST_METHOD'];
$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$id = $_GET['id'] ?? null;

$docHandler = new DocumentHandler($pdo);

switch ($method) {
    // ✅ GET: List documents or get single document metadata
    case 'GET':
        if ($id) {
            $docHandler->getDocument($id);
            break;
        }
        $docHandler->listDocuments();
        break;

    // ✅ POST: Upload new document
    case 'POST':
        $docHandler->uploadDocument($_FILES['document'] ?? null);
        break;
    // ✅ DELETE: Delete document
    case 'DELETE':
       $docHandler->deleteDocument($id);
        break; 
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
