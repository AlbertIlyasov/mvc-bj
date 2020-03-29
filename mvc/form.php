<?php

namespace MVC;

use MVC\Traits\MagicGetterTrait;
use MVC\Traits\MagicSetterTrait;

class Form
{
    use MagicGetterTrait, MagicSetterTrait;

    protected $labels;
    protected $errors;
    protected $rules;
    protected $filters;

    public function __construct()
    {
        $this->filters = [];
        if (!$this->rules) {
            return;
        }
        foreach ($this->rules as $rule) {
            $fields = is_array($rule[0]) ? $rule[0] : [$rule[0]];
            $ruleName = $rule[1];
            $this->filters[$ruleName]['fields'] = array_merge(
                $this->filters[$ruleName]['fields'] ?? [],
                $fields
            );
        }

        foreach ($this->filters as &$rule) {
            $rule['fields'] = array_unique($rule['fields']);
            $rule['fields'] = array_values($rule['fields']);
        }
    }

    public function validate(): bool
    {
        foreach ($this->filters as $ruleName => $filter) {
            $this->validateRule($filter['fields'], $ruleName);
        }
        return !$this->hasErrors();
    }

    public function validateRule(array $fields, string $ruleName): self
    {
        if ('required' == $ruleName) {
            $this->validateRuleRequired($fields);
        } elseif ('email' == $ruleName) {
            $this->validateRuleEmail($fields);
        }
        return $this;
    }

    public function validateRuleRequired(array $fields): self
    {
        foreach ($fields as $field) {
            if ('' == $this->$field) {
                $this->errors[$field]['required'] = '';
            }
        }
        return $this;
    }

    public function validateRuleEmail(array $fields): self
    {
        foreach ($fields as $field) {
            if ($this->$field && !filter_var($this->$field, FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field]['email'] = '';
            }
        }
        return $this;
    }

    public function isRequired(string $field): bool
    {
        return isset($this->filters['required'])
            && in_array($field, $this->filters['required']['fields']);
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    public function getErrorTextRequired(string $field): string
    {
        if (isset($this->errors[$field]['required'])) {
            return $this->errors[$field]['required'] ?: 'Необходимо заполнить поле "' . $this->labels()[$field] . '"';
        } elseif (isset($this->errors[$field]['email'])) {
            return $this->errors[$field]['email'] ?: 'Укажите корректный e-mail';
        }
        return '';
    }

    public function getErrorText(string $field): string
    {
        return $this->getErrorTextRequired($field);
    }

    public function labels(): array
    {
        return $this->labels;
    }
}
