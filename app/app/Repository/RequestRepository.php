<?php


namespace App\Repository;


use App\Models\Request;

class RequestRepository
{
    public static function createRequest()
    {
        $request = new Request();
        $request->status = Request::OPEN;
        $request->save();

        return $request;
    }
}