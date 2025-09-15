i<?php
include  '../../src/db.php';
include  '../../src/searchHandler.php';

header("Content-Type: application/json");
// Allow CORS (for testing, adjust in production)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

$searchHandler = new SearchHandler($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['q'])) {
    $searchHandler->searchDocuments($_GET['q']);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Missing query parameter ?q="]);
}
