<?php
include  '../../src/db.php';
include  '../../src/searchHandler.php';

header("Access-Control-Allow-Origin: http://localhost"); // or "*" for all origins
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

$searchHandler = new SearchHandler($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['q'])) {
    $searchHandler->searchDocuments($_GET['q']);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Missing query parameter ?q="]);
}
