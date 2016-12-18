# Interact with the Gracenote Web API

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

The `atomescrochys/laravel-gracenote` package provide and easy way to interact with the Gracenote Web API from any Laravel 5.3 application.


## Install

You can install this package via composer:

``` bash
$ composer require atomescrochus/laravel-gracenote
```

Then you have to install the package' service provider:

```php
// config/app.php
'providers' => [
    ...
    Atomescrochus\Gracenote\GracenoteServiceProvider::class,
];
```

You will have to publish the configuration files also:
```bash
php artisan vendor:publish --provider="Atomescrochus\Gracenote\GracenoteServiceProvider" --tag="config"
```

You should check the published config file for values to add to your environment file.

## Usage

``` php
$skeleton = new League\Skeleton();
echo $skeleton->echoPhrase('Hello, League!');
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email jp@atomescroch.us instead of using the issue tracker.

## Credits

- [Jean-Philippe Murray][https://github.com/jpmurray]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
