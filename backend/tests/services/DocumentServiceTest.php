<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/services/documentService.php';
require_once __DIR__ . '/../../src/db/documents.php';

class DocumentServiceTest extends TestCase
{
    private $service;
    private static $testFilePath;

    protected function setUp(): void
    {
        $this->service = new DocumentService();
    }

    public static function setUpBeforeClass(): void
    {
        // Create a dummy text file for upload tests
        self::$testFilePath = sys_get_temp_dir() . '/phpunit_test.txt';
        file_put_contents(self::$testFilePath, "This is a test document.");
    }

    public static function tearDownAfterClass(): void
    {
        // Remove the dummy file
        if (file_exists(self::$testFilePath)) {
            unlink(self::$testFilePath);
        }
    }

    public function testListAllDocumentsReturnsArray()
    {
        $result = $this->service->listAllDocuments();
        $this->assertIsArray($result);
    }

    public function testUploadDocumentStoresFileAndReturnsId()
    {
        $file = [
            'name' => 'phpunit_test.txt',
            'type' => 'text/plain',
            'tmp_name' => self::$testFilePath,
            'error' => 0,
            'size' => filesize(self::$testFilePath)
        ];

        $docId = $this->service->uploadDocument($file);
        $this->assertIsInt($docId);
        // Clean up: delete the uploaded document
        $this->service->deleteDocumentById($docId);
    }

    public function testGetDocumentByIdReturnsDocument()
    {
        // First, upload a document
        $file = [
            'name' => 'phpunit_test.txt',
            'type' => 'text/plain',
            'tmp_name' => self::$testFilePath,
            'error' => 0,
            'size' => filesize(self::$testFilePath)
        ];
        $docId = $this->service->uploadDocument($file);

        $doc = $this->service->getDocumentById($docId);
        $this->assertIsArray($doc);
        $this->assertEquals($docId, $doc['id']);

        // Clean up
        $this->service->deleteDocumentById($docId);
    }

    public function testDeleteDocumentByIdRemovesDocument()
    {
        // Upload a document
        $file = [
            'name' => 'phpunit_test.txt',
            'type' => 'text/plain',
            'tmp_name' => self::$testFilePath,
            'error' => 0,
            'size' => filesize(self::$testFilePath)
        ];
        $docId = $this->service->uploadDocument($file);

        // Delete it
        $this->service->deleteDocumentById($docId);

        // Now, trying to get it should throw FileNotFound
        $this->expectException(FileNotFound::class);
        $this->service->getDocumentById($docId);
    }

    public function testStoreFileStoresFileAndReturnsPath()
    {
        $file = [
            'name' => 'phpunit_test.txt',
            'type' => 'text/plain',
            'tmp_name' => self::$testFilePath,
            'error' => 0,
            'size' => filesize(self::$testFilePath)
        ];

        $targetPath = $this->service->storeFile($file);
        $this->assertFileExists($targetPath);

        // Clean up
        if (file_exists($targetPath)) {
            unlink($targetPath);
        }
    }

    public function testGetUniqueFilenameReturnsUniqueName()
    {
        $dir = sys_get_temp_dir();
        $filename = 'phpunit_test.txt';
        // Create a file to force a conflict
        $existingFile = $dir . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($existingFile, "dummy");

        $uniqueName = $this->service->getUniqueFilename($dir . DIRECTORY_SEPARATOR, $filename);
        $this->assertNotEquals($filename, $uniqueName);

        // Clean up
        unlink($existingFile);
    }
}