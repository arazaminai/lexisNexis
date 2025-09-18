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

    public function testSearchReturnsArrayWithResultsKey()
    {
        $results = $this->service->searchDocuments('test');
        $this->assertIsArray($results);
        $this->assertArrayHasKey('results', $results);
        $this->assertArrayHasKey('cached', $results);
    }

    public function testSearchReturnsResultsArray()
    {
        $results = $this->service->searchDocuments('test');
        $this->assertIsArray($results['results']);
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

    public function testHighlightSnippetHighlightsQuery()
    {
        $content = "This is a test document. The test is successful.";
        $query = "test";
        $snippet = $this->service->highlightSnippet($content, $query);
        $this->assertStringContainsString('<mark>test</mark>', $snippet);
    }

    public function testHighlightSnippetReturnsNullForEmptyContent()
    {
        $snippet = $this->service->highlightSnippet('', 'test');
        $this->assertNull($snippet);
    }

    public function testHighlightSnippetReturnsPartialSnippet()
    {
        $content = str_repeat("A ", 150) . "test" . str_repeat(" B", 150);
        $query = "test";
        $snippet = $this->service->highlightSnippet($content, $query);
        $this->assertLessThanOrEqual(203, strlen($snippet)); // 200 chars + "..."
        $this->assertStringContainsString('<mark>test</mark>', $snippet);
    }

    public function testCacheWorks()
    {
        $query = "cachetest";
        // First call - should not be cached
        $firstCall = $this->service->searchDocuments($query);
        $this->assertFalse($firstCall['cached']);

        // Second call - should be cached
        $secondCall = $this->service->searchDocuments($query);
        $this->assertTrue($secondCall['cached']);

        // Results should be the same
        $this->assertEquals($firstCall['results'], $secondCall['results']);
    }
}