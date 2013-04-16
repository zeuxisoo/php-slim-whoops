## Installing

- Install the composer

```
curl -sS https://getcomposer.org/installer | php
```

- Edit composer.json

```
{
	"require": {
		"zeuxisoo/slim-whoops": "dev-master"
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

## Important Note

- Please make sure you already installed **whoops** library.
