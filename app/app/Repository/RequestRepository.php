<?php


namespace App\Repository;

use App\Exceptions\RequestRepositoryException;
use App\Models\Request;

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
        $request = Request::findByID($id);
        $request->status = Request::CLOSE;
        return $request->save();
    }

    public static function getStatus(int $id): string
    {
        $request = Request::findByID($id);
        return $request->status;
    }

    public static function setStatus(int $id, string $status): bool
    {
        try {
            $request = Request::findByID($id);
            $request->status = $status;
            return $request->save();
        } catch (\Exception $e) {
            throw new RequestRepositoryException($e->getMessage(), $e->getCode());
        }
    }
}