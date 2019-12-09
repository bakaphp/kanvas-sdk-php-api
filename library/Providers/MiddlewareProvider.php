<?php

declare(strict_types=1);

namespace Gewaer\Providers;

use Gewaer\Middleware\AuthenticationMiddleware;
use Canvas\Providers\MiddlewareProvider as CanvasMiddlewareProvider;

class MiddlewareProvider extends CanvasMiddlewareProvider
{
    protected $globalMiddlewares = [
        // Before the handler has been executed
    ];

    /**
     * This are the routes you have access to via the Baka routes components.
     *
     * This follows the order you specify on this array so
     *  - Token will run before Auth
     *
     * @var array
     */
    protected $routeMiddlewares = [
    ];

    /**
     * This are the routes you have access to via the Baka routes components.
     *
     * This follows the order you specify on this array so
     *  - Token will run before Auth
     *
     * @var array
     */
    protected $canvasRouteMiddlewares = [
        'auth.jwt' => AuthenticationMiddleware::class,
        //'auth.acl' => AclMiddleware::class,
        //'auth.subscription' => SubscriptionMiddleware::class
    ];
}
