<?php

class FileNotFound extends Exception {
    public function __construct($message = "File not found", $code = 404, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}