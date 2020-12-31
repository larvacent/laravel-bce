# laravel-bce

This is a baidu cloud expansion for the laravel

[![License](https://poser.pugx.org/larva/laravel-bce/license.svg)](https://packagist.org/packages/larva/laravel-bce)
[![Latest Stable Version](https://poser.pugx.org/larva/laravel-bce/v/stable.png)](https://packagist.org/packages/larva/laravel-bce)
[![Total Downloads](https://poser.pugx.org/larva/laravel-bce/downloads.png)](https://packagist.org/packages/larva/laravel-bce)

## 接口支持
- CDN
- 自然语言处理 NLP
- SMS

## 环境需求

- PHP >= 7.1

## Installation

```bash
composer require larva/laravel-bce
```

## for Laravel

This service provider must be registered.

```php
// config/app.php

'providers' => [
    '...',
    Larva\Baidu\Cloud\BceServiceProvider::class,
];
```


## Use

```php
try {
	$cdn = \Larva\Baidu\Cloud\Bce::get('cdn');
	
} catch (\Exception $e) {
	print_r($e->getMessage());
}

try {
	$nlp = \Larva\Baidu\Cloud\Bce::get('nlp');
	$tags = $nlp->keywords('标题','内容');
    print_r($tags);
} catch (\Exception $e) {
	print_r($e->getMessage());
}
```