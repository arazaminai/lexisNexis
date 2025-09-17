<?php
require_once '../../src/controllers/searchController.php';

header("Access-Control-Allow-Origin: http://localhost"); // or "*" for all origins
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

$searchHandler = new SearchController($_GET);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $searchHandler->searchDocuments();
} else {
    http_response_code(404);
    echo json_encode(["error" => "Not Found"]);
}
