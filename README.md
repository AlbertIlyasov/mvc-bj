# MVC-BJ
Example: http://mvc-bj.11121.ru/

## Create form
Model of form:
```php
class TaskForm extends \MVC\Form
{
    protected $username;
    protected $email;
    protected $descr;

    protected $labels = [
        'username' => 'Username',
        'email'    => 'E-mail',
        'descr'    => 'Description',
    ];

    protected $rules = [
        [['username','email','descr'], 'required'],
        ['email', 'email'],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->magicAvailableNames = [
            'username',
            'email',
            'descr',
        ];
        $this->magicSetterAvailableNames = [
            'username',
            'email',
            'descr',
        ];
    }
}
```

2 ways in view:
Full:
```php
    <?= $this->buildBeginForm() ?>
        <?= $this->buildInput('username', $form) ?>
        <?= $this->buildInput('email', $form, 'email') ?>
        <?= $this->buildInput('descr', $form, 'textarea') ?>
        <?= $this->buildSubmitButton('Сохранить') ?>
    <?= $this->buildEndForm() ?>
```
Short:
```php
<?= $this->buildForm($form, [['username'], ['email', 'email'], ['descr', 'textarea']], 'Сохранить') ?>
```

## Create collection
```php
class TaskCollection extends \MVC\Collection {}
```

## Grid
```php
<?
$dataProvider = new DataProvider(new TaskCollection);

$columns = [
    [
        'attr'  => 'username',
        'label' => TaskForm::USERNAME_LABEL,
        'sort'  => true,
        'value' => function($data) use ($isAdmin) {
            $username = $this->html->encode($data['username']);
            if (!$isAdmin) {
                return $username;
            }
            return sprintf(
                '<a href="%s">%s</span>',
                $this->url->build(['index/add', 'id' => $data['id']]),
                $username
            );
        },
    ],
    [
        'attr'  => 'email',
        'label' => TaskForm::EMAIL_LABEL,
        'sort'  => true,
    ],
    [
        'attr'  => 'descr',
        'label' => TaskForm::DESCR_LABEL,
    ],
];
?>


<?= $this->buildGrid($data, $columns) ?>
```
