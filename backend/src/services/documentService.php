<?php 
require_once __DIR__ . '/../db/documents.php';
require_once __DIR__ . '/../errors/fileNotFound.php';
require_once __DIR__ . '/../services/documentService.php';

class DocumentService {
    private $documentDB;
    private $uploadfolder = "/static/uploads/";
    private $uploadpath;

    public function __construct() {
        $this->documentDB = new DocumentDB();
        $this->uploadpath = __DIR__ .  "/../.." . $this->uploadfolder;
        if (!is_dir($this->uploadpath)) {
            mkdir($this->uploadpath, 0777, true);   
        }
    }

    public function listAllDocuments(): array {
        return $this->documentDB->listDocuments();
    }

    public function getDocumentById($id, $headings=["*"]) {
        $doc = $this->documentDB->getDocument($id, $headings);
        if (!$doc) {
            throw(new FileNotFound("Document not found"));
        }
        return $doc;
        
    }

    public function deleteDocumentById($id) {
        $doc = $this->documentDB->getDocument($id, ["filepath"]);

        if (!$doc) {
            throw(new FileNotFound("Document not found"));
        }

        if (file_exists($this->uploadpath . $doc['filepath'])) {
            unlink($this->uploadpath . $doc['filepath']);
        }
        $this->documentDB->deleteDocument($id);
        return;
    }


    public function uploadDocument($file): int {
        // Validate file
        if (!isset($file) || $file['error']) {
            throw new InvalidArgumentException("No file uploaded or upload error");
        }

        // set the file unique name
        $file['name'] = $this->getUniqueFilename($this->uploadpath, $file['name']);
        // Store file
        $targetFile = $this->storeFile($file);

        // Insert metadata
        $docId = $this->documentDB->insertDocumentMeta(
                    $file['name'],
                    $this->uploadfolder . basename($targetFile),
                    $file['type'],
                    $file['size']
                );

        // Extract text content for indexing
        $content = $this->extractText($file['type'], $targetFile);

        // Insert into index
        $this->documentDB->insertDocumentIndex($docId, $content);

        return $docId;
    }

    public function storeFile($file) {
        $allowed = ['text/plain', 'application/pdf'];
        if (!in_array($file['type'], $allowed)) {
            throw new UnexpectedValueException("Only TXT or PDF files allowed");
        }

        
        if (!is_dir($this->uploadpath)) mkdir($this->uploadpath, 0777, true);

        $filename = $this->getUniqueFilename($this->uploadpath, $file['name']);
        
        $targetFile = $this->uploadpath . $filename;

        // Open input and output streams
        $input = fopen($file['tmp_name'], 'rb');
        if (!$input) {
            throw new RuntimeException("Failed to open uploaded file");
        }

        $output = fopen($targetFile, 'wb');
        if (!$output) {
            fclose($input);
            throw new RuntimeException("Failed to open target file for writing");
        }

        // Stream copy in 8KB chunks
        while (!feof($input)) {
            $chunk = fread($input, 8192);
            fwrite($output, $chunk);
        }

        fclose($input);
        fclose($output);

        return $targetFile;
    }

    function getUniqueFilename($directory, $filename) {
        // Extract file extension and name
        $info = pathinfo($this->uploadpath . $filename);
        
        $basename = $info['filename']; // filename without extension
        $extension = isset($info['extension']) ? '.' . $info['extension'] : '';

        $counter = 1;
        $newFilename = $basename . $extension;

        // Check if file exists, if yes -> add _1, _2, etc.
        while (file_exists($directory . DIRECTORY_SEPARATOR . $newFilename)) {
            $newFilename = $basename . "_" . $counter . $extension;
            $counter++;
        }

        return $newFilename;
    }

    private function extractText($fileType, $targetFile) {
        if ($fileType === 'text/plain') {
            return file_get_contents($targetFile);
        }

        $content = "";
        $tmpTxt = $targetFile . ".txt";
        exec("pdftotext -layout " . escapeshellarg($targetFile) . " " . escapeshellarg($tmpTxt));
        if (file_exists($tmpTxt)) {
            $content = file_get_contents($tmpTxt);
            $content = trim($content); 
            unlink($tmpTxt);
        }
        return $content;
    }
}