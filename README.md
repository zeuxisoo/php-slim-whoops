# Slim whoops

PHP whoops error on slim framework

## Status

[![Build Status](https://travis-ci.org/zeuxisoo/php-slim-whoops.svg?branch=0.7.x)](https://travis-ci.org/zeuxisoo/php-slim-whoops)
[![Coverage Status](https://coveralls.io/repos/github/zeuxisoo/php-slim-whoops/badge.svg)](https://coveralls.io/github/zeuxisoo/php-slim-whoops)
[![Downloads this Month](https://img.shields.io/packagist/dm/zeuxisoo/slim-whoops.svg)](https://packagist.org/packages/zeuxisoo/slim-whoops)
[![Latest Stable Version](https://poser.pugx.org/zeuxisoo/slim-whoops/v/stable)](https://github.com/zeuxisoo/php-slim-whoops/releases)

## Installation

Install the composer

    curl -sS https://getcomposer.org/installer | php

Edit `composer.json`

| Slim | Whoops    | Version | Global Mode | PHP DI |
| ---- | --------- | ------- | ----------- | ------ |
|   1  |  n/a      | 0.1.*   | no          | no     |
|   2  |  1.*      | 0.3.*   | no          | no     |
|   3  |  <= 1.*   | 0.4.*   | no          | no     |
|   3  |  >= 2.*   | 0.5.*   | no          | no     |
|   3  |  >= 2.*   | 0.6.*   | yes         | yes    |
|   4  |  >= 2.*   | 0.7.*   | no          | no     |

For `Slim framework 4`, The `composer.json` will looks like

    {
        "require": {
            "zeuxisoo/slim-whoops": "0.7.*"
        }
    }

Now, `install` or `update` the dependencies

	php composer.phar install

## Basic Usage

Add to middleware with default settings

    $app->add(new Zeuxisoo\Whoops\Slim\WhoopsMiddleware());

Or you can pass more settings to the `WhoopsMiddleware`

    $app->add(new Zeuxisoo\Whoops\Slim\WhoopsMiddleware([
        'enable' => true,
        'editor' => 'sublime',
        'title'  => 'Custom whoops page title',
    ]));

## Custom Handler Usage

In this usage, you can make your own handler for whoops, like:

	$simplyErrorHandler = function($exception, $inspector, $run) {
	    $message = $exception->getMessage();
	    $title   = $inspector->getExceptionName();

	    echo "{$title} -> {$message}";
	    exit;
	};

And then pass it to the `WhoopsMiddleware`:

	new Zeuxisoo\Whoops\Slim\WhoopsMiddleware([], [$simplyErrorHandler]);

## Important Note

Version `0.3.0` or above version

- The `whoops` library is installed by default base on the [Whoops Framework Integration Document][1]

Version `0.2.0`

- You must to install the `whoops` library manually.



[1]: https://github.com/filp/whoops/blob/master/docs/Framework%20Integration.md#contributing-an-integration-with-a-framework	"Whoops Framework Integration Document"
