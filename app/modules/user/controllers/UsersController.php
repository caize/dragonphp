<?php

/*
+------------------------------------------------------------------------+
| DragonPHP Website                                                      |
+------------------------------------------------------------------------+
| Copyright (c) 2016-2017 DragonPHP Team (https://www.dragonphp.com)      |
+------------------------------------------------------------------------+
| This source file is subject to the New BSD License that is bundled     |
| with this package in the file LICENSE.txt.                             |
|                                                                        |
| If you did not receive a copy of the license and are unable to         |
| obtain it through the world-wide-web, please send an email             |
| to license@dragonphp.com so we can send you a copy immediately.       |
+------------------------------------------------------------------------+
| Authors: Frank Kennedy Yuan <kideny@gmail.com>                     |
+------------------------------------------------------------------------+
*/

namespace DragonPHP\User\Controllers;

use Phalcon\Tag;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use DragonPHP\User\Models\Users;
use DragonPHP\User\Models\PasswordChanges;
use DragonPHP\User\Forms\UsersForm;
use DragonPHP\User\Forms\ChangePasswordForm;

/**
 * Vokuro\Controllers\UsersController
 * CRUD to manage users
 */
class UsersController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setTemplateBefore('private');
    }

    public function indexAction()
    {
        $this->persistent->conditions = null;

        $this->view->form = new UsersForm();
    }

    /**
     * Searches for users
     */
    public function searchAction()
    {
        $numberPage = 1;

        if ($this->request->isPost()) {

            $query = Criteria::fromInput($this->di, 'DragonPHP\User\Models\Users', $this->request->getPost());
            $this->persistent->searchParams = $query->getParams();

        } else {

            $numberPage = $this->request->getQuery("page", "int");

        }

        $parameters = [];

        if ($this->persistent->searchParams) {

            $parameters = $this->persistent->searchParams;

        }

        $users = Users::find($parameters);

        if (count($users) == 0) {

            $this->flash->notice("The search did not find any users");

            return $this->dispatcher->forward([
                "action" => "index"
            ]);
        }

        $paginator = new Paginator([
            "data" => $users,
            "limit" => 10,
            "page" => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Creates a User
     */
    public function createAction()
    {

        if ($this->request->isPost()) {

            if ($form->isValid($this->request->getPost()) == false) {

                foreach ($form->getMessages() as $message) {

                    $this->flash->error($message);
                }

            } else {

                $user = new Users([
                    'firstName' => $this->request->getPost('firstName', 'striptags'),
                    'lastName' => $this->request->getPost('lastName', 'striptags'),
                    'loginName' => $this->request->getPost('loginName', 'striptags'),
                    'email' => $this->request->getPost('email', 'email'),
                    'password' => $this->security->hash($this->request->getPost('password')),
                    'confirmPassword' => $this->security->hash($this->request->getPost('confirmPassword')),
                ]);

                if (!$user->save()) {

                    $this->flash->error($user->getMessages());

                } else {

                    $this->flash->success("User was created successfully");

                    Tag::resetInput();

                }
            }
        }

        $this->view->form = $form;
    }

    /**
     * Saves the user from the 'edit' action
     */
    public function editAction($id)
    {
        $user = Users::findFirstById($id);

        if (!$user) {
            $this->flash->error("User was not found");
            return $this->dispatcher->forward([
                        'action' => 'index'
            ]);
        }
        if ($this->request->isPost()) {
            $user->assign([
                'name' => $this->request->getPost('name', 'striptags'),
                'profilesId' => $this->request->getPost('profilesId', 'int'),
                'email' => $this->request->getPost('email', 'email'),
                'banned' => $this->request->getPost('banned'),
                'suspended' => $this->request->getPost('suspended'),
                'active' => $this->request->getPost('active')
            ]);
            $form = new UsersForm($user, [
                'edit' => true
            ]);
            if ($form->isValid($this->request->getPost()) == false) {

                foreach ($form->getMessages() as $message) {
                    $this->flash->error($message);
                }

            } else {
                if (!$user->save()) {
                    $this->flash->error($user->getMessages());
                } else {
                    $this->flash->success("User was updated successfully");
                    Tag::resetInput();
                }
            }
        }

        $this->view->user = $user;

        $this->view->form = new UsersForm($user, [
            'edit' => true
        ]);
    }

    /**
     * Deletes a User
     *
     * @param int $id
     */
    public function deleteAction($id)
    {
        $user = Users::findFirstById($id);

        if (!$user) {

            $this->flash->error("User was not found");

            return $this->dispatcher->forward([
                'action' => 'index'
            ]);
        }
        if (!$user->delete()) {

            $this->flash->error($user->getMessages());

        } else {

            $this->flash->success("User was deleted");

        }

        return $this->dispatcher->forward([
            'action' => 'index'
        ]);
    }

    public function changePasswordAction()
    {
        $form = new ChangePasswordForm();

        if ($this->request->isPost()) {

            if (!$form->isValid($this->request->getPost())) {

                foreach ($form->getMessages() as $message) {

                    $this->flash->error($message);

                }

            } else {

                $user = $this->auth->getUser();

                $user->password = $this->security->hash($this->request->getPost('password'));

                $user->mustChangePassword = 'N';

                $passwordChange = new PasswordChanges();

                $passwordChange->user = $user;

                $passwordChange->ipAddress = $this->request->getClientAddress();

                $passwordChange->userAgent = $this->request->getUserAgent();

                if (!$passwordChange->save()) {

                    $this->flash->error($passwordChange->getMessages());

                } else {

                    $this->flash->success('Your password was successfully changed');

                    Tag::resetInput();

                }
            }
        }
        $this->view->form = $form;
    }

}