# Ramsey\Uuid\Doctrine Changelog

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
