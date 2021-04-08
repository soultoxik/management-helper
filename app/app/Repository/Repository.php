<?php


namespace App\Repository;

use App\Storage\Cache;

class Repository
{
    protected Cache $cache;

    public function __construct(Cache $redisDAO)
    {
        $this->cache = $redisDAO;
    }

}