# Interact with the Gracenote Web API

[![Latest Stable Version](https://poser.pugx.org/atomescrochus/laravel-gracenote/v/stable)](https://packagist.org/packages/atomescrochus/laravel-gracenote)
[![License](https://poser.pugx.org/atomescrochus/laravel-gracenote/license)](https://packagist.org/packages/atomescrochus/laravel-gracenote)
[![StyleCI](https://styleci.io/repos/76792572/shield?branch=master)](https://styleci.io/repos/76792572)
[![Total Downloads](https://poser.pugx.org/atomescrochus/laravel-gracenote/downloads)](https://packagist.org/packages/atomescrochus/laravel-gracenote)

WIP: not ready for production yet.

The `atomescrochys/laravel-gracenote` package provide and easy way to interact with the Gracenote Web API from any Laravel 5.3 application.

## Install

You can install this package via composer:

``` bash
$ composer require atomescrochus/laravel-gracenote
```

Then you have to install the package' service provider and aliase:

```php
// config/app.php
'providers' => [
    ...
    Atomescrochus\Gracenote\GracenoteServiceProvider::class,
];

'aliases' => [
	....
    'GracenoteAPI' => Atomescrochus\Gracenote\Facades\Gracenote::class,
];
```

You will have to publish the configuration files also:
```bash
php artisan vendor:publish --provider="Atomescrochus\Gracenote\GracenoteServiceProvider" --tag="config"
```

You should check the published config file for values to add to your environment file.

## Usage

``` php
// $results will be a collection of the results
$results = GracenoteAPI::lang('eng')
    ->searchType('track_title')
    ->query('Poker face')
    ->search();
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email jp@atomescroch.us instead of using the issue tracker.

## Credits

- [Jean-Philippe Murray][https://github.com/jpmurray]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
