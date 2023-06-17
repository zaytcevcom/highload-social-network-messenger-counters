<?php

declare(strict_types=1);

namespace App\Counters\Command\Counter\Increase;

use App\Counters\Helper\CounterHelper;
use Doctrine\DBAL\Connection;
use ZayMedia\Shared\Components\Cacher\Cacher;

final class IncreaseHandler
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Cacher $cacher,
        private readonly CounterHelper $counterHelper,
    ) {
    }

    public function handle(IncreaseCommand $command): void
    {
        $this->saveToDb(
            conversationId: $command->conversationId,
            userId: $command->userId,
            value: $command->value
        );

        $this->saveToCache(
            conversationId: $command->conversationId,
            userId: $command->userId,
            value: $command->value
        );
    }

    private function saveToDb(int $conversationId, int $userId, int $value): void
    {
        $success = $this->connection
            ->createQueryBuilder()
            ->update('counter', 'c')
            ->set('c.value', 'c.value + :value')
            ->where('c.conversation_id = :conversationId')
            ->andWhere('c.user_id = :userId')
            ->setParameter('conversationId', $conversationId)
            ->setParameter('userId', $userId)
            ->setParameter('value', $value)
            ->executeStatement();

        if (!$success) {
            $this->connection
                ->createQueryBuilder()
                ->insert('counter')
                ->values([
                    'conversation_id' => $conversationId,
                    'user_id' => $userId,
                    'value' => $value,
                ])
                ->executeQuery();
        }
    }

    private function saveToCache(int $conversationId, int $userId, int $value): void
    {
        echo 'key: ' . $this->counterHelper->getKeyId($conversationId, $userId);

        $this->cacher->increase(
            key: $this->counterHelper->getKeyId($conversationId, $userId),
            value: $value
        );
    }
}
