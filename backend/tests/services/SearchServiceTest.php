<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/services/searchService.php';

class SearchServiceTest extends TestCase
{
    private $service;

    protected function setUp(): void
    {
        $this->service = new SearchService();
    }

    public function testSearchReturnsArray()
    {
        $results = $this->service->searchDocuments('unlikelysearchtermthatdoesnotexist');
        $this->assertIsArray($results);
        $this->assertArrayHasKey('results', $results);
    }

    public function testSearchReturnsEmptyArrayForNoMatch()
    {
        $results = $this->service->searchDocuments('unlikelysearchtermthatdoesnotexist');
        $this->assertArrayHasKey('results', $results);
        $this->assertTrue(empty($results['results']), 'Expected empty result for non-matching search.');
    }

    public function testSearchThrowsExceptionOnEmptyQuery()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->searchDocuments('');
    }
}