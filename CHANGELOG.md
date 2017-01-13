# Changelog

All Notable changes to `atomescrochus/laravel-gracenote` will be documented in this file following SemVer.

# 1.3.4 - 2017-01-13
## Added
- Optimizing cache usage vs API request

# 1.3.3 - 2017-01-13
## Added
- Named object is md5-ed

## 1.3.2 - 2017-01-12
### Added
- Cache names are now hashed

## 1.3.1 - 2017-01-02

### Added
- Results now include a `status` field containing the status text returned by Gracenote Web API.

## 1.3.0 - 2017-01-01

A.K.A the I have not life, so I'm not hungover release.

### Added
- Possibility to search by ID with `getTrackById()`;

## 1.2.0 - 2016-12-13

### Added
- Possibility to set the search mode;
- Results now includes artists origin, type (sex) and era, when available and if search mode is set;
- Results now includes images (artist image and covertart), when available and if search mode is set;

## 1.1.1 - 2016-12-27

### Fixed
- Moved tests outside the `src` folder to the root of the package.

### Removed
- Support for PHP > `7.0`.

## 1.1.0 - 2016-12-27

### Added
- Note in comments for the artisan command to get user_id;
- Added exception for missing search terms before searching the API;
- Added exception for usage errors (wrong search type) while setting correspondent value;
- Added some tests.

## 1.0.0 - 2016-12-19

### Added
- Method to format the results of a search by track title.
- Method to format the results of a search by album title.
- Method to format the results of a search by artist.
- Results returned by a search includes raw results and a collection formatted results.
- Artisan command to get the Gracenote user id for the app.

### Fixed
- The cache key for a search item now includes the search type so same search terms on multiple type don't conflicts.

## 0.3.0 - 2016-12-19

### Added
- Results now gets cached for a default time of 60 minutes, but time can be changed via `cache()` method.

## 0.2.0 - 2016-12-19

### Added
- Added facade.

## 0.1.0 - 2016-12-18

### Added
- Layed the base of the package, we can now search for results in Gracenote via their WebAPI, see readme for the how to.
- Publishing to packagist for testing purpose.

## Template - YYYY-MM-DD

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing
