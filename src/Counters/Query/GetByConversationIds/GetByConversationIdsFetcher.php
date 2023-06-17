<?php

declare(strict_types=1);

namespace App\Counters\Query\GetByConversationIds;

use App\Counters\Helper\CounterHelper;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use ZayMedia\Shared\Components\Cacher\Cacher;

use function ZayMedia\Shared\Components\Functions\toArrayString;

final class GetByConversationIdsFetcher
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Cacher $cacher,
        private readonly CounterHelper $counterHelper,
    ) {
    }

    public function fetch(GetByConversationIdsQuery $query): array
    {
        $ids = toArrayString($query->ids);

        if (\count($ids) === 0) {
            return [];
        }

        if ($result = $this->getByCache($ids, $query->userId)) {
            return $result;
        }

        return $this->getByDb($ids, $query->userId);
    }

    /** @param string[] $ids */
    private function getByCache(array $ids, int $userId): ?array
    {
        $keys = [];

        foreach ($ids as $id) {
            $keys[] = $this->counterHelper->getKeyId((int)$id, $userId);
        }

        $counters = $this->cacher->mGet($keys);

        $result = [];

        foreach ($ids as $key => $id) {
            if (!$counters[$key]) {
                continue;
            }

            $result[] = [
                'id'    => (int)$id,
                'value' => (int)$counters[$key],
            ];
        }

        return $result;
    }

    /**
     * @param string[] $ids
     * @throws Exception
     */
    private function getByDb(array $ids, int $userId): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select(['conversation_id', 'value'])
            ->from('counter')
            ->where($queryBuilder->expr()->in('conversation_id', $ids))
            ->andWhere('user_id = :userId')
            ->setParameter('userId', $userId);

        /** @var array{conversation_id: int, value: int}[] $rows */
        $rows = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();

        $result = [];

        foreach ($rows as $row) {
            $result[] = [
                'id'    => $row['conversation_id'],
                'value' => $row['value'],
            ];
        }

        return $result;
    }
}
