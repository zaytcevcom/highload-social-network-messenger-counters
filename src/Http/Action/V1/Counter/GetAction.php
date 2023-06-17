<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Counter;

use App\Counters\Query\GetByConversationIds\GetByConversationIdsFetcher;
use App\Counters\Query\GetByConversationIds\GetByConversationIdsQuery;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZayMedia\Shared\Components\Serializer\Denormalizer;
use ZayMedia\Shared\Components\Validator\Validator;
use ZayMedia\Shared\Helpers\OpenApi\ResponseSuccessful;
use ZayMedia\Shared\Helpers\OpenApi\Security;
use ZayMedia\Shared\Http\Middleware\Identity\Authenticate;
use ZayMedia\Shared\Http\Response\JsonDataResponse;

#[OA\Get(
    path: '/counters',
    description: 'Получение счетчика новых сообщений в указанных беседах',
    summary: 'Получение счетчика новых сообщений в указанных беседах',
    security: [Security::BEARER_AUTH],
    tags: ['Counters'],
    responses: [new ResponseSuccessful()]
)]
#[OA\Parameter(
    name: 'ids',
    description: 'Идентификаторы бесед (через запятую)',
    in: 'query',
    required: true,
    schema: new OA\Schema(
        type: 'string',
    ),
    example: '1,2,3'
)]
final class GetAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Denormalizer $denormalizer,
        private readonly GetByConversationIdsFetcher $fetcher,
        private readonly Validator $validator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::getIdentity($request);

        $query = $this->denormalizer->denormalizeQuery(
            data: array_merge(
                $request->getQueryParams(),
                ['userId' => $identity->id],
            ),
            type: GetByConversationIdsQuery::class
        );

        $this->validator->validate($query);

        $result = $this->fetcher->fetch($query);

        return new JsonDataResponse(
            $result
        );
    }
}
