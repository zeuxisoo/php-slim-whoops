## Status

[![Build Status](https://travis-ci.org/zeuxisoo/php-slim-whoops.png?branch=master)](https://travis-ci.org/zeuxisoo/php-slim-whoops)

## Installing

- Install the composer

```
curl -sS https://getcomposer.org/installer | php
```

- Edit composer.json

For Slim framework 3, Pease use the `0.4.0` or `0.5.0`

```
{
	"require": {
		"zeuxisoo/slim-whoops": "0.4.*" // for whoops <= 1.*
        "zeuxisoo/slim-whoops": "0.5.*" // for whoops >= 2.*
	}
}
```

For Slim framework 2, Please use the `0.3.0`.

```
{
    "require": {
        "zeuxisoo/slim-whoops": "0.3.*"
    }
}
```

Older version (without dependency injection support)

```
{
    "require": {
        "zeuxisoo/slim-whoops": "0.1.*"
    }
}
```

- Install/update your dependencies

```
php composer.phar install
```

## Usage

- add the middleware into slim application

```
$app->add(new \Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware);
```

## Options

- Opening referenced files with your favorite editor or IDE

```
$app = new App([
    'settings' => [
        'debug'         => true,
        'whoops.editor' => 'sublime' // Support click to open editor
    ]
]);
```

## Important Note

From `0.3.0`, the `whoops` library is installed by default base on the [Whoops Framework Integration Document](https://github.com/filp/whoops/blob/master/docs/Framework%20Integration.md#contributing-an-integration-with-a-framework)

If you are using the version `0.2.0`, you must to install the `whoops` library manually.

## Testing

- Run the test cases

```
php vendor/bin/phpunit
```
