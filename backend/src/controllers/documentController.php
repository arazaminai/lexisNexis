<?php 
require_once 'controller.php';
require_once __DIR__ . '/../services/documentService.php';
require_once __DIR__ . '/../errors/fileNotFound.php';

class DocumentController extends Controller {
    private $id;
    private $documentService;

    public function __construct($params) {
        parent::__construct();
        
        $this->id = isset($params['id']) ? $params['id'] : null;
        $this->documentService = new DocumentService();
    }
    public function getDocuments() {
        try{
            if (isset($this->id)) {
                try{
                    $doc = $this->documentService->getDocumentById($this->id, ["id", "filename", "filepath", "filetype", "uploaded_at"]);
                    return $this->jsonResponse($doc);
                }
                catch (FileNotFound $e){
                    return $this->handleError($e->getMessage(), 400);
                }
            }

            $docs = $this->documentService->listAllDocuments();
            return $this->jsonResponse($docs);
        }
        catch (Exception $e){
            return $this->handleError($e->getMessage(), 500);
        }
    }

    public function deleteDocument() {       
        try {
            $this->documentService->deleteDocumentById($this->id);
        } catch (FileNotFound $e) {
            return $this->handleError($e->getMessage(), 400);
        } catch (Exception $e) {
            return $this->handleError("Failed to delete document", 500);
        }
        
        return http_response_code(204);
    }

    // Upload a new document
    public function uploadDocument($files) {
        $file = $files['document'] ?? null;
        
        try{
            $docId = $this->documentService->uploadDocument($file);
        } catch (InvalidArgumentException $e){
            return $this->handleError($e->getMessage(), 400);
        } catch (UnexpectedValueException $e){
            return $this->handleError($e->getMessage(), 422);
        } catch (Exception $e){
            return $this->handleError($e->getMessage(), 500);
        }

        // 6. Response
        return $this->jsonResponse([
            "message" => "File uploaded and indexed successfully",
            "id" => $docId
        ], 201);
    }
}