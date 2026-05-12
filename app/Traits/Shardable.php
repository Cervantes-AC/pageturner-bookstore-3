<?php

namespace App\Traits;

trait Shardable
{
    public function getShardConnection(): string
    {
        $shardId = $this->id % 4;
        return "mysql_shard_{$shardId}";
    }
}
