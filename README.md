# 

## Required modules

## Install

```bash
$ composer require --prefer-dist mirocow/yii2-elasticsearch
```

## Configure

```php
return [
    'components' => [

        // ...

        'ofd' => [
            'class' => \mirocow\ofd\api\OfdFermaApi::class,
            'ofdFermaApiUri' => 'https://ferma.ofd.ru',
            'login' => '',
            'password' => '',
        ],

        // ...

    ],
    'modules' => [

        // ...

        'ofd' => [
            'class' => \mirocow\ofd\Module::class,
            'layout' => '//ubold',
        ],

        // ...

    ]
];
```

## Migrations

```bash
$ php ./yii migrate/up --interactive=0 --migrationPath=@mirocow/ofd/migrations
```