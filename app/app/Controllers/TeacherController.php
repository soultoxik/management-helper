<?php

namespace App\Controllers;

use App\Response\JsonResponse;

class TeacherController
{
    public function create()
    {
        $data = ['asd' => 123];
        return JsonResponse::respond($data,201);
    }

    public function search()
    {
        $data = ['asd' => 123];
        return JsonResponse::respond($data);
    }

    public function update(array $args)
    {
        $data = ['asd' => 123, 'args' => $args];
        return JsonResponse::respond($data);
    }

    public function delete(array $args)
    {
        $data = ['asd' => 123, 'args' => $args];
        return JsonResponse::respond($data);
    }
}