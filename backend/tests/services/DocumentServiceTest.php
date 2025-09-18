<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/services/documentService.php';
require_once __DIR__ . '/../../src/db/documents.php';

class DocumentServiceTest extends TestCase
{
    private $service;

    protected function setUp(): void
    {
        $this->service = new DocumentService();
    }

    public function testListAllDocumentsReturnsArray()
    {
        $result = $this->service->listAllDocuments();
        $this->assertIsArray($result);
    }

    public function testGetDocumentByIdReturnsNullIfNotFound()
    {
        $this->expectException(FileNotFound::class);
        $this->service->getDocumentById(-1);
    }

    public function testUploadDocumentWithInvalidFile()
    {
        $invalidFile = [
            'name' => 'bad.exe',
            'type' => 'application/x-msdownload',
            'tmp_name' => '/tmp/bad.exe',
            'error' => 0,
            'size' => 123
        ];
        $this->expectException(Exception::class);
        $this->service->uploadDocument($invalidFile);
    }

    public function testDeleteDocumentByIdWithInvalidId()
    {
        $this->expectException(FileNotFound::class);
        $this->service->deleteDocumentById(-1);
    }

    public function testStoreFileThrowsExceptionOnInvalidPath()
    {
        $this->expectException(TypeError::class);
        $this->service->storeFile('/invalid/path/to/file.txt', 'file.txt');
    }

    public function testGetDocumentByIdWithValidId()
    {
        $documents = $this->service->listAllDocuments();
        if (!empty($documents)) {
            $first = $documents[0];
            $doc = $this->service->getDocumentById($first['id']);
            $this->assertIsArray($doc);
            $this->assertEquals($first['id'], $doc['id']);
        } else {
            $this->markTestSkipped('No documents available to test getDocumentById with valid ID.');
        }
    }
}