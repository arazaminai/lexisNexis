<?php
// include  '../config.php';
$env = parse_ini_file(__DIR__ . '/../.env');


$dsn = "mysql:host=" . $env['MYSQL_HOST'] . 
    ";dbname=" . $env['MYSQL_DATABASE'] . 
    ";port=" . $env['MYSQL_PORT'];
$username = $env['MYSQL_USER'];
$password = $env['MYSQL_PASSWORD'];

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);


    $pdo->exec("
        CREATE TABLE IF NOT EXISTS documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL,
            filepath VARCHAR(500) NOT NULL,
            filetype ENUM('text/plain', 'application/pdf') NOT NULL,
            filesize BIGINT UNSIGNED,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_uploaded_at (uploaded_at),
            INDEX idx_filetype (filetype)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS document_index (
            doc_id INT NOT NULL,
            content LONGTEXT NOT NULL,
            PRIMARY KEY (doc_id),
            FULLTEXT INDEX idx_content (content),
            CONSTRAINT fk_doc FOREIGN KEY (doc_id) REFERENCES documents(id) ON DELETE CASCADE
        );
    ");
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "DB Connection failed"]);
    exit;
}