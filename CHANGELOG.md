# Ramsey\Uuid\Doctrine Changelog

## 1.6.0

_Released: 2020-01-27_

* `UuidType::convertToPHPValue()` now checks for instances of `UuidInterface`
  instead of `Uuid`.
* When `UuidBinaryOrderedTimeType` fails to encode or decode a UUID because it
  is not a version 1 UUID, the `ConversionException` thrown now includes more
  information about the format expected ("UuidV1").
* `UuidBinaryOrderedTimeType::getUuidFactory()` and
  `UuidBinaryOrderedTimeType::getCodec()` are now `protected` instead of
  `private`.
* Set minimum ramsey/uuid version to 3.5. This is required for use of the
  `OrderedTimeCodec` this library has supported since version 1.3.0.
* Ensure the library supports the forthcoming ramsey/uuid version 4.0.0.

## 1.5.0

_Released: 2018-08-11_

* Check whether values are `UuidInterface` objects or strings, rather than specific `Uuid` objects. This allows more flexibility in supporting alternate types of UUID objects.

## 1.4.3

_Released: 2017-11-07_

* Revert `getBindingType()` ([#45](https://github.com/ramsey/uuid-doctrine/pull/45)) work until a solution can be implemented for the "array to string conversion" notice reported in [#47](https://github.com/ramsey/uuid-doctrine/issues/47)

## 1.4.2

_Released: 2017-11-06_

* Add `getBindingType()` method to binary types ([#45](https://github.com/ramsey/uuid-doctrine/pull/45)); this fixes issues where binary UUIDs were not treated properly by certain database clients when binding query parameters

## 1.4.1

_Released: 2017-07-18_

* Use global `UuidFactory` in `UuidOrderedTimeGenerator`; this provides the ability to configure the factory used ([#36](https://github.com/ramsey/uuid-doctrine/issues/36), [#37](https://github.com/ramsey/uuid-doctrine/pull/37))

## 1.4.0

_Released: 2017-07-05_

* Add generator for time-optimized UUIDs ([#33](https://github.com/ramsey/uuid-doctrine/pull/33))
* Remove HHVM testing on Travis CI

## 1.3.0

_Released: 2017-04-13_

* Add `UuidBinaryOrderedTimeType` to store UUIDv1 in MySQL-optimized format ([#14](https://github.com/ramsey/uuid-doctrine/issues/14))
* Use `static::NAME` instead of `self::NAME` ([#25](https://github.com/ramsey/uuid-doctrine/issues/25))
* Various documentation updates

## 1.2.0

_Released: 2016-03-23_

* Add UuidGenerator class to make Doctrine integration easier

## 1.1.0

_Released: 2016-01-01_

* Now requiring the use of doctrine/dbal ^2.5 to support binary field types; this fixes a bug reported in [#7](https://github.com/ramsey/uuid-doctrine/issues/7)

## 1.0.1

_Released: 2015-10-19_

* Loosen doctrine/dbal version requirement to ~2.3
* Fix converting UUID string to a binary UUID
* Add project [Code of Conduct](https://github.com/ramsey/uuid-doctrine/blob/master/CONDUCT.md)

## 1.0.0

_Released: 2015-09-28_

* Separated from [ramsey/uuid](https://github.com/ramsey/uuid) library into a stand-alone package
