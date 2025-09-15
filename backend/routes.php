<?php
phpinfo();
// Full URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host     = $_SERVER['HTTP_HOST'];
$request  = $_SERVER['REQUEST_URI'];
$url      = $protocol . "://" . $host . $request;

// Request method (GET, POST, PUT, DELETE...)
$method = $_SERVER['REQUEST_METHOD'];

echo "URL: " . $url . "<br>";
echo "Method: " . $method;
?>