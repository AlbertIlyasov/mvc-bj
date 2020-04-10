<?php

namespace Custom\Controllers;

use Custom\Models\Task;
use Custom\Models\TaskCollection;
use Custom\Models\TaskForm;
use MVC\DataProvider;
use MVC\Exceptions\NotFoundRecordException;

class IndexController extends \MVC\Controllers\Controller
{
    public function IndexAction()
    {
        $dataProvider = new DataProvider(new TaskCollection);
        if (!$dataProvider->pagination->validate()) {
            return $this->NotFoundAction();
        }
        $this->render('list', [
            'data'    => $dataProvider->getData(),
            'isAdmin' => $this->session->isAdmin(),
        ]);
    }

    public function AddAction()
    {
        try {
            $id = $this->request->get('id');
            if ($id && !$this->session->isAdmin()) {
                return $this->response->redirect($this->url->build(['auth/login']));
            }
            $isNew = !$id;
            $task = new Task($id);
            $form = new TaskForm;
            if (!$this->request->isPost()) {
                $form->username = $task->username;
                $form->email    = $task->email;
                $form->text     = $task->text;
                $form->status   = $task->status;

                return $this->render('form', [
                    'form'  => $form,
                    'isNew' => $isNew,
                ]);
            }

            $form->username = $this->request->post('username');
            $form->email    = $this->request->post('email');
            $form->text     = $this->request->post('text');
            $form->status   = $this->request->post('status');
            $result = null;
            if ($form->validate()) {
                $task->username  = $form->username;
                $task->email     = $form->email;
                $task->text      = $form->text;
                $task->isChanged = !$task->isNew();
                $task->status    = $form->status;
                $result = $task->save();
                //@todo redirect with flash
            }
            $this->render('form', [
                'form'   => $form,
                'isNew'  => $isNew,
                'result' => $result,
            ]);
        } catch (NotFoundRecordException $e) {
            $this->NotFoundAction();
        }
    }
}
