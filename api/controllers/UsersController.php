<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Phalcon\Http\Response;
use Kanvas\Sdk\Kanvas;
use Kanvas\Sdk\Auth;
use Kanvas\Sdk\Users;
use Canvas\Http\Exception\InternalServerErrorException;


/**
 * Class UsersController.
 *
 * @package Gewaer\Api\Controllers
 *
 * @property Redis $redis
 * @property Beanstalk $queue
 * @property Mysql $db
 * @property \Monolog\Logger $log
 */
class UsersController extends BaseController
{
    /**
     * Constructor.
     *
     * @return void
     */
    public function onConstruct()
    {

    }

    /**
     * Index.
     *
     * @method GET
     * @url /
     *
     * @return Response
     */
    public function index($id = null) : Response
    {
        return $this->response(Users::all());
    }

    /**
     * Get User.
     *
     * @param mixed $id
     *
     * @method GET
     * @url /v1/users/{id}
     *
     * @return Response
     */
    public function getById($id) : Response
    {
        $user = Users::retrieve($id);

        return $this->response($user);
    }

    /**
     * Create new User.
     *
     * @method GET
     * @url /status
     *
     * @return Response
     */
    public function create() : Response
    {
        $request = $this->request->getPostData();

        $user = Users::create($request);
        return $this->response($user);
    }

    /**
     * Update User.
     *
     * @method GET
     * @url /status
     *
     * @return Response
     */
    public function edit($id) : Response
    {
        $request = $this->request->getPutData();

        $user = Users::update($id, $request);
        return $this->response($user);
    }

    /**
     * Update User.
     *
     * @method GET
     * @url /status
     *
     * @return Response
     */
    public function delete($id) : Response
    {
        if ((int) $this->userData->getId() === (int) $id) {
            throw new InternalServerErrorException(
                'Cant delete your own user . If you want to close your account contact support or go to app settings'
            );
        }
        
        $user = Users::delete($id);
        return $this->response($user);
    }
}