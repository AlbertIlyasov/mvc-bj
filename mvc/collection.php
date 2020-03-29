<?php

namespace MVC;

class Collection
{
    private $table;
    private $sort;
    private $limit;
    private $offset;

    public function getTable(): string
    {
        if ($this->table) {
            return $this->table;
        }
        preg_match('/([^\\\]+)Collection$/', get_class($this), $b);
        return $this->table = strtolower($b[1]);
    }

    public function setLimit(int $limit, ?int $page = null): self
    {
        $this->limit  = $limit;
        $this->offset = (($page < 2 ? 1 : $page)-1) * $limit;
        return $this;
    }

    public function setSort(string $field, ?int $direction = SORT_DESC): self
    {
        $this->sort[$field] = SORT_DESC == $direction ? SORT_DESC : SORT_ASC;
        return $this;
    }

    public function all(): array
    {
        $sql = sprintf(
            'SELECT * FROM %s %s %s %s',
            $this->getTable(),
            $this->buildWhere(),
            $this->buildSort(),
            $this->buildLimit()
        );
        $rows = [];
        foreach (App::get()->getDb()->query($sql) as $row) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function count(): int
    {
        $sql = sprintf(
            'SELECT COUNT(*) as count FROM %s %s %s',
            $this->getTable(),
            $this->buildWhere(),
            $this->buildSort()
        );
        return $this->count = App::get()->getDb()->query($sql)->fetch()['count'];
    }

    public function buildWhere(): string
    {
        return '';
    }

    public function buildSort(): string
    {
        if (empty($this->sort)) {
            return '';
        }
        $sql = '';
        foreach ($this->sort as $field => $direction) {
            $sql .= sprintf('`%s` %s', $field, SORT_DESC == $direction ? 'desc' : '');
        }
        return 'ORDER BY ' . $sql;
    }

    public function buildLimit(): string
    {
        if (!$this->limit) {
            return '';
        }
        $page = $this->offset ? [$this->offset, $this->limit] : [$this->limit];
        return 'LIMIT ' . implode(', ', $page);
    }
}
