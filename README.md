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

## Options

- Opening referenced files with your favorite editor or IDE

```
$app->config('whoops.editor', 'sublime');  // add this line
$app->add(new \Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware);
```

## Important Note

- Please make sure you already installed **whoops** library.

## Testing

- Run the test cases

```
php vendor/bin/codecept run
```

- Create the acceptance case

```
php vendor/bin/codecept generate:cept acceptance [case-name]
```
