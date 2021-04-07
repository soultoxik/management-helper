<?php


namespace App\Repository\Traits;

use App\Storage\Cache;

trait CacheTrait
{
    private Cache $cache;

    public function setRedis(Cache $cache)
    {
        $this->cache = $cache;
    }
}