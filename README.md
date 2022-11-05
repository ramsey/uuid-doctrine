<h1 align="center">ramsey/uuid-doctrine</h1>

<p align="center">
    <strong>Use <a href="https://github.com/ramsey/uuid">ramsey/uuid</a> as a <a href="https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html">Doctrine field type</a></strong>
</p>

<p align="center">
    <a href="https://github.com/ramsey/uuid-doctrine"><img src="http://img.shields.io/badge/source-ramsey/uuid--doctrine-blue.svg?style=flat-square" alt="Source Code"></a>
    <a href="https://packagist.org/packages/ramsey/uuid-doctrine"><img src="https://img.shields.io/packagist/v/ramsey/uuid-doctrine.svg?style=flat-square&label=release" alt="Download Package"></a>
    <a href="https://php.net"><img src="https://img.shields.io/packagist/php-v/ramsey/uuid-doctrine.svg?style=flat-square&colorB=%238892BF" alt="PHP Programming Language"></a>
    <a href="https://github.com/ramsey/uuid-doctrine/blob/main/LICENSE"><img src="https://img.shields.io/packagist/l/ramsey/uuid-doctrine.svg?style=flat-square&colorB=darkcyan" alt="Read License"></a>
    <a href="https://github.com/ramsey/uuid-doctrine/actions/workflows/continuous-integration.yml"><img src="https://img.shields.io/github/actions/workflow/status/ramsey/uuid-doctrine/continuous-integration.yml?branch=main&logo=github&style=flat-square" alt="Build Status"></a>
    <a href="https://codecov.io/gh/ramsey/uuid-doctrine"><img src="https://img.shields.io/codecov/c/gh/ramsey/uuid-doctrine?label=codecov&logo=codecov&style=flat-square" alt="Codecov Code Coverage"></a>
    <a href="https://shepherd.dev/github/ramsey/uuid-doctrine"><img src="https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fshepherd.dev%2Fgithub%2Framsey%2Fuuid-doctrine%2Fcoverage" alt="Psalm Type Coverage"></a>
</p>

The ramsey/uuid-doctrine package provides the ability to use
[ramsey/uuid][ramsey-uuid] as a [Doctrine field type][doctrine-field-type].

This project adheres to a [code of conduct](CODE_OF_CONDUCT.md).
By participating in this project and its community, you are expected to
uphold this code.

## Installation

Install this package as a dependency using [Composer](https://getcomposer.org).

``` bash
composer require ramsey/uuid-doctrine
```

## Usage

### Configuration

To configure Doctrine to use ramsey/uuid as a field type, you'll need to set up
the following in your bootstrap:

``` php
\Doctrine\DBAL\Types\Type::addType('uuid', 'Ramsey\Uuid\Doctrine\UuidType');
```

In Symfony:

``` yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            uuid: Ramsey\Uuid\Doctrine\UuidType
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

In Laravel:

```php
<?php
// config/doctrine.php
    'custom_types'               => [
        \Ramsey\Uuid\Doctrine\UuidType::NAME => \Ramsey\Uuid\Doctrine\UuidType::class
    ],
```

In [roave/psr-container-doctrine](https://github.com/Roave/psr-container-doctrine):

```php
<?php
use Ramsey\Uuid\Doctrine\UuidType;

return [
    'doctrine' => [
        'types' => [
            UuidType::NAME => UuidType::class,
        ],
        /* ... */
    ],
    /* ... */
];
```

### Mappings

Then, in your models, you may annotate properties by setting the `@Column`
type to `uuid`, and defining a custom generator of `Ramsey\Uuid\UuidGenerator`.
Doctrine will handle the rest.

``` php
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

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
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}
```

or, as follows, with [PHP 8 attributes](https://www.php.net/attributes) and [type declarations](https://www.php.net/types.declarations):

``` php
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: "products")
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected UuidInterface|string $id;

    public function getId(): string
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

### Binary database columns

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
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            uuid_binary:  Ramsey\Uuid\Doctrine\UuidBinaryType
# Uncomment if using doctrine/orm <2.8
        # mapping_types:
            # uuid_binary: binary
```

Then, when annotating model class properties, use `uuid_binary` instead of `uuid`:

    @Column(type="uuid_binary")

### InnoDB-optimised binary UUIDs - deprecated

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
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            uuid_binary_ordered_time: Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType
# Uncomment if using doctrine/orm <2.8
        # mapping_types:
            # uuid_binary_ordered_time: binary
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

### InnoDB-optimised binary UUIDs - new way

With the introduction of new
[UUID types](https://www.ietf.org/archive/id/draft-peabody-dispatch-new-uuid-format-04.html)
(including sortable, unix epoch based UUID version 7) it is now recommended
to use regular `uuid_binary` with `Ramsey\Uuid\Doctrine\UuidV7Generator` for primary keys.

In your bootstrap, place the following:

``` php
\Doctrine\DBAL\Types\Type::addType('uuid_binary', 'Ramsey\Uuid\Doctrine\UuidBinaryType');
$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('uuid_binary', 'binary');
```

In Symfony:

``` yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            uuid_binary:  Ramsey\Uuid\Doctrine\UuidBinaryType
# Uncomment if using doctrine/orm <2.8
        # mapping_types:
            # uuid_binary: binary
```

Then, in your models, you may annotate properties by setting the `@Column`
type to `uuid_binary`, and defining a custom generator of
`Ramsey\Uuid\UuidV7Generator`. Doctrine will handle the rest.

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
     * @Column(type="uuid_binary", unique=true)
     * @GeneratedValue(strategy="CUSTOM")
     * @CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidV7Generator")
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
<id name="id" column="id" type="uuid_binary">
    <generator strategy="CUSTOM"/>
    <custom-id-generator class="Ramsey\Uuid\Doctrine\UuidV7Generator"/>
</id>
```

