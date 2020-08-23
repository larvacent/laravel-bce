# laravel-bce

This is a baidu cloud expansion for the laravel

[![License](https://poser.pugx.org/larva/laravel-bce/license.svg)](https://packagist.org/packages/larva/laravel-bce)
[![Latest Stable Version](https://poser.pugx.org/larva/laravel-bce/v/stable.png)](https://packagist.org/packages/larva/laravel-bce)
[![Total Downloads](https://poser.pugx.org/larva/laravel-bce/downloads.png)](https://packagist.org/packages/larva/laravel-bce)

## 接口支持
- CDN
- 自然语言处理 AIP
- SMS

## 环境需求

- PHP >= 5.6

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
	$cdn = Bce::get('cdn');
	
} catch (\Exception $e) {
	print_r($e->getMessage());
}
```