# Changelog

All Notable changes to `atomescrochus/laravel-gracenote` will be documented in this file following SemVer.

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
