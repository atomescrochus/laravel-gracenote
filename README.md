# Interact with the Gracenote Web API

[![Latest Stable Version](https://poser.pugx.org/atomescrochus/laravel-gracenote/v/stable)](https://packagist.org/packages/atomescrochus/laravel-gracenote)
[![License](https://poser.pugx.org/atomescrochus/laravel-gracenote/license)](https://packagist.org/packages/atomescrochus/laravel-gracenote)
[![StyleCI](https://styleci.io/repos/76792572/shield?branch=master)](https://styleci.io/repos/76792572)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/atomescrochus/laravel-gracenote/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/atomescrochus/laravel-gracenote/?branch=master)
[![Total Downloads](https://poser.pugx.org/atomescrochus/laravel-gracenote/downloads)](https://packagist.org/packages/atomescrochus/laravel-gracenote)

The `atomescrochus/laravel-gracenote` package provide and easy way to interact with the Gracenote Web API from any Laravel 5.3 application.

This package is usable in production, but should still be considered a work in progress (contribution welcomed!). It required PHP >= `7.0`.

## Install

You can install this package via composer:

``` bash
$ composer require atomescrochus/laravel-gracenote
```

Then you have to install the package' service provider and alias:

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

You will have to publish the configuration files also if you want to change the default value:
```bash
php artisan vendor:publish --provider="Atomescrochus\Gracenote\GracenoteServiceProvider" --tag="config"
```

You are also required to put the values you can fetch in your Gracenote developer account in your .env files:

```
GRACENOTE_CLIENT_ID=12345678
GRACENOTE_CLIENT_TAG=abcdefg12345678
GRACENOTE_USER_ID=wxyz-9876
```

**NOTE**: If you don't have your Gracenote user id, you can get it by setting the `GRACENOTE_CLIENT_ID` and `GRACENOTE_CLIENT_TAG` value, then run `php artisan gracenote:user-id`. This will run the appropriate API call to fetch your user id so you can add it to your .env file.

## Usage

``` php
// $results will be an object containing a collection of results and raw response data from Gracenote
$results = GracenoteAPI::lang('eng') // natural language of metadata
    ->cache(120) // integer representing minutes to cache results for
    ->searchType('track_title') //either 'track_title', 'album_title', or 'artist'
    ->query('Poker face') // the search query
    ->search(); // do some magic
```

## Tests

Some tests are provided. I don't think it's as extensive as it could be, but it shows expected behavior works well, assuming that Gracenote API is responding. The tests are also thought to be ran while the package is installed in Laravel, and not standalone. If anyone wants to improve on the current test, please do!

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email jp@atomescroch.us instead of using the issue tracker.

## Credits

- [Jean-Philippe Murray](https://github.com/jpmurray)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
