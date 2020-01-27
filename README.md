# ramsey/uuid-doctrine

[![Source Code][badge-source]][source]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![Build Status][badge-build]][build]
[![Coverage Status][badge-coverage]][coverage]
[![Total Downloads][badge-downloads]][downloads]

The ramsey/uuid-doctrine package provides the ability to use
[ramsey/uuid][ramsey-uuid] as a [Doctrine field type][doctrine-field-type].

This project adheres to a [Contributor Code of Conduct][conduct]. By
participating in this project and its community, you are expected to uphold this
code.

## Installation

The preferred method of installation is via [Packagist][] and [Composer][]. Run
the following command to install the package and add it as a requirement to
your project's `composer.json`:

```bash
composer require ramsey/uuid-doctrine
```

## Examples

### Configuration

To configure Doctrine to use ramsey/uuid as a field type, you'll need to set up
the following in your bootstrap:

``` php
\Doctrine\DBAL\Types\Type::addType('uuid', 'Ramsey\Uuid\Doctrine\UuidType');
```

In Symfony:

``` yaml
# app/config/config.yml
doctrine:
    dbal:
        types:
            uuid:  Ramsey\Uuid\Doctrine\UuidType
```

In Zend Framework:

```php
<?php
// module.config.php
use Ramsey\Uuid\Doctrine\UuidType;

return [
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'types' => [
                    UuidType::NAME => UuidType::class,
```

### Usage

Then, in your models, you may annotate properties by setting the `@Column`
type to `uuid`, and defining a custom generator of `Ramsey\Uuid\UuidGenerator`.
Doctrine will handle the rest.

``` php
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="products")
 */
class Product
{
    /**
     * @var \Ramsey\Uuid\UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}
```

If you use the XML Mapping instead of PHP annotations.

``` xml
<id name="id" column="id" type="uuid">
    <generator strategy="CUSTOM"/>
    <custom-id-generator class="Ramsey\Uuid\Doctrine\UuidGenerator"/>
</id>
```

You can also use the YAML Mapping.

``` yaml
id:
    id:
        type: uuid
        generator:
            strategy: CUSTOM
        customIdGenerator:
            class: Ramsey\Uuid\Doctrine\UuidGenerator
```

### Binary Database Columns

In the previous example, Doctrine will create a database column of type `CHAR(36)`,
but you may also use this library to store UUIDs as binary strings. The
`UuidBinaryType` helps accomplish this.

In your bootstrap, place the following:

``` php
\Doctrine\DBAL\Types\Type::addType('uuid_binary', 'Ramsey\Uuid\Doctrine\UuidBinaryType');
$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('uuid_binary', 'binary');
```

In Symfony:

``` yaml
# app/config/config.yml
doctrine:
    dbal:
        types:
            uuid_binary:  Ramsey\Uuid\Doctrine\UuidBinaryType
        mapping_types:
            uuid_binary: binary
```

Then, when annotating model class properties, use `uuid_binary` instead of `uuid`:

    @Column(type="uuid_binary")

### InnoDB-optimised binary UUIDs
More suitable if you want to use UUIDs as primary key. Note that this can cause
unintended effects if:

