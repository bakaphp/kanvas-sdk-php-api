<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Kanvas\Sdk\Kanvas;
use Kanvas\Sdk\Auth;
use Kanvas\Sdk\Users;
use Phalcon\Http\Response;

/**
 * Class AuthController.
 *
 * @package Gewaer\Api\Controllers
 *
 * @property Users $userData
 * @property Request $request
 * @property Config $config
 * @property \Baka\Mail\Message $mail
 * @property Apps $app
 */
class AuthController extends BaseController
{
    /**
     * User Login.
     * @method POST
     * @url /v1/auth
     *
     * @return Response
     */
    public function login() : Response
    {
        $request = $this->request->getPostData();
        $auth = Auth::auth($request);
        Kanvas::setAuthToken($auth->token);

        return $this->response($auth);
    }

    /**
     * User Signup.
     *
     * @method POST
     * @url /v1/users
     *
     * @return Response
     */
    public function signup() : Response
    {
        $request = $this->request->getPostData();
        $user = Users::create($request);
        return $this->response($user);
    }

    /**
     * Send email to change current email for user.
     * @param int $id
     * @return Response
     */
    public function sendEmailChange(int $id): Response
    {
        //Search for user
        $user = Users::getById($id);
        if (!is_object($user)) {
            throw new NotFoundHttpException(_('User not found'));
        }
        //Send email
        $this->sendEmail($user, 'email-change');
        return $this->response($user);
    }

    /**
     * Change user's email.
     * @param string $hash
     * @return Response
     */
    public function changeUserEmail(string $hash): Response
    {
        $request = $this->request->getPostData();
        //Ok let validate user password
        $validation = new CanvasValidation();
        $validation->add('password', new PresenceOf(['message' => _('The password is required.')]));
        $validation->add('new_email', new EmailValidator(['message' => _('The email is not valid.')]));
        $validation->add(
            'password',
            new StringLength([
                'min' => 8,
                'messageMinimum' => _('Password is too short. Minimum 8 characters.'),
            ])
        );
        //validate this form for password
        $validation->setFilters('password', 'trim');
        $validation->setFilters('default_company', 'trim');
        $validation->validate($request);
        $newEmail = $validation->getValue('new_email');
        $password = $validation->getValue('password');
        //Search user by key
        $user = Users::getByUserActivationEmail($hash);
        if (!is_object($user)) {
            throw new NotFoundHttpException(_('User not found'));
        }
        $this->db->begin();
        $user->email = $newEmail;
        if (!$user->update()) {
            throw new ModelException((string)current($user->getMessages()));
        }
        if (!$userData = $this->loginUsers($user->email, $password)) {
            $this->db->rollback();
        }
        $this->db->commit();
        return $this->response($userData);
    }

    /**
     * Send the user how filled out the form to the specify email
     * a link to reset his password.
     *
     * @return Response
     */
    public function recover(): Response
    {
        $request = $this->request->getPostData();
        $validation = new CanvasValidation();
        $validation->add('email', new EmailValidator(['message' => _('The email is not valid.')]));
        $validation->validate($request);
        $email = $validation->getValue('email');
        $recoverUser = Users::getByEmail($email);
        $recoverUser->generateForgotHash();
        $recoverUser->notify(new ResetPassword($recoverUser));
        return $this->response(_('Check your email to recover your password'));
    }

    /**
     * Reset the user password.
     * @method PUT
     * @url /v1/reset
     *
     * @return Response
     */
    public function reset(string $key) : Response
    {
        //is the key empty or does it existe?
        if (empty($key) || !$userData = Users::findFirst(['user_activation_forgot = :key:', 'bind' => ['key' => $key]])) {
            throw new Exception(_('This Key to reset password doesn\'t exist'));
        }
        $request = $this->request->getPostData();
        // Get the new password and the verify
        $newPassword = trim($request['new_password']);
        $verifyPassword = trim($request['verify_password']);
        //Ok let validate user password
        PasswordValidation::validate($newPassword, $verifyPassword);
        // Has the password and set it
        $userData->resetPassword($newPassword);
        $userData->user_activation_forgot = '';
        $userData->updateOrFail();
        //log the user out of the site from all devices
        $session = new Sessions();
        $session->end($userData);
        $userData->notify(new PasswordUpdate($userData));
        return $this->response(_('Password Updated'));
    }
}
