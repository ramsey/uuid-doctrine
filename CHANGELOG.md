# ramsey/uuid-doctrine Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 1.8.0 - 2021-11-08

### Added

* Support doctrine/dbal v3
* Add `UuidType::requiresSQLCommentHint()` and `UuidType::getMappedDatabaseTypes()`
  to avoid unnecessary `ALTER TABLE` statements after each schema update.

## 1.7.0 - 2021-08-07

### Added

* Support ramsey/uuid v4
* Support PHP 8

### Changed

* Changed primary Doctrine dependency to doctrine/dbal and moved doctrine/orm
  dependency to `require-dev`.

## 1.6.0 - 2020-01-27

### Added

* Ensure the library supports the forthcoming ramsey/uuid version 4.0.0.

### Changed

* `UuidBinaryOrderedTimeType::getUuidFactory()` and
  `UuidBinaryOrderedTimeType::getCodec()` are now `protected` instead of
  `private`.
* Set minimum ramsey/uuid version to 3.5. This is required for use of the
  `OrderedTimeCodec` this library has supported since version 1.3.0.

### Fixed

* `UuidType::convertToPHPValue()` now checks for instances of `UuidInterface`
  instead of `Uuid`.
* When `UuidBinaryOrderedTimeType` fails to encode or decode a UUID because it
  is not a version 1 UUID, the `ConversionException` thrown now includes more
  information about the format expected ("UuidV1").

## 1.5.0 - 2018-08-11

### Added

* Check whether values are `UuidInterface` objects or strings, rather than
  specific `Uuid` objects. This allows more flexibility in supporting alternate
  types of UUID objects.

## 1.4.3 - 2017-11-07

### Fixed

* Revert `getBindingType()` ([#45](https://github.com/ramsey/uuid-doctrine/pull/45))
  work until a solution can be implemented for the "array to string conversion"
  notice reported in [#47](https://github.com/ramsey/uuid-doctrine/issues/47)

## 1.4.2 - 2017-11-06

### Fixed

* Add `getBindingType()` method to binary types ([#45](https://github.com/ramsey/uuid-doctrine/pull/45));
  this fixes issues where binary UUIDs were not treated properly by certain
  database clients when binding query parameters

## 1.4.1 - 2017-07-18

### Fixed

* Use global `UuidFactory` in `UuidOrderedTimeGenerator`; this provides the
  ability to configure the factory used ([#36](https://github.com/ramsey/uuid-doctrine/issues/36),
  [#37](https://github.com/ramsey/uuid-doctrine/pull/37))

## 1.4.0 - 2017-07-05

### Added

* Add generator for time-optimized UUIDs
  ([#33](https://github.com/ramsey/uuid-doctrine/pull/33))

### Removed

* Remove support for HHVM

## 1.3.0 - 2017-04-13

### Added

* Add `UuidBinaryOrderedTimeType` to store UUIDv1 in MySQL-optimized format
  ([#14](https://github.com/ramsey/uuid-doctrine/issues/14))

### Fixed

* Use `static::NAME` instead of `self::NAME`
  ([#25](https://github.com/ramsey/uuid-doctrine/issues/25))

## 1.2.0 - 2016-03-23

### Added

* Add UuidGenerator class to make Doctrine integration easier

## 1.1.0 - 2016-01-01

### Changed

* Now requiring the use of doctrine/dbal ^2.5 to support binary field types;
  this fixes a bug reported in [#7](https://github.com/ramsey/uuid-doctrine/issues/7)

## 1.0.1 - 2015-10-19

### Added

* Add project [Code of Conduct](https://github.com/ramsey/uuid-doctrine/blob/main/CODE_OF_CONDUCT.md)

### Fixed

* Loosen doctrine/dbal version requirement to ~2.3
* Fix converting UUID string to a binary UUID

## 1.0.0 - 2015-09-28

* Initial release!
* Separated from [ramsey/uuid](https://github.com/ramsey/uuid) library into a
  stand-alone package
