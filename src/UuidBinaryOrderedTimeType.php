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

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;
use Throwable;

use function bin2hex;
use function is_object;
use function is_string;
use function method_exists;

/**
 * Field type mapping for the Doctrine Database Abstraction Layer (DBAL).
 *
 * UUID fields will be stored as a binary in the database and converted back to
 * the Uuid value object when querying.
 */
class UuidBinaryOrderedTimeType extends Type
{
    public const NAME = 'uuid_binary_ordered_time';

    public const ASSERT_FORMAT = 'UuidV1';

    private ?UuidFactory $factory = null;

    private ?OrderedTimeCodec $codec = null;

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getBinaryTypeDeclarationSQL(
            [
                'length' => '16',
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

        if (!is_string($value) || $value === '') {
            return null;
        }

        try {
            return $this->decode($value);
        } catch (Throwable $e) {
            throw ConversionException::conversionFailed($value, self::NAME);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof UuidInterface) {
            return $this->encode($value);
        }

        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_string($value) || (is_object($value) && method_exists($value, '__toString'))) {
                $uuid = $this->getUuidFactory()->fromString((string) $value);

                return $this->encode($uuid);
            }
        } catch (Throwable $e) {
            // Ignore the exception and pass through.
        }

        throw ConversionException::conversionFailed($value, self::NAME);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public function getBindingType(): int
    {
        return ParameterType::BINARY;
    }

    /**
     * Creates/returns a UuidFactory instance that uses a specific codec
     * that creates a binary that can be time-ordered
     */
    protected function getUuidFactory(): UuidFactory
    {
        if ($this->factory === null) {
            $this->factory = new UuidFactory();
        }

        return $this->factory;
    }

    protected function getCodec(): OrderedTimeCodec
    {
        if ($this->codec === null) {
            $this->codec = new OrderedTimeCodec(
                $this->getUuidFactory()->getUuidBuilder(),
            );
        }

        return $this->codec;
    }

    /**
     * Using this type only makes sense with Uuid version 1 as this is the only
     * kind of UUID that can be time-ordered. Passing any other UUID into
     * this type is likely a mistake
     *
     * @throws ConversionException
     */
    private function assertUuidV1(UuidInterface $value): void
    {
        /** @psalm-suppress DeprecatedMethod */
        if ($value->getVersion() !== 1) {
            throw ConversionException::conversionFailedFormat(
                $value->toString(),
                self::NAME,
                self::ASSERT_FORMAT,
            );
        }
    }

    /**
     * @throws ConversionException
     */
    private function encode(UuidInterface $uuid): string
    {
        $this->assertUuidV1($uuid);

        return $this->getCodec()->encodeBinary($uuid);
    }

    /**
     * @throws ConversionException
     */
    private function decode(string $bytes): UuidInterface
    {
        try {
            $decoded = $this->getCodec()->decodeBytes($bytes);
        } catch (UnsupportedOperationException $e) {
            throw ConversionException::conversionFailedFormat(
                bin2hex($bytes),
                self::NAME,
                self::ASSERT_FORMAT,
            );
        }

        $this->assertUuidV1($decoded);

        return $decoded;
    }
}
