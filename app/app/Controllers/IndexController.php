<?php
namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;

class IndexController
{
    public function index(ServerRequestInterface $request): array
    {
        $test = $request->getQueryParams();

        return ['message'=>'hello world','queryParams' => $test];
    }

    public function test(): array
    {
        return ['test' => 'test'];
    }
}