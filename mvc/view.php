<?php

namespace MVC;

use MVC\Exceptions\UndefinedFormInputTypeException;

class View
{
    const TEMPLATES_PATH = 'views/templates/';

    private $config;
    private $request;
    private $url;
    private $html;
    private $templates = [
        'form' => [
            'begin' => 'theme-default/form/begin.html',
            'input' => 'theme-default/form/input.html',
            'input_error' => 'theme-default/form/input_error.html',
            'textarea' => 'theme-default/form/textarea.html',
            'select' => 'theme-default/form/select.html',
            'submitButton' => 'theme-default/form/submitButton.html',
            'end' => 'theme-default/form/end.html',
        ],
    ];

    public function __construct(
        array $config,
        \MVC\Request $request,
        \MVC\Url $url,
        \MVC\Html $html
    ) {
        $this->config  = $config;
        $this->request = $request;
        $this->url     = $url;
        $this->html    = $html;
    }

    public function render(string $templateFile, array $options = []): void
    {
        extract($options);
        ob_start();
        require_once $templateFile;
        $content = ob_get_contents();
        ob_end_clean();
        require_once $this->config['path'] . 'views/layouts/main.php';
    }

    public function buildGrid(array $data, array $columns = []): string
    {
        if (!$columns) {
            return '';
        }
        return '<table class="table table-striped">' . $this->buildHead($columns) . $this->buildBody($data, $columns) . '</table>';
    }

    private function buildHead(array $columns): string
    {
        $head = '';
        foreach ($columns as $column) {
            if (empty($column['sort'])) {
                $head .= '<th scope="col">' . ($column['label'] ?? $column['attr']) . '</th>';
                continue;
            }
            $hasSort = isset($this->request->sort[$column['attr']]);
            $desc = sprintf(
                '<a href="%s"%s>&darr;</a>',
                $this->url->build(['sort' => $column['attr'] . '_desc']),
                $hasSort && SORT_DESC == $this->request->sort[$column['attr']] ? ' class="sort_active"' : ''
            );
            $asc = sprintf(
                '<a href="%s"%s>&uarr;</a>',
                $this->url->build(['sort' => $column['attr'] . '_asc']),
                $hasSort && SORT_DESC != $this->request->sort[$column['attr']] ? ' class="sort_active"' : ''
            );
            $head .= '<th scope="col">' . ($column['label'] ?? $column['attr']) . $desc . $asc . '</th>';
        }
        return '<thead><tr>' . $head . '</tr></thead>';
    }

    private function buildBody(array $data, array $columns): string
    {
        if (!$data) {
            return '<tr><td colspan="'.count($columns).'">Пусто</td></tr>';
        }
        $body = '';
        foreach ($data as $row) {
            $cells = '';
            foreach ($columns as $column) {
                $cells .= '<td>' . $this->buildValue($column, $row) . '</td>';
            }
            $body .= '<tr>' . $cells . '</tr>';
        }
        return '<tbody>' . $body . '</tbody>';
    }

    private function buildValue(array $column, array $row): string
    {
        if (isset($column['value'])) {
            return $column['value']($row);
        }

        $value = $this->html->encode($row[$column['attr']]);
        if (empty($column['format'])) {
            return $value;
        }

        if ('bool' == $column['format']) {
            return '<input type="checkbox"' . ($value ? ' checked' : '') . ' readonly disabled>';
        }

        return $value;
    }

    public function buildForm(
        \MVC\Form $form,
        array $fields,
        string $submitLabel = 'Отправить'
    ): string {
        $inputs = '';
        foreach ($fields as $fieldData) {
            $field   = $fieldData[0];
            $type    = $fieldData[1] ?? 'text';
            $options = $fieldData[2] ?? [];
            $inputs .= $this->buildInput($field, $form, $type, $options);
        }
        return $this->buildBeginForm()
            . $inputs
            . $this->buildSubmitButton($submitLabel)
            . $this->buildEndForm();
    }

    public function buildBeginForm(): string
    {
        return $this->getTemplate('form/begin');
    }

    public function buildEndForm(): string
    {
        return $this->getTemplate('form/end');
    }

    public function buildSubmitButton(string $label): string
    {
        return str_replace('submitLabel', $label, $this->getTemplate('form/submitButton'));
    }

    public function buildInput(string $field, $form, string $type = 'text', array $options = []): string
    {
        $allowedTypes = [
            'text',
            'password',
            'email',
            'textarea',
            'select',
        ];
        if (!in_array($type, $allowedTypes)) {
            throw new UndefinedFormInputTypeException($type . ' - undefined input type');
        }

        $value = $form->$field;
        $encodedValue = $this->html->encode($value);
        if ('textarea' == $type) {
            $input = $this->getTemplate('form/textarea');
        } elseif ('select' == $type) {
            $input = $this->getTemplate('form/select');
        } else {
            $input = $this->getTemplate('form/input');
        }

        $input = str_replace('inputType', $type, $input);
        $input = str_replace('fieldLabel', $form->labels()[$field], $input);
        $input = str_replace('fieldName', $field, $input);
        $input = str_replace('fieldValue', $encodedValue, $input);
        $input = str_replace(
            ' tpl-attr-required',
            $form->isRequired($field) ? ' required' : '',
            $input
        );

        if ('select' == $type) {
            $input = $this->buildSelect($input, $value, $options);
        }

        $input = str_replace(
            ' tpl-css-error',
            $form->hasError($field) ? ' is-invalid' : '',
            $input
        );
        $input = str_replace(
            '<!--error-->',
            $form->hasError($field)
                ? str_replace(
                    'tpl-errorText',
                    $form->getErrorText($field),
                    $this->getTemplate('form/input_error')
                )
                : '',
            $input
        );

        return $input;
    }

    public function buildSelect(string $input, ?string $value, array $options): string
    {
        $optionStartText = '<option ';
        $optionEndText   = '</option>';
        $optionTpl = substr(
            $input,
            strpos($input, $optionStartText),
            strpos($input, $optionEndText)-strpos($input, $optionStartText)+strlen($optionEndText)
        );

        $optionsText = '';
        foreach ($options as $optionValue => $label) {
            $option = $optionTpl;
            $option = str_replace(
                ' tpl-attr-selected',
                $value == $optionValue ? ' selected' : '',
                $option
            );
            $option = str_replace('optionValue', $this->html->encode($optionValue), $option);
            $option = str_replace('optionLabel', $this->html->encode($label), $option);

            $optionsText .= $option . PHP_EOL;
        }

        return str_replace($optionTpl, $optionsText, $input);
    }

    private function getTemplate(string $templatePath): string
    {
        return file_get_contents($this->getTemplateFilename($templatePath));
    }

    private function getTemplateFilename(string $templatePath): string
    {
        $path = explode('/', $templatePath);
        $filename = $this->templates;
        foreach ($path as $key) {
            $filename = $filename[$key];
        }
        return $this->config['path'] . static::TEMPLATES_PATH . $filename;
    }
}