### Working with binary identifiers

When working with binary identifiers you may wish to convert them into a readable format.
As of MySql 8.0 you can use the BIN_TO_UUID and UUID_TO_BIN functions documented [here](https://dev.mysql.com/doc/refman/8.0/en/miscellaneous-functions.html).
The second argument determines if the byte order should be swapped, therefore when using ```uuid_binary``` you should pass 0
and when using ```uuid_binary_ordered_time``` you should pass 1.

For other versions you can use the following:

``` sql
DELIMITER $$

CREATE
    FUNCTION BIN_TO_UUID(bin_uuid BINARY(16), swap_flag BOOLEAN)
    RETURNS CHAR(36)
    DETERMINISTIC
    BEGIN
       DECLARE hex_uuid CHAR(32);
       SET hex_uuid = HEX(bin_uuid);
       RETURN LOWER(CONCAT(
            IF(swap_flag, SUBSTR(hex_uuid, 9, 8),SUBSTR(hex_uuid, 1, 8)), '-',
            IF(swap_flag, SUBSTR(hex_uuid, 5, 4),SUBSTR(hex_uuid, 9, 4)), '-',
            IF(swap_flag, SUBSTR(hex_uuid, 1, 4),SUBSTR(hex_uuid, 13, 4)), '-',
            SUBSTR(hex_uuid, 17, 4), '-',
            SUBSTR(hex_uuid, 21)
        ));
    END$$


CREATE
    FUNCTION UUID_TO_BIN(str_uuid CHAR(36), swap_flag BOOLEAN)
    RETURNS BINARY(16)
    DETERMINISTIC
    BEGIN
      RETURN UNHEX(CONCAT(
          IF(swap_flag, SUBSTR(str_uuid, 15, 4),SUBSTR(str_uuid, 1, 8)),
          SUBSTR(str_uuid, 10, 4),
          IF(swap_flag, SUBSTR(str_uuid, 1, 8),SUBSTR(str_uuid, 15, 4)),
          SUBSTR(str_uuid, 20, 4),
          SUBSTR(str_uuid, 25))
      );
    END$$

DELIMITER ;
```

Tests:

```
mysql> select '07a2f327-103a-11e9-8025-00ff5d11a779' as uuid, BIN_TO_UUID(UUID_TO_BIN('07a2f327-103a-11e9-8025-00ff5d11a779', 0), 0) as flip_flop;
+--------------------------------------+--------------------------------------+
| uuid                                 | flip_flop                            |
+--------------------------------------+--------------------------------------+
| 07a2f327-103a-11e9-8025-00ff5d11a779 | 07a2f327-103a-11e9-8025-00ff5d11a779 |
+--------------------------------------+--------------------------------------+
1 row in set (0.00 sec)

mysql> select '07a2f327-103a-11e9-8025-00ff5d11a779' as uuid, BIN_TO_UUID(UUID_TO_BIN('07a2f327-103a-11e9-8025-00ff5d11a779', 1), 1) as flip_flop;
+--------------------------------------+--------------------------------------+
| uuid                                 | flip_flop                            |
+--------------------------------------+--------------------------------------+
| 07a2f327-103a-11e9-8025-00ff5d11a779 | 07a2f327-103a-11e9-8025-00ff5d11a779 |
+--------------------------------------+--------------------------------------+
1 row in set (0.00 sec)
```

### More information

For more information on getting started with Doctrine, check out the "[Getting
Started with Doctrine][doctrine-getting-started]" tutorial.

## Contributing

Contributions are welcome! To contribute, please familiarize yourself with
[CONTRIBUTING.md](CONTRIBUTING.md).

## Coordinated Disclosure

Keeping user information safe and secure is a top priority, and we welcome the
contribution of external security researchers. If you believe you've found a
security issue in software that is maintained in this repository, please read
[SECURITY.md][] for instructions on submitting a vulnerability report.

## ramsey/uuid-doctrine for enterprise

Available as part of the Tidelift Subscription.

The maintainers of ramsey/uuid-doctrine and thousands of other packages are
working with Tidelift to deliver commercial support and maintenance for the open
source packages you use to build your applications. Save time, reduce risk, and
improve code health, while paying the maintainers of the exact packages you use.
[Learn more.](https://tidelift.com/subscription/pkg/packagist-ramsey-uuid-doctrine?utm_source=packagist-ramsey-uuid-doctrine&utm_medium=referral&utm_campaign=enterprise&utm_term=repo)

## Copyright and License

The ramsey/uuid-doctrine library is copyright © [Ben Ramsey](https://benramsey.com/) and
licensed for use under the MIT License (MIT). Please see [LICENSE](LICENSE) for more
information.

[percona-optimized-uuids]: https://www.percona.com/blog/2014/12/19/store-uuid-optimized-way/
[benramsey-com-uuid-talk]: https://benramsey.com/talks/2016/11/tnphp-uuid/
[ramsey-uuid]: https://github.com/ramsey/uuid
[doctrine-field-type]: https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html
[doctrine-getting-started]: https://www.doctrine-project.org/projects/doctrine-orm/en/current/tutorials/getting-started.html
[slide 58]: https://speakerdeck.com/ramsey/identify-all-the-things-with-uuids-true-north-php-2016?slide=58
[security.md]: https://github.com/ramsey/uuid-doctrine/blob/main/SECURITY.md
