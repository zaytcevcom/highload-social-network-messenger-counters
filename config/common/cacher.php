<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use ZayMedia\Shared\Components\Cacher\Cacher;
use ZayMedia\Shared\Components\Cacher\RedisCacher;

use function App\Components\env;

return [
    Cacher::class => static function (ContainerInterface $container): RedisCacher {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{
         *     host:string,
         *     port:integer,
         *     password:string
         * } $config
         */
        $config = $container->get('config')['cacher-redis'];

        return new RedisCacher(
            host: $config['host'],
            port: $config['port'],
            password: $config['password']
        );
    },

    'config' => [
        'cacher-redis' => [
            'host' => env('REDIS_HOST'),
            'port' => (int)env('REDIS_PORT'),
            'password' => env('REDIS_PASSWORD'),
        ],
    ],
];
