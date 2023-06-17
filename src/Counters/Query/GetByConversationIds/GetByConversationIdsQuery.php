<?php

declare(strict_types=1);

namespace App\Counters\Query\GetByConversationIds;

use Symfony\Component\Validator\Constraints as Assert;

final class GetByConversationIdsQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,
        #[Assert\NotBlank]
        public readonly array|string $ids,
    ) {
    }
}
