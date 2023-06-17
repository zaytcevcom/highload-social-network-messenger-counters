<?php

declare(strict_types=1);

namespace App\Counters\Entity\Counter;

use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: 'counter')]
#[ORM\UniqueConstraint(name: 'IDX_UNIQUE', columns: ['conversation_id', 'user_id'])]
final class Counter
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $conversationId;

    #[ORM\Column(type: 'integer')]
    private int $userId;

    #[ORM\Column(type: 'integer')]
    private int $value;

    private function __construct(
        int $conversationId,
        int $userId,
    ) {
        $this->conversationId = $conversationId;
        $this->userId = $userId;
        $this->value = 0;
    }

    public static function create(
        int $conversationId,
        int $userId,
    ): self {
        return new self(
            $conversationId,
            $userId
        );
    }

    public function getId(): int
    {
        if (null === $this->id) {
            throw new DomainException('Id not set');
        }
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getConversationId(): int
    {
        return $this->conversationId;
    }

    public function setConversationId(int $conversationId): void
    {
        $this->conversationId = $conversationId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }
}
