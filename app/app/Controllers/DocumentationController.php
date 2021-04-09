<?php


namespace App\Controllers;


class DocumentationController
{
    public function generateDocumentation()
    {
        require_once __DIR__ . '/../../documentation/api.php';
    }

    public function documentation()
    {
        require_once __DIR__ . '/../../documentation/swagger/index.html';
    }
}