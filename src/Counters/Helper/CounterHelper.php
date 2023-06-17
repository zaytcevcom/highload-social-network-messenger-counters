<?php

declare(strict_types=1);

namespace App\Counters\Helper;

class CounterHelper
{
    public function getKeyId(int $conversationId, int $userId): string
    {
        return $conversationId . ':' . $userId;
    }
}
