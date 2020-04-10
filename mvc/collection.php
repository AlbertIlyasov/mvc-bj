<?php

namespace MVC;

class Collection
{
    private $table;
    private $sort;
    private $limit;
    private $offset;
    private $placeholders = [];
    private $fields;

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

    public function setSort(string $field, ?int $direction = SORT_ASC): bool
    {
        if (!$this->validateField($field)) {
            return false;
        }
        $this->sort[$field] = SORT_DESC == $direction ? 'desc' : '';
        return true;
    }

    private function validateField(string $field): bool
    {
        return in_array($field, $this->getFields());
    }

    private function getFields(): array
    {
        if (null !== $this->fields) {
            return $this->fields;
        }
        $this->fields = array_column(
            App::get()
                ->getDb()
                ->query(
                    sprintf('SHOW COLUMNS FROM `%s`', $this->getTable())
                )
                ->fetchAll(),
            'Field'
        );
        return $this->fields;
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
        $sth = App::get()->getDb()->prepare($sql);
        $sth->execute($this->buildPlaceholders());
        while ($row = $sth->fetch()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function count(): int
    {
        $sql = sprintf(
            'SELECT COUNT(*) as count FROM %s %s',
            $this->getTable(),
            $this->buildWhere()
        );
        return $this->count = App::get()->getDb()->query($sql)->fetch()['count'];
    }

    public function buildWhere(): string
    {
        return '';
    }

    private function addPlaceholder(string $name, ?string $value = null): self
    {
        $this->placeholders[$name] = $value ?? $name;
        return $this;
    }

    private function removePlaceholder(string $name): self
    {
        unset($this->placeholders[$name]);
        return $this;
    }

    private function buildPlaceholders(): array
    {
        $result = [];
        foreach ($this->placeholders as $name => $value) {
            $result[':' . $name] = $value;
        }
        return $result;
    }

    public function buildSort(): string
    {
        if (!$this->sort) {
            return '';
        }
        $sql = '';
        foreach ($this->sort as $field => $direction) {
            $sql .= sprintf('`%s` %s', $field, $direction);
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
