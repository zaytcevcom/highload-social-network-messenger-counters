<?php

declare(strict_types=1);

use App\Http\Action;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use ZayMedia\Shared\Components\Router\StaticRouteGroup as Group;

return static function (App $app): void {
    $app->group('/v1', new Group(static function (RouteCollectorProxy $group): void {
        $group->get('', Action\V1\OpenApiAction::class);

        $group->group('/counters', new Group(static function (RouteCollectorProxy $group): void {
            $group->get('', Action\V1\Counter\GetAction::class);
        }));
    }));
};
