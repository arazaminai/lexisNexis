<?php
// include  '../config.php';
class Db {
    private $pdo;

    public function __construct() {
        // $env = $_ENV;
        $dsn = "mysql:host=" . getenv('MYSQL_HOST') . 
            ";dbname=" . getenv('MYSQL_DATABASE') . 
            ";port=" . getenv('MYSQL_PORT');
        $username = getenv('MYSQL_USER');
        $password = getenv('MYSQL_PASSWORD');

        $this->connect($dsn, $username, $password);

        $this->initializeSchema();
    }

    private function connect($dsn, $username, $password) {
        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->pdo;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            exit;
        }
    }

    private function initializeSchema() {
        $this->pdo->exec("
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

        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS document_index (
                doc_id INT NOT NULL,
                content LONGTEXT NOT NULL,
                PRIMARY KEY (doc_id),
                FULLTEXT INDEX idx_content (content),
                CONSTRAINT fk_doc FOREIGN KEY (doc_id) REFERENCES documents(id) ON DELETE CASCADE
            );
        ");
    }

    public function getConnection() {
        return $this->pdo;
    }
}