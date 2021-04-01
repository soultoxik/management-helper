<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    const OPEN = 'open';
    const CLOSE = 'completed';

    protected $table = 'requests';
    protected $fillable = [
        'status',
    ];
}