* decoding bytes that were not generated using this method
* another code (that isn't aware of this method) attempts to decode the
  resulting bytes

More information in this [Percona article][percona-optimized-uuids]
and [UUID Talk by Ben Ramsey][benramsey-com-uuid-talk] (starts at [slide 58][]).

``` php
\Doctrine\DBAL\Types\Type::addType('uuid_binary_ordered_time', 'Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType');
$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('uuid_binary_ordered_time', 'binary');
```

In Symfony:

 ``` yaml
# app/config/config.yml
doctrine:
    dbal:
        types:
            uuid_binary_ordered_time: Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType
        mapping_types:
            uuid_binary_ordered_time: binary
```

Then, in your models, you may annotate properties by setting the `@Column`
type to `uuid_binary_ordered_time`, and defining a custom generator of
`Ramsey\Uuid\UuidOrderedTimeGenerator`. Doctrine will handle the rest.

``` php
/**
 * @Entity
 * @Table(name="products")
 */
class Product
{
    /**
     * @var \Ramsey\Uuid\UuidInterface
     *
     * @Id
     * @Column(type="uuid_binary_ordered_time", unique=true)
     * @GeneratedValue(strategy="CUSTOM")
     * @CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator")
     */
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}
```

If you use the XML Mapping instead of PHP annotations.

``` xml
<id name="id" column="id" type="uuid_binary_ordered_time">
    <generator strategy="CUSTOM"/>
    <custom-id-generator class="Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator"/>
</id>
```

You can use this format in mysql cli with this two functions:

``` sql
CREATE
  FUNCTION `uuid_to_ouuid`(uuid BINARY(36))
  RETURNS binary(16) DETERMINISTIC
  RETURN UNHEX(CONCAT(
  SUBSTR(uuid, 15, 4),
  SUBSTR(uuid, 10, 4),
  SUBSTR(uuid, 1, 8),
  SUBSTR(uuid, 20, 4),
  SUBSTR(uuid, 25, 12)
));

CREATE
  FUNCTION ouuid_to_uuid(uuid BINARY(16))
  RETURNS VARCHAR(36)
  RETURN LOWER(CONCAT(
  SUBSTR(HEX(uuid), 9, 8), '-',
  SUBSTR(HEX(uuid), 5, 4), '-',
  SUBSTR(HEX(uuid), 1, 4), '-',
  SUBSTR(HEX(uuid), 17,4), '-',
  SUBSTR(HEX(uuid), 21, 12 )
));
```

Test:

```
mysql> select '07a2f327-103a-11e9-8025-00ff5d11a779' as uuid , ouuid_to_uuid(uuid_to_ouuid('07a2f327-103a-11e9-8025-00ff5d11a779')) as flip_flop;
+--------------------------------------+--------------------------------------+
| uuid                                 | flip_flop                            |
+--------------------------------------+--------------------------------------+
| 07a2f327-103a-11e9-8025-00ff5d11a779 | 07a2f327-103a-11e9-8025-00ff5d11a779 |
+--------------------------------------+--------------------------------------+
1 row in set (0.00 sec)
```


### More Information

For more information on getting started with Doctrine, check out the "[Getting
Started with Doctrine][doctrine-getting-started]" tutorial.

## Contributing

Contributions are welcome! Please read [CONTRIBUTING][] for details.

## Copyright and License

The ramsey/uuid-doctrine library is copyright © [Ben Ramsey](https://benramsey.com/) and
licensed for use under the MIT License (MIT). Please see [LICENSE][] for more
information.

[percona-optimized-uuids]: https://www.percona.com/blog/2014/12/19/store-uuid-optimized-way/
[benramsey-com-uuid-talk]: https://benramsey.com/talks/2016/11/tnphp-uuid/

[ramsey-uuid]: https://github.com/ramsey/uuid
[conduct]: https://github.com/ramsey/uuid-doctrine/blob/master/.github/CODE_OF_CONDUCT.md
[doctrine-field-type]: https://www.doctrine-project.org/projects/doctrine-dbal/en/2.10/reference/types.html
[packagist]: https://packagist.org/packages/ramsey/uuid-doctrine
[composer]: http://getcomposer.org/
[contributing]: https://github.com/ramsey/uuid-doctrine/blob/master/.github/CONTRIBUTING.md
[doctrine-getting-started]: https://www.doctrine-project.org/projects/doctrine-orm/en/current/tutorials/getting-started.html
[slide 58]: https://speakerdeck.com/ramsey/identify-all-the-things-with-uuids-true-north-php-2016?slide=58

[badge-source]: http://img.shields.io/badge/source-ramsey/uuid--doctrine-blue.svg?style=flat-square
[badge-release]: https://img.shields.io/packagist/v/ramsey/uuid-doctrine.svg?style=flat-square
[badge-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[badge-build]: https://img.shields.io/travis/ramsey/uuid-doctrine/master.svg?style=flat-square
[badge-coverage]: https://img.shields.io/coveralls/ramsey/uuid-doctrine/master.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/ramsey/uuid-doctrine.svg?style=flat-square

[source]: https://github.com/ramsey/uuid-doctrine
[release]: https://packagist.org/packages/ramsey/uuid-doctrine
[license]: https://github.com/ramsey/uuid-doctrine/blob/master/LICENSE
[build]: https://travis-ci.org/ramsey/uuid-doctrine
[coverage]: https://coveralls.io/r/ramsey/uuid-doctrine?branch=master
[downloads]: https://packagist.org/packages/ramsey/uuid-doctrine
