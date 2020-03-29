<?php

namespace MVC\Controllers;

use MVC\App;

class Controller
{
    protected $config;
    protected $request;
    protected $response;
    protected $url;
    protected $view;
    protected $session;

    public function __construct(
        array $config,
        \MVC\Request $request,
        \MVC\Response $response,
        \MVC\Url $url,
        \MVC\View $view,
        \MVC\Session $session
    ) {
        $this->config   = $config;
        $this->request  = $request;
        $this->response = $response;
        $this->url      = $url;
        $this->view     = $view;
        $this->session  = $session;
    }

    protected function render(string $templateName, array $options = []): void
    {
        $templateFile = $this->config['path'] . 'views/'
            . strtolower($this->request->controller) . '/'
            . $templateName . '.php';
        $this->view->render($templateFile, $options);
    }

    public function NotFoundAction()
    {
        return (new ErrorsController(
            $this->config,
            $this->request,
            $this->response,
            $this->url,
            $this->view,
            $this->session
        ))->NotFoundAction();
    }
}
