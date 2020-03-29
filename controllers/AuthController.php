<?php

namespace Custom\Controllers;

use Custom\Models\User;
use Custom\Models\AuthForm;

class AuthController extends \MVC\Controllers\Controller
{
    public function LoginAction()
    {
        if ($this->session->isAdmin()) {
            return $this->response->redirect();
        }

        $form = new AuthForm;
        if (!$this->request->isPost()) {
            return $this->render('form', [
                'form' => $form,
            ]);
        }

        $user = new User;
        $result = null;
        $form->login  = $this->request->post('login');
        $form->passwd = $this->request->post('passwd');
        if ($form->validate()) {
            $result = $user->validate($form->login, $form->passwd);
        }
        if ($result) {
            $user->login($form->login, $form->passwd);
        }

        $form->passwd = null;
        $this->render('form', [
            'form'   => $form,
            'result' => $result,
        ]);
    }

    public function LogoutAction()
    {
        if ($this->session->isAdmin()) {
            $this->session->logout();
        }
        return $this->response->redirect($this->url->build(['auth/login']));
    }
}
