<?php

/**
 * This file is part of the ramsey/uuid-doctrine library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <http://benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Throwable;

use function class_exists;
use function is_object;
use function is_resource;
use function is_string;
use function method_exists;
use function stream_get_contents;

/**
 * Field type mapping for the Doctrine Database Abstraction Layer (DBAL).
 *
 * UUID fields will be stored as a string in the database and converted back to
 * the Uuid value object when querying.
 */
class UuidBinaryType extends Type
{
    use GetBindingTypeImplementation;

    public const NAME = 'uuid_binary';

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getBinaryTypeDeclarationSQL(
            [
                'length' => 16,
                'fixed' => true,
            ],
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?UuidInterface
    {
        if ($value instanceof UuidInterface) {
            return $value;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        if (!is_string($value) || $value === '') {
            return null;
        }

        try {
            $uuid = Uuid::fromBytes($value);
        } catch (Throwable $e) {
            throw class_exists(ValueNotConvertible::class)
                ? ValueNotConvertible::new($value, self::NAME)
                : ConversionException::conversionFailed($value, self::NAME);
        }

        return $uuid;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof UuidInterface) {
            return $value->getBytes();
        }

        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_string($value) || (is_object($value) && method_exists($value, '__toString'))) {
                return Uuid::fromString((string) $value)->getBytes();
            }
        } catch (Throwable $e) {
            // Ignore the exception and pass through.
        }

        throw class_exists(ValueNotConvertible::class)
            ? ValueNotConvertible::new($value, self::NAME)
            : ConversionException::conversionFailed($value, self::NAME);
    }

    /**
     * {@inheritDoc}
     *
     * @deprecated this method is deprecated and will be removed in Uuid-Doctrine 3.0
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * {@inheritDoc}
     *
     * @deprecated this method is deprecated and will be removed in Uuid-Doctrine 3.0
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
