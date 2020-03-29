<?php

namespace MVC;

use PDO;

class App
{
    use Traits\MagicGetterTrait;

    private static $instance;
    private $db;
    private $config;
    private $request;
    private $response;
    private $url;
    private $html;
    private $view;
    private $session;

    private function __construct()
    {
        $this->config = require __DIR__ . '/../config/config.php';
        $this->initDb();
        $this->request  = new Request;
        $this->response = new Response($this->config['domain']);
        $this->url      = new Url($this->request);
        $this->html     = new Html;
        $this->view     = new View($this->config, $this->request, $this->url, $this->html);
        $this->session  = new Session;
        $this->magicAvailableNames = [
            'config',
            'request',
            'url',
            'session',
        ];
        session_start();
    }

    public function initDb()
    {
        $db = require $this->config['path'] . 'config/db.php';
        $dsn = sprintf('%s:dbname=%s;host=%s', $db['driver'], $db['name'], $db['host']);
        $options = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
        $this->db = new PDO($dsn, $db['login'], $db['passwd'], $options);
    }

    public static function get(): self
    {
        if (!static::$instance) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    public function run()
    {
        $controllerClass = sprintf(
            '\\%s\\Controllers\\%sController',
            $this->request->module,
            $this->request->controller
        );
        if (!class_exists($controllerClass)) {
            (new Controllers\ErrorsController(
                $this->config,
                $this->request,
                $this->response,
                $this->url,
                $this->view,
                $this->session
            ))->NotFoundAction();
            die;
        }

        $controller = new $controllerClass(
            $this->config,
            $this->request,
            $this->response,
            $this->url,
            $this->view,
            $this->session
        );
        $actionMethod = $this->request->action . 'Action';
        if (!method_exists($controller, $actionMethod)) {
            $controller->NotFoundAction();
            die;
        }

        $controller->$actionMethod();
    }

    public function getDb(): PDO
    {
        return $this->db;
    }
}
