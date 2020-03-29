<?php

namespace MVC;

class DataProvider
{
    use Traits\MagicGetterTrait;

    private $collection;
    private $config;
    private $request;
    private $pagination;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
        $this->config     = App::get()->config;
        $this->request    = App::get()->request;
        $this->pagination = new Pagination($this->config, $this->request, $collection->count(), App::get()->url);
        $this->magicAvailableNames = [
            'pagination'
        ];
    }

    public function getData(): array
    {
        $this->collection->setLimit($this->config['gridSize'], $this->request->page);
        $sort = $this->request->sort;
        if ($sort) {
            [$field, $direction] = each($sort);
            $this->collection->setSort($field, $direction);
        }
        return $this->collection->all();
    }
}
