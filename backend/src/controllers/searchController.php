<?php
require_once 'controller.php';
require_once __DIR__ . '/../services/searchService.php';

class SearchController extends Controller {
    private $query;
    private $searchService;

    public function __construct($params) {
        parent::__construct();
        $this->searchService = new SearchService();

        $this->query = isset($params['q']) ? $params['q'] : null;

    }

    public function searchDocuments() {
        $results = $this->searchService->searchDocuments($this->query);    
        
        return $this->jsonResponse($results);
    }

    
}
