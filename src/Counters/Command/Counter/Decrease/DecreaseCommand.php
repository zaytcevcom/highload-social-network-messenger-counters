<?php

declare(strict_types=1);

namespace App\Counters\Command\Counter\Decrease;

use Symfony\Component\Validator\Constraints as Assert;

final class DecreaseCommand
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $conversationId,
        #[Assert\NotBlank]
        public readonly int $userId,
        #[Assert\NotBlank]
        public readonly int $value,
    ) {
    }
}
