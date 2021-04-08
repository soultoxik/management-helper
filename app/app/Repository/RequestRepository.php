<?php


namespace App\Repository;

use App\Models\Request;
use League\Route\Http\Exception\NotFoundException;

class RequestRepository extends Repository
{
    public static function createRequest(): Request
    {
        $request = new Request();
        $request->status = Request::OPEN;
        $request->save();

        return $request;
    }

    public static function closeRequest(int $id): bool
    {
        $request = Request::where('id', $id)->first();
        if (empty($request)) {
            throw new NotFoundException('Request (' . $id . ') not found');
        }
        $request->status = Request::CLOSE;
        return $request->save();
    }

    public static function getStatus(int $id): string
    {
        $request = Request::where('id', $id)->first();
        if (empty($request)) {
            throw new NotFoundException('Request (' . $id . ') not found');
        }
        return $request->status;
    }
}