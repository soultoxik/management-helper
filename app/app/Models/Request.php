<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use League\Route\Http\Exception\NotFoundException;

class Request extends Model
{
    const OPEN = 'open';
    const CLOSE = 'completed';

    protected $table = 'requests';
    protected $fillable = [
        'status',
    ];

    public static function findByID(int $id): Request
    {
        $request = Request::where('id', $id)->first();
        if (empty($request)) {
            throw new NotFoundException('Request (' . $id . ') not found');
        }
        return $request;
    }
}
