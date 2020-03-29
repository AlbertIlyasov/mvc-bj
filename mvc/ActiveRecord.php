<?php

namespace MVC;

use MVC\Exceptions\NotFoundRecordException;

class ActiveRecord
{
    private $idFieldName = 'id';
    private $id;
    private $table;

    public function __construct(?int $id = null)
    {
        if ($id) {
            $this->load($id);
        }
    }

    private function load($id): void
    {
        $sql = sprintf(
            'SELECT * FROM %s WHERE `%s` = %s',
            $this->getTable(),
            $this->getIdFieldName(),
            $id
        );
        $row = App::get()->getDb()->query($sql)->fetch();
        if (empty($row[$this->getIdFieldName()])) {
            throw new NotFoundRecordException('Not found record with ID = ' . $id);
        }
        $this->setId($row[$this->getIdFieldName()]);
        foreach ($this->getValues() as $field => $value) {
            $this->{$field} = $row[$field];
        }
    }

    public function getTable(): string
    {
        if ($this->table) {
            return $this->table;
        }
        preg_match('/([^\\\]+)$/', get_class($this), $b);
        return $this->table = strtolower($b[1]);
    }

    public function getIdFieldName(): ?string
    {
        return $this->idFieldName;
    }

    public function getId(): ?int
    {
        return $this->{$this->getIdFieldName()};
    }

    public function setId(int $id): self
    {
        $this->{$this->getIdFieldName()} = $id;
        return $this;
    }

    public function isNew(): bool
    {
        return !$this->getId();
    }

    public function save(): bool
    {
        if ($this->getId()) {
            return $this->update();
        }
        return $this->insert();
    }

    private function getValues(): array
    {
        $fields = get_object_vars($this);
        $fields[$this->getIdFieldName()] = $this->getId();
        unset($fields['table']);
        unset($fields['idFieldName']);
        return $fields;
    }

    private function insert(): bool
    {
        $sqlFields = [];
        $sqlValues = [];
        foreach ($this->getValues() as $field => $value) {
            $sqlFields[] = sprintf('`%s`', $field);
            $sqlValues[':' . $field] = $value ?? '';
        }
        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $this->getTable(),
            implode(', ', $sqlFields),
            implode(', ', array_keys($sqlValues))
        );

        $sth = App::get()->getDb()->prepare($sql);
        $sth->execute($sqlValues);
        $this->setId(App::get()->getDb()->lastInsertId());
        return $this->getId();
    }

    private function update(): bool
    {
        $sqlFields = [];
        $sqlValues = [];
        foreach ($this->getValues() as $field => $value) {
            $sqlFields[] = sprintf('`%s` = :%s', $field, $field);
            $sqlValues[':' . $field] = $value;
        }
        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE `%s` = :%s',
            $this->getTable(),
            implode(', ', $sqlFields),
            $this->getIdFieldName(),
            $this->getIdFieldName()
        );

        return App::get()->getDb()->prepare($sql)->execute($sqlValues);
    }
}
