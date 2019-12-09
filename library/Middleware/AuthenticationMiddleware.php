<?php

declare(strict_types=1);

namespace Gewaer\Middleware;

use Phalcon\Mvc\Micro;
use Baka\Auth\Models\Sessions;
use Kanvas\Sdk\Users;
use Canvas\Constants\Flags;
use Canvas\Http\Exception\UnauthorizedException;
use Kanvas\Sdk\Auth;
use Kanvas\Sdk\Kanvas;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class AuthenticationMiddleware.
 *
 * @package Niden\Middleware
 */
class AuthenticationMiddleware implements MiddlewareInterface
{
    /**
     * Call me.
     *
     * @param Micro $api
     * @todo need to check section for auth here
     * @return bool
     */
    public function call(Micro $api)
    {
        $config = $api->getService('config');
        $request = $api->getService('request');

        //cant be empty jwt
        if (empty($request->getBearerTokenFromHeader())) {
            throw new UnauthorizedException('Missing Token');
        }

        /**
         * This is where we will find if the user exists based on
         * the token passed using Bearer Authentication.
         */
        $token = $request->getBearerTokenFromHeader();
        Kanvas::setAuthToken($token);

        $api->getDI()->setShared(
            'userData',
            function () use ($config, $token, $request) {

                return Users::getSelf();
            }
        );

        return true;
    }
}
