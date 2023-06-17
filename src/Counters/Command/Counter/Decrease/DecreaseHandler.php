<?php

declare(strict_types=1);

namespace App\Counters\Command\Counter\Decrease;

use App\Counters\Helper\CounterHelper;
use Doctrine\DBAL\Connection;
use ZayMedia\Shared\Components\Cacher\Cacher;

final class DecreaseHandler
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Cacher $cacher,
        private readonly CounterHelper $counterHelper,
    ) {
    }

    public function handle(DecreaseCommand $command): void
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
            ->set('c.value', 'c.value - :value')
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
                    'value' => -1 * $value,
                ])
                ->executeQuery();
        }
    }

    private function saveToCache(int $conversationId, int $userId, int $value): void
    {
        $this->cacher->decrease(
            key: $this->counterHelper->getKeyId($conversationId, $userId),
            value: $value
        );
    }
}
