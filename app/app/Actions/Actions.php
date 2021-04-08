<?php


namespace App\Actions;

use App\Storage\Cache;
use App\Storage\RedisDAO;

class Actions
{
    protected Cache $cache;

    public function __construct()
    {
        $this->cache = new RedisDAO();
    }
}