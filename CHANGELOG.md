# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.4.0] - 2020-12-28
### Changed
- Support for PHP 8 and require Guzzle 7.

## [0.3.5] - 2020-06-04
### Changed
- Allow Symfony 5 dependency.

## [0.3.4] - 2019-04-05
### Fixed
- `getClaims` is now actually returning the claims instead of always an empty array.

## [0.3.3] - 2019-04-05
### Added
- Getters the private key and hash algorithm.

## [0.3.2] - 2019-04-05
### Added
- Getters for the `Client` constructor parameters to ease testing.

## [0.3.1] - 2018-07-17
### Changed
- Allow Symfony 4 dependency.

## [0.3.0] - 2018-05-31
### Fixed
- Set return type of the payload to `array`.

## [0.2.0] - 2018-05-30
### Changed
- Always return a `Response` object.

## [0.1.0] - 2018-05-29
### Added
- Initial implementation of JWT API.
