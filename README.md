Yii2 Tilda Api
==============
Tilda platform api extension for Yii2

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require globus/yii2-tilda-api:dev-master
```

or add

```
"globus/yii2-tilda-api": "*"
```
to the require section of your `composer.json` file.


Apply migrations

```
php yii migrate --migrationPath=../vendor/globus/yii2-tilda-api/migrations
```

Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
    'components' => [
         ...
         'tilda' => [
             'class' => 'globus\tilda\TildaApi',
             'publicKey' => '**********',
             'secretKey' => '**********',
             'assetsUrl' => Yii::getAlias('@storageUrl') . '/tilda',
             'assetsPath' => Yii::getAlias('@storage') . '/web/tilda',
         ],
     ],
```
Once the extension is installed, simply use it in your code by  :

```php
Yii::$app->tilda->getPage($pageID)
```