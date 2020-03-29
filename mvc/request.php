<?php

namespace MVC;

use Exception;

class Request implements RequestInterface
{
    use Traits\MagicGetterTrait;

    private $module;
    private $controller;
    private $action;
    private $query;
    private $post;
    private $sort;
    private $page;

    public function __construct()
    {
        $this->query = $_GET;
        $this->post  = $_POST ?? [];
        $this->parseRoute();
        $this->parseSort();
        $this->parsePage();
        $this->magicAvailableNames = [
                'module',
                'controller',
                'action',
                'query',
                'sort',
                'page',
            ];
    }

    private function parseRoute(): void
    {
        if (empty($this->query['r'])) {
            $this->module     = 'Custom';
            $this->controller = 'Index';
            $this->action     = 'Index';
            return;
        }

        $path = explode('/', $_GET['r']);
        if (3 == count($path)) {
            $this->module     = $path[0];
            $this->controller = $path[1];
            $this->action     = $path[2];
        } elseif (2 == count($path)) {
            $this->module     = 'Custom';
            $this->controller = $path[0];
            $this->action     = $path[1];
        }
    }

    private function parseSort(): void
    {
        $this->sort = [];
        if (empty($this->query['sort'])) {
            return;
        }
        $sort = explode('_', $this->query['sort']);
        if (2 != count($sort)) {
            return;
        }
        $direction = 'desc' == array_pop($sort) ? SORT_DESC : SORT_ASC;
        $this->sort = [implode('_', $sort) => $direction];
    }

    private function parsePage(): void
    {
        $this->page = (int) ($this->query['page'] ?? 1);
    }

    public function get(string $name)
    {
        return $this->query[$name] ?? null;
    }

    public function post(string $name)
    {
        return $this->post[$name] ?? null;
    }

    public function isPost()
    {
        return 'POST' == $_SERVER['REQUEST_METHOD'];
    }
}
