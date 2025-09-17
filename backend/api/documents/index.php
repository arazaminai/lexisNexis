<?php
require_once '../../src/controllers/documentController.php';

// Allow CORS (for testing, adjust in production)
header("Access-Control-Allow-Origin: http://localhost"); // or "*" for all origins
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

$method = $_SERVER['REQUEST_METHOD'];
// $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

$controller = new DocumentController($_GET);

switch ($method) {
    // ✅ GET: List documents or get single document metadata
    case 'GET':
        $controller->getDocuments();
        break;

    // ✅ POST: Upload new document
    case 'POST':
        $controller->uploadDocument($_FILES);
        break;
    // ✅ DELETE: Delete document
    case 'DELETE':
        $controller->deleteDocument();
        break; 
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
