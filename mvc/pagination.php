<?php

namespace MVC;

use MVC\Exceptions\NotFoundPagePaginationException;

class Pagination
{
    private $config;
    private $request;
    private $count;
    private $pages;
    private $url;

    public function __construct(
        array $config,
        RequestInterface $request,
        int $count,
        Url $url
    ) {
        $this->config  = $config;
        $this->request = $request;
        $this->count   = $count;
        $this->url   = $url;
    }

    public function validate(array $data = []): bool
    {
        $limit = $this->config['gridSize'];
        $this->pages = ceil($this->count/$limit) ?: 1;
        return $this->request->page <= $this->pages && $this->request->page >= 1;
    }

    public function render(): string
    {
        if (1 == $this->pages) {
            return '';
        }

        $page = $this->request->page;
        $pageLinks = '';
        for ($i=1; $i<=$this->pages; $i++) {
            if ($page != $i) {
                $pageLinks .= sprintf(
                    '<li class="page-item"><a class="page-link" href="%s">%d</a></li>',
                    $this->url->build(['page' => $i]),
                    $i
                );
                continue;
            }
            $pageLinks .= sprintf(
                '<li class="page-item active">'
                    . '<a class="page-link" href="%s">%d <span class="sr-only">(current)</span></a>'
                . '</li>',
                $this->url->build(['page' => $i]),
                $i
            );
        }

        return '<nav aria-label="Page navigation example">'
                .'<ul class="pagination">'
                    . $this->renderPreviousButton()
                    . $pageLinks
                    . $this->renderNextButton()
                .'</ul>'
            .'</nav>';
    }

    public function renderPreviousButton(): string
    {
        $page = $this->request->page;
        return sprintf('
            <li class="page-item%s">
              <a class="page-link" href="%s" aria-label="Назад">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Назад</span>
              </a>
            </li>',
            $page > 1 ? '' : ' disabled',
            $this->url->build(['page' => $page-1])
        );
    }

    public function renderNextButton(): string
    {
        $page = $this->request->page;
        return sprintf('
            <li class="page-item%s">
              <a class="page-link" href="%s" aria-label="Далее">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Далее</span>
              </a>
            </li>',
            $page < $this->pages ? '' : ' disabled',
            $this->url->build(['page' => $page+1])
        );
    }
}